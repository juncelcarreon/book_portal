<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RejectedEbookTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'author_name',
        'book_title',
      
        'year',
        'month',
        'quantity',
        'price',
        'proceeds',
        'royalty'
    ];
}
