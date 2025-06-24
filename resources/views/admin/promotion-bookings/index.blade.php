@extends('admin.layouts.admin')

@section('title', 'จัดการการจองกิจกรรม')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">จัดการการจองกิจกรรม</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">รายการจองทั้งหมด</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
    <table class="table table-bordered" id="bookingsTable">
        <thead>
            <tr>
                <th>รหัสการจอง</th>
                <th>กิจกรรม</th>
                <th>ผู้จอง</th>
                <th>จำนวนที่นั่ง</th>
                <th>ยอดรวม</th>
                <th>สถานะการชำระเงิน</th>
                <th>วันที่จอง</th>
                <th>จัดการ</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bookings as $booking)
                <tr>
                    <td>{{ $booking->booking_code }}</td>
                    <td>
                        @if($booking->promotion)
                            {{ $booking->promotion->title }}
                        @else
                            <span class="text-muted">กิจกรรมถูกลบแล้ว</span>
                        @endif
                    </td>
                    <td>{{ $booking->user->name ?? 'ไม่พบข้อมูลผู้ใช้' }}</td>
                    <td>{{ $booking->number_of_participants }} ที่นั่ง</td>
                    <td>฿{{ number_format($booking->final_price, 2) }}</td>
                    <td>
                        <span class="badge bg-{{ $booking->payment_status === 'paid' ? 'success' : 'warning' }}">
                            {{ $booking->payment_status === 'paid' ? 'ชำระแล้ว' : 'รอชำระเงิน' }}
                        </span>
                    </td>
                    <td>{{ $booking->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <a href="{{ route('admin.promotion-bookings.show', $booking) }}" 
                           class="btn btn-sm btn-info">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">ไม่พบข้อมูลการจอง</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
            </div>
            {{ $bookings->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#bookingsTable').DataTable({
        order: [[3, 'desc']],
        pageLength: 10,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Thai.json'
        }
    });
});
</script>
@endpush