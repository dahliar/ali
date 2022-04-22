<?php

namespace App\Imports;
use App\Models\Honorarium;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;


class EmployeeHonorariumImport implements ToCollection, WithStartRow
{
    /**
    * @param Collection $collection
    */
    private $text = "";
    function __construct() {   
        $this->honorarium = new Honorarium();
    }

    public function startRow(): int
    {
        return 2;
    }
    public function collection(Collection $collection)
    {
        $this->text = $this->honorarium->honorariumStore($collection);

    }
    public function getImportResult(): string
    {
        return $this->text;
    }
}
