<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeOfWorkNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type_of_work_id'
    ];
}
