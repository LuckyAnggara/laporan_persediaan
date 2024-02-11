<?php

namespace App\Console\Commands;

use App\Models\LabaRugi;
use App\Models\LabaRugiBulanan;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateLabaRugiBulanan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:labarugibulanan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Laba Rugi Bulanan';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        info("Cron Job Generate Laba Rugi Bulanan running at " . now());

        $month =  Carbon::now()->format('m');
        $year = Carbon::now()->format('Y');
        $lastMonth =  Carbon::now()->subMonth()->format('m');
        $lastYear =  Carbon::now()->subYear()->format('Y');
        $data = LabaRugi::selectRaw("nomor, account, class, sum(balance) as balance")
        ->groupBy('nomor')
        ->groupBy('account')
        ->groupBy('class')
        ->whereMonth('created_at', $month)
        ->get();

        $persediaanAwal = $this->persediaanBulan($lastMonth, $month == 01 ? $lastYear : $year);
        $persediaanAkhir = $this->persediaanBulan($month, $year);

        $data[3] = array(
            'nomor' => 4,
            'class' => '',
            'account' => 'PERSEDIAAN AWAL',
            'balance' => $persediaanAwal
        );
        $data[5] = array(
            'nomor' => 6,
            'class' => '',
            'account' => 'PERSEDIAAN AKHIR',
            'balance' =>  $persediaanAkhir
        );
        $data[6] = array(
            'nomor' => 7,
            'class' => 'fw-bold text-danger',
            'account' => 'HARGA POKOK PENJUALAN (4+5-6)',
            'balance' =>  $persediaanAwal + $data[4]->balance - $persediaanAkhir
        );
        $data[7] = array(
            'nomor' => 8,
            'class' => 'fw-bold',
            'account' => 'TOTAL PENDAPATAN (3-7)',
            'balance' =>  $data[2]['balance'] - $data[6]['balance']
        );
        $data[10] = array(
            'nomor' => 11,
            'class' => 'fw-bold',
            'account' => 'LABA / RUGI (8-9-10)',
            'balance' =>  $data[7]['balance'] - $data[8]['balance'] - $data[9]['balance']
        );

        foreach ($data as $key => $d) {
            LabaRugiBulanan::create([
                'nomor' => $d['nomor'],
                'account' => $d['account'],
                'class' => $d['class'],
                'balance' => $d['balance'],
            ]);
        }
        
        return 0;
    }
}
