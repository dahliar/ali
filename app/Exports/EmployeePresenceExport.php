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


class EmployeePresenceExport implements FromQuery, WithHeadings, WithStyles, WithColumnFormatting, ShouldAutoSize
{
    use Exportable;
    protected $presenceDate;
    protected $rowCount=1;

    function __construct($presenceDate) {        
        $this->presenceDate = $presenceDate;
    }

    public function query()
    {
        $x = $this->presenceDate;

        $query = DB::table('employees as e')
        ->select(
            'e.id as id', 
            'u.name as name', 
            'e.nip as nip',
            'e.nik as nik',
            'os.name as orgStructure',
            'sp.name as jabatan',
            'wp.name as bagian',
            DB::raw("STR_TO_DATE('".$x."','%Y-%m-%d') as presenceDate"),
            DB::raw("'08:00' as jamMasuk"),
            DB::raw("'17:00' as jamKeluar"),
            DB::raw("'1' as statusMasuk"),
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
        ->where('e.isActive', '1')
        ->where('mapping.isActive', '1')
        ->where('e.employmentStatus', '!=', '3')
        ->wherenull('p.start')
        ->orderBy('sp.name')
        ->orderBy('wp.name')
        ->orderBy('u.name');

        $this->rowCount = $query->count() + 1;

        return $query;
    }
    public function styles(Worksheet $sheet)
    {
        $sheet->getProtection()->setSheet(true);
        $sheet->getStyle('H2:J'.$this->rowCount)->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
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

        $sheet->getStyle('I2:K'.$this->rowCount)->applyFromArray($styleArrayEditable);
        $sheet->getStyle('A1:H'.$this->rowCount)->applyFromArray($styleArrayNonEditable);

        //$sheet->setCellValue('M2', 'Jenis Presensi');
        //$sheet->setCellValue('M3', '0 : Tidak Masuk');
        //$sheet->setCellValue('M4', '1 : Masuk');
        //$sheet->getStyle('M2:M4')->applyFromArray($styleArrayNonEditable);

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
            'NIP',
            'NIK',
            'Posisi',
            'Jabatan',
            'Bagian',
            'Tanggal',
            'Jam Masuk',
            'Jam Keluar',
            'Presensi',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'H' => 'yyyy-mm-dd',
            'I' => NumberFormat::FORMAT_TEXT,
            'J' => NumberFormat::FORMAT_TEXT,
        ];
    }
}