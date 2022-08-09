<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PodFake extends Model
{
    use HasFactory;

    public $table = 'pod_fakes';

    protected $fillable = [
        'author',
        'book',
        'year',
        'month',
        'flag',
        'status',
        'format',
        'quantity',
        'price'
    ];
}
