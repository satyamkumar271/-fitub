<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'category',
        'author_name',
        'image_path',
        'excerpt',
        'meta_title',
        'meta_description',
        'content',
        'featured',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'featured' => 'boolean',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];
}
