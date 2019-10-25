<?php

namespace App\Http\Controllers;

use App\Supplier;
use App\Product;
use App\SupplierTransaction;
use Illuminate\Http\Request;
use DB;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $suppliers = Supplier::all()->toArray();
        return view('supplier.index', compact('suppliers'));
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
            'name' => ['required', 'unique:suppliers'],
        ]);

        $supplier = new Supplier([
            "name" => $request['name']
        ]);

        $supplier->save();
        return redirect()->back()->with('supplier_success_status', 'Supplier Added');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $supplier = Supplier::find($id);
        $products = Product::all()->toArray();
        $supplier_transactions = SupplierTransaction::where('supplier_id', $id)->get()->toArray();

        // Generate supplier import return details from supplier transactions 
        $raw_details = SupplierTransaction::where('supplier_id', $id)
                                    ->select('product', 'status', DB::raw('sum(supplier_transactions.quantity) quantity'))
                                    ->groupBy('product', 'status')
                                    ->get()
                                    ->toArray();
        $supplier_import_return_details = Product::select('prod_name')->addSelect(DB::raw("0 as quantity"))->get()->keyBy('prod_name')->toArray();
        foreach($raw_details as $entry){
            if($entry['status'] === 'Import'){
                $supplier_import_return_details[$entry['product']]['quantity'] += $entry['quantity'];
            }
            else{
                $supplier_import_return_details[$entry['product']]['quantity'] -= $entry['quantity'];
            }
        }
        return view('supplier.show', compact('supplier', 'products', 'supplier_transactions', 'supplier_import_return_details'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function edit(Supplier $supplier)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => ['required', 'unique:suppliers,name,'.$id],
        ]);

        $supplier = Supplier::find($id);
        $supplier->name = $request->get('name');

        $supplier->save();
        return redirect()->back()->with('supplier_success_status', 'Supplier Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function destroy(Supplier $supplier)
    {
        //
    }

    public function deleteSelected(Request $request){
        $ids = $request['supplier_checkbox'];
        DB::table("suppliers")->whereIn('id',$ids)->delete();
        return redirect()->back()->with('supplier_success_status', 'Supplier(s) Deleted');
    }
}
