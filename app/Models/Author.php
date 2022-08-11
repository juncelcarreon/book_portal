<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'firstname',
        'middle_initial',
        'lastname',
        'suffix',
        'uid',
        'email',
        'contact_number',
        'address',
    ];

    public function pod_transcations()
    {
        return $this->hasMany(PodTransaction::class);
    }

    public function ebook_transcations()
    {
        return $this->hasMany(EbookTransaction::class);
    }

    public function getFullName()
    {
        return $this->firstname ." ". $this->lastname;
    }

    public function getFullName2()
    {
        return $this->lastname." ".$this->firstname;
    }
}
