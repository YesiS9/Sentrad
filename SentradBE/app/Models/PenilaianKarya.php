<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PenilaianKarya extends Model
{
    use HasUuids, SoftDeletes, HasFactory;


    protected $fillable = [
        'kuota_id',
        'tingkatan_id',
        'regisIndividu_id',
        'regisKelompok_id',
        'tgl_penilaian',
        'total_nilai',
        'komentar',
    ];

    public function rubrikPenilaians()
    {
        return $this->hasMany(RubrikPenilaian::class, 'penilaian_karya_id', 'id');
    }

    public function kuota()
    {
        return $this->belongsTo(kuota::class, 'kuota_id', 'id');
    }

    public function registrasiIndividu()
    {
        return $this->belongsTo(RegistrasiIndividu::class, 'regisIndividu_id', 'id');
    }

    public function registrasiKelompok()
    {
        return $this->belongsTo(RegistrasiKelompok::class, 'regisKelompok_id', 'id');
    }


    public function getRegistrationTypeAttribute()
    {
        if ($this->regisIndividu_id) {
            return 'individu';
        } elseif ($this->regisKelompok_id) {
            return 'kelompok';
        } else {
            return null;
        }
    }
}
