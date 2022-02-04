<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithMapping;
use DB;


class EmployeePresenceExport implements FromQuery, WithHeadings, WithStyles, WithColumnFormatting
{
    use Exportable;
    protected $presenceDate;

    function __construct($presenceDate) {        
        $this->presenceDate = $presenceDate;
        
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getProtection()->setSheet(true);
        $sheet->getStyle('G2:H400')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);


        $styleArray = [
            'font' => [
                'bold' => true,
            ],
            'borders' => [
                'outline' => [
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


        $sheet->getStyle('A2:F400')->applyFromArray($styleArray);

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
            'Nama',
            'NIK',
            'Posisi',
            'Jabatan',
            'Bagian',
            'Tanggal',
            'Jam Masuk',
            'Jam Keluar',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'G' => 'yyyy-mm-dd',
        ];
    }

    public function query()
    {
        $x = $this->presenceDate;

        $query = DB::table('employees as e')
        ->select(
            'e.id as id', 
            'u.name as name', 
            'e.nik as nik',
            'os.name as orgStructure',
            'sp.name as jabatan',
            'wp.name as bagian',
            DB::raw("STR_TO_DATE(".$x.",'%Y-%m-%d') as presenceDate"),
        )
        ->leftJoin('presences as p', function($join) use ($x){
            $join->on('e.id', '=', 'p.employeeId')
            ->where(DB::raw("(STR_TO_DATE(p.start,'%Y-%m-%d'))"), '=', $x);

        })
        ->join('users as u', 'u.id', '=', 'e.userid')
        ->join('employeeorgstructuremapping as mapping', 'mapping.idemp', '=', 'e.id')
        ->join('organization_structures as os', 'mapping.idorgstructure', '=', 'os.id')
        ->join('structural_positions as sp', 'os.idstructuralpos', '=', 'sp.id')
        ->join('work_positions as wp', 'os.idworkpos', '=', 'wp.id')
        ->where('e.employmentStatus', '1')
        ->where('mapping.isActive', '1')
        ->orderBy('e.id');

        return $query;
    }
}
