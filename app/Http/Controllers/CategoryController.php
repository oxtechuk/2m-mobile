<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Category;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->input('search') . '%');
        }

        $categories = $query->withCount('products')->latest()->get();

        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name',
            'description' => 'nullable|string|max:255',
        ]);

        Category::create($validated);

        flash('تمت إضافة القسم الجديد بنجاح.')->success();

        return redirect()->route('categories.index');
    }

    public function show($id)
    {
        $category = Category::with('products')->findOrFail($id);
        return view('categories.show', compact('category'));
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name,' . $category->id,
            'description' => 'nullable|string|max:255',
        ]);

        $category->update($validated);

        flash('تم تحديث القسم بنجاح.')->success();

        return redirect()->route('categories.index');
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        
        if ($category->products()->exists()) {
            flash('لا يمكن حذف هذا القسم لأنه يحتوي على منتجات مسجلة بالفعل.')->error();
            return redirect()->route('categories.index');
        }

        $category->delete();

        flash('تم حذف القسم بنجاح.')->warning();

        return redirect()->route('categories.index');
    }
}
