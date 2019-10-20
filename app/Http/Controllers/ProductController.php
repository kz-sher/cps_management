<?php

namespace App\Http\Controllers;

use App\Product;
use App\Supplier;
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
        $this->validate($request, [
            'prod_name' => ['required', 'unique:products'],
        ]);

        $product = new Product([
            "prod_name" => $request['prod_name'],
            "curr_stock" => 0,
        ]);

        $product->save();
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
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'prod_name' => ['required', 'unique:products,prod_name,'.$id],
            'curr_stock' => ['required', 'min:1'],
        ]);

        $product = Product::find($id);
        
        $product_stock_history = new ProductStockHistory([
            "person_involved" => "Admin",
            "stock_status" => "Manual",
            "stock_amount_status" => ($product['curr_stock'] > $request['curr_stock'])? "down" : "up",
            "stock_amount" => $request['curr_stock'],
        ]);

        $product->prod_name = $request->get('prod_name');
        $product->curr_stock = $request->get('curr_stock');

        $product->save();
        $product_stock_history->save();
        return redirect()->route('product.index')->with('product_success_status', 'Product Updated');
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
        return redirect()->route('product.index')->with('product_success_status', 'Product(s) Deleted');
    }

    public function importProduct(Request $request, $id)
    {
        dd($request->all());
        $this->validate($request, [
            'supplier' => ['required'],
            'import_stock' => ['required', 'min:1'],
        ]);

        
        $product = Product::find($id);

        $product_stock_history = new ProductStockHistory([
            "person_involved" => $request['supplier'],
            "stock_status" => "Import",
            "stock_amount_status" => "up",
            "stock_amount" => $request['import_stock'],
        ]);

        $product->curr_stock += $request->get('import_stock');

        $product->save();
        $product_stock_history->save();
        return redirect()->route('product.index')->with('product_success_status', 'Product Imported');
    }
}
