<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('complaints')->orderBy('sort_order')->get();

        return view('admin.kategori', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name',
            'description' => 'nullable|string|max:300',
            'icon' => 'nullable|string|max:100',
            'color' => 'required|string|max:50',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['sort_order'] = Category::max('sort_order') + 1;

        Category::create($validated);

        return back()->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name,'.$category->id,
            'description' => 'nullable|string|max:300',
            'icon' => 'nullable|string|max:100',
            'color' => 'required|string|max:50',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $category->update($validated);

        return back()->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        if ($category->complaints()->exists()) {
            return back()->withErrors(['error' => 'Kategori tidak dapat dihapus karena masih memiliki aspirasi.']);
        }

        $category->delete();

        return back()->with('success', 'Kategori berhasil dihapus.');
    }
}
