<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Product;
use App\CustomerTransaction;
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
    public function store(Request $request, $id)
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
            'customer_id' => $id,
            'product_id' => $prod_id,
            'date' => new Carbon($request['customer_transaction_date']),
            'product' => $request['customer_transaction_prod'],
            'description' => $request['customer_transaction_desc'],
            'quantity' => $request['customer_transaction_qty'],
            'rate' => $request['customer_transaction_rate'],
            'status' => $request['customer_transaction_status']
        ]);

        $customer_transaction->save();
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
        $customer_transaction = CustomerTransaction::find($transaction_id);      

        // Retrieve product id by product name
        $prod_id = Product::where('prod_name', $request['update_customer_transaction_prod'])->pluck('id')->toArray()[0];

        // Update customer transaction
        $customer_transaction->product_id = $prod_id;
        $customer_transaction->date = new Carbon($request['update_customer_transaction_date']);
        $customer_transaction->product = $request['update_customer_transaction_prod'];
        $customer_transaction->description = $request['update_customer_transaction_desc'];
        $customer_transaction->quantity = $request['update_customer_transaction_qty'];
        $customer_transaction->rate = $request['update_customer_transaction_rate'];
        $customer_transaction->status = $request['update_customer_transaction_status'];

        $customer_transaction->save();
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
}
