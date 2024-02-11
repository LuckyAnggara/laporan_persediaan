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

class InputController extends Controller
{
    function index(Request $request)
    {
        $bulan = $request->bulan == null ? Carbon::now()->format('m'): $request->bulan;
        $tahun = $request->tahun == null ? Carbon::now()->format('Y') : $request->tahun;
        $tanggalAkhir =  Carbon::create($tahun, $bulan)->lastOfMonth()->format('d');
        return view('input',['bulan'=> $bulan, 'tahun'=> $tahun, 'tanggalAkhir' => $tanggalAkhir]);
    }

  
}
