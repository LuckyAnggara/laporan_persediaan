<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithProperties;


class PelangganExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles, WithProperties
{
    /**
    * @return \Illuminate\Support\Collection
    */

    use Exportable;

    protected $pelanggan;
    protected $year;

    public function __construct(array $pelanggan, $year)
    {
        $this->year = $year;
        $this->pelanggan = $pelanggan;
    }

    public function headings(): array
    {
        return [
            'NAMA PELANGGAN',
            'ID PELANGGAN',
            'ALAMAT',
            'NOMOR TELEPON',
            'TOTAL',
        ];
    }

    // public function map($pelanggan): array
    // {
    //     return [
    //         $pelanggan->nama_pelanggan,
    //         $pelanggan->nomor_telepon,
    //         $pelanggan->alamat,
    //         number_format($pelanggan->total),
    //     ];
    // }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:D1')->getFont()->setBold(true);
    }

    public function properties(): array
    {
        return [
            'creator'        => 'Berkah Baja Makmur',
            'lastModifiedBy' => 'Berkah Baja Makmur',
            'title'          => 'Laporan Pelanggan '. $this->year .'',
            'description'    => 'Laporan Pelanggan '. $this->year .'',
            'subject'        => 'Pelanggan',
            'category'       => 'Pelanggan',
            'manager'        => 'Lucky Anggara',
            'company'        => 'Berkah Baja Makmur',
        ];
    }


    public function array(): array
    {
        return $this->pelanggan;
    }
}
