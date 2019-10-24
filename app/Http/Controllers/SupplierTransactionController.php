<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Product;
use App\SupplierTransaction;
use App\Rules\ValidSupplierTransactionStatus;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;
use Validator;

class SupplierTransactionController extends Controller
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
            'date' => $request['supplier_transaction_date'],
            'product' => $request['supplier_transaction_prod'],
            'description' => $request['supplier_transaction_desc'],
            'quantity' => $request['supplier_transaction_qty'],
            'status' => $request['supplier_transaction_status']
        ];
        $rules = [
            'date' => 'required|date',
            'product' => 'required',
            'description' => 'required',
            'quantity' => 'required|integer|gt:0',
            'status' => ['required', new ValidSupplierTransactionStatus]
        ];
        $messages = [];
        $custom_attrs = [];

        $validator = Validator::make($data, $rules, $messages, $custom_attrs);

        // Check if validator fails
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Retrieve product id by product name
        $prod_id = Product::where('prod_name', $request['supplier_transaction_prod'])->pluck('id')->toArray()[0];

        // Create new supplier transaction
        $supplier_transaction = new SupplierTransaction([
            'supplier_id' => $id,
            'product_id' => $prod_id,
            'date' => new Carbon($request['supplier_transaction_date']),
            'product' => $request['supplier_transaction_prod'],
            'description' => $request['supplier_transaction_desc'],
            'quantity' => $request['supplier_transaction_qty'],
            'status' => $request['supplier_transaction_status']
        ]);

        $supplier_transaction->save();
        return redirect()->back()->with('supplier_transaction_success_status', 'Supplier Transaction Added');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\SupplierTransaction  $supplierTransaction
     * @return \Illuminate\Http\Response
     */
    public function show(SupplierTransaction $supplierTransaction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\SupplierTransaction  $supplierTransaction
     * @return \Illuminate\Http\Response
     */
    public function edit(SupplierTransaction $supplierTransaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SupplierTransaction  $supplierTransaction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SupplierTransaction $supplierTransaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SupplierTransaction  $supplierTransaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(SupplierTransaction $supplierTransaction)
    {
        //
    }

    public function deleteSelected(Request $request){
        $ids = $request['supplier_transaction_checkbox'];
        DB::table("supplier_transactions")->whereIn('id',$ids)->delete();
        return redirect()->back()->with('supplier_transaction_success_status', 'Supplier Transaction(s) Deleted');
    }
}
