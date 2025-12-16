<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::with('parent')->latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('category_name_en', 'LIKE', "%{$search}%")
                  ->orWhere('category_name_bn', 'LIKE', "%{$search}%");
            });
        }

        $categories = $query->paginate(10);

        return Inertia::render('Categories/Index', [
            'categories' => $categories,
            'filters' => $request->only(['search']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Categories/Create', [
            'categories' => Category::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_name_en' => 'required|string|max:255',
            'category_name_bn' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'status' => 'nullable|boolean',
        ]);

        try {
            DB::beginTransaction();

            $validated['status'] = $request->input('status', 1) ? 1 : 0;
            $validated['created_by'] = auth()->id();

            $category = Category::create($validated);

            DB::commit();

            return redirect()->route('categories.index')
                ->with('success', 'Category created successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create category: ' . $e->getMessage()]);
        }
    }

    public function edit(Category $category)
    {
        return Inertia::render('Categories/Edit', [
            'category' => $category,
            'categories' => Category::where('id', '!=', $category->id)->get(),
        ]);
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'category_name_en' => 'required|string|max:255',
            'category_name_bn' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'status' => 'required|boolean',
        ]);

        try {
            DB::beginTransaction();

            // Ensure parent_id is null if empty
            $validated['parent_id'] = $request->filled('parent_id') ? $request->parent_id : null;
            $validated['updated_by'] = auth()->id();

            $category->update($validated);

            DB::commit();

            return redirect()->route('categories.index')
                ->with('success', 'Category updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to update category: ' . $e->getMessage()]);
        }
    }


    public function destroy(Category $category)
    {
        try {
            DB::beginTransaction();

            // Optional: prevent deleting parent with children
            if ($category->children()->count() > 0) {
                return back()->withErrors(['error' => 'Cannot delete category with subcategories']);
            }

            $category->delete();

            DB::commit();

            return redirect()->route('categories.index')
                ->with('success', 'Category deleted successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to delete category: ' . $e->getMessage()]);
        }
    }

    public function show(Category $category)
    {
        return response()->json([
            'id' => $category->id,
            'category_name_en' => $category->category_name_en,
            'category_name_bn' => $category->category_name_bn,
            'parent_id' => $category->parent_id,
            'status' => $category->status,
            'parent' => $category->parent ? [
                'id' => $category->parent->id,
                'category_name_en' => $category->parent->category_name_en,
                'category_name_bn' => $category->parent->category_name_bn,
            ] : null,
        ]);
    }
}
