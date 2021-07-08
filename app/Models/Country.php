<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_short',
        'created_by',
        'modified_by'
    ];

    public function cities()
    {
        return $this->hasMany(City::class);
    }

    public function ads()
    {
        return $this->hasMany(Ad::class);
    }

    public function activeAds(){
        return $this->hasMany(Ad::class, "country_id")->where("is_active", 1)->where("is_archived", 0)->where("end_date",">=", date('Y-m-d'));
    }
}
