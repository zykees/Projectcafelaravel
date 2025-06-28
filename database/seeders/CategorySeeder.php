<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'กาแฟ',
                'description' => 'เมนูกาแฟสดและเครื่องดื่มกาแฟ',
                'status' => 'active',
            ],
            [
                'name' => 'เบเกอรี่',
                'description' => 'ขนมอบ ขนมเค้ก และเบเกอรี่โฮมเมด',
                'status' => 'active',
            ],
            [
                'name' => 'เครื่องดื่ม',
                'description' => 'น้ำผลไม้ สมูทตี้ และเครื่องดื่มอื่นๆ',
                'status' => 'active',
            ],
            [
                'name' => 'อาหารว่าง',
                'description' => 'อาหารว่างสำหรับเด็กและครอบครัว',
                'status' => 'inactive',
            ],
        ];

        foreach ($categories as $cat) {
    $slug = Str::slug($cat['name']);
    if (empty($slug)) {
        $slug = 'cat-' . Str::random(6);
    }
    Category::create([
        'name' => $cat['name'],
        'slug' => $slug,
        'description' => $cat['description'],
        'status' => $cat['status'],
    ]);
}
    }
}