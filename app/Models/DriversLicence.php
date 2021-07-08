<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriversLicence extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'drivers_licence_category_id',
        'own_vehicle',
        'additional_info',
        'created_by',
        'modified_by'
    ];
}
