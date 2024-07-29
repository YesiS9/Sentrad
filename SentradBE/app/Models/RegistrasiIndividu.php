<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RegistrasiIndividu extends Model
{
    use HasUuids, SoftDeletes, HasFactory;

    protected $fillable = [
        'seniman_id',
        'penilaian_karya_id',
        'nama',
        'tgl_lahir',
        'tgl_mulai',
        'alamat',
        'noTelp',
        'email',
        'status_individu',
    ];

    public function seniman()
    {
        return $this->belongsTo(Seniman::class);
    }
}

