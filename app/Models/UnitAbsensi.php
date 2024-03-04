<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitAbsensi extends Model
{
    use HasFactory;

    protected $table = 'unit_absensi';

    protected $fillable = ['id', 'nama', 'latitude', 'longitude', 'radius', 'id_pegawai'];
}
