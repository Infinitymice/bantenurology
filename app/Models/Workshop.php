<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workshop extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'harga',
    ];

    // Relasi ke model Registration
    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }
}

