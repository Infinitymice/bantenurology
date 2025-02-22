<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomAvailability extends Model
{
    protected $table = 'room_availability';
    
    protected $fillable = ['accommodation_id', 'date', 'available_rooms'];
    
    protected $dates = ['date'];
    
    public function accommodation()
    {
        return $this->belongsTo(Accommodation::class);
    }
}
