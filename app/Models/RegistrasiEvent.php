<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class RegistrasiEvent extends Pivot
{
    protected $table = 'registrasi_events';

    protected $fillable = [
        'registrasi_id',
        'event_id',
        'original_price',
        'final_price',
        'discount_type',
        'discount_percentage',
        'discount_code'
    ];
}