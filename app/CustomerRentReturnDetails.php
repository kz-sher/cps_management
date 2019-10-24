<?php

namespace App;

use App\CustomerTransaction;
use Illuminate\Database\Eloquent\Model;

class CustomerRentReturnDetails extends Model
{
    protected $fillable = ['customer_id', 'product_id', 'product', 'amount'];

    private function _rent($amount){
		$this->attributes['amount'] += $amount;
    }

    private function _return($amount){
		$this->attributes['amount'] -= $amount;
    }

    private function _isValidReturn($amount){
    	return $this->attributes['amount'] - $amount >=0;
    }

    private function _isValidUpdate(){
    	return $this->attributes['amount'] >= 0;
    }

    private function _updateDetails($status, $amount, $REVERSE=false, $GUARD=false){
    	if (($status === "Rent") && !$REVERSE){
    		$this->_rent($amount);
        }
		else{
            if ($GUARD){
                if (!$this->_isValidReturn($amount)){
                    return false;
                }
            }
            $this->_return($amount);
		}
        return true;
    }

    public function updateDetails(CustomerTransaction $customer_transaction){
        $status = $customer_transaction['status'];
        $amount = $customer_transaction['quantity'];
        return $this->_updateDetails($status, $amount, false, true);
    }

    public function rollback(CustomerTransaction $customer_transaction, $STRICT=false){
        $status = $customer_transaction['status'];
        $amount = $customer_transaction['quantity'];
        return $this->_updateDetails($status, $amount, true, $STRICT);
    }

}
