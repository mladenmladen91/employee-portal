<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'text',
        'link',
        'created_by',
        'modified_by'
    ];
}
