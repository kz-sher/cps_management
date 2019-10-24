<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerTransaction extends Model
{
    protected $fillable = [
    	'customer_id',
    	'product_id',
    	'product',
    	'date',
    	'description',
    	'quantity',
    	'rate',
    	'status'
    ];
}
