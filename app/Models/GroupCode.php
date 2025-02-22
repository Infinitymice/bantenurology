<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'group_name',
        'max_members',
        'current_members',
    ];
}
