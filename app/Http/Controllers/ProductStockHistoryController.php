<?php

namespace App\Http\Controllers;

use App\ProductStockHistory;
use Illuminate\Http\Request;

class ProductStockHistoryController extends Controller
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ProductStockHistory  $productStockHistory
     * @return \Illuminate\Http\Response
     */
    public function show(ProductStockHistory $productStockHistory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ProductStockHistory  $productStockHistory
     * @return \Illuminate\Http\Response
     */
    public function edit(ProductStockHistory $productStockHistory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ProductStockHistory  $productStockHistory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProductStockHistory $productStockHistory)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ProductStockHistory  $productStockHistory
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProductStockHistory $productStockHistory)
    {
        //
    }

    public function clearHistory(){
        ProductStockHistory::whereNotNull('id')->delete();    
        return redirect()->route('product.index')->with('stock_history_success_status', 'Histories Cleared');    
    }
}
