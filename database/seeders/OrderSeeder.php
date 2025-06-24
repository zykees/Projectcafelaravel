<?php


namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $products = Product::all();
        
        foreach (range(1, 50) as $index) {
            $orderDate = Carbon::now('Asia/Bangkok')->subDays(rand(0, 30));
            $items = rand(1, 5);
            $total = 0;
            
            $order = Order::create([
                'user_id' => $users->random()->id,
                'status' => 'completed',
                'total_amount' => 0,
                'payment_method' => 'cash',
                'created_at' => $orderDate,
                'updated_at' => $orderDate
            ]);

            // สร้างรายการสินค้าในออเดอร์
            for ($i = 0; $i < $items; $i++) {
                $product = $products->random();
                $quantity = rand(1, 3);
                $price = $product->price;
                $subtotal = $price * $quantity;
                $total += $subtotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $price,
                    'subtotal' => $subtotal,  // เพิ่มฟิลด์ subtotal
                    'created_at' => $orderDate,
                    'updated_at' => $orderDate
                ]);
            }

            // อัพเดทยอดรวมของออเดอร์
            $order->update([
                'total_amount' => $total
            ]);
        }
    }
}