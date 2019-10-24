<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerTransaction extends Model
{
    protected $fillable = [
    	'customer_id',
    	'product_id',
    	'date',
    	'product',
    	'description',
    	'quantity',
    	'rate',
    	'status'
    ];
}
