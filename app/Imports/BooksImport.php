<?php

namespace App\Imports;

use App\Models\Book;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;


class BooksImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */


    public function model(array $row)
    {
        HeadingRowFormatter::default('none');
        $book = Book::where('title', $row['title'] ?? $row['producttitle'])->get();
        if(count($book) == 0 ){
            return new Book([
                'product_id' => $row['alternativeproductid#'] ?? '',
                'title' => $row['producttitle'] ?? $row['title']
            ]);
        }
    }

    public function headingRow(): int
    {
        return 1;
    }
}
