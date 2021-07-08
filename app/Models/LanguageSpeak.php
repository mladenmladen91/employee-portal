<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LanguageSpeak extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'created_by',
        'modified_by'
    ];
}
