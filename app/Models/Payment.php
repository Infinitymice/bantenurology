<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'registrasi_id',
        'invoice_number',
        'amount',
        'proof_of_transfer',
        'bank_name',
        'payment_date',
        'payment_expiry',
        'status',
        'failed_reason',
        'invoice_url',
        'note',
    ];

    // relasi ke model Registrasi
    public function registrasi()
    {
        return $this->belongsTo(Registrasi::class, 'registrasi_id'); 
    }


    // // Optional: menambahkan metode untuk memformat tanggal kadaluarsa
    // public function getPaymentExpiryAttribute($value)
    // {
    //     return \Carbon\Carbon::parse($value)->format('d-m-Y H:i');
    // }

    // Untuk memformat jumlah pembayaran dengan mata uang
    public function getFormattedAmountAttribute()
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    // Cari status pembayaran
    public function scopeUnpaid($query)
    {
        return $query->where('payment_status', 'pending');
    }

    public function events()
    {
        return $this->belongsToMany(Event::class, 'registrasi_events', 'registrasi_id', 'event_id');
    }



}
