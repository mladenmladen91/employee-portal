<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_short',
        'zip_code',
        'country_id',
        'created_by',
        'modified_by'
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function ads()
    {
        return $this->hasMany(Ad::class);
    }

    public function activeAds(){
        return $this->hasMany(Ad::class, "city_id")->where("is_active", 1)->where("is_archived", 0)->where("end_date",">=", date('Y-m-d'));
    }
}
