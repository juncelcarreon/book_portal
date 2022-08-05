<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'uid',
        'email',
        'contact_number',
        'address',
    ];

    public function pod_transcations()
    {
        return $this->hasMany(PodTransaction::class);
    }


}
