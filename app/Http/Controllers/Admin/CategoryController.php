<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::withCount('products');

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Search by name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Sort
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'products_count':
                $query->orderBy('products_count', 'desc');
                break;
            default:
                $query->latest();
                break;
        }

        $categories = $query->paginate(10)->withQueryString();
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:categories',
                'description' => 'nullable|string|max:1000',
                'status' => 'required|in:active,inactive'
            ]);

            $validated['slug'] = Str::slug($request->name);

            DB::beginTransaction();
            Category::create($validated);
            DB::commit();

            return redirect()
                ->route('admin.categories.index')
                ->with('success', 'เพิ่มหมวดหมู่สำเร็จ');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    public function show(Category $category)
    {
        $category->load(['products' => function($query) {
            $query->latest()->take(5);
        }]);
        
        return view('admin.categories.show', compact('category'));
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
                'description' => 'nullable|string|max:1000',
                'status' => 'required|in:active,inactive'
            ]);

            $validated['slug'] = Str::slug($request->name);

            DB::beginTransaction();
            $category->update($validated);
            DB::commit();

            return redirect()
                ->route('admin.categories.index')
                ->with('success', 'อัพเดตหมวดหมู่สำเร็จ');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    public function destroy(Category $category)
    {
        try {
            if ($category->products()->count() > 0) {
                return back()->with('error', 'ไม่สามารถลบหมวดหมู่ที่มีสินค้าอยู่ได้');
            }

            DB::beginTransaction();
            $category->delete();
            DB::commit();

            return redirect()
                ->route('admin.categories.index')
                ->with('success', 'ลบหมวดหมู่สำเร็จ');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    public function toggleStatus(Category $category)
    {
        try {
            $category->status = $category->status === 'active' ? 'inactive' : 'active';
            $category->save();

            return back()->with('success', 'อัพเดตสถานะสำเร็จ');
        } catch (\Exception $e) {
            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }
}