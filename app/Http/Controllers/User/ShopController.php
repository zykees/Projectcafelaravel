<?php

namespace App\Http\Controllers\User;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order; // Add this line
use Illuminate\Http\Request;
use Cart; // Assuming you are using a package like "darryldecode/cart" for cart management
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\User\UserOrderController;


class ShopController extends Controller
{
public function index(Request $request)
{
    $query = \App\Models\Product::query();  

    // Filter by category (เฉพาะถ้ามีเลือก)
    if ($request->filled('category')) {
        $query->where('category_id', $request->category);
    }

    // Filter by search (ถ้ามี)
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    // เงื่อนไขอื่นๆ เช่น สินค้าพร้อมขาย
    $query->where('status', 'available');

    $products = $query->paginate(16)->appends($request->query());
    $categories = \App\Models\Category::where('status', 'active')->get();

    return view('User.shop.index', compact('products', 'categories'));
}

    public function show(Product $product)
    {
        // Load relationships
        $product->load('category');

        // Get related products from same category
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('status', 'available')
            ->where('stock', '>', 0)
            ->take(4)
            ->get();

        return view('User.shop.product', compact('product', 'relatedProducts'));
    }

    public function addToCart(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => "required|integer|min:1|max:{$product->stock}"
        ]);

        // Check if product is available
        if ($product->status !== 'available' || $product->stock <= 0) {
            return back()->with('error', 'สินค้าไม่พร้อมขายในขณะนี้');
        }

        // Add to cart using Cart facade
        Cart::add([
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'quantity' => $request->quantity,
            'attributes' => [
                'image' => $product->image
            ]
        ]);

        return back()->with('success', 'เพิ่มสินค้าลงตะกร้าเรียบร้อยแล้ว');
    }
    public function checkout()
{
    if (Cart::isEmpty()) {
        return redirect()->route('user.shop.cart')
            ->with('error', 'ตะกร้าสินค้าว่างเปล่า');
    }

    $user = auth()->user();
    return view('User.shop.checkout', compact('user'));
}

    public function processCheckout(Request $request)
    {
        if (Cart::isEmpty()) {
            return redirect()->route('user.shop.cart')
                ->with('error', 'ตะกร้าสินค้าว่างเปล่า');
        }

        $validated = $request->validate([
            'shipping_name' => 'required|string|max:255',
            'shipping_address' => 'required|string',
            'shipping_phone' => 'required|string',
            'payment_method' => 'required|in:bank_transfer'
        ]);

          try {
        DB::beginTransaction();

        $order = Order::create([
            'user_id' => auth()->id(),
            'order_code' => 'ORD-' . time(),
            'total_amount' => Cart::getTotal(),
            'shipping_name' => $validated['shipping_name'],
            'shipping_address' => $validated['shipping_address'],
            'shipping_phone' => $validated['shipping_phone'],
            'payment_method' => $validated['payment_method'],
            'status' => 'pending',
            'payment_status' => 'pending'
        ]);

        foreach(Cart::getContent() as $item) {
            $order->items()->create([
                'product_id' => $item->id,
                'quantity' => $item->quantity,
                'price' => $item->price
            ]);
        }

        DB::commit();
        Cart::clear();

        // เปลี่ยน redirect ให้ไปที่ route ที่ถูกต้อง
        return redirect()->route('user.orders.show', ['order' => $order])
            ->with('success', 'สั่งซื้อสำเร็จ! กรุณาแจ้งชำระเงินเพื่อดำเนินการต่อ');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
    }

    
    public function showOrder(Order $order)
    {
        // Verify order belongs to current user
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }

        // Load relationships
        $order->load(['items.product', 'user']);

        return view('User.orders.show', compact('order'));
    }
     public function downloadQuotation(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $pdf = PDF::loadView('User.orders.quotation', [
            'order' => $order->load('items.product')
        ]);

        return $pdf->download("ใบเสนอราคา-{$order->order_code}.pdf");
    }
}