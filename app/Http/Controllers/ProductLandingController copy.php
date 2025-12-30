<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class ProductLandingController extends Controller
{
    /**
     * Get only leaf child category IDs under a parent
     */
    // private function getLeafChildIds($categories, $parentId)
    private function getLeafChildIds($categories, $parentId)
    {
        $result = [];

        foreach ($categories as $category) {
            if ($category->parent_id == $parentId) {

                if (!in_array($category->id, $result)) {
                    $result[] = $category->id;
                }

                $children = $categories->where('parent_id', $category->id);

                if ($children->isNotEmpty()) {
                    $result = array_merge(
                        $result,
                        $this->getLeafChildIds($categories, $category->id)
                    );
                }
            }
        }

        // unique
        return array_values(array_unique($result));
    }


    public function search(Request $request)
    {
        $query = Product::query()
            ->with('category')
            ->where('status', 1);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('product_name_en', 'LIKE', "%{$search}%")
                  ->orWhere('product_name_bn', 'LIKE', "%{$search}%")
                  ->orWhere('description_en', 'LIKE', "%{$search}%")
                  ->orWhere('description_bn', 'LIKE', "%{$search}%");
            });
        }

        /* 📂 Category (only leaf children) */
        if ($request->filled('category_id')) {

        $categories = Category::select('id', 'parent_id')->get();

        $leafCategoryIds = $this->getLeafChildIds(
            $categories,
            $request->category_id
        );

        // যদি leaf না থাকে → নিজেকেই search করবে
        if (empty($leafCategoryIds)) {
            $leafCategoryIds = [$request->category_id];
        }

        // নিজেকেও include করা
        $leafCategoryIds[] = $request->category_id;

        // duplicate থাকলে remove
        $leafCategoryIds = array_unique($leafCategoryIds);

        $query->whereIn('category_id', $leafCategoryIds);
        }


        /* 💰 Price filter */
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        /* 📦 Stock */
        if ($request->filled('in_stock_only')) {
            $query->where('is_in_stock', 1);
        }

        /* 🔃 Sorting */
        match ($request->input('sort_by')) {
            'price_low_high' => $query->orderBy('price', 'asc'),
            'price_high_low' => $query->orderBy('price', 'desc'),
            'name_asc' => $query->orderBy('product_name_en', 'asc'),
            'name_desc' => $query->orderBy('product_name_en', 'desc'),
            default => $query->latest(),
        };

        $products = $query->paginate($request->input('per_page', 12));

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
   }

    
    public function show($id)
    {
        $product = Product::with('category')
            ->where('status', 1)
            ->findOrFail($id);
            
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $product->id,
                'auto_id' => $product->auto_id,
                'product_name_en' => $product->product_name_en,
                'product_name_bn' => $product->product_name_bn,
                'category' => $product->category ? [
                    'id' => $product->category->id,
                    'name' => $product->category->name,
                ] : null,
                'price' => $product->price,
                'discount_price' => $product->discount_price,
                'quantity' => $product->quantity,
                'is_in_stock' => $product->is_in_stock,
                'status' => $product->status,
                'description_en' => $product->description_en,
                'description_bn' => $product->description_bn,
                'short_description_en' => $product->short_description_en,
                'short_description_bn' => $product->short_description_bn,
                'calculation' => $product->calculation,
                'seller_details' => $product->seller_details,
                'mobile_no' => $product->mobile_no,
                'image_url' => $product->image_url,
                'images_urls' => $product->images_urls,
                'video_url' => $product->video_url,
                'created_at' => $product->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $product->updated_at->format('Y-m-d H:i:s'),
            ]
        ]);
    }

    public function categories()
    {
        $data = Category::select('id', 'category_name_en', 'category_name_bn', 'parent_id' )->get();
         return response()->json([
            'success' => true,
            'data' => [
                'data' => $data,
            ],
        ]);
    }
}