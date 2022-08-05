<?php

namespace App\Imports;

use App\Models\Author;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AuthorsImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $author = Author::where('name', $row['name'])->get();
        if(count($author) == 0){
            return new Author([
                'name' => $row['name'],
                'uid' => $row['id'],
                'email' => $row['email_address'],
                'contact_number' => $row['home_phone'],
                'address' => $row['address']
            ]);
        }
    }

    public function headingRow(): int
    {
        return 1;
    }
}
