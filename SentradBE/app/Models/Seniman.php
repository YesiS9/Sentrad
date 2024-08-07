<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Seniman extends Model
{
    use HasUuids, SoftDeletes, HasFactory;
    protected $table = 'seniman';

    protected $fillable = [
        'user_id',
        'nama_seniman',
        'tgl_lahir',
        'deskripsi_seniman',
        'alamat_seniman',
        'noTelp_seniman',
        'lama_pengalaman',
        'status_seniman'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function tingkatan()
    {
        return $this->belongsTo(Tingkatan::class, 'tingkatan_id', 'id');
    }

    public function registrasiIndividu()
    {
        return $this->hasMany(RegistrasiIndividu::class);
    }

    public function registrasiKelompok()
    {
        return $this->hasMany(RegistrasiKelompok::class);
    }
}

