<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'event_discounts',
        'discount_types',
        'max_uses',
        'times_used',
        'valid_until',
        'is_active',
        'event_types'
    ];

    protected $casts = [
        'event_types' => 'array',
        'event_discounts' => 'array',
        'discount_types' => 'array',
        'valid_until' => 'datetime',
        'is_active' => 'boolean'
    ];
}
