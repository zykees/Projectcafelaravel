<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\PromotionBooking;
use App\Notifications\BookingStatusChanged;
use App\Notifications\PaymentStatusChanged;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
class PromotionBookingController extends Controller
{
   public function index(Request $request)
{
    $query = PromotionBooking::with(['promotion', 'user']);

    // Filter: สถานะการจอง
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // Filter: สถานะการชำระเงิน
    if ($request->filled('payment_status')) {
        $query->where('payment_status', $request->payment_status);
    }

    // Filter: กิจกรรม (promotion)
    if ($request->filled('promotion_id')) {
        $query->where('promotion_id', $request->promotion_id);
    }

    // Filter: วันที่จอง
    if ($request->filled('date_from')) {
        $query->whereDate('created_at', '>=', $request->date_from);
    }
    if ($request->filled('date_to')) {
        $query->whereDate('created_at', '<=', $request->date_to);
    }

    // Filter: ค้นหา (booking code, ชื่อผู้จอง, เบอร์, email)
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('booking_code', 'like', "%{$search}%")
              ->orWhereHas('user', function($uq) use ($search) {
                  $uq->where('name', 'like', "%{$search}%")
                     ->orWhere('email', 'like', "%{$search}%");
              })
              ->orWhereHas('promotion', function($pq) use ($search) {
                  $pq->where('title', 'like', "%{$search}%");
              });
        });
    }

    // Sort
    switch ($request->get('sort', 'latest')) {
        case 'oldest':
            $query->orderBy('created_at', 'asc');
            break;
        case 'total_desc':
            $query->orderBy('final_price', 'desc');
            break;
        case 'total_asc':
            $query->orderBy('final_price', 'asc');
            break;
        default:
            $query->orderBy('created_at', 'desc');
            break;
    }

    $bookings = $query->paginate(15)->appends($request->query());
    $promotions = \App\Models\Promotion::orderBy('title')->get();

    return view('admin.promotion-bookings.index', compact('bookings', 'promotions'));
}
    public function show(PromotionBooking $booking)
    {
        return view('admin.promotion-bookings.show', compact('booking'));
    }

    public function edit(PromotionBooking $booking)
    {
        return view('admin.promotion-bookings.edit', compact('booking'));
    }

    public function update(Request $request, PromotionBooking $booking)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled',
            'payment_status' => 'required|in:pending,paid,rejected',
            'admin_comment' => 'nullable|string|max:500'
        ]);

        $booking->update($validated);

        // Send notification to user about status change
        if($booking->wasChanged('status')) {
            $booking->user->notify(new BookingStatusChanged($booking));
        }

        return redirect()
            ->route('admin.promotion-bookings.show', $booking)
            ->with('success', 'อัพเดทสถานะการจองเรียบร้อย');
    }

   public function updatePaymentStatus(Request $request, PromotionBooking $booking)
{
    try {
        $validated = $request->validate([
            'payment_status' => 'required|in:pending,paid,rejected',
            'admin_comment' => 'nullable|string|max:500'
        ]);

        $booking->update($validated);

        // ใช้ try-catch แยกสำหรับการส่ง notification
        try {
            $booking->user->notify(new PaymentStatusChanged($booking));
        } catch (\Exception $e) {
            // Log error but don't stop the process
            Log::error('Failed to send notification: ' . $e->getMessage());
        }

        return back()->with('success', 'อัพเดทสถานะการชำระเงินเรียบร้อย');

    } catch (\Exception $e) {
        return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}

    
    public function downloadPaymentSlip(PromotionBooking $booking)
{
    if (!$booking->payment_slip) {
        return back()->with('error', 'ไม่พบไฟล์สลิปการโอนเงิน');
    }

    // แก้ไขการอ้างอิง path ให้ถูกต้อง
    $path = storage_path('app/public/' . $booking->payment_slip);
    
    if (!file_exists($path)) {
        return back()->with('error', 'ไฟล์ไม่พบในระบบ');
    }

    return response()->download($path);
}
   public function updateStatus(Request $request, PromotionBooking $booking)
{
    $request->validate([
        'status' => 'required|in:pending,confirmed,completed,cancelled'
    ]);
    $booking->status = $request->status;
    $booking->save();

    return redirect()->route('admin.promotion-bookings.index')
        ->with('success', 'อัปเดตสถานะการจองเรียบร้อยแล้ว');
}


}