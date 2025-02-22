<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettingAbsensi extends Model
{
    use HasFactory;

    protected $table = 'settings_absensi';

    protected $fillable = ['active_session'];

}
