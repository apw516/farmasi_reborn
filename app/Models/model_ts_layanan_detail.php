<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class model_ts_layanan_detail extends Model
{
    use HasFactory;
    const UPDATED_AT = null;
    const CREATED_AT = null;
    protected $connection = 'mysql';
    protected $table = 'ts_layanan_detail';
    protected $guarded = [];
}
