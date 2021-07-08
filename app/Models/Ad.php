<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'location',
        'video',
        'number_of_applications',
        'start_date',
        'end_date',
        'is_active',
        'is_archived',
        'city_id',
        'country_id',
        'type_of_work_id',
        'deleted',
        'created_by',
        'modified_by',
        'deleted_by',
        'position',
        'job_type_id',
        'work_time_id',
        'education_level_id',
        'short_description'
    ];


    public function creator(){
        return $this->belongsTo(User::class, "user_id");
    }

    public function country(){
        return $this->belongsTo(Country::class);
    }
    public function city(){
        return $this->belongsTo(City::class);
    }

    public function type_of_work(){
        return $this->belongsTo(TypeOfWork::class, "type_of_work_id");
    }

    public function shared_adds(){
        return $this->hasMany(AdSharedInfo::class);
    }
    public function answers(){
        return $this->hasMany(AdAnswer::class);
    }
    public function questions(){
        return $this->hasMany(AdQuestion::class);
    }

    
}
