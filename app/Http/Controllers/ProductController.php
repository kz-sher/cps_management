<?php

namespace App\Http\Controllers;

use App\Product;
use App\ProductStockHistory;
use Illuminate\Http\Request;
use DB;

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
        $product_stock_histories = ProductStockHistory::all()->toArray();
        return view('product.index', compact('products', 'product_stock_histories'));
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
        $this->validate($request, [
            'prod_name' => ['required', 'unique:products'],
            'curr_stock' => ['required'],
        ]);

        $product = new Product([
            "prod_name" => $request['prod_name'],
            "curr_stock" => $request['curr_stock']
        ]);

        $product_stock_history = new ProductStockHistory([
            "person_involved" => "self",
            "stock_status" => "created",
            "amount" => $request['curr_stock']
        ]);

        $product->save();
        $product_stock_history->save();
        return redirect()->route('product.index')->with('product_success_status', 'Product Added');
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
    public function update(Request $request, Product $product)
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
        $ids = $request->ids;
        DB::table("products")->whereIn('id',explode(",",$ids))->delete();
        return response()->json(['success'=>"Products(s) Deleted successfully."]);
    }
}
