<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackagePurchaseHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'package_id',
        'purchase_auto',
        'purchase_date',
        'expire_date',
        'created_by',
        'modified_by'
    ];
}
