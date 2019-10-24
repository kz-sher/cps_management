<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Product;
use App\CustomerTransaction;
use App\CustomerRentReturnDetails;
use App\Rules\ValidCustomerTransactionStatus;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;
use Validator;

class CustomerTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $customer_id)
    {
        // Manual Validator
        $data = [
            'date' => $request['customer_transaction_date'],
            'product' => $request['customer_transaction_prod'],
            'description' => $request['customer_transaction_desc'],
            'quantity' => $request['customer_transaction_qty'],
            'rate' => $request['customer_transaction_rate'],
            'status' => $request['customer_transaction_status']
        ];
        $rules = [
            'date' => 'required|date',
            'product' => 'required',
            'description' => 'required',
            'quantity' => 'required|integer|gt:0',
            'rate' => 'required|numeric|gt:0',
            'status' => ['required', new ValidCustomerTransactionStatus]
        ];
        $messages = [];
        $custom_attrs = [];

        $validator = Validator::make($data, $rules, $messages, $custom_attrs);

        // Check if validator fails
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Retrieve product id by product name
        $prod_id = Product::where('prod_name', $request['customer_transaction_prod'])->pluck('id')->toArray()[0];

        // Create new customer transaction
        $customer_transaction = new CustomerTransaction([
            'customer_id' => $customer_id,
            'product_id' => $prod_id,
            'date' => new Carbon($request['customer_transaction_date']),
            'product' => $request['customer_transaction_prod'],
            'description' => $request['customer_transaction_desc'],
            'quantity' => $request['customer_transaction_qty'],
            'rate' => $request['customer_transaction_rate'],
            'status' => $request['customer_transaction_status']
        ]);

        // Check if customer rent/return detail exists
        $cust_rr_detail = CustomerRentReturnDetails::where('customer_id', $customer_id)->where('product_id', $prod_id)->first();
        if ($cust_rr_detail === null){ // Record not found

            $cust_rr_detail = CustomerTransactionController::CRRDcreate([
                'status' => $request['customer_transaction_status'],
                'customer_id' => $customer_id,
                'product_id' => $prod_id,
                'product' => $request['customer_transaction_prod'],
                'amount' => $request['customer_transaction_qty']
            ]);

            if (!$cust_rr_detail){
                return redirect()->back()->withErrors(['Customer cannot return products that he/she has not rented yet']);
            }
        }
        else{ // Update existing instance

            if (!$cust_rr_detail->updateDetails($customer_transaction)){
                return redirect()->back()->withErrors(['Customer cannot return amount more than the one rented for chosen product']);
            }
        }

        $customer_transaction->save();
        $cust_rr_detail->save();
        return redirect()->back()->with('customer_transaction_success_status', 'Customer Transaction Added');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CustomerTransaction  $customerTransaction
     * @return \Illuminate\Http\Response
     */
    public function show(CustomerTransaction $customerTransaction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CustomerTransaction  $customerTransaction
     * @return \Illuminate\Http\Response
     */
    public function edit(CustomerTransaction $customerTransaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CustomerTransaction  $customerTransaction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $customer_id, $transaction_id)
    {
        // Manual Validator
        $data = [
            'date' => $request['update_customer_transaction_date'],
            'product' => $request['update_customer_transaction_prod'],
            'description' => $request['update_customer_transaction_desc'],
            'quantity' => $request['update_customer_transaction_qty'],
            'rate' => $request['update_customer_transaction_rate'],
            'status' => $request['update_customer_transaction_status']
        ];
        $rules = [
            'date' => 'required|date',
            'product' => 'required',
            'description' => 'required',
            'quantity' => 'required|integer|gt:0',
            'rate' => 'required|numeric|gt:0',
            'status' => ['required', new ValidCustomerTransactionStatus]
        ];
        $messages = [];
        $custom_attrs = [];

        $validator = Validator::make($data, $rules, $messages, $custom_attrs);

        // Check if validator fails
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Retrieve customer transaction
        $curr_customer_transaction = CustomerTransaction::find($transaction_id);
        $new_customer_transaction = CustomerTransaction::find($transaction_id);

        // Retrieve product id by product name
        $prod_id = Product::where('prod_name', $request['update_customer_transaction_prod'])->pluck('id')->toArray()[0];

        // Update customer transaction
        $new_customer_transaction->product_id = $prod_id;
        $new_customer_transaction->date = new Carbon($request['update_customer_transaction_date']);
        $new_customer_transaction->product = $request['update_customer_transaction_prod'];
        $new_customer_transaction->description = $request['update_customer_transaction_desc'];
        $new_customer_transaction->quantity = $request['update_customer_transaction_qty'];
        $new_customer_transaction->rate = $request['update_customer_transaction_rate'];
        $new_customer_transaction->status = $request['update_customer_transaction_status'];

        // Update customer rent/return details
        // Check if the product has changed for updating customer rent/return details
        // Then rollback
        $curr_cust_rr_detail = CustomerRentReturnDetails::where('customer_id', $customer_id)->where('product_id', $curr_customer_transaction->product_id)->first();
        $new_cust_rr_detail = CustomerRentReturnDetails::where('customer_id', $customer_id)->where('product_id', $new_customer_transaction->product_id)->first();
        if($curr_customer_transaction->product === $new_customer_transaction->product){
            $rollback_status = $curr_cust_rr_detail->rollback($curr_customer_transaction);
            $new_cust_rr_detail = $curr_cust_rr_detail;
        }
        else{
            $rollback_status = $curr_cust_rr_detail->rollback($curr_customer_transaction, true); 

            if ($new_cust_rr_detail === null){ // Record not found
                $new_cust_rr_detail = CustomerTransactionController::CRRDcreate([
                    'status' => $new_customer_transaction['status'],
                    'customer_id' => $new_customer_transaction['customer_id'],
                    'product_id' => $new_customer_transaction['product_id'],
                    'product' => $new_customer_transaction['product'],
                    'amount' => 0
                ]);

                if (!$new_cust_rr_detail){
                    return redirect()->back()->withErrors(['Customer cannot return products that he/she has not rented yet']);
                }
            }
        }
        // Update existing instance
        if (!$rollback_status || !$new_cust_rr_detail->updateDetails($new_customer_transaction)){
            return redirect()->back()->withErrors(['Customer cannot return amount more than the one rented for chosen product']);
        }

        $new_customer_transaction->save();
        $curr_cust_rr_detail->save();
        $new_cust_rr_detail->save();
        return redirect()->back()->with('customer_transaction_success_status', 'Customer Transaction Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CustomerTransaction  $customerTransaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(CustomerTransaction $customerTransaction)
    {
        //
    }

    public function deleteSelected(Request $request){
        $ids = $request['customer_transaction_checkbox'];
        DB::table("customer_transactions")->whereIn('id',$ids)->delete();
        return redirect()->back()->with('customer_transaction_success_status', 'Customer Transaction(s) Deleted');
    }

    public static function CRRDcreate($input_details){

        // Error occurs if customer return found for first detail init
        if ($input_details['status'] === "Return"){
            return false;
        }
        else{ // Create new customer rent/return details
            $cust_rr_detail = new CustomerRentReturnDetails([
                'customer_id' => $input_details['customer_id'],
                'product_id' => $input_details['product_id'],
                'product' => $input_details['product'],
                'amount' => $input_details['amount']
            ]);
            return $cust_rr_detail;
        }
    }
}
