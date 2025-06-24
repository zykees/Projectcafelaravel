<?php


namespace Database\Seeders;

use App\Models\Promotion;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PromotionSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now('Asia/Bangkok');

        $promotions = [
            [
                'title' => 'ส่วนลดประจำวัน',
                'description' => 'ลด 10% สำหรับเครื่องดื่มทุกชนิด',
                'discount' => 10.00, // เก็บเป็นเปอร์เซ็นต์
                'max_uses' => 100, // จำกัดการใช้ 100 ครั้ง
                'used_count' => 0,
                'max_users' => 50, // จำกัดผู้ใช้ 50 คน
                'min_order_amount' => 0.00, // ไม่มียอดขั้นต่ำ
                'max_discount_amount' => 100.00, // ส่วนลดสูงสุด 100 บาท
                'starts_at' => $now->copy()->startOfDay(),
                'ends_at' => $now->copy()->addDays(7)->endOfDay(),
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'title' => 'ซื้อครบ 500 ลด 100',
                'description' => 'ส่วนลด 100 บาท เมื่อซื้อครบ 500 บาท',
                'discount' => 100.00, // ส่วนลดแบบตายตัว
                'max_uses' => 50,
                'used_count' => 0,
                'max_users' => 30,
                'min_order_amount' => 500.00,
                'max_discount_amount' => 100.00,
                'starts_at' => $now->copy()->startOfDay(),
                'ends_at' => $now->copy()->addMonth()->endOfDay(),
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'title' => 'โปรโมชั่นสมาชิกใหม่',
                'description' => 'ส่วนลด 15% สำหรับสมาชิกใหม่',
                'discount' => 15.00,
                'max_uses' => 1, // ใช้ได้ครั้งเดียว
                'used_count' => 0,
                'max_users' => null, // ไม่จำกัดจำนวนผู้ใช้
                'min_order_amount' => 200.00,
                'max_discount_amount' => 150.00,
                'starts_at' => $now->copy()->startOfDay(),
                'ends_at' => $now->copy()->addMonths(3)->endOfDay(), // ใช้ได้ 3 เดือน
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now
            ]
        ];

        foreach ($promotions as $promotion) {
            Promotion::create($promotion);
        }
    }
}