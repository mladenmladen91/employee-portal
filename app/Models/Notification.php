<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'sender_id',
        'title',
        'text',
        'seen',
        'created_by',
        'modified_by',
        'type',
        'particular_id'
    ];
}
