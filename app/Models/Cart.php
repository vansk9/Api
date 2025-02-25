<?php

namespace App\Models;

// use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $collection = 'api'; // Nama koleksi di MongoDB

    protected $fillable = [
        'user_id',
        'product_id',
        'product_name',
        'quantity',
        'price',
    ];
}
