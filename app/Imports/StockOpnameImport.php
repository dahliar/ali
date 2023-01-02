<?php

namespace App\Imports;
use App\Models\Presence;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;


class EmployeePresenceImport implements ToCollection, WithStartRow
{
    /**
    * @param Collection $collection
    */
    private $text = "";
    private $isLembur = 0;
    function __construct($isLembur) {   
        $this->presence = new Presence();
        $this->isLembur = $isLembur;     
    }

    public function startRow(): int
    {
        return 2;
    }
    public function collection(Collection $collection)
    {
        $this->text = $this->presence->presenceCalculator($collection, $this->isLembur);

    }
    public function getImportResult(): string
    {
        return $this->text;
    }
}
