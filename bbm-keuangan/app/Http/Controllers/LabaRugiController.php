<?php

namespace App\Http\Controllers;

use App\Models\Biaya;
use App\Models\Gaji;
use App\Models\HargaPokokProduksi;
use App\Models\LabaRugi;
use App\Models\LabaRugiBulanan;
use App\Models\Pembelian;
use App\Models\Penjualan;
use App\Models\Persediaan;
use App\Models\ReturPenjualan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LabaRugiController extends Controller
{
    function index(Request $request)
    {
        $tanggal  = $request->input('tanggal', date('Y-m-d'));

        $fromDate = Carbon::parse($tanggal)->startOfDay();
        $toDate = Carbon::parse($tanggal)->endOfDay();

        $fromDate2 = Carbon::parse($tanggal)->startOfDay();
        $toDate2 = Carbon::parse($tanggal)->endOfDay();

        $data1 = LabaRugi::whereBetween('created_at', [$fromDate, $toDate])
            ->get();

        $data2 = LabaRugi::whereBetween('created_at', [$fromDate2->subDays(), $toDate2->subDays()])
            ->get();
     

        // return $data2;

        return view('labarugi.index', ['data1' => $data1, 'data2' => $data2, 'tanggal1' => $fromDate, 'tanggal2' => $fromDate2]);
    }

    function indexBulanan(Request $request)
    {
        $bulan  = $request->input('bulan', Carbon::now()->format('m'));
        
        if($bulan){
            $year = Carbon::now()->format('Y');
            $lastMonth  = Carbon::createFromDate($year, $bulan,1)->subMonth()->format('m');
        }
        // return $lastMonth;
        $data1 = LabaRugiBulanan::whereMonth('created_at',$bulan)
            ->get();

        $data2 = LabaRugiBulanan::whereMonth('created_at',$lastMonth)
            ->get();

        // return $data2;

        return view('labarugi.indexbulanan', ['data1' => $data1, 'data2' => $data2, 'bulan'=> $bulan, 'bulan1' => Carbon::createFromDate($year,$bulan, 1)->Format('F Y'), 'lastMonth' => Carbon::createFromDate($year,$lastMonth, 1)->Format('F Y')]);
    }

    static function persediaan($date)
    {
        $startDate = '2020-01-01 00:00:01';
        $endDate = Carbon::parse($date)->format('Y-m-d 23:59:59');
        $master = Persediaan::selectRaw('sum(debit) as debit, sum(kredit) as kredit, kode_barang, sum(debit - kredit) as balance')
            ->with('barang')
            ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
            // ->whereNot('saldo',  0)
            ->groupBy('kode_barang');
        $persediaan = $master->get();

        $totalSemuaPersediaan = 0;
        foreach ($persediaan as $key => $value) {
            $harga = Pembelian::where('kode_barang', $value->kode_barang)->whereNot('saldo',  0)->first();
            if ($harga) {
                $value->harga_pokok =  $harga->harga_beli;
            } else {
                $value->harga_pokok = 0;
            }
            $totalSemuaPersediaan = $totalSemuaPersediaan + ($value->balance * $value->harga_pokok);
        }

        return $totalSemuaPersediaan;
        // return $persediaan;
    }

    function run()
    {

        for ($i = 1; $i < 31; $i++) {
            $this->generateLabaRugi($i);
        }

        return 'ok';
    }

    function generateLabaRugi($d)
    {
        $tanggal = date('Y-10-' . $d);
        $fromDate = Carbon::parse($tanggal)->startOfDay();
        $toDate = Carbon::parse($tanggal)->endOfDay();

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
            'nomor' => 1,
            'account' => 'PENJUALAN',
            'class' => 'fw-bold',
            'balance' => $totalPenjualan->total_penjualan
        );
        $data[1] = array(
            'nomor' => 2,
            'class' => 'text-danger',
            'account' => 'RETUR PENJUALAN',
            'balance' => $returPenjualan->retur_total == null ? 0 : $returPenjualan->retur_total
        );
        $data[2] = array(
            'nomor' => 3,
            'class' => 'fw-bold',
            'account' => 'TOTAL PENJUALAN (1-2)',
            'balance' => $totalPenjualan->total_penjualan - $totalPenjualan->diskon - $data[1]['balance']
        );
        $data[3] = array(
            'nomor' => 4,
            'class' => '',
            'account' => 'PERSEDIAAN AWAL',
            'balance' => $persediaanAwal
        );
        $data[4] = array(
            'nomor' => 5,
            'class' => '',
            'account' => 'TOTAL PEMBELIAN',
            'balance' => $pembelian->total_pembelian == null ? 0 : $pembelian->total_pembelian
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
            'balance' =>  $persediaanAwal + $pembelian->total_pembelian - $persediaanAkhir
        );
        $data[7] = array(
            'nomor' => 8,
            'class' => 'fw-bold',
            'account' => 'TOTAL PENDAPATAN (3-7)',
            'balance' =>  $data[2]['balance'] - $data[6]['balance']
        );
        $data[8] = array(
            'nomor' => 9,
            'class' => 'text-danger',
            'account' => 'BIAYA OPERASIONAL',
            'balance' =>  $totalBiaya
        );
        $data[9] = array(
            'nomor' => 10,
            'class' => 'text-danger',
            'account' => 'GAJI',
            'balance' =>  $gaji->total
        );
        $data[10] = array(
            'nomor' => 11,
            'class' => 'fw-bold',
            'account' => 'LABA / RUGI (8-9-10)',
            'balance' =>  $data[7]['balance'] - $data[8]['balance'] - $data[9]['balance']
        );


        foreach ($data as $key => $d) {
            LabaRugi::create([
                'nomor' => $d['nomor'],
                'account' => $d['account'],
                'class' => $d['class'],
                'balance' => $d['balance'],
                'created_at' => $tanggal,
            ]);
        }
    }

    function testMonth()
    {
        // $fromDate = Carbon::parse($tanggal)->startOfDay();
        // $toDate = Carbon::parse($tanggal)->endOfDay();

        $data = LabaRugi::selectRaw("nomor, account, class, sum(balance) as balance")
        ->groupBy('nomor')
        ->groupBy('account')
        ->groupBy('class')
        ->whereMonth('created_at', '11')
        // ->whereNot('nomor', '7')
        // ->whereNot('nomor', '8')
        // ->whereNot('nomor', '10')
        ->get();

        $persediaanAwal = $this->persediaanBulan(10);
        $persediaanAkhir = $this->persediaanBulan(11);

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
                'created_at' => '2022-10-31'
            ]);
        }
        
        return $data;

    }

    static function persediaanBulan($bulan)
    {
        $startDate = '2020-01-01 00:00:01';
        $year = Carbon::now()->format('Y');
        // $year =  Carbon::now()->subYears()->format('Y');
        // return $year;
        $endDate = Carbon::createFromDate($year, $bulan,1)->endOfMonth();

        $master = Persediaan::selectRaw('sum(debit) as debit, sum(kredit) as kredit, kode_barang, sum(debit - kredit) as balance')
            ->with('barang')
            ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
            // ->whereNot('saldo',  0)
            ->groupBy('kode_barang');
        $persediaan = $master->get();

        $totalSemuaPersediaan = 0;
        foreach ($persediaan as $key => $value) {
            $harga = Pembelian::where('kode_barang', $value->kode_barang)->whereNot('saldo',  0)->first();
            if ($harga) {
                $value->harga_pokok =  $harga->harga_beli;
            } else {
                $value->harga_pokok = 0;
            }
            $totalSemuaPersediaan = $totalSemuaPersediaan + ($value->balance * $value->harga_pokok);
        }

        return $totalSemuaPersediaan;
        // return $persediaan;
    }
}
