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

        // Retrieve product by product name
        $product = Product::where('prod_name', $request['customer_transaction_prod'])->first();

        // Update product stock
        CustomerTransactionController::updateProductStock($product, $data['status'], $data['quantity']);

        // Check if product stock is sufficient when return required
        if (!$product->isValidStock()){
            $product->refresh();
            return redirect()->back()->withErrors([
                                            Product::showInvalidStockError(), 
                                            $product->description()
                                        ]);
        }

        // Create new customer transaction
        $customer_transaction = new CustomerTransaction([
            'customer_id' => $customer_id,
            'product_id' => $product['id'],
            'date' => new Carbon($request['customer_transaction_date']),
            'product' => $request['customer_transaction_prod'],
            'description' => $request['customer_transaction_desc'],
            'quantity' => $request['customer_transaction_qty'],
            'rate' => $request['customer_transaction_rate'],
            'status' => $request['customer_transaction_status']
        ]);

        $product->save();
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

        // Retrieve old product from current transaction
        $old_product = Product::where('prod_name', $customer_transaction['product'])->first();

        // Retrieve new product by product name
        $new_product = Product::where('prod_name', $request['update_customer_transaction_prod'])->first();

        // Update product stock
        // Check if product has changed in this update
        if ($old_product->is($new_product)){ // Different products
            $product_details = [$new_product->description()];
            CustomerTransactionController::updateProductStock($new_product, $customer_transaction['status'], $customer_transaction['quantity'], false);
        }
        else{
            $product_details = [$old_product->description(), $new_product->description()];
            CustomerTransactionController::updateProductStock($old_product, $customer_transaction['status'], $customer_transaction['quantity'], false);
        }
        CustomerTransactionController::updateProductStock($new_product, $data['status'], $data['quantity']);

        // Check if product stock is sufficient when return required
        if (!$old_product->isValidStock() || !$new_product->isValidStock()){
            $old_product->refresh();
            $new_product->refresh();
            return redirect()->back()->withErrors(
                                            array_merge(
                                                [Product::showInvalidStockError()],
                                                $product_details
                                            ));
        }

        // Update customer transaction
        $customer_transaction->product_id = $new_product['id'];
        $customer_transaction->date = new Carbon($data['date']);
        $customer_transaction->product = $data['product'];
        $customer_transaction->description = $data['description'];
        $customer_transaction->quantity = $data['quantity'];
        $customer_transaction->rate = $data['rate'];
        $customer_transaction->status = $data['status'];

        $old_product->save();
        $new_product->save();
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

    // Helper function that determines the way to update product stock by status given
    public static function updateProductStock(Product $product, $status, $qty, $NOREVERSE=true){
        if ( ($status === 'Rent') === $NOREVERSE){
            $product->rent($qty);    
        }
        else{
            $product->return($qty);
        }
    }
}
