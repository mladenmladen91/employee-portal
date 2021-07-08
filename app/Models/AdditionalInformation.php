<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdditionalInformation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'text',
        'created_by',
        'modified_by'
    ];
}
