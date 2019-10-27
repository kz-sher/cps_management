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
        $customers = Customer::paginate(10);
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
            "name" => $request['name'],
            "debt" => 0
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
        $customer_transactions = CustomerTransaction::where('customer_id', $id)->paginate(10);
        
        // Generate customer rent return details from customer transactions 
        $raw_details = CustomerTransaction::where('customer_id', $id)
                                    ->select('product', 'status', DB::raw('sum(customer_transactions.quantity) quantity'))
                                    ->groupBy('product', 'status')
                                    ->get()
                                    ->toArray();
        $customer_rent_return_details = Product::select('prod_name')->addSelect(DB::raw("0 as quantity"))->get()->keyBy('prod_name')->toArray();
        foreach($raw_details as $entry){
            if($entry['status'] === 'Rent'){
                $customer_rent_return_details[$entry['product']]['quantity'] += $entry['quantity'];
            }
            else{
                $customer_rent_return_details[$entry['product']]['quantity'] -= $entry['quantity'];
            }
        }
        return view('customer.show', compact('customer', 'products', 'customer_transactions', 'customer_rent_return_details'));
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
