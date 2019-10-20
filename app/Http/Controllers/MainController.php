<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Product;
use Illuminate\Http\Request;

class MainController extends Controller
{
    public function index(){
    	$customers = Customer::all()->toArray();
        $products = Product::all()->toArray();
        return view('main', compact('customers','products'));
    }
}
