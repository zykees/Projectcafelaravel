<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use App\Models\PromotionBooking;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class PromotionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

   public function index(Request $request)
{
    $query = Promotion::withCount(['bookings as total_bookings', 'bookings as confirmed_bookings' => function($query) {
        $query->where('status', 'confirmed');
    }]);

    // Filter by status
    if ($request->has('status')) {
        $query->where('status', $request->status);
    }

    $promotions = $query->latest()->paginate(10);
    return view('admin.promotions.index', compact('promotions'));
}

    public function create()
    {
        return view('admin.promotions.create');
    }

   public function store(Request $request)
{
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        'description' => 'required|string',
        'activity_details' => 'required|string',
        'max_participants' => 'required|integer|min:1',
        'price_per_person' => 'required|numeric|min:0',
        'discount' => 'nullable|numeric|min:0|max:100',
        'starts_at' => 'required|date',
        'ends_at' => 'required|date|after:starts_at',
        'location' => 'required|string',
        'included_items' => 'nullable|string',
        'status' => 'required|in:active,inactive'
    ]);
   try {
        // Handle image upload
        if ($request->hasFile('image')) {
            // Debug log
            Log::info('Uploading image for promotion');
            
            $image = $request->file('image');
            $path = $image->store('promotions', 'public');
            
            // Debug log
            Log::info('Image stored at: ' . $path);
            
            $validated['image'] = $path;
        }

        // เพิ่มบรรทัดนี้
        $validated['is_featured'] = $request->has('is_featured');
        $promotion = Promotion::create($validated);

        return redirect()
            ->route('admin.promotions.index')
            ->with('success', 'สร้างกิจกรรมสำเร็จ');

    } catch (\Exception $e) {
        Log::error('Error creating promotion: ' . $e->getMessage());
        return back()
            ->withInput()
            ->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}

    public function show(Promotion $promotion)
{
    $stats = [
        'total_bookings' => $promotion->bookings()->count(),
        'confirmed_bookings' => $promotion->bookings()->where('status', 'confirmed')->count(),
        'total_participants' => $promotion->current_participants,
        'total_revenue' => $promotion->bookings()
            ->where('status', 'confirmed')
            ->sum('final_price'),
        'remaining_slots' => $promotion->getRemainingSlots()
    ];

    $recentBookings = $promotion->bookings()
        ->with('user')
        ->latest()
        ->take(5)
        ->get();

    return view('admin.promotions.show', compact('promotion', 'stats', 'recentBookings'));
}

    public function edit(Promotion $promotion)
    {
        return view('admin.promotions.edit', compact('promotion'));
    }

    public function update(Request $request, Promotion $promotion)
{
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'activity_details' => 'required|string',
        'max_participants' => 'required|integer|min:1',
        'price_per_person' => 'required|numeric|min:0',
        'discount' => 'nullable|numeric|min:0|max:100',
        'starts_at' => 'required|date',
        'ends_at' => 'required|date|after:starts_at',
        'location' => 'required|string',
        'included_items' => 'nullable|string',
        'status' => 'required|in:active,inactive'
    ]);
     $validated['is_featured'] = $request->has('is_featured');
    try {
        $promotion->update($validated);
        return redirect()
            ->route('admin.promotions.index')
            ->with('success', 'อัพเดทโปรโมชั่นกิจกรรมสำเร็จ');
    } catch (\Exception $e) {
        return back()
            ->withInput()
            ->withErrors(['error' => 'ไม่สามารถอัพเดทโปรโมชั่นกิจกรรมได้']);
    }
}

    public function destroy(Promotion $promotion)
    {
        try {
            if ($promotion->orders()->exists()) {
                return back()->withErrors([
                    'error' => 'Cannot delete promotion with associated orders'
                ]);
            }

            $promotion->forceDelete();
            return redirect()
                ->route('admin.promotions.index')
                ->with('success', 'Promotion deleted successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete promotion']);
        }
    }
    public function viewBookings(Promotion $promotion)
{
    $bookings = $promotion->bookings()
        ->with('user')
        ->latest()
        ->paginate(10);

    $stats = [
        'total_bookings' => $bookings->total(),
        'total_participants' => $promotion->bookings()->sum('number_of_participants'),
        'total_revenue' => $promotion->bookings()
            ->where('status', 'confirmed')
            ->sum('final_price'),
        'available_slots' => $promotion->max_participants - $promotion->current_participants
    ];

    return view('admin.promotions.bookings', compact('promotion', 'bookings', 'stats'));
}

public function downloadQuotation($bookingId)
{
    $booking = PromotionBooking::with(['user', 'promotion'])->findOrFail($bookingId);
    
    $pdf = PDF::loadView('user.promotion_bookings.quotation', [
        'booking' => $booking,
        'promotion' => $booking->promotion
    ]);

    return $pdf->download('ใบเสนอราคา_' . $booking->booking_code . '.pdf');
}
}