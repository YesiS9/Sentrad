<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Penilai extends Model
{
    use HasUuids, SoftDeletes, HasFactory;

    protected $fillable = [
        'user_id',
        'nama_penilai',
        'alamat_penilai',
        'noTelp_penilai',
        'bidang_ahli',
        'lembaga',
        'tgl_lahir',
        'status_penilai'
    ];

    public function rubrik()
    {
        return $this->hasMany(Rubrik::class);
    }

    public function penilaian_karya()
    {
        return $this->hasMany(PenilaianKarya::class);
    }
}
