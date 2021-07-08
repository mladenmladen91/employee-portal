<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForeignLanguage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'languages_id',
        'language_reads_id',
        'language_writes_id',
        'language_speaks_id',
        'created_by',
        'modified_by'
    ];
}
