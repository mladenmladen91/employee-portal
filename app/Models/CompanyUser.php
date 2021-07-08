<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'contact_person',
        'contact_person_position',
        'contact_phone',
        'contact_mail',
        'contact_website',
        'deleted',
        'created_by',
        'modified_by',
        'deleted_by',
        'owner'
    ];
}
