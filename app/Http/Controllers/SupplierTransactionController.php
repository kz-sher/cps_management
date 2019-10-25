<?php

namespace App\Http\Controllers;

use App\Supplier;
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
    public function store(Request $request, $supplier_id)
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

        // Retrieve product by product name
        $product = Product::where('prod_name', $request['supplier_transaction_prod'])->first();

        // Update product stock
        SupplierTransactionController::updateProductStock($product, $data['status'], $data['quantity']);

        // Check if product stock is sufficient when return required
        if (!$product->isValidStock()){
            $product->refresh();
            return redirect()->back()->withErrors([
                                            Product::showInvalidStockError(), 
                                            $product->description()
                                        ]);
        }

        // Create new supplier transaction
        $supplier_transaction = new SupplierTransaction([
            'supplier_id' => $supplier_id,
            'product_id' => $product['id'],
            'date' => new Carbon($request['supplier_transaction_date']),
            'product' => $request['supplier_transaction_prod'],
            'description' => $request['supplier_transaction_desc'],
            'quantity' => $request['supplier_transaction_qty'],
            'status' => $request['supplier_transaction_status']
        ]);

        $product->save();
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
    public function update(Request $request, $supplier_id, $transaction_id)
    {
        // Manual Validator
        $data = [
            'date' => $request['update_supplier_transaction_date'],
            'product' => $request['update_supplier_transaction_prod'],
            'description' => $request['update_supplier_transaction_desc'],
            'quantity' => $request['update_supplier_transaction_qty'],
            'status' => $request['update_supplier_transaction_status']
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

        // Retrieve supplier transaction
        $supplier_transaction = SupplierTransaction::find($transaction_id);

        // Retrieve old product from current transaction
        $old_product = Product::where('prod_name', $supplier_transaction['product'])->first();

        // Retrieve new product by product name
        $new_product = Product::where('prod_name', $request['update_supplier_transaction_prod'])->first();

        // Update product stock
        // Check if product has changed in this update
        if ($old_product->is($new_product)){ // Different products
            $product_details = [$new_product->description()];
            SupplierTransactionController::updateProductStock($new_product, $supplier_transaction['status'], $supplier_transaction['quantity'], false);
        }
        else{
            $product_details = [$old_product->description(), $new_product->description()];
            SupplierTransactionController::updateProductStock($old_product, $supplier_transaction['status'], $supplier_transaction['quantity'], false);
        }
        SupplierTransactionController::updateProductStock($new_product, $data['status'], $data['quantity']);

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

        // Update supplier transaction
        $supplier_transaction->product_id = $new_product['id'];
        $supplier_transaction->date = new Carbon($data['date']);
        $supplier_transaction->product = $data['product'];
        $supplier_transaction->description = $data['description'];
        $supplier_transaction->quantity = $data['quantity'];
        $supplier_transaction->status = $data['status'];

        $old_product->save();
        $new_product->save();
        $supplier_transaction->save();
        return redirect()->back()->with('supplier_transaction_success_status', 'Supplier Transaction Updated');
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

    // Helper function that determines the way to update product stock by status given
    public static function updateProductStock(Product $product, $status, $qty, $NOREVERSE=true){
        if ( ($status === 'Return') === $NOREVERSE){
            $product->payback($qty);    
        }
        else{
            $product->import($qty);
        }
    }
}
