<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SupplierTransaction extends Model
{
    protected $fillable = [
    	'supplier_id',
    	'product_id',
    	'date',
    	'product',
    	'description',
    	'quantity',
    	'status'
    ];
}
