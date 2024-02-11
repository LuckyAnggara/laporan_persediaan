<?php

namespace App\Http\Controllers;

use App\Exports\PersediaanExport;
use App\Models\LaporanPersediaan;
use App\Models\Pembelian;
use App\Models\Persediaan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class PersediaanController extends Controller
{

    function home(Request $request)
    {
        return view('welcome');
    }

    function index(Request $request)
    {

        $tanggal = $request->tanggal;
        $name = $request->searchQuery;
        $limit =  $request->input('limit', 10);
        $startDate = '2020-01-01 00:00:01';

        if (!$tanggal) {
            $tanggalShow = date("Y/m/d g:i A");
            $newDate = Carbon::parse(date("Y/m/d"))->format('Y-m-d 23:59:59');
            $master = Persediaan::selectRaw('sum(debit) as debit, sum(kredit) as kredit, kode_barang, sum(debit - kredit) as balance')
                ->with('barang')
                ->whereBetween('tanggal_transaksi', [$startDate, $newDate])
                ->when($name, function ($query, $name) {
                return $query->where('kode_barang', 'like', '%' . $name . '%');
                })
                // ->whereNot('saldo',  0)
                ->groupBy('kode_barang');
        } else {
            $tanggalShow = $tanggal;
            $newDate = Carbon::parse($tanggal)->format('Y-m-d 23:59:59');
            $master = Persediaan::selectRaw('sum(debit) as debit, sum(kredit) as kredit, kode_barang, sum(debit - kredit) as balance')
                ->with('barang')
                ->whereBetween('tanggal_transaksi', [$startDate, $newDate])
                 ->when($name, function ($query, $name) {
                return $query->where('kode_barang', 'like', '%' . $name . '%');
                })
                // ->whereNot('saldo',  0)
                ->groupBy('kode_barang');
        }
        $master2 = $master->get();


        $persediaan = $master->paginate($limit);
        $persediaan->appends(['tanggal' => $tanggal]);
        $persediaan->appends(['limit' => $limit]);

        $totalSemuaPersediaan = 0;
        foreach ($master2 as $key => $value) {
            $harga = Pembelian::where('kode_barang', $value->kode_barang)->whereNot('saldo',  0)->first();
            if ($harga) {
                $value->harga_pokok =  $harga->harga_beli;
            } else {
                $value->harga_pokok = 0;
            }
            $totalSemuaPersediaan = $totalSemuaPersediaan + ($value->balance * $value->harga_pokok);
        }

        foreach ($persediaan as $key => $value) {
            $harga = Pembelian::where('kode_barang', $value->kode_barang)->whereNot('saldo',  0)->first();
            if ($harga) {
                $value->harga_pokok =  $harga->harga_beli;
            } else {
                $value->harga_pokok = 0;
            }
        }
        return view('persediaan.index', ['persediaan' => $persediaan, 'totalSemuaPersediaan' => $totalSemuaPersediaan, 'tanggal' => $tanggalShow, 'limit' => $limit, 'searchQuery' =>$name]);
    }

    function laporan(Request $request)
    {
        $tanggal = $request->tanggal;
        $limit =  $request->input('limit', 10);

        if (!$tanggal) {
            $tanggalShow = date("Y/m/d g:i A");
            $newDate = Carbon::parse(date("Y/m/d"))->format('Y-m-d');;
        } else {
            $tanggalShow = $tanggal;
            $newDate = Carbon::parse($tanggal)->format('Y-m-d');
        }
        $master = LaporanPersediaan::with('barang')
            ->whereDate('created_at', $newDate);

        $master2 = $master->get();

        $master = $master->paginate($limit);
        $master->appends(['tanggal' => $tanggal]);
        $master->appends(['limit' => $limit]);

        $totalSemuaPersediaan = 0;
        foreach ($master2 as $key => $value) {
            $totalSemuaPersediaan = $totalSemuaPersediaan + $value->total;
        }

        $tanggalData = LaporanPersediaan::select(DB::raw('DATE(created_at) as date'))->groupBy('date')->get();

        // return $tanggalData;


        return view('persediaan.laporan', ['persediaan' => $master,  'totalSemuaPersediaan' => $totalSemuaPersediaan, 'tanggal' => $tanggalShow, 'limit' => $limit, 'tanggalData' => $tanggalData]);
    }

    function excelExport(Request $request){
        $tanggal = $request->tanggal;
        $limit =  $request->input('limit', 10);

        if (!$tanggal) {
            $tanggalShow = date("Y/m/d g:i A");
            $newDate = Carbon::parse(date("Y/m/d"))->format('Y-m-d');;
        } else {
            $tanggalShow = $tanggal;
            $newDate = Carbon::parse($tanggal)->format('Y-m-d');
        }
        $newDate = '2022-12-01';
        // return $tanggalData;
        return (new PersediaanExport($newDate))->download('persediaan.xlsx');
    }
}
