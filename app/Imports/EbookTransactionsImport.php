<?php

namespace App\Imports;

use App\Helpers\HumanNameFormatterHelper;
use App\Helpers\NameHelper;
use App\Models\Author;
use App\Models\Book;
use App\Models\EbookTransaction;
use App\Models\RejectedAuthor;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EbookTransactionsImport implements ToModel,WithHeadingRow
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

        $author = Author::where('firstname', 'LIKE', NameHelper::normalize($name->FIRSTNAME) ."%")->where('lastname','LIKE', NameHelper::normalize($name->LASTNAME) ."%")->first();
        if($author){
            $book = Book::where('title', $row['producttitle'])->first();
            if($book){
                $royalty = $row['proceedsofsaleduepublisher'] / 2;
                $date = Carbon::parse($row['transactiondatetime']);
                return new EbookTransaction([
                    'author_id' => $author->id,
                    'book_id' => $book->id,
                    'year' => $date->year,
                    'month' => $date->month,
                    'quantity' => $row['grosssoldquantity'],
                    'price' => $row['unitprice'],
                    'royalty' => $royalty,
                ]);
            }
        }else{
            RejectedAuthor::create([
                'author' => $row['productauthors'],
                'type' => 'ebook'
            ]);
        }
    }

    public function headingRow(): int
    {
        return 1;
    }
}
