<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // สร้าง default user
        User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '0812345678',
            'password' => Hash::make('password'),
            'address' => 'Test Address',
            'status' => 'active'
        ]);

        // สร้าง random users
        User::factory(19)->create();
    }
}