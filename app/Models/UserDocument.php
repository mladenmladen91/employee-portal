<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'document_name',
        'document_link',
        'created_by',
        'modified_by'
    ];
}
