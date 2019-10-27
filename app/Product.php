<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['prod_name', 'curr_stock'];
    public static $invalid_stock_error = 'You have no enough product stock for chosen product';
    public static $invalid_delete_error = 'Invalid delete as no enough product stock for updating chosen product';

	public function isValidStock(){
		return $this->attributes['curr_stock'] >= 0;
	}

	public function import($amount){
		$this->attributes['curr_stock'] += $amount;
	}

	public function payback($amount){
		$this->attributes['curr_stock'] -= $amount;
	}

	public function rent($amount){
		$this->payback($amount);
	}

	public function return($amount){
		$this->import($amount);
	}

	public static function showInvalidStockError(){
		return Product::$invalid_stock_error;
	}

	public static function showInvalidDeleteError(){
		return Product::$invalid_delete_error;
	}

	public function description(){ 
		return 'Current stock of '.$this->attributes['prod_name'].' is '.$this->attributes['curr_stock'];
	}
}
