<?php

namespace App\Imports;

use App\Models\EbookTransaction;
use Maatwebsite\Excel\Concerns\ToModel;

class EbookTransactionsImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new EbookTransaction([
            //
        ]);
    }
}
