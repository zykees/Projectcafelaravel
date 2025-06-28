<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(Request $request)
    {
        $query = Order::with(['user', 'items.product', 'promotion']);

        // Filter by status
       if ($request->filled('status')) {
    $query->where('status', $request->status);
}

        // Filter by payment status
        if ($request->filled('payment_status')) {
    $query->where('payment_status', $request->payment_status);
}

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search by order code or customer name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_code', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Sort orders
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'total_asc':
                $query->orderBy('total_amount', 'asc');
                break;
            case 'total_desc':
                $query->orderBy('total_amount', 'desc');
                break;
            case 'oldest':
                $query->oldest();
                break;
            default:
                $query->latest();
                break;
        }

        $orders = $query->paginate(10)->withQueryString();

        // Get statistics for dashboard
        $stats = [
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'completed_orders' => Order::where('status', 'completed')->count(),
            'today_orders' => Order::whereDate('created_at', Carbon::today())->count(),
            // 'total_revenue' => Order::where('status', 'completed')->sum('total_amount'),
        // ถ้าอยากให้ total_revenue ตรงกับยอดสุทธิจริง ให้คำนวณใหม่
        ];

        return view('admin.orders.index', compact('orders', 'stats'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items.product', 'promotion']);
        
        $stats = [
            'subtotal' => $order->items->sum(function($item) {
                return $item->quantity * $item->price;
            }),
            'total_items' => $order->items->sum('quantity'),
            'discount' => $order->discount_amount ?? 0,
            'final_total' => $order->total_amount
        ];

        return view('admin.orders.show', compact('order', 'stats'));
    }

    public function edit(Order $order)
    {
        $order->load(['items.product', 'promotion']);
        $statuses = [
            'pending' => 'รอดำเนินการ',
            'processing' => 'กำลังดำเนินการ',
            'completed' => 'เสร็จสิ้น',
            'cancelled' => 'ยกเลิก'
        ];
        
        $paymentStatuses = [
            'pending' => 'รอชำระเงิน',
            'paid' => 'ชำระแล้ว',
            'failed' => 'การชำระเงินล้มเหลว'
        ];

        return view('admin.orders.edit', compact('order', 'statuses', 'paymentStatuses'));
    }

    public function update(Request $request, Order $order)
{
    $validated = $request->validate([
        'status' => 'required|in:pending,processing,completed,cancelled',
        'payment_status' => 'required|in:pending,paid,failed',
        'notes' => 'nullable|string|max:500'
    ]);

    try {
        DB::beginTransaction();
        
        $oldStatus = $order->status;
        $oldPaymentStatus = $order->payment_status;
        
        // Update order status
        $order->update($validated);

        // Check if order is now completed and paid
        if ($validated['status'] === 'completed' && $validated['payment_status'] === 'paid') {
            // Decrease stock for each product
            foreach ($order->items as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    // Check if enough stock
                    if ($product->stock < $item->quantity) {
                        throw new \Exception("สินค้า {$product->name} มีจำนวนไม่เพียงพอในคลัง");
                    }
                    // Decrease stock
                    $product->decrement('stock', $item->quantity);
                }
            }
        }

        // If order was completed but now cancelled, restore stock
        if ($oldStatus === 'completed' && $validated['status'] === 'cancelled') {
            foreach ($order->items as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    // Restore stock
                    $product->increment('stock', $item->quantity);
                }
            }
        }

        DB::commit();

        return redirect()
            ->route('admin.orders.index')
            ->with('success', 'อัพเดตออเดอร์สำเร็จ');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()
            ->withInput()
            ->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}

    public function updateStatus(Request $request, Order $order)
{
    $validated = $request->validate([
        'status' => 'required|in:pending,processing,completed,cancelled'
    ]);

    try {
        DB::beginTransaction();
        
        $oldStatus = $order->status;
        
        // Update order status
        $order->update($validated);

        // If order becomes completed and is already paid
        if ($validated['status'] === 'completed' && $order->payment_status === 'paid') {
            foreach ($order->items as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    if ($product->stock < $item->quantity) {
                        throw new \Exception("สินค้า {$product->name} มีจำนวนไม่เพียงพอในคลัง");
                    }
                    $product->decrement('stock', $item->quantity);
                }
            }
        }

        // If completed order is cancelled
        if ($oldStatus === 'completed' && $validated['status'] === 'cancelled') {
            foreach ($order->items as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->increment('stock', $item->quantity);
                }
            }
        }

        DB::commit();
        return back()->with('success', 'อัพเดตสถานะสำเร็จ');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}

    public function destroy(Order $order)
    {
        try {
            if ($order->status === 'completed') {
                return back()->with('error', 'ไม่สามารถลบออเดอร์ที่เสร็จสิ้นแล้วได้');
            }

            DB::beginTransaction();

            // Restore product quantities if order is not cancelled
            if ($order->status !== 'cancelled') {
                foreach ($order->items as $item) {
                    $item->product->increment('stock', $item->quantity);
                }
            }

            // Delete related records
            $order->items()->delete();
            $order->delete();

            DB::commit();
            return redirect()
                ->route('admin.orders.index')
                ->with('success', 'ลบออเดอร์สำเร็จ');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    public function print(Order $order)
    {
        $order->load(['user', 'items.product', 'promotion']);
        return view('admin.orders.print', compact('order'));
    }
    public function getCalculatedTotalAttribute()
{
    return $this->items->sum(function($item) {
        $discountPercent = $item->product->discount_percent ?? 0;
        $originalPrice = $item->product->price;
        $discountedPrice = $discountPercent > 0
            ? round($originalPrice * (1 - $discountPercent/100), 2)
            : $originalPrice;
        return $discountedPrice * $item->quantity;
    });
}

public function getCalculatedDiscountAttribute()
{
    return $this->items->sum(function($item) {
        $discountPercent = $item->product->discount_percent ?? 0;
        $originalPrice = $item->product->price;
        $discountedPrice = $discountPercent > 0
            ? round($originalPrice * (1 - $discountPercent/100), 2)
            : $originalPrice;
        return ($originalPrice - $discountedPrice) * $item->quantity;
    });
}

public function getCalculatedSubtotalAttribute()
{
    return $this->items->sum(function($item) {
        return $item->product->price * $item->quantity;
    });
}
    public function export(Request $request)
    {
        // Add export functionality if needed
    }
}