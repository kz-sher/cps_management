<?php

namespace App\Http\Controllers;

use App\Product;
use App\Supplier;
use App\ProductStockHistory;
use Illuminate\Http\Request;
use DB;
use Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::all()->toArray();
        $suppliers = Supplier::all()->toArray();
        $product_stock_histories = ProductStockHistory::all()->toArray();
        return view('product.index', compact('products', 'suppliers', 'product_stock_histories'));
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
    public function store(Request $request)
    {
        // Manual Validator
        $data = ['prod_name' => $request['new_prod_name']];
        $rules = ['prod_name' => 'required|unique:products'];
        $messages = [];
        $custom_attrs = ['prod_name' => 'product name'];

        $validator = Validator::make($data, $rules, $messages, $custom_attrs);

        // Check if validator fails
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Else create new product
        $product = new Product([
            "prod_name" => $request['new_prod_name'],
            "curr_stock" => 0,
        ]);

        $product->save();
        return redirect()->back()->with('product_success_status', 'Product Added');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Manual Validator
        $data = [
            'prod_name' => $request['update_prod_name'],
            'curr_stock' => $request['update_stock'],
        ];
        $rules = [
            'prod_name' => ['required', 'unique:products,prod_name,'.$id],
            'curr_stock' => ['integer', 'required', 'gt:0']
        ];
        $messages = [];
        $custom_attrs = [
            'prod_name' => 'product name',
            'curr_stock' => 'stock given'
        ];

        $validator = Validator::make($data, $rules, $messages, $custom_attrs);

        // Check if validator fails
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $product = Product::find($id);      

        $product_stock_history = new ProductStockHistory([
            "person_involved" => "Admin",
            "stock_status" => "Manual",
            "old_prod_name" => $product['prod_name'],
            "new_prod_name" => ($request['update_prod_name'] === $product['prod_name'])? '-': $request['update_prod_name'],
            "stock_amount_status" => ($product['curr_stock'] > $request['update_stock'])? "down" : "up",
            "original_stock_amount" => $product['curr_stock'],
            "update_stock_amount" => $request['update_stock'] - $product['curr_stock'],
            "curr_stock_amount" => $request['update_stock'],
        ]);

        $product->prod_name = $request->get('update_prod_name');
        $product->curr_stock = $request->get('update_stock');

        $product->save();
        $product_stock_history->save();
        return redirect()->back()->with('product_success_status', 'Product Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // $product = Product::all()->find($id);
        // $product->delete();
        // return redirect()->route('main.index')->with('product_danger_status', 'Product Deleted');
    }

    public function deleteSelected(Request $request){
        $ids = $request['product_checkbox'];
        DB::table("products")->whereIn('id',$ids)->delete();
        return redirect()->back()->with('product_success_status', 'Product(s) Deleted');
    }

    public function importProduct(Request $request, $id)
    {
        // Manual Validator
        $data = [
            'prod_name' => $request['supplier'], // Stupid way to check as it belongs to stock histories
            'curr_stock' => $request['import_stock'],
        ];
        $rules = [
            'prod_name' => ['required'],
            'curr_stock' => ['integer', 'required', 'gt:0']
        ];
        $messages = [];
        $custom_attrs = [
            'prod_name' => 'supplier',
            'curr_stock' => 'stock given'
        ];

        $validator = Validator::make($data, $rules, $messages, $custom_attrs);

        // Check if validator fails
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $product = Product::find($id);
        $supplier = Supplier::find($request['supplier']);

        $product_stock_history = new ProductStockHistory([
            "person_involved" => $supplier['name'],
            "stock_status" => "Import",
            "old_prod_name" => $product['prod_name'],
            "new_prod_name" => '-',
            "stock_amount_status" => "up",
            "stock_amount" => $request['import_stock'],
            "original_stock_amount" => $product['curr_stock'],
            "update_stock_amount" => $request['import_stock'],
            "curr_stock_amount" => $product['curr_stock'] + $request['import_stock'],
        ]);

        $product->curr_stock += $request->get('import_stock');

        $product->save();
        $product_stock_history->save();
        return redirect()->back()->with('product_success_status', 'Product Imported');
    }
}
