<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Americano',
                'description' => 'Classic espresso drink with hot water',
                'price' => 45.00,
                'category' => 'coffee',
                'image' => 'americano.jpg',
                'status' => 'available',
                'stock' => rand(50, 200) // เพิ่ม stock
            ],
            [
                'name' => 'Latte',
                'description' => 'Espresso with steamed milk',
                'price' => 55.00,
                'category' => 'coffee',
                'image' => 'latte.jpg',
                'status' => 'available',
                'stock' => rand(50, 200) // เพิ่ม stock
            ],
            [
                'name' => 'Chocolate Cake',
                'description' => 'Rich chocolate layer cake',
                'price' => 65.00,
                'category' => 'dessert',
                'image' => 'chocolate-cake.jpg',
                'status' => 'available',
                'stock' => rand(20, 100) // เพิ่ม stock
            ]
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}