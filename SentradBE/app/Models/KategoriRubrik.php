<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KategoriRubrik extends Model
{
    use HasUuids, SoftDeletes, HasFactory;

    protected $fillable = [
        'kategori_id',
        'rubrik_id',
    ];
}
