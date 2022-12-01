<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\Persediaan;
use Illuminate\Http\Request;

class PersediaanController extends Controller
{

    function home(Request $request)
    {
        return view('welcome');
    }

    function index(Request $request)
    {

        $tanggal = null;

        if ($request->all()) {
            $persediaan = Persediaan::selectRaw('sum(debit) as debit, sum(kredit) as kredit, kode_barang, sum(debit - kredit) as saldo')
                ->with('barang')
                ->groupBy('kode_barang')
                ->get();
        } else {
            $persediaan = Persediaan::selectRaw('sum(debit) as debit, sum(kredit) as kredit, kode_barang, sum(debit - kredit) as saldo')
                ->with('barang')
                ->groupBy('kode_barang')
                ->get();
        }



        $total = 0;
        foreach ($persediaan as $key => $value) {
            $harga = Pembelian::where('kode_barang', $value->kode_barang)->whereNot('saldo',  0)->first();
            if ($harga) {
                $value->harga_pokok =  $harga->harga_beli;
            } else {
                $value->harga_pokok = 0;
            }

            $total = $total + ($value->saldo * $value->harga_pokok);
        }

        return view('persediaan.index', ['persediaan' => $persediaan, 'total' => $total, 'tanggal' => $tanggal]);
    }
}
