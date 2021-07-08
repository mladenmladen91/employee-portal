<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'question_id',
        'text_anwser',
        'video_anwser',
        'created_by',
        'modified_by'
    ];
}
