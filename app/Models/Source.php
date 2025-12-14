<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Source extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_name_en',
        'source_name_bn',
        'source_auto_id',
        'mobile_no',
        'details_en',
        'details_bn',
        'status',
        'created_by',
        'updated_by',
    ];
}