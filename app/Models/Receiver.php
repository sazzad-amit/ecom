<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receiver extends Model
{
    use HasFactory;

    protected $fillable = [
        'receiver_name_en',
        'receiver_name_bn',
        'receiver_auto_id',
        'mobile_no',
        'details_en',
        'details_bn',
        'status',
        'created_by',
        'updated_by',
    ];
}
