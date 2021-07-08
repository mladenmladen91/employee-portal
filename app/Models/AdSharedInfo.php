<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdSharedInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ad_id',
        'reminder',
        'favourite',
        'applied',
        'selected',
        'created_by',
        'modified_by',
        'seen',
        'viewed'
    ];

    public function ad(){
        return $this->belongsTo(Ad::class);
    }

    public function user(){
        return $this->belongsTo(User::class, "user_id");
    }
}
