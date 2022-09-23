<?php

namespace App\Imports;

use App\Helpers\DatabaseDataValidatorHelper;
use App\Helpers\HumanNameFormatterHelper;
use App\Helpers\NameHelper;
use App\Models\Author;
use App\Models\Book;
use App\Models\PodTransaction;
use App\Models\RejectedAuthor;
use App\Models\RejectedPodTransaction;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Str;

class PodTransactionsImport implements ToModel, WithHeadingRow, WithChunkReading, WithBatchInserts
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */

    public function model(array $row)
    {
        $date = now();
        if ($row['author'] != null) {
            $newName = $row['author'];
            if (str_contains($newName, ",")) {
                $newName = explode(", ", $row['author']);
                $newName = $newName[1] . " " . $newName[0];
            }
            $formattedName = (new HumanNameFormatterHelper)->parse($newName);
            $author = Author::where('firstname', 'LIKE', NameHelper::normalize($formattedName->FIRSTNAME) . "%")->where('lastname', 'LIKE', NameHelper::normalize($formattedName->LASTNAME) . "%")->first();
            $royalty = number_format((float)($row['mtd_quantity'] ?? $row['ptd_quantity'] * $row['list_price']) * 0.15, 2);
            if ($author) {
                $book = Book::where('title', $row['title'])->first();
                if ($book) {
                    PodTransaction::create([
                        'author_id' => $author->id,
                        'book_id' => $book->id,
                        'year' => $row['year'] ?? $date->year,
                        'month' => $row['mm'] ?? $date->month,
                        'flag' => $row['flag'] ?? 'No',
                        'status' => $row['status'] ?? '',
                        'format' => $row['format'] ?? Str::contains($row['binding_type'], Str::title('perfectbound')) == true ? 'Perfectbound' : Str::title($row['binding_type']),
                        'quantity' => $row['mtd_quantity'] ?? $row['ptd_quantity'],
                        'price' => $row['list_price'],
                        'royalty' => $royalty
                    ]);
                } else {
                    $newBook = Book::create([
                        'title' => $row['book'] ?? $row['title']
                    ]);
                    PodTransaction::create([
                        'author_id' => $author->id,
                        'book_id' => $newBook->id,
                        'year' => $row['year'] ?? $date->year,
                        'month' => $row['mm'] ?? $date->month,
                        'flag' => $row['flag'] ?? 'No',
                        'status' => $row['status'] ?? '',
                        'format' => $row['format'] ?? Str::contains($row['binding_type'], Str::title('perfectbound')) == true ? 'Perfectbound' : Str::title($row['binding_type']),
                        'quantity' => $row['mtd_quantity'] ?? $row['ptd_quantity'],
                        'price' => $row['list_price'],
                        'royalty' => $royalty
                    ]);
                }
            } else {
                RejectedPodTransaction::create([
                    'author_name' => $row['author'],
                    'book_title' => $row['title'],
                        'year' => $row['year'] ?? $date->year,
                        'month' => $row['mm'] ?? $date->month,
                        'flag' => $row['flag'] ?? 'No',
                        'status' => $row['status'] ?? '',
                        'format' => $row['format'] ?? Str::contains($row['binding_type'], Str::title('perfectbound')) == true ? 'Perfectbound' : Str::title($row['binding_type']),
                        'quantity' => $row['mtd_quantity'] ?? $row['ptd_quantity'],
                        'price' => $row['list_price'],
                        'royalty' => $royalty
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
