<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductStockHistory extends Model
{
    protected $fillable = ['person_involved', 'stock_status', 'amount'];
}
