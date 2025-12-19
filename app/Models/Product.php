<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

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

    protected $casts = [
        'images' => 'array',
        'is_in_stock' => 'boolean',
        'status' => 'integer',
    ];

    // Add appends for the computed URLs
    protected $appends = ['image_url', 'images_urls', 'video_url'];

    /**
     * Get the relative URL for the main image
     */
    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return '/images/placeholder.jpg';
        }

        // If it's already a full URL, convert to relative path
        if (filter_var($this->image, FILTER_VALIDATE_URL)) {
            $parsedUrl = parse_url($this->image);
            if (isset($parsedUrl['path'])) {
                return $parsedUrl['path']; // Return just the path part
            }
        }

        // If it starts with 'products/', add '/storage/' prefix
        if (strpos($this->image, 'products/') === 0) {
            return '/storage/' . $this->image;
        }

        // If it doesn't have '/storage/' prefix, add it
        if (strpos($this->image, '/storage/') !== 0) {
            return '/storage/' . ltrim($this->image, '/');
        }

        return $this->image;
    }
    
    /**
     * Get array of relative URLs for additional images
     */
    public function getImagesUrlsAttribute()
    {
        if (!$this->images || empty($this->images)) {
            return [];
        }
        
        $images = is_array($this->images) ? $this->images : json_decode($this->images, true);
        
        if (!is_array($images) || empty($images)) {
            return [];
        }
        
        return array_map(function($image) {
            if (!$image) return null;
            
            if (filter_var($image, FILTER_VALIDATE_URL)) {
                $parsedUrl = parse_url($image);
                return isset($parsedUrl['path']) ? $parsedUrl['path'] : $image;
            }
            
            if (strpos($image, 'products/') === 0) {
                return '/storage/' . $image;
            }
            
            if (strpos($image, '/storage/') !== 0) {
                return '/storage/' . ltrim($image, '/');
            }
            
            return $image;
        }, $images);
    }
    
    /**
     * Get the relative URL for the video
     */
    public function getVideoUrlAttribute()
    {
        if (!$this->video) {
            return null;
        }
        
        if (filter_var($this->video, FILTER_VALIDATE_URL)) {
            $parsedUrl = parse_url($this->video);
            return isset($parsedUrl['path']) ? $parsedUrl['path'] : $this->video;
        }
        
        if (strpos($this->video, 'products/') === 0) {
            return '/storage/' . $this->video;
        }
        
        if (strpos($this->video, '/storage/') !== 0) {
            return '/storage/' . ltrim($this->video, '/');
        }
        
        return $this->video;
    }

    /**
     * Set image attribute - ensure we store only relative path
     */
    public function setImageAttribute($value)
    {
        if ($value && filter_var($value, FILTER_VALIDATE_URL)) {
            // Extract path from URL
            $parsedUrl = parse_url($value);
            if (isset($parsedUrl['path'])) {
                $path = $parsedUrl['path'];
                // Remove '/storage/' prefix for storage
                $path = str_replace('/storage/', '', $path);
                $this->attributes['image'] = $path;
                return;
            }
        }
        $this->attributes['image'] = $value;
    }
    
    /**
     * Set images attribute - ensure we store only relative paths
     */
    public function setImagesAttribute($value)
    {
        if (is_array($value)) {
            $cleanedImages = array_map(function($image) {
                if ($image && filter_var($image, FILTER_VALIDATE_URL)) {
                    $parsedUrl = parse_url($image);
                    if (isset($parsedUrl['path'])) {
                        $path = $parsedUrl['path'];
                        return str_replace('/storage/', '', $path);
                    }
                }
                return $image;
            }, $value);
            
            $this->attributes['images'] = json_encode($cleanedImages);
        } else {
            $this->attributes['images'] = $value;
        }
    }

    // Relationships
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