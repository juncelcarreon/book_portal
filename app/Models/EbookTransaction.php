<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EbookTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'author_id',
        'book_id',
        'year',
        'month',
        'quantity',
        'price',
        'royalty'
    ];

    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
