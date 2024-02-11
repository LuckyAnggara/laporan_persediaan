<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    use HasFactory;
    protected $table = 'master_pelanggan';

    public function total_penjualan($start_date = '2022-01-01', $end_date ='2022-12-31')
    {
        return $this->hasMany(Penjualan::class, 'id_pelanggan')
            ->selectRaw('id_pelanggan, SUM(grand_total) as total_penjualan')
            ->whereBetween('tanggal_transaksi', [$start_date, $end_date])
            ->groupBy('id_pelanggan');
    }
}
