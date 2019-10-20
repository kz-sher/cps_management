<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Product;
use Illuminate\Http\Request;
use DB;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customers = Customer::all()->toArray();
        return view('customer.index', compact('customers'));
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
            'full_name' => ['required', 'unique:customers'],
        ]);

        $customer = new Customer([
            "full_name" => $request['full_name']
        ]);

        $customer->save();
        return redirect()->route('customer.index')->with('customer_success_status', 'Customer Added');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(Customer $customer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Customer $customer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // dd($id);
        // $ids = $request->ids;
        // DB::table("products")->whereIn('id',explode(",",$ids))->delete();
        // return response()->json(['success'=>"Products Deleted successfully."]);
        // dd($request); 
        // $customer = Customer::all()->find($id);
        // $customer->delete();
        // return redirect()->route('main.index')->with('customer_danger_status', 'Customer Deleted');
    }

    public function deleteSelected(Request $request){
        $ids = $request->ids;
        DB::table("customers")->whereIn('id',explode(",",$ids))->delete();
        return response()->json(['success'=>"Customer(s) Deleted successfully."]);
    }
}
