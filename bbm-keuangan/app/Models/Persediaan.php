<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Persediaan extends Model
{
    use HasFactory;

    protected $table = 'master_detail_persediaan';

    public function barang()
    {
        return $this->hasOne(Barang::class, 'kode_barang', 'kode_barang');
    }
}
