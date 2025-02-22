<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventType extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function getNameAttribute()
    {
        return $this->attributes['name'];
    }

    public function registrasis()
    {
        return $this->belongsToMany(Registrasi::class, 'registrasi_event_types', 'event_type_id', 'registrasi_id');
    }
}

