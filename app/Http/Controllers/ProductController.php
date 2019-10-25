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
        return view('product.index', compact('products'));
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
        //
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
}
