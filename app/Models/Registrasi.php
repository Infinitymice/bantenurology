<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registrasi extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'nik',
        'institusi',
        'email',
        'category',
        'other',
        'phone',
        'address',
        'source',
    ];

    public function payments()
    {
        return $this->hasMany(Payment::class, 'registrasi_id');
    }

    // Menambahkan hubungan many-to-many dengan model EventType
    // public function events()
    // {
    //     return $this->belongsToMany(Event::class, 'registrasi_events', 'registrasi_id', 'event_id');
    // }

    public function events()
    {
        return $this->belongsToMany(Event::class, 'registrasi_events', 'registrasi_id', 'event_id')
            ->using(RegistrasiEvent::class)
            ->withPivot([
                'original_price',
                'final_price',
                'discount_type',
                'discount_percentage',
                'discount_code'
            ])
            ->withTimestamps();
    }

    // Event.php
    public function eventType()
    {
        return $this->belongsTo(EventType::class);
    }

    public function absensi()
    {
        return $this->hasMany(Absensi::class, 'registrasi_id');
    }

    public function accommodations()
    {
        return $this->belongsToMany(Accommodation::class, 'registrasi_accommodation')
            ->withPivot('check_in_date', 'check_out_date', 'quantity', 'total_price')
            ->withTimestamps();
    }

}
