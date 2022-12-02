<?php

namespace App\Console\Commands;

use App\Models\Biaya;
use App\Models\Gaji;
use App\Models\LabaRugi;
use App\Models\LaporanPersediaan;
use App\Models\Pembelian;
use App\Models\Penjualan;
use App\Models\Persediaan;
use App\Models\ReturPenjualan;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GeneratePersediaan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:labarugi';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Laporan Laba Rugi Harian';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        info("Cron Job Generate Laba Rugi running at " . now());

        $fromDate = Carbon::now()->startOfDay();
        $toDate = Carbon::now()->endOfDay();

        $biaya = Biaya::selectRaw('kategori_biaya, sum(total) as total')
            ->with('nama')
            ->groupBy('kategori_biaya')
            ->whereBetween('tanggal', [$fromDate, $toDate])
            ->get();

        $totalBiaya = 0;
        foreach ($biaya as $key => $b) {
            $totalBiaya = $totalBiaya + $b->total;
        }

        $gaji = Gaji::selectRaw('sum(total_pembayaran) as total')
            ->whereBetween('tanggal', [$fromDate, $toDate])
            ->first();

        $totalPenjualan = Penjualan::selectRaw('sum(total_penjualan) as total_penjualan')
            ->selectRaw('sum(diskon) as diskon')
            ->selectRaw('sum(ongkir) as ongkir')
            ->selectRaw('sum(pajak_masukan) as pajak_masukan')
            ->whereBetween('tanggal_transaksi', [$fromDate, $toDate])->first();

        $returPenjualan = ReturPenjualan::selectRaw('sum(retur_grand_total) as retur_total')
            ->whereBetween('tanggal_transaksi', [$fromDate, $toDate])->first();

        $pembelian = Pembelian::selectRaw('sum(total_harga) as total_pembelian')
            ->whereBetween('tanggal_input', [$fromDate, $toDate])->first();

        $persediaanAwal = $this->persediaan($fromDate->subDay());
        $persediaanAkhir = $this->persediaan($fromDate->addDay());

        $data[0] = array(
            'no' => 1,
            'account' => 'PENJUALAN',
            'class' => 'fw-bold',
            'balance' => $totalPenjualan->total_penjualan
        );
        $data[1] = array(
            'no' => 2,
            'class' => 'text-danger',
            'account' => 'RETUR PENJUALAN',
            'balance' => $returPenjualan->retur_total == null ? 0 : $returPenjualan->retur_total
        );
        $data[2] = array(
            'no' => 3,
            'class' => 'fw-bold',
            'account' => 'TOTAL PENJUALAN (1-2)',
            'balance' => $totalPenjualan->total_penjualan - $totalPenjualan->diskon - $data[1]['balance']
        );
        $data[3] = array(
            'no' => 4,
            'class' => '',
            'account' => 'PERSEDIAAN AWAL',
            'balance' => $persediaanAwal
        );
        $data[4] = array(
            'no' => 5,
            'class' => '',
            'account' => 'TOTAL PEMBELIAN',
            'balance' => $pembelian->total_pembelian == null ? 0 : $pembelian->total_pembelian
        );
        $data[5] = array(
            'no' => 6,
            'class' => '',
            'account' => 'PERSEDIAAN AKHIR',
            'balance' =>  $persediaanAkhir
        );
        $data[6] = array(
            'no' => 7,
            'class' => 'fw-bold text-danger',
            'account' => 'HARGA POKOK PENJUALAN (4+5-6)',
            'balance' =>  $persediaanAwal + $pembelian->total_pembelian - $persediaanAkhir
        );
        $data[7] = array(
            'no' => 8,
            'class' => 'fw-bold',
            'account' => 'TOTAL PENDAPATAN (3-7)',
            'balance' =>  $data[2]['balance'] - $data[6]['balance']
        );
        $data[8] = array(
            'no' => 9,
            'class' => 'text-danger',
            'account' => 'BIAYA OPERASIONAL',
            'balance' =>  $totalBiaya
        );
        $data[9] = array(
            'no' => 10,
            'class' => 'text-danger',
            'account' => 'GAJI',
            'balance' =>  $gaji->total
        );
        $data[10] = array(
            'no' => 11,
            'class' => 'fw-bold',
            'account' => 'LABA / RUGI (8-9-10)',
            'balance' =>  $data[7]['balance'] - $data[8]['balance'] - $data[9]['balance']
        );


        foreach ($data as $key => $d) {
            LabaRugi::create([
                'no' => $d['no'],
                'account' => $d['account'],
                'class' => $d['class'],
                'balance' => $d['balance'],
            ]);
        }

        info("Cron Job Done at " . now());
        return 0;
    }
}
