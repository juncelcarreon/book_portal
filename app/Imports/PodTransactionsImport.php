<?php

namespace App\Imports;

use App\Helpers\DatabaseDataValidatorHelper;
use App\Helpers\HumanNameFormatterHelper;
use App\Helpers\NameHelper;
use App\Models\Author;
use App\Models\Book;
use App\Models\PodTransaction;
use App\Models\RejectedAuthor;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Illuminate\Contracts\Queue\ShouldQueue;

class PodTransactionsImport implements ToModel, WithHeadingRow, WithChunkReading, WithBatchInserts
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        if($row['author'] != null){
            $newName = $row['author'];
            if(str_contains($newName,",")){
                $newName = explode(", ", $row['author']);
                $newName = $newName[1] ." ". $newName[0];
            }
            $formattedName = (new HumanNameFormatterHelper)->parse($newName);
            $author = Author::where('firstname', 'LIKE', NameHelper::normalize($formattedName->FIRSTNAME) ."%")->where('lastname','LIKE', NameHelper::normalize($formattedName->LASTNAME) ."%")->first();
            if($author){
                $book = Book::where('title', $row['title'])->first();
                $royalty = number_format((float)($row['mtd_quantity'] * $row['list_price']) * 0.15, 2);
                if($book){
                    PodTransaction::create([
                        'author_id' => $author->id,
                        'book_id' => $book->id,
                        'year' => $row['year'],
                        'month' => $row['mm'],
                        'flag' => $row['flag'],
                        'status' => $row['status'],
                        'format' => $row['format'],
                        'quantity' => $row['mtd_quantity'],
                        'price' => $row['list_price'],
                        'royalty' => $royalty
                    ]);
                }else{
                    $newBook = Book::create([
                        'title' => $row['book'] ?? $row['title']
                    ]);
                    PodTransaction::create([
                        'author_id' => $author->id,
                        'book_id' => $newBook->id,
                        'year' => $row['year'],
                        'month' => $row['mm'],
                        'flag' => $row['flag'],
                        'status' => $row['status'],
                        'format' => $row['format'],
                        'quantity' => $row['mtd_quantity'],
                        'price' => $row['list_price'],
                        'royalty' => $royalty
                    ]);
                }
            }else{
                RejectedAuthor::create([
                    'author' => $row['author'],
                    'type' => 'pod'
                ]);
            }
        }
    }

    public function headingRow(): int
    {
        return 1;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function batchSize(): int
    {
        return 1000;
    }
}
