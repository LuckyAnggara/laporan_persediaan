<?php

namespace App\Exports;

use App\Models\LaporanPersediaan;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;

use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithProperties;


class PersediaanExport implements FromQuery, WithHeadings, ShouldAutoSize, WithMapping,WithStyles, WithProperties
{
    /**
    * @return \Illuminate\Support\Collection
    */

    use Exportable;

    public function __construct($date)
    {
        $this->date = $date;
    }

    public function headings(): array
    {
        return [
            'KODE BARANG',
            'NAMA BARANG',
            'MASUK',
            'KELUAR',
            'SALDO',
            'HARGA MODAL',
            'TOTAL',
        ];
    }

    public function map($persediaan): array
    {
        return [
            $persediaan->kode_barang,
            $persediaan->barang->nama_barang,
            number_format($persediaan->debit),
            number_format($persediaan->kredit),
            number_format($persediaan->balance),
            number_format($persediaan->harga),
            number_format($persediaan->total),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:H1')->getFont()->setBold(true);
    }

    public function properties(): array
    {
        return [
            'creator'        => 'Berkah Baja Makmur',
            'lastModifiedBy' => 'Berkah Baja Makmur',
            'title'          => 'Laporan Persediaan '. $this->date .'',
            'description'    => 'Laporan Persediaan '. $this->date .'',
            'subject'        => 'Persediaan',
            'category'       => 'Persediaan',
            'manager'        => 'Lucky Anggara',
            'company'        => 'Berkah Baja Makmur',
        ];
    }


    public function query()
    {
        return LaporanPersediaan::query()->with('barang')->whereDate('created_at', $this->date)->whereNot('total',0);
    }
}
