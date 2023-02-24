<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use App\Http\Controllers\TransactionController;


use DB;


class StockOpnameImport implements ToCollection, WithStartRow
{
    /**
    * @param Collection $collection
    */
    private $text = "";
    private $stockOpnameDate = 0;

    function __construct($stockOpnameDate) {   
        $this->stockOpnameDate = $stockOpnameDate;     
    }

    public function startRow(): int
    {
        return 2;
    }
    public function collection(Collection $collection)
    {
        $a=0;
        $mesej = "";
        $tran = new TransactionController();

        foreach ($collection as $row) 
        {
            if ($row[11] == 1){
                try{ 
                    $affected = DB::table('items')
                    ->where('id', $row[0])
                    ->update(['amount' => $row[10]]);

                    $tran->stockChangeLog(3, "Stock Opname tanggal ".$this->stockOpnameDate, $row[0], $row[10]);
                }
                catch(\Exception $e){
                    $mesej.=$row[0].", ";
                }
            }
        }
        $this->text = $mesej;
    }
    public function getImportResult(): string
    {
        return $this->text;
    }
}
