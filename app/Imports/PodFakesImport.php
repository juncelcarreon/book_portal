<?php

namespace App\Imports;

use App\Models\PodFake;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PodFakesImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading, ShouldQueue
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return  new PodFake([
           'author' => $row['author'],
            'book' => $row['title'],
            'year' => $row['year'],
            'month' => $row['mm'],
            'flag' => $row['flag'],
            'status' => $row['status'],
            'format' => $row['format'],
            'quantity' => $row['mtd_quantity'],
            'price' => $row['list_price'],
        ]);

    }

    public function headingRow(): int
    {
        return 1;
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
