<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calendar extends Model
{
    use HasFactory;
    public $timestamps = [ "created_at" ];
    const UPDATED_AT = null;
    function user() {
        return $this->belongsTo(User::class)->first();
    }
    function events() {
        return $this->hasMany(Event::class)->get();
    }
    protected $fillable = [
        'user_id',
        'title',
        'is_active',
        'is_default'
    ];
}
