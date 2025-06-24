<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ตั้งค่า timezone เป็นไทย
        date_default_timezone_set('Asia/Bangkok');
        Carbon::setLocale('th');

        $this->call([
            AdminSeeder::class,
            UserSeeder::class,
            ProductSeeder::class,
            PromotionSeeder::class,
            BookingSeeder::class,
            OrderSeeder::class
        ]);
    }
}