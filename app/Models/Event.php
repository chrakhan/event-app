<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'user_id',
        'location',
        'event_date',
        'status'
    ];

    // Event belongs to a User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
