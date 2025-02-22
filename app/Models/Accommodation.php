<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accommodation extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'capacity', 'price', 'qty', 'location', 'description', 'is_active'];
   

    public function roomAvailability()
    {
        return $this->hasOne(RoomAvailability::class);
    }

    
    public function registrations()
    {
        return $this->belongsToMany(Registrasi::class, 'registrasi_accomodation')
                    ->withPivot('quantity', 'check_in_date', 'check_out_date', 'total_price')
                    ->withTimestamps();
    }


}
