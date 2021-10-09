<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invite extends Model
{
    use HasFactory;
    public $timestamps = null;
    function calendar() {
        return $this->belongsTo(Calendar::class)->first();
    }
    protected $fillable = [
        'calendar_id',
        'status',
        'user_id'
    ];
}
