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
    public function index()
{
    $bookings = PromotionBooking::with(['promotion', 'user'])
        ->latest()
        ->paginate(10);

    return view('admin.promotion-bookings.index', compact('bookings'));
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
}