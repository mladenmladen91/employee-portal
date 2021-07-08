<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkExperience extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'job_category_id',
        'company_name',
        'location',
        'position',
        'description',
        'start_date',
        'end_date',
        'ongoing',
        'created_by',
        'modified_by'
    ];

    public function job_category(){
        return $this->belongsTo(JobCategory::class, "job_category_id");
    }
}
