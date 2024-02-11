<?php

namespace App\Http\Controllers;

use App\Exports\PelangganExport;
use App\Models\Pelanggan;
use App\Models\Penjualan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PelangganController extends Controller
{
    function index(Request $request)
    {
        $year = Carbon::now()->format('Y');


        $sortBy = $request->input('sortby', 'nama_pelanggan');
        $sortDir = $request->input('sortdir', 'asc');
        $tahun = $request->input('tahun',$year);
        $limit = $request->input('limit', 10);

        $rentang_waktu = [$tahun . '-01-01', $tahun . '-12-31'];

        $pelanggan = Pelanggan::select('master_pelanggan.nama_pelanggan','master_pelanggan.id_pelanggan','master_pelanggan.alamat','master_pelanggan.nomor_telepon', DB::raw('COALESCE(SUM(master_penjualan.total_penjualan), 0) as total'))
            ->leftJoin('master_penjualan', 'master_pelanggan.id_pelanggan', '=', 'master_penjualan.id_pelanggan')
            ->where('master_pelanggan.status_pelanggan', 0)
            ->whereBetween('master_penjualan.tanggal_transaksi', $rentang_waktu)
            ->groupBy('master_pelanggan.id_pelanggan', 'master_pelanggan.id','master_pelanggan.nama_pelanggan','master_pelanggan.alamat','master_pelanggan.nomor_telepon')
            ->orderBy($sortBy, $sortDir)
            ->paginate($limit);

        // $pelanggan = Pelanggan::where('status_pelanggan', 0)
        // ->orderBy($sortBy, $sortDir)
        // ->paginate($limit);

        // foreach ($pelanggan as $key => $p) {
        //     $total = Penjualan::selectRaw('sum(total_penjualan) as total')->where('id_pelanggan', $p->id_pelanggan)->whereBetween('tanggal_transaksi', $rentang_waktu)->first();
        //     $p->total = $total->total;
        // }

        // $pelanggan->appends(['tanggal' => $tanggal]);

        $pelanggan->appends(['limit' => $limit]);
        $pelanggan->appends(['tahun' => $tahun]);
        $pelanggan->appends(['sortby' => $sortBy]);
        $pelanggan->appends(['sortdir' => $sortDir]);
        // return $master;
        return view('pelanggan.index', compact('pelanggan', 'limit', 'tahun', 'sortDir', 'sortBy'));
    }

    function excelExport(Request $request){
        $year = Carbon::now()->format('Y');
        $tahun = $request->input('tahun',$year);

        $rentang_waktu = [$tahun . '-01-01', $tahun . '-12-31'];

        $pelanggan = Pelanggan::select('master_pelanggan.nama_pelanggan','master_pelanggan.id_pelanggan','master_pelanggan.alamat','master_pelanggan.nomor_telepon', DB::raw('COALESCE(SUM(master_penjualan.total_penjualan), 0) as total'))
            ->leftJoin('master_penjualan', 'master_pelanggan.id_pelanggan', '=', 'master_penjualan.id_pelanggan')
            ->where('master_pelanggan.status_pelanggan', 0)
            ->whereBetween('master_penjualan.tanggal_transaksi', $rentang_waktu)
            ->groupBy('master_pelanggan.id_pelanggan', 'master_pelanggan.id','master_pelanggan.nama_pelanggan','master_pelanggan.alamat','master_pelanggan.nomor_telepon')
            ->orderBy('nama_pelanggan', 'asc')
            ->orderBy('total', 'asc')
            ->get();
         $pelanggan->toArray();

        // return $tanggalData;
        return (new PelangganExport($pelanggan->toArray(), $year))->download('pelanggan.xlsx');
    }
}
