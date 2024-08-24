<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'image',
        'price',
        'currency',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'float',
        'currency' => 'string',
    ];
}
