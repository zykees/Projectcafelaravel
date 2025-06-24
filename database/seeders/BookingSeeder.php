<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $statuses = ['pending', 'confirmed', 'cancelled'];
        
        foreach (range(1, 30) as $index) {
            $date = Carbon::now('Asia/Bangkok')->subDays(rand(0, 30));
            
            Booking::create([
                'user_id' => $users->random()->id,
                'booking_date' => $date->format('Y-m-d'),
                'booking_time' => sprintf('%02d:00:00', rand(9, 20)),
                'number_of_guests' => rand(1, 6),
                'status' => $statuses[array_rand($statuses)],
                'notes' => 'Test booking notes',
                'created_at' => $date,
                'updated_at' => $date
            ]);
        }
    }
}