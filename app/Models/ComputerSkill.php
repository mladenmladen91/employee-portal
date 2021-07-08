<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComputerSkill extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'computer_skill_name_id',
        'computer_skill_knowledge_level_id',
        'video_anwser', '
        created_by',
        'modified_by'
    ];
}
