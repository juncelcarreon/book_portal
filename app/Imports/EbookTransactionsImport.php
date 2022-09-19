<?php

namespace App\Imports;

use App\Helpers\HumanNameFormatterHelper;
use App\Helpers\NameHelper;
use App\Models\Author;
use App\Models\Book;
use App\Models\EbookTransaction;
use App\Models\RejectedAuthor;
use App\Models\RejectedEbookTransaction;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EbookTransactionsImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $name = $row['productauthors'];
        $name = (new HumanNameFormatterHelper)->parse($name);

        $author = Author::where('firstname', 'LIKE', NameHelper::normalize($name->FIRSTNAME) . "%")->where('lastname', 'LIKE', NameHelper::normalize($name->LASTNAME) . "%")->first();
        $date = Carbon::parse($row['transactiondatetime']);
        if ($author) {
            $book = Book::where('title', $row['producttitle'])->first();
            if ($book) {
                return new EbookTransaction([
                    'author_id' => $author->id,
                    'book_id' => $book->id,
                    'year' => $date->year,
                    'month' => $date->month,
                    'quantity' => $row['grosssoldquantity'],
                    'price' => $row['unitprice'],
                    'proceeds' => $row['proceedsofsaleduepublisher'],
                    'royalty' => $row['proceedsofsaleduepublisher'] / 2
                ]);
            }
        } else {
            RejectedEbookTransaction::create([
                'author_name' => $row['productauthors'],
                'book_title' => $row['producttitle'],
                'year' => $date->year,
                'month' => $date->month,
                'quantity' => $row['grosssoldquantity'],
                'price' => $row['unitprice'],
                'proceeds' => $row['proceedsofsaleduepublisher'],
                'royalty' => $row['proceedsofsaleduepublisher'] / 2
            ]);
        }
    }

    public function headingRow(): int
    {
        return 1;
    }
}
