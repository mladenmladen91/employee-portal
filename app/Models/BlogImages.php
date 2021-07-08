<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogImages extends Model
{
    use HasFactory;

    protected $fillable = [
        'blog_id',
        'images',
        'created_by',
        'modified_by'
    ];
}
