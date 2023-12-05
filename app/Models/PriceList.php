<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceList extends Model
{
    use HasFactory;

    protected $table = 'price_list';

    protected $fillable = ['number', 'name', 'bottle_size', 'price', 'price_gbp'];

    public $timestamps = true;
}
