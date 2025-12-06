<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // Mass assignable fields
    protected $fillable = [
        'auto_id',
        'product_name_en',
        'product_name_bn',
        'category_id',
        'price',
        'discount_price',
        'image',
        'images',
        'video',
        'description_en',
        'description_bn',
        'short_description_en',
        'short_description_bn',
        'calculation',
        'is_in_stock',
        'seller_details',
        'mobile_no',
        'quantity',
        'status',
        'created_by',
        'updated_by',
    ];

    // JSON cast for multiple images
    protected $casts = [
        'images' => 'array',  // automatically converts JSON to array
        'is_in_stock' => 'boolean',
        'status' => 'integer',
    ];

    // Optional: relationships (assuming category is another model)
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
