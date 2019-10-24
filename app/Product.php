<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['prod_name', 'curr_stock'];

	// Function that helps mapping different field names from column names
    public function setFieldAttributes($field_vals) {
        foreach($field_vals as $field => $val){
            $this->attributes[$field] = $val;
        }
    }
}
