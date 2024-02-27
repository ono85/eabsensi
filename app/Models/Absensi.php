<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'absensi';

    protected $dates = ['tanggal'];

    protected $fillable = ['id', 'filename', 'latitude', 'longitude', 'tanggal', 'id_pegawai'];
}
