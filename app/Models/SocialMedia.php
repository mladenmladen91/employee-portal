<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialMedia extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'user_id',
        'link',
        'created_by',
        'modified_by'
    ];
}
