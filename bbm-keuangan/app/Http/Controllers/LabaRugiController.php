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
        $tanggal = $request->input('tanggal', date('Y-m-d'));

        $fromDate = Carbon::parse($tanggal)->startOfDay();
        $toDate = Carbon::parse($tanggal)->endOfDay();

        $fromDate2 = Carbon::parse($tanggal)->startOfDay();
        $toDate2 = Carbon::parse($tanggal)->endOfDay();

        $data1 = LabaRugi::whereBetween('created_at', [$fromDate, $toDate])->get();

        $data2 = LabaRugi::whereBetween('created_at', [$fromDate2->subDays(), $toDate2->subDays()])->get();

        // return $data2;

        return view('labarugi.index', ['data1' => $data1, 'data2' => $data2, 'tanggal1' => $fromDate, 'tanggal2' => $fromDate2]);
    }

    function indexBulanan(Request $request)
    {
        $bulan = $request->input('bulan', Carbon::now()->format('m'));
        $year = $request->input('tahun', Carbon::now()->format('Y'));

        if ($bulan) {
            // $year = Carbon::now()->format('Y');
            $lastMonth = Carbon::createFromDate($year, $bulan, 1)
                ->subMonth()
                ->format('m');
        }
        // return $lastMonth;
        $data1 = LabaRugiBulanan::whereMonth('created_at', $bulan)
            ->whereYear('created_at', $year)
            ->get();

        $data2 = LabaRugiBulanan::whereMonth('created_at', $lastMonth)
            ->whereYear('created_at', $year)
            ->get();

        // return $data2;

        return view('labarugi.indexbulanan', ['data1' => $data1, 'data2' => $data2, 'bulan' => $bulan, 'tahun' => $year, 'bulan1' => Carbon::createFromDate($year, $bulan, 1)->Format('F Y'), 'lastMonth' => Carbon::createFromDate($year, $lastMonth, 1)->Format('F Y')]);
    }

    function indexTahunan(Request $request)
    {
        if ($request->input('tahun') == null) {
            $year = Carbon::now()
                ->subYear()
                ->format('Y');
        } else {
            $year = Carbon::create($request->input('tahun'))->format('Y');
        }

        $dd = 12;
        $result = [];
        for ($i = 1; $i < $dd; $i++) {
            $balance = LabaRugiBulanan::where('nomor', $i)
                ->whereYear('created_at', $year)
                ->sum('balance');

            $detail = LabaRugiBulanan::where('nomor', $i)->first();

            $result[] = [
                'nomor' => $i,
                'account' => $detail->account,
                'balance' => $balance,
                'class' => $detail->class,
            ];
        }

        return view('labarugi.indextahunan', ['data' => $result, 'tahun' => $year]);
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
            $harga = Pembelian::where('kode_barang', $value->kode_barang)
                ->whereNot('saldo', 0)
                ->first();
            if ($harga) {
                $value->harga_pokok = $harga->harga_beli;
            } else {
                $value->harga_pokok = 0;
            }
            $totalSemuaPersediaan = $totalSemuaPersediaan + $value->balance * $value->harga_pokok;
        }

        return $totalSemuaPersediaan;
        // return $persediaan;
    }

    function generateLabaRugi($d, $bulan, $tahun)
    {
        $tanggal = date($tahun . '-' . $bulan . '-' . $d);
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
            ->whereBetween('tanggal_transaksi', [$fromDate, $toDate])
            ->first();

        $returPenjualan = ReturPenjualan::selectRaw('sum(retur_grand_total) as retur_total')
            ->whereBetween('tanggal_transaksi', [$fromDate, $toDate])
            ->first();

        $pembelian = Pembelian::selectRaw('sum(total_harga) as total_pembelian')
            ->whereBetween('tanggal_input', [$fromDate, $toDate])
            ->first();

        $persediaanAwal = $this->persediaan($fromDate->subDay());
        $persediaanAkhir = $this->persediaan($fromDate->addDay());

        $data[0] = [
            'nomor' => 1,
            'account' => 'PENJUALAN',
            'class' => 'fw-bold',
            'balance' => $totalPenjualan->total_penjualan ?? 0,
        ];
        $data[1] = [
            'nomor' => 2,
            'class' => 'text-danger',
            'account' => 'RETUR PENJUALAN',
            'balance' => $returPenjualan->retur_total == null ? 0 : $returPenjualan->retur_total,
        ];
        $data[2] = [
            'nomor' => 3,
            'class' => 'fw-bold',
            'account' => 'TOTAL PENJUALAN (1-2)',
            'balance' => $totalPenjualan->total_penjualan - $totalPenjualan->diskon - $data[1]['balance'],
        ];
        $data[3] = [
            'nomor' => 4,
            'class' => '',
            'account' => 'PERSEDIAAN AWAL',
            'balance' => $persediaanAwal,
        ];
        $data[4] = [
            'nomor' => 5,
            'class' => '',
            'account' => 'TOTAL PEMBELIAN',
            'balance' => $pembelian->total_pembelian == null ? 0 : $pembelian->total_pembelian,
        ];
        $data[5] = [
            'nomor' => 6,
            'class' => '',
            'account' => 'PERSEDIAAN AKHIR',
            'balance' => $persediaanAkhir,
        ];
        $data[6] = [
            'nomor' => 7,
            'class' => 'fw-bold text-danger',
            'account' => 'HARGA POKOK PENJUALAN (4+5-6)',
            'balance' => $persediaanAwal + $pembelian->total_pembelian - $persediaanAkhir,
        ];
        $data[7] = [
            'nomor' => 8,
            'class' => 'fw-bold',
            'account' => 'TOTAL PENDAPATAN (3-7)',
            'balance' => $data[2]['balance'] - $data[6]['balance'],
        ];
        $data[8] = [
            'nomor' => 9,
            'class' => 'text-danger',
            'account' => 'BIAYA OPERASIONAL',
            'balance' => $totalBiaya,
        ];
        $data[9] = [
            'nomor' => 10,
            'class' => 'text-danger',
            'account' => 'GAJI',
            'balance' => $gaji->total ?? 0,
        ];
        $data[10] = [
            'nomor' => 11,
            'class' => 'fw-bold',
            'account' => 'LABA / RUGI (8-9-10)',
            'balance' => $data[7]['balance'] - $data[8]['balance'] - $data[9]['balance'],
        ];

        foreach ($data as $key => $d) {
            LabaRugi::create([
                'nomor' => $d['nomor'],
                'account' => $d['account'],
                'class' => $d['class'],
                'balance' => $d['balance'],
                'created_at' => $tanggal,
            ]);
        }

        return 'Sukses';
    }

    static function persediaanBulan($bulan, $tahun)
    {
        $startDate = '2020-01-01 00:00:01';
        // $year = Carbon::now()->format('Y');
        $endDate = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth();

        $master = Persediaan::selectRaw('sum(debit) as debit, sum(kredit) as kredit, kode_barang, sum(debit - kredit) as balance')
            ->with('barang')
            ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
            // ->whereNot('saldo',  0)
            ->groupBy('kode_barang');
        $persediaan = $master->get();

        $totalSemuaPersediaan = 0;
        foreach ($persediaan as $key => $value) {
            $harga = Pembelian::where('kode_barang', $value->kode_barang)
                ->whereNot('saldo', 0)
                ->first();
            if ($harga) {
                $value->harga_pokok = $harga->harga_beli;
            } else {
                $value->harga_pokok = 0;
            }
            $totalSemuaPersediaan = $totalSemuaPersediaan + $value->balance * $value->harga_pokok;
        }

        return $totalSemuaPersediaan;
        // return $persediaan;
    }

    function run(Request $request)
    {
        $tahun = $request->input('tahun', Carbon::now()->format('Y'));
        $bulan = $request->input('bulan', Carbon::now()->format('m'));
        // for ($a = 1; $a < 13; $a++) {
        // $bulan  = Carbon::create($tahun, $a)->format('m');
        $dd = Carbon::create($tahun, $bulan)
            ->lastOfMonth()
            ->format('d');
        for ($i = 1; $i < $dd + 1; $i++) {
            $data[$i] = $this->generateLabaRugi($i, $bulan, $tahun);
        }
        // }
        return $data;
    }

    function testYear(Request $request)
    {
        $tahun = $request->input('tahun', Carbon::now()->format('Y'));
        $dd = 12;
        $result = [];
        for ($i = 1; $i < $dd; $i++) {
            $data = LabaRugiBulanan::where('nomor', $i)
                ->whereYear('created_at', $tahun)
                ->sum('balance');
            $result[$i] = $data;
        }
        return $result;
    }

    function testMonth(Request $request)
    {
        $tahun = $request->input('tahun', Carbon::now()->format('Y'));
        $dd = 2;

        for ($i = 1; $i < $dd; $i++) {
            $bulan = Carbon::create($tahun, $i)->format('m');
            $subBulan = Carbon::create($tahun, $i)
                ->subMonth()
                ->format('m');
            $subTahun = Carbon::create($tahun, $i)
                ->subMonth()
                ->format('Y');
            $lastDay = Carbon::create($tahun, $bulan)
                ->lastOfMonth()
                ->format('d');
            $result[$i] = Carbon::create($tahun, $bulan, $lastDay);

            $data = LabaRugi::selectRaw('nomor, account, class, sum(balance) as balance')
                ->groupBy('nomor')
                ->groupBy('account')
                ->groupBy('class')
                ->whereMonth('created_at', $bulan)
                ->whereYear('created_at', $tahun)
                ->get();
            $persediaanAwal = $this->persediaanBulan($subBulan, $bulan == 01 ? $subTahun : $tahun);
            $persediaanAkhir = $this->persediaanBulan($bulan, $tahun);

            $data[3] = [
                'nomor' => 4,
                'class' => '',
                'account' => 'PERSEDIAAN AWAL',
                'balance' => $persediaanAwal,
            ];
            $data[5] = [
                'nomor' => 6,
                'class' => '',
                'account' => 'PERSEDIAAN AKHIR',
                'balance' => $persediaanAkhir,
            ];
            $data[6] = [
                'nomor' => 7,
                'class' => 'fw-bold text-danger',
                'account' => 'HARGA POKOK PENJUALAN (4+5-6)',
                'balance' => $persediaanAwal + $data[4]->balance - $persediaanAkhir,
            ];
            $data[7] = [
                'nomor' => 8,
                'class' => 'fw-bold',
                'account' => 'TOTAL PENDAPATAN (3-7)',
                'balance' => $data[2]['balance'] - $data[6]['balance'],
            ];
            $data[10] = [
                'nomor' => 11,
                'class' => 'fw-bold',
                'account' => 'LABA / RUGI (8-9-10)',
                'balance' => $data[7]['balance'] - $data[8]['balance'] - $data[9]['balance'],
            ];

            foreach ($data as $key => $d) {
                LabaRugiBulanan::create([
                    'nomor' => $d['nomor'],
                    'account' => $d['account'],
                    'class' => $d['class'],
                    'balance' => $d['balance'],
                    'created_at' => Carbon::create($tahun, $bulan, $lastDay),
                ]);
            }
        }
        return $result;
    }

    function generateLabaRugi2(Request $request)
    {
        $tahun = $request->tahun;
        $bulan = $request->bulan;
        $d = $request->d;

        $tanggal = date($tahun . '-' . $bulan . '-' . $d);
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
            ->whereBetween('tanggal_transaksi', [$fromDate, $toDate])
            ->first();

        $returPenjualan = ReturPenjualan::selectRaw('sum(retur_grand_total) as retur_total')
            ->whereBetween('tanggal_transaksi', [$fromDate, $toDate])
            ->first();

        $pembelian = Pembelian::selectRaw('sum(total_harga) as total_pembelian')
            ->whereBetween('tanggal_input', [$fromDate, $toDate])
            ->first();

        $persediaanAwal = $this->persediaan($fromDate->subDay());
        $persediaanAkhir = $this->persediaan($fromDate->addDay());

        $data[0] = [
            'nomor' => 1,
            'account' => 'PENJUALAN',
            'class' => 'fw-bold',
            'balance' => $totalPenjualan->total_penjualan ?? 0,
        ];
        $data[1] = [
            'nomor' => 2,
            'class' => 'text-danger',
            'account' => 'RETUR PENJUALAN',
            'balance' => $returPenjualan->retur_total == null ? 0 : $returPenjualan->retur_total,
        ];
        $data[2] = [
            'nomor' => 3,
            'class' => 'fw-bold',
            'account' => 'TOTAL PENJUALAN (1-2)',
            'balance' => $totalPenjualan->total_penjualan - $totalPenjualan->diskon - $data[1]['balance'],
        ];
        $data[3] = [
            'nomor' => 4,
            'class' => '',
            'account' => 'PERSEDIAAN AWAL',
            'balance' => $persediaanAwal,
        ];
        $data[4] = [
            'nomor' => 5,
            'class' => '',
            'account' => 'TOTAL PEMBELIAN',
            'balance' => $pembelian->total_pembelian == null ? 0 : $pembelian->total_pembelian,
        ];
        $data[5] = [
            'nomor' => 6,
            'class' => '',
            'account' => 'PERSEDIAAN AKHIR',
            'balance' => $persediaanAkhir,
        ];
        $data[6] = [
            'nomor' => 7,
            'class' => 'fw-bold text-danger',
            'account' => 'HARGA POKOK PENJUALAN (4+5-6)',
            'balance' => $persediaanAwal + $pembelian->total_pembelian - $persediaanAkhir,
        ];
        $data[7] = [
            'nomor' => 8,
            'class' => 'fw-bold',
            'account' => 'TOTAL PENDAPATAN (3-7)',
            'balance' => $data[2]['balance'] - $data[6]['balance'],
        ];
        $data[8] = [
            'nomor' => 9,
            'class' => 'text-danger',
            'account' => 'BIAYA OPERASIONAL',
            'balance' => $totalBiaya,
        ];
        $data[9] = [
            'nomor' => 10,
            'class' => 'text-danger',
            'account' => 'GAJI',
            'balance' => $gaji->total ?? 0,
        ];
        $data[10] = [
            'nomor' => 11,
            'class' => 'fw-bold',
            'account' => 'LABA / RUGI (8-9-10)',
            'balance' => $data[7]['balance'] - $data[8]['balance'] - $data[9]['balance'],
        ];

        foreach ($data as $key => $d) {
            LabaRugi::create([
                'nomor' => $d['nomor'],
                'account' => $d['account'],
                'class' => $d['class'],
                'balance' => $d['balance'],
                'created_at' => $tanggal,
            ]);
        }
        return 'Sukses';
    }

    function generateLabaRugiBulanan(Request $request)
    {
        $month = $request->bulan;
        $year = $request->tahun;
        $lastMonth = $request->bulan - 1;
        $lastYear = $request->tahun - 1;
        $lastDay = Carbon::create($year, $month)
            ->lastOfMonth()
            ->format('d');
        $data = LabaRugi::selectRaw('nomor, account, class, sum(balance) as balance')
            ->groupBy('nomor')
            ->groupBy('account')
            ->groupBy('class')
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->get();

        $persediaanAwal = $this->persediaanBulan($lastMonth, $month == 1 ? $lastYear : $year);
        $persediaanAkhir = $this->persediaanBulan($month, $year);

        $data[3] = [
            'nomor' => 4,
            'class' => '',
            'account' => 'PERSEDIAAN AWAL',
            'balance' => $persediaanAwal,
        ];
        $data[5] = [
            'nomor' => 6,
            'class' => '',
            'account' => 'PERSEDIAAN AKHIR',
            'balance' => $persediaanAkhir,
        ];
        $data[6] = [
            'nomor' => 7,
            'class' => 'fw-bold text-danger',
            'account' => 'HARGA POKOK PENJUALAN (4+5-6)',
            'balance' => $persediaanAwal + $data[4]->balance - $persediaanAkhir,
        ];
        $data[7] = [
            'nomor' => 8,
            'class' => 'fw-bold',
            'account' => 'TOTAL PENDAPATAN (3-7)',
            'balance' => $data[2]['balance'] - $data[6]['balance'],
        ];
        $data[10] = [
            'nomor' => 11,
            'class' => 'fw-bold',
            'account' => 'LABA / RUGI (8-9-10)',
            'balance' => $data[7]['balance'] - $data[8]['balance'] - $data[9]['balance'],
        ];

        foreach ($data as $key => $d) {
            LabaRugiBulanan::create([
                'nomor' => $d['nomor'],
                'account' => $d['account'],
                'class' => $d['class'],
                'balance' => $d['balance'],
                'created_at' => Carbon::create($year, $month, $lastDay),
            ]);
        }

        return 0;
    }
}
