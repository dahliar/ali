<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithMapping;
use DB;


class StockOpnameExport implements FromQuery, WithHeadings, WithStyles, WithColumnFormatting, ShouldAutoSize
{
    use Exportable;
    protected $rowCount=1;

    function __construct() {        
    }

    public function query()
    {
        $query = DB::table('items as i')
        ->select(
            'i.id as id', 
            'sp.nameBahasa as speciesName',
            'sh.name as shape',
            's.name as size',
            'g.name as grade',
            'f.name as freezing',
            'i.name as itemname',
            'i.amount as jumlahPacked',
            DB::raw('concat(weightbase, "Kg/",p.shortname) as weightbase'),
            DB::RAW('(i.amount * i.weightbase) as amount'),
            DB::raw("'0' as jumlahBaru")
        )
        ->join('sizes as s', 'i.sizeId', '=', 's.id')
        ->join('shapes as sh', 'i.shapeId', '=', 'sh.id')
        ->join('species as sp', 's.speciesId', '=', 'sp.id')
        ->join('grades as g', 'i.gradeId', '=', 'g.id')
        ->join('packings as p', 'i.packingId', '=', 'p.id')
        ->join('freezings as f', 'i.freezingId', '=', 'f.id')
        ->where('i.isActive','=', 1)
        ->orderBy('sp.nameBahasa', 'asc')
        ->orderBy('g.name', 'asc')
        ->orderBy('s.name', 'asc')

        $this->rowCount = $query->count() + 1;

        return $query;
    }
    public function styles(Worksheet $sheet)
    {
        $sheet->getProtection()->setSheet(true);
        $sheet->getStyle('K2:K'.$this->rowCount)->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
        $styleArrayEditable = [
            'font' => [
                'bold' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '00000000'],
                ],
            ],
        ];
        $styleArrayNonEditable = [
            'font' => [
                'bold' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '00000000'],
                ],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => [
                    'argb' => 'FFFFFF00',
                ],
            ],
        ];

        $sheet->getStyle('K2:M'.$this->rowCount)->applyFromArray($styleArrayEditable);
        $sheet->getStyle('A1:J'.$this->rowCount)->applyFromArray($styleArrayNonEditable);

        return [
            1    => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'rotation' => 90,
                    'color' => ['argb' => 'FFA0A0A0'],
                ],
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Spesies',
            'Olahan',
            'Size',
            'Grade',
            'Pembekuan',
            'Barang',
            'Existing Jumlah Packing MC/Karung ',
            'Weightbase',
            'Existing Jumlah Barang Kg',
            'Baru Jumlah Update Packing MC/Karung',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_TEXT,
            'E' => NumberFormat::FORMAT_TEXT
        ];
    }
}
