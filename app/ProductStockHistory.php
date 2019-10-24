<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductStockHistory extends Model
{
    protected $fillable = [
    	'person_involved', 
    	'stock_status', 
    	'old_prod_name',
    	'new_prod_name', 
    	'stock_amount_status', 
    	'original_stock_amount', 
    	'update_stock_amount', 
    	'curr_stock_amount'
    ];
}
