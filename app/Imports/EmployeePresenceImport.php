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
    function __construct() {   
        $this->presence = new Presence();     
    }

    public function startRow(): int
    {
        return 2;
    }
    public function collection(Collection $collection)
    {
        $this->presence->presenceCalculator($collection);
    }
}