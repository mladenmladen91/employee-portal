<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'education_level_id',
        'education_area_id',
        'education_title_id',
        'institution',
        'course',
        'city_id',
        'start_date',
        'end_date',
        'ongoing',
        'created_by',
        'modified_by'
    ];

    public function education_level(){
        return $this->belongsTo(EducationLevel::class, "education_level_id");
    }

    public function education_area(){
        return $this->belongsTo(EducationArea::class, "education_area_id");
    }

    public function education_title(){
        return $this->belongsTo(EducationTitle::class, "education_title_id");
    }
}
