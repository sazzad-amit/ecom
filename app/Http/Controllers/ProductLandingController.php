<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class ProductLandingController extends Controller
{
    public function search(Request $request)
    {
        $query = Product::query()->with('category');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('product_name_en', 'LIKE', "%{$search}%")
                  ->orWhere('product_name_bn', 'LIKE', "%{$search}%")
                  ->orWhere('description_en', 'LIKE', "%{$search}%")
                  ->orWhere('description_bn', 'LIKE', "%{$search}%");
            });
        }

        // Category filter
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        // Price range filter
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->input('min_price'));
        }
        
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->input('max_price'));
        }

        // In stock filter
        if ($request->filled('in_stock_only')) {
            $query->where('is_in_stock', 1);
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'latest');
        switch ($sortBy) {
            case 'price_low_high':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high_low':
                $query->orderBy('price', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('product_name_en', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('product_name_en', 'desc');
                break;
            default: // 'latest'
                $query->latest();
                break;
        }

        $perPage = $request->input('per_page', 12);
        $products = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => [
                'data' => $products->items(),
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
            'filters' => $request->only(['search', 'category_id', 'min_price', 'max_price', 'in_stock_only', 'sort_by'])
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