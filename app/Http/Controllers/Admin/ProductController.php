<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(Request $request)
    {
        $query = Product::with('category');

        // Filter by category
        if ($request->filled('category_id')) {
    $query->where('category_id', $request->category_id);
}

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by price range
        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }
        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        // Search by name or description
if ($request->filled('search')) {
    $search = $request->search;
    $query->where(function($q) use ($search) {
        $q->where('name', 'like', "%{$search}%")
          ->orWhere('description', 'like', "%{$search}%");
    });
        }

        // Sort by
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            default:
                $query->latest();
                break;
        }

        $categories = Category::where('status', 'active')->get();
        $products = $query->paginate(10);

      
        // Get statistics
        $stats = [
            'total_products' => Product::count(),
            'active_products' => Product::where('status', 'available')->count(),
            'out_of_stock' => Product::where('stock', 0)->count(),
            'total_categories' => Category::where('status', 'active')->count(),
        ];
          if ($request->wantsJson()) {
        return response()->json([
            'data' => $products,
            'stats' => $stats
        ]);
    }

        return view('admin.products.index', compact('products', 'categories', 'stats'));
    }

    public function create()
    {
        $categories = Category::where('status', 'active')->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:products',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'category_id' => 'required|exists:categories,id',
                'status' => 'required|in:available,unavailable',
                'discount_percent' => 'nullable|numeric|min:0|max:100'
            ]);

            DB::beginTransaction();

            $data = $validated;
            $data['slug'] = Str::slug($request->name);
            $data['featured'] = $request->has('featured'); // เพิ่มบรรทัดนี้

            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('products', 'public');
            }
            $validated['discount_percent'] = $request->input('discount_percent', 0);
            Product::create($data);

            DB::commit();

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'เพิ่มสินค้าสำเร็จ');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
       
    }

    public function show(Product $product)
    {
        $product->load('category');
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::where('status', 'active')->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:products,name,' . $product->id,
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'category_id' => 'required|exists:categories,id',
                'status' => 'required|in:available,unavailable',
                'discount_percent' => 'nullable|numeric|min:0|max:100'
            ]);

            DB::beginTransaction();

            $data = $validated;
            $data['slug'] = Str::slug($request->name);
            $data['featured'] = $request->has('featured'); // เพิ่มบรรทัดนี้

            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }
                $data['image'] = $request->file('image')->store('products', 'public');
            }

                $validated['discount_percent'] = $request->input('discount_percent', 0);

            $product->update($data);

            DB::commit();

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'อัพเดตสินค้าสำเร็จ');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    public function destroy(Product $product)
    {
        try {
            // ลบรูปภาพ (ถ้ามี)
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            // ลบข้อมูลจาก database จริงๆ
            $product->forceDelete();
            
            return response()->json([
                'success' => true,
                'message' => 'ลบสินค้าสำเร็จ'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateStock(Request $request, Product $product)
    {
        try {
            $validated = $request->validate([
                'stock' => 'required|integer|min:0',
                'stock_note' => 'nullable|string|max:255'
            ]);

            $product->update($validated);

            return back()->with('success', 'อัพเดตสต็อกสำเร็จ');
        } catch (\Exception $e) {
            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

 public function toggleStatus(Product $product)
{
    try {
        $product->status = $product->status === 'available' ? 'unavailable' : 'available';
        $product->save();

        return response()->json([
            
            'success' => true,
            'status' => $product->status,
            'status_text' => $product->status_text,
            'message' => 'เปลี่ยนสถานะสำเร็จ'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
        ], 500);
    }
}
}