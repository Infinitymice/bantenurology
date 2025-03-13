<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

   
    protected $fillable = [
        'event_type_id', 'name', 'early_bid_price', 'onsite_price', 'early_bid_date', 'kuota'
    ];

    public function eventType()
    {
        return $this->belongsTo(EventType::class, 'event_type_id');
    }

    public function getNameAttribute()
    {
        return $this->attributes['name'];
    }

    public function registrasis()
    {
        return $this->belongsToMany(Registrasi::class, 'registrasi_events', 'event_id', 'registrasi_id');
    }

    public function pivot()
    {
        return $this->belongsToMany(Registrasi::class, 'registrasi_events')
            ->withPivot(['original_price', 'final_price', 'discount_percentage', 'discount_code']);
    }

    // public function registrasi()
    // {
    //     return $this->hasMany(Registrasi::class);
    // }
}
