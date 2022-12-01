<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanPersediaan extends Model
{
    use HasFactory;
    protected $table = 'laporan_persediaan';
    protected $fillable = ['kode_barang', 'debit','kredit','balance','harga','total'];

    public function barang()
    {
        return $this->hasOne(Barang::class, 'kode_barang', 'kode_barang');
    }
}
