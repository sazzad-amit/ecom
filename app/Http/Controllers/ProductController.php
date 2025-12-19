<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{

    // public function index(Request $request)
    // {
    //     $query = Product::with('category')
    //         ->latest();
    //     if ($request->filled('search')) {
    //         $search = $request->input('search');
    //         $query->where(function($q) use ($search) {
    //             $q->where('product_name_en', 'LIKE', "%{$search}%")
    //               ->orWhere('product_name_bn', 'LIKE', "%{$search}%")
    //               ->orWhere('auto_id', 'LIKE', "%{$search}%");
    //         });
    //     }
        
    //     $products = $query->paginate(10);
        
    //     return Inertia::render('Products/Index', [
    //         'products' => $products,
    //         'filters' => $request->only(['search'])
    //     ]);
    // }
    public function index()
    {
        $products = Product::with('category')
            ->latest()
            ->paginate(10);
        
        return Inertia::render('products/Index', [
            'products' => $products,
            'filters' => request()->only(['search'])
        ]);
    }

    public function create()
    {
        return Inertia::render('products/Create', [
            'categories' => Category::all()
        ]);
    }

    // public function store(Request $request)
    // {
    //     // Add better debugging
    //     \Log::info('Product store request', [
    //         'all_data' => $request->all(),
    //         'files' => $request->allFiles(),
    //         'has_image' => $request->hasFile('image'),
    //         'has_images' => $request->hasFile('images'),
    //     ]);

    //     $validated = $request->validate([
    //         'product_name_en' => 'required|string|max:255',
    //         'product_name_bn' => 'nullable|string|max:255',
    //         'category_id' => 'nullable|exists:categories,id', // Made nullable since it's commented
    //         'price' => 'required|numeric|min:0',
    //         'discount_price' => 'nullable|numeric|min:0',
    //         'quantity' => 'nullable|integer|min:0',
    //         'is_in_stock' => 'nullable',
    //         'status' => 'nullable',
    //         'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
    //         'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
    //         'video' => 'nullable|mimes:mp4,mov,avi,wmv|max:10240',
    //         'description_en' => 'nullable|string',
    //         'description_bn' => 'nullable|string',
    //         'short_description_en' => 'nullable|string',
    //         'short_description_bn' => 'nullable|string',
    //         'calculation' => 'nullable|string',
    //         'seller_details' => 'nullable|string',
    //         'mobile_no' => 'nullable|string|max:20',
    //     ]);

    //     try {
    //         DB::beginTransaction();

    //         // Set created_by from authenticated user
    //         $validated['created_by'] = auth()->id();
    //         $validated['is_in_stock'] = $request->input('is_in_stock', 0) ? 1 : 0;
    //         $validated['status'] = $request->input('status', 1) ? 1 : 0;

    //         // Handle file uploads
    //         if ($request->hasFile('image')) {
    //             $validated['image'] = $this->storeFile($request->file('image'), 'products/images');
    //         }

    //         // Handle multiple images
    //         if ($request->hasFile('images')) {
    //             $images = [];
    //             foreach ($request->file('images') as $image) {
    //                 $images[] = $this->storeFile($image, 'products/images');
    //             }
    //             $validated['images'] = json_encode($images);
    //         }

    //         // Handle video upload
    //         if ($request->hasFile('video')) {
    //             $validated['video'] = $this->storeFile($request->file('video'), 'products/videos');
    //         }

    //         // Create product
    //         $product = Product::create($validated);
            
    //         // Generate auto_id after product creation
    //         $product->update([
    //             'auto_id' => date('dmy') . str_pad($product->id, 9, '0', STR_PAD_LEFT)
    //         ]);

    //         DB::commit();

    //         return redirect()->route('products.index')
    //             ->with('success', 'Product created successfully');
                
    //     } catch (\Exception $e) {
    //         DB::rollBack();
            
    //         \Log::error('Product creation failed', [
    //             'error' => $e->getMessage(),
    //             'trace' => $e->getTraceAsString()
    //         ]);
            
    //         // Clean up uploaded files if transaction fails
    //         if (isset($validated['image'])) {
    //             Storage::disk('public')->delete($validated['image']);
    //         }
    //         if (isset($validated['images'])) {
    //             $images = json_decode($validated['images'], true);
    //             foreach ($images as $img) {
    //                 Storage::disk('public')->delete($img);
    //             }
    //         }
    //         if (isset($validated['video'])) {
    //             Storage::disk('public')->delete($validated['video']);
    //         }
            
    //         return back()
    //             ->withInput()
    //             ->withErrors(['error' => 'Failed to create product: ' . $e->getMessage()]);
    //     }
    // }
    public function store(Request $request)
{
    \Log::info('Product store request', [
        'all_data' => $request->all(),
        'files' => $request->allFiles(),
        'has_image' => $request->hasFile('image'),
        'has_images' => $request->hasFile('images'),
    ]);

    $validated = $request->validate([
        'product_name_en' => 'required|string|max:255',
        'product_name_bn' => 'nullable|string|max:255',
        'category_id' => 'nullable|exists:categories,id',
        'price' => 'required|numeric|min:0',
        'discount_price' => 'nullable|numeric|min:0',
        'quantity' => 'nullable|integer|min:0',
        'is_in_stock' => 'nullable',
        'status' => 'nullable',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        'video' => 'nullable|mimes:mp4,mov,avi,wmv|max:10240',
        'description_en' => 'nullable|string',
        'description_bn' => 'nullable|string',
        'short_description_en' => 'nullable|string',
        'short_description_bn' => 'nullable|string',
        'calculation' => 'nullable|string',
        'seller_details' => 'nullable|string',
        'mobile_no' => 'nullable|string|max:20',
    ]);

    try {
        DB::beginTransaction();

        $validated['created_by'] = auth()->id();
        $validated['is_in_stock'] = $request->input('is_in_stock', 0) ? 1 : 0;
        $validated['status'] = $request->input('status', 1) ? 1 : 0;

        // Store main image - Just store the path (not full URL)
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products/images', 'public');
            $validated['image'] = $imagePath; // Just the path like 'products/images/filename.jpg'
            \Log::info('Main image stored', ['path' => $imagePath]);
        }

        // Store additional images
        if ($request->hasFile('images')) {
            $images = [];
            foreach ($request->file('images') as $image) {
                $imagePath = $image->store('products/images', 'public');
                $images[] = $imagePath; // Just the path
                \Log::info('Additional image stored', ['path' => $imagePath]);
            }
            $validated['images'] = json_encode($images);
        }

        // Store video
        if ($request->hasFile('video')) {
            $videoPath = $request->file('video')->store('products/videos', 'public');
            $validated['video'] = $videoPath; // Just the path like 'products/videos/filename.mp4'
            \Log::info('Video stored', ['path' => $videoPath]);
        }

        $product = Product::create($validated);
        
        $product->update([
            'auto_id' => date('dmy') . str_pad($product->id, 9, '0', STR_PAD_LEFT)
        ]);

        DB::commit();

        \Log::info('Product created successfully', [
            'product_id' => $product->id,
            'image_path' => $product->image ?? null,
            'images' => $product->images ?? null
        ]);

        return redirect()->route('products.index')
            ->with('success', 'Product created successfully');
            
    } catch (\Exception $e) {
        DB::rollBack();
        
        \Log::error('Product creation failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return back()
            ->withInput()
            ->withErrors(['error' => 'Failed to create product: ' . $e->getMessage()]);
    }
}
    /**
     * Store a single file with unique filename
     */
    private function storeFile($file, string $directory): string
    {
        $filename = time() . '_' . uniqid() . '_' . preg_replace('/[^a-zA-Z0-9.]/', '_', $file->getClientOriginalName());
        
        return $file->storeAs($directory, $filename, 'public');
    }

    /**
     * Cleanup uploaded files on failure
     */
    private function cleanupUploadedFiles(array $data): void
    {
        $fileFields = ['image', 'video', 'images'];
        
        foreach ($fileFields as $field) {
            if (isset($data[$field])) {
                if ($field === 'images' && is_string($data[$field])) {
                    $images = json_decode($data[$field], true);
                    if (is_array($images)) {
                        foreach ($images as $imagePath) {
                            if (Storage::disk('public')->exists($imagePath)) {
                                Storage::disk('public')->delete($imagePath);
                            }
                        }
                    }
                } elseif (is_string($data[$field]) && Storage::disk('public')->exists($data[$field])) {
                    Storage::disk('public')->delete($data[$field]);
                }
            }
        }
    }

    public function edit(Product $product)
    {
        return Inertia::render('products/Edit', [
            'product' => $product,
            'categories' => Category::all()
        ]);
    }

    public function update(Request $request, Product $product)
{
    $validated = $request->validate([
        'product_name_en' => 'required|string|max:255',
        'product_name_bn' => 'nullable|string|max:255',
        'category_id' => 'required|exists:categories,id',
        'price' => 'required|numeric|min:0',
        'discount_price' => 'nullable|numeric|min:0|lt:price',
        'quantity' => 'nullable|integer|min:0',
        'is_in_stock' => 'boolean',
        'status' => 'boolean',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        'video' => 'nullable|mimes:mp4,mov,avi,wmv|max:10240',
        'description_en' => 'nullable|string',
        'description_bn' => 'nullable|string',
        'short_description_en' => 'nullable|string',
        'short_description_bn' => 'nullable|string',
        'calculation' => 'nullable|string',
        'seller_details' => 'nullable|string',
        'mobile_no' => 'nullable|string|max:20',
    ]);

    try {
        DB::beginTransaction();

        $validated['updated_by'] = auth()->id();
        $validated['is_in_stock'] = $request->boolean('is_in_stock');
        $validated['status'] = $request->boolean('status');

        // Handle main image update
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            
            // Store new image
            $validated['image'] = $request->file('image')->store('products/images', 'public');
        } elseif ($request->has('remove_image')) {
            // Handle image removal
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = null;
        }

        // Handle additional images update
        if ($request->hasFile('images')) {
            // Delete old additional images if exists
            if ($product->images) {
                $oldImages = json_decode($product->images, true);
                if (is_array($oldImages)) {
                    foreach ($oldImages as $oldImage) {
                        if (Storage::disk('public')->exists($oldImage)) {
                            Storage::disk('public')->delete($oldImage);
                        }
                    }
                }
            }
            
            // Store new images
            $images = [];
            foreach ($request->file('images') as $image) {
                $images[] = $image->store('products/images', 'public');
            }
            $validated['images'] = json_encode($images);
        } elseif ($request->has('remove_images')) {
            // Handle images removal
            if ($product->images) {
                $oldImages = json_decode($product->images, true);
                if (is_array($oldImages)) {
                    foreach ($oldImages as $oldImage) {
                        if (Storage::disk('public')->exists($oldImage)) {
                            Storage::disk('public')->delete($oldImage);
                        }
                    }
                }
            }
            $validated['images'] = null;
        }

        // Handle video update
        if ($request->hasFile('video')) {
            // Delete old video if exists
            if ($product->video && Storage::disk('public')->exists($product->video)) {
                Storage::disk('public')->delete($product->video);
            }
            
            // Store new video
            $validated['video'] = $request->file('video')->store('products/videos', 'public');
        } elseif ($request->has('remove_video')) {
            // Handle video removal
            if ($product->video && Storage::disk('public')->exists($product->video)) {
                Storage::disk('public')->delete($product->video);
            }
            $validated['video'] = null;
        }

        $product->update($validated);

        DB::commit();

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully');
            
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Product update failed', ['error' => $e->getMessage()]);
        return back()->withErrors(['error' => 'Failed to update product: ' . $e->getMessage()]);
    }
}

    public function destroy(Product $product)
    {
        try {
            DB::beginTransaction();

            // Delete associated files
            $this->deleteProductFiles($product);
            
            $product->delete();

            DB::commit();

            return redirect()->route('products.index')
                ->with('success', 'Product deleted successfully');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to delete product.']);
        }
    }

    private function deleteProductFiles(Product $product): void
    {
        // Delete main image
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        // Delete multiple images
        if ($product->images) {
            $images = json_decode($product->images, true);
            if (is_array($images)) {
                foreach ($images as $imagePath) {
                    if (Storage::disk('public')->exists($imagePath)) {
                        Storage::disk('public')->delete($imagePath);
                    }
                }
            }
        }

        // Delete video
        if ($product->video && Storage::disk('public')->exists($product->video)) {
            Storage::disk('public')->delete($product->video);
        }
    }

    // routes/api.php or routes/web.php

// Or in your controller
public function show($id)
{
    $product = Product::with(['category'])->findOrFail($id);
    
    // Format the response
    return response()->json([
        'id' => $product->id,
        'product_name_en' => $product->product_name_en,
        'product_name_bn' => $product->product_name_bn,
        'category_id' => $product->category_id,
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
        'image_url' => $product->image ? asset('storage/' . $product->image) : null,
        'video_url' => $product->video ? asset('storage/' . $product->video) : null,
        'images' => []
    
    ]);
}
}