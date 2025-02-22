<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;

    protected $table = 'absensi';
    protected $casts = [
        'waktu_absen' => 'datetime', // Konversi kolom waktu_absen menjadi Carbon
    ];

    // Relasi dengan tabel registrasi
    public function registrasi()
    {
        return $this->belongsTo(Registrasi::class, 'registrasi_id');
    }

    // protected $dates = ['waktu_absen'];
}
