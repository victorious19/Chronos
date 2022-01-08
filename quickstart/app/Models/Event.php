<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;
    public $timestamps = null;
    function calendar() {
        return $this->belongsTo(Calendar::class)->first();
    }

    protected $fillable = [
        'category',
        'start',
        'end',
        'title',
        'description',
        'color',
        'calendar_id'
    ];
}
