<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use App\Models\PromotionBooking;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PromotionBookingController extends Controller
{
    public function index()
{
    $bookings = auth()->user()
        ->promotionBookings()
        ->with('promotion')
        ->latest()
        ->paginate(10);

    return view('User.promotion-bookings.index', compact('bookings'));
}
    public function create(Promotion $promotion)
    {
        if ($promotion->isExpired()) {
            return back()->with('error', 'โปรโมชั่นนี้ไม่สามารถจองได้แล้ว');
        }

        return view('User.promotion-bookings.create', compact('promotion'));
    }

    public function show(PromotionBooking $booking)
    {
        try {
            $this->authorize('view', $booking);
            
            return view('User.promotion-bookings.show', compact('booking'));
        } catch (\Exception $e) {
            return back()->with('error', 'ไม่มีสิทธิ์เข้าถึงข้อมูลการจองนี้');
        }
    }

   public function store(Request $request, Promotion $promotion)
    {
        try {
            $this->authorize('create', PromotionBooking::class);

            // Validate the request
            $validated = $request->validate([
                'number_of_participants' => [
                    'required',
                    'integer',
                    'min:1',
                    'max:' . $promotion->getRemainingSlots()
                ],
                'activity_date' => [
                    'required',
                    'date',
                    'after:today',
                    'before_or_equal:' . $promotion->ends_at->format('Y-m-d')
                ],
                'activity_time' => [
                    'required',
                    'date_format:H:i',
                    'after_or_equal:' . $promotion->starts_at->format('H:i'),
                    'before_or_equal:' . $promotion->ends_at->format('H:i')
                ],
                'note' => 'nullable|string|max:500'
            ]);

            // Calculate prices
            $totalPrice = $promotion->price_per_person * $validated['number_of_participants'];
            $discountAmount = ($totalPrice * $promotion->discount) / 100;
            $finalPrice = $totalPrice - $discountAmount;

            // Create booking
            $booking = PromotionBooking::create([
                'user_id' => auth()->id(),
                'promotion_id' => $promotion->id,
                'booking_code' => 'PB' . time() . rand(1000, 9999),
                'number_of_participants' => $validated['number_of_participants'],
                'activity_date' => $validated['activity_date'],
                'activity_time' => $validated['activity_time'],
                'note' => $validated['note'],
                'total_price' => $totalPrice,
                'discount_amount' => $discountAmount,
                'final_price' => $finalPrice,
                'status' => 'pending',
                'payment_status' => 'pending'
            ]);

            // Update promotion participants count
            $promotion->increment('current_participants', $validated['number_of_participants']);

            return redirect()->route('user.promotion-bookings.show', $booking)
                           ->with('success', 'จองกิจกรรมสำเร็จ กรุณาชำระเงินเพื่อยืนยันการจอง');

        } catch (\Exception $e) {
            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage())
                        ->withInput();
        }
    }
    public function downloadQuotation(PromotionBooking $booking)
    {
        try {
            // ตรวจสอบสิทธิ์การเข้าถึง
            $this->authorize('view', $booking);

            // สร้าง PDF จาก view
            $pdf = PDF::loadView('User.promotion-bookings.quotation', [
                'booking' => $booking,
                'user' => auth()->user(),
                'promotion' => $booking->promotion
            ]);

            // ตั้งชื่อไฟล์
            $filename = 'quotation-' . $booking->booking_code . '.pdf';

            // ส่งไฟล์ให้ดาวน์โหลด
            return $pdf->download($filename);

        } catch (\Exception $e) {
            return back()->with('error', 'ไม่สามารถดาวน์โหลดใบเสนอราคาได้: ' . $e->getMessage());
        }
    }
//  public function uploadPayment(Request $request, PromotionBooking $promotionBooking)
// {
//     if ($promotionBooking->user_id !== auth()->id()) {
//         abort(403);
//     }

//     $request->validate([
//         'payment_slip' => 'required|image|mimes:jpeg,png,jpg|max:2048',
//         'payment_date' => 'required|date',
//         'payment_amount' => 'required|numeric|min:' . $promotionBooking->final_price
//     ], [
//         'payment_slip.required' => 'กรุณาเลือกไฟล์สลิปการโอนเงิน',
//         'payment_slip.image' => 'ไฟล์ต้องเป็นรูปภาพเท่านั้น',
//         'payment_slip.max' => 'ขนาดไฟล์ต้องไม่เกิน 2MB',
//         'payment_date.required' => 'กรุณาระบุวันที่โอนเงิน',
//         'payment_amount.required' => 'กรุณาระบุจำนวนเงิน',
//         'payment_amount.min' => 'จำนวนเงินต้องไม่น้อยกว่ายอดที่ต้องชำระ'
//     ]);

//     try {
//         // ตั้งชื่อไฟล์ใหม่
//         $filename = $promotionBooking->booking_code . '_' . time() . '.' . $request->file('payment_slip')->getClientOriginalExtension();

//         // อัพโหลดไฟล์ไปที่ storage/app/public/payment_slips
//         $request->file('payment_slip')->storeAs('public/payment_slips', $filename);

        
//         // เซฟ path แบบ public/payment_slips/ชื่อไฟล์ ลงฐานข้อมูล
//         $promotionBooking->update([
//             'payment_slip' => 'public/payment_slips/' . $filename,
//             'payment_date' => $request->payment_date,
//             'payment_amount' => $request->payment_amount,
//             'payment_status' => 'pending'
//         ]);

//         return back()->with('success', 'อัพโหลดสลิปการโอนเงินเรียบร้อยแล้ว รอการตรวจสอบ');

//     } catch (\Exception $e) {
//         return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
//     }
// }

   public function uploadPaymentSlip(Request $request, PromotionBooking $booking)
{
    try {
        $this->authorize('uploadPaymentSlip', $booking);

         $validated = $request->validate([
            'payment_slip' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'payment_date' => 'required',
            'payment_amount' => 'required|numeric|min:' . $booking->final_price
        ], [
            'payment_slip.required' => 'กรุณาเลือกไฟล์สลิปการโอนเงิน',
            'payment_slip.image' => 'ไฟล์ต้องเป็นรูปภาพเท่านั้น',
            'payment_slip.max' => 'ขนาดไฟล์ต้องไม่เกิน 2MB',
            'payment_date.required' => 'กรุณาระบุวันที่โอนเงิน',
            'payment_amount.required' => 'กรุณาระบุจำนวนเงิน',
            'payment_amount.min' => 'จำนวนเงินต้องไม่น้อยกว่ายอดที่ต้องชำระ'
        ]);
        // แปลง payment_date จาก input type="datetime-local"
        $paymentDate = $request->payment_date
            ? \Carbon\Carbon::parse(str_replace('T', ' ', $request->payment_date))
            : null;

        if ($request->hasFile('payment_slip')) {
            // Delete old slip if exists
            if ($booking->payment_slip) {
                Storage::delete($booking->payment_slip); // ลบไฟล์เดิม (ใช้ path เต็ม)
            }

            // Store new slip
            $file = $request->file('payment_slip');
            $filename = $booking->booking_code . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/payment_slips', $filename);

            // Update booking record
            $booking->update([
                'payment_slip' => $path,
                'payment_status' => 'pending',
                'payment_date' => $paymentDate,
                'payment_amount' => $request->payment_amount,
                'status' => 'pending'
            ]);

            return back()->with('success', 'อัพโหลดสลิปการโอนเงินสำเร็จ กรุณารอการตรวจสอบ');
        }

        return back()->with('error', 'กรุณาเลือกไฟล์สลิปการโอนเงิน');

    } catch (\Exception $e) {
        return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage())
                    ->withInput();
    }
}

}