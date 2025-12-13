<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductLandingController extends Controller
{
    public function search(Request $request)
    {
        $query = Product::query();

        // 🔍 Search by product name (EN / BN)
        if ($request->filled('q')) {
            $search = $request->q;

            $query->where(function ($q) use ($search) {
                $q->where('product_name_en', 'like', "%{$search}%")
                  ->orWhere('product_name_bn', 'like', "%{$search}%");
            });
        }

        // 📂 Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // 💰 Price range filter
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // 📦 Stock filter
        if ($request->filled('in_stock')) {
            $query->where('is_in_stock', $request->in_stock);
        }

        // ✅ Only active products
        $query->where('status', 1);

        // 🔽 Latest first
        $products = $query->latest()->paginate(12);

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }
}
