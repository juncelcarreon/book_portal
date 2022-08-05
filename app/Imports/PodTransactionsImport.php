<?php

namespace App\Imports;

use App\Models\Author;
use App\Models\Book;
use App\Models\PodTransaction;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\ToCollection;

class PodTransactionsImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        if($row['author'] != null){
            $splitName = explode(", ", $row['author']);
            if(count($splitName) > 1){
                $newName = $splitName[1]. " " .$splitName[0];
            }else{
                $newName = $row['author'];
            }
            $author = Author::where('name', 'LIKE',  $newName ."%")->first();
            if($author){
                $book = Book::where('title', $row['title'])->first();
                if($book){
                    return new PodTransaction([
                        'author_id' => $author['id'],
                        'book_id' => $book['id'],
                        'year' => $row['year'],
                        'month' => $row['month'],
                        'flag' => $row['flag'],
                        'status' => $row['status'],
                        'format' => $row['format'],
                        'quantity' => $row['mtd_quantity'],
                        'price' => $row['list_price'],
                        'royalty' => ($row['mtd_quantity'] * $row['list_price']) * 0.15
                    ]);
                }
            }
        }
    }

    public function headingRow(): int
    {
        return 1;
    }
}
