<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'ad_id',
        'text_question',
        'video_question',
        'created_by',
        'modified_by'
    ];
}
