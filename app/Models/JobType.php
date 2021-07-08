<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type_of_work_id',
        'created_by',
        'modified_by'
    ];
}
