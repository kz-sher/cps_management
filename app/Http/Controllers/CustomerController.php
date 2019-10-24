<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Product;
use App\CustomerTransaction;
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
            'name' => ['required', 'unique:customers'],
        ]);

        $customer = new Customer([
            "name" => $request['name']
        ]);

        $customer->save();
        return redirect()->back()->with('customer_success_status', 'Customer Added');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $customer = Customer::find($id);
        $products = Product::all()->toArray();
        $customer_transactions = CustomerTransaction::where('customer_id', $id)->get()->toArray();
        return view('customer.show', compact('customer', 'products', 'customer_transactions'));
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
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => ['required', 'unique:customers,name,'.$id],
        ]);

        $customer = Customer::find($id);
        $customer->name = $request->get('name');

        $customer->save();
        return redirect()->back()->with('customer_success_status', 'Customer Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function deleteSelected(Request $request){
        $ids = $request['customer_checkbox'];
        DB::table("customers")->whereIn('id',$ids)->delete();
        return redirect()->back()->with('customer_success_status', 'Customer(s) Deleted');
    }
}
