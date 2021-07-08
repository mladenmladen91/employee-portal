<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Selection extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'selection_type',
        'selection_duration',
        'selection_stage',
        'created_by',
        'modified_by'
    ];
}
