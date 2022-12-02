<?php

namespace App\Console\Commands;

use App\Models\LaporanPersediaan;
use App\Models\Pembelian;
use App\Models\Persediaan;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GeneratePersediaan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:persediaan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Laporan Persediaan Harian';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        info("Cron Job running at " . now());

        $startDate = '2020-01-01 00:00:01';
        $endate = date('Y-m-d 23:59:59');
        $persediaan = Persediaan::selectRaw('sum(debit) as debit, sum(kredit) as kredit, kode_barang, sum(debit - kredit) as balance')
            ->with('barang')
            ->whereBetween('tanggal_transaksi', [$startDate, $endate])
            // ->whereNot('saldo',  0)
            ->groupBy('kode_barang');

        $data = $persediaan->get();
        foreach ($data as $key => $value) {
            $harga = Pembelian::where('kode_barang', $value->kode_barang)->whereNot('saldo',  0)->first();
            if ($harga) {
                $value->harga_pokok =  $harga->harga_beli;
            } else {
                $value->harga_pokok = 0;
            }

            LaporanPersediaan::create([
                'kode_barang' => $value->kode_barang,
                'debit' => $value->debit,
                'kredit' => $value->kredit,
                'balance' => $value->balance,
                'harga' => $value->harga_pokok,
                'total' => $value->harga_pokok * $value->balance,
            ]);
        }
        info("Cron Job Done at " . now());
        return 0;
    }
}
