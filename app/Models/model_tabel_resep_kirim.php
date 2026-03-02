<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class model_tabel_resep_kirim extends Model
{
    use HasFactory;
    const UPDATED_AT = null;
    const CREATED_AT = null;
    protected $connection = 'mysql';
    protected $table = 'apt_online_resep_kirim_obat';
    protected $guarded = [];
}
