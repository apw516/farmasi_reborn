<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class model_stok_persediaan extends Model
{
    use HasFactory;
    const UPDATED_AT = null;
    const CREATED_AT = null;
    protected $connection = 'mysql';
    protected $table = 'ti_stok_pesediaan';
    protected $guarded = [];
    public function barang()
    {
        return $this->belongsTo(MasterBarang::class, 'kode_barang','kode_barang');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'kode_unit','kode_unit');
    }
    public function po_header()
    {
        return $this->belongsTo(model_tg_po_header::class, 'nomor_dokumen','kode_po');
    }
    public function mt_supplier()
    {
        return $this->belongsTo(model_master_supplier::class, 'kode_supplier','kode_supplier');
    }
}
