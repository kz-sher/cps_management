@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
        	
            <!-- Breadcrumb -->

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb border shadow-sm">
                    <li class="breadcrumb-item">
                        <a href="{{ url('/main') }}">Home</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <a href="{{ url('/main/product') }}">Products</a>
                    </li>
                </ol>
            </nav>

            <!-- Product Section -->

            <div id="product-inventory">
                @if (session('product_success_status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('product_success_status') }}
                    </div>
                @endif

                @if(count($errors) > 0)
                    <div class="alert alert-danger" role="alert">
                        @foreach($errors->all() as $error)
                            {{ $error }}
                        @endforeach
                    </div>
                @endif

                <!-- Product List Button Menu -->

                <div class="d-flex flex-row">
                    <div class="d-flex flex-row flex-grow-1">
                    </div>
                    <div class="d-flex flex-row justify-content-end">
                        <div class="form-group mr-1">
                            <button class="btn btn-success">
                                <i class="fas fa-edit"></i>
                            </button>
                        </div>
                        <div class="form-group mr-1">
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#add_product_modal_box" data-whatever="@getbootstrap">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-danger" id="product_bulk_delete_button" data-url="{{ url('productDeleteSelected') }}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Product Modal Box - Add -->

                <div class="modal fade" id="add_product_modal_box" tabindex="-1" role="dialog" aria-labelledby="add_product_modal_box_label" aria-hidden="true">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="add_product_modal_box_label">New Product</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                      <div class="modal-body">
                        <form method="post" action="{{ action('ProductController@store') }}" id="add_product_form">
                          {{ csrf_field() }}
                          <div class="form-group">
                            <label for="product_name" class="col-form-label">Product Name:</label>
                            <input type="text" name="prod_name" class="form-control" id="product_name">
                          </div>
                          <div class="form-group">
                            <label for="product_stock_amount" class="col-form-label">Stock Amount:</label>
                            <input type="number" name="curr_stock" class="form-control" id="product_stock_amount" value=0>
                          </div>
                        </form>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" onclick="document.getElementById('add_product_form').submit();"class="btn btn-primary">Submit</button>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Product Table -->

                <table class="table table-bordered table-hover shadow-sm" id="product_table">
                    <thead class="thead-dark">
                        <tr>
                            <th width="5%">
                                <input type="checkbox" name="product_bulk_delete" class="product_bulk_checkbox">
                            </th>
                            <th width="47.5%">Products</th>
                            <th width="47.5%">Stock Amount</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @if (count($products) === 0)
                            <tr>
                                <td colspan="100%">No product found</td>
                            </tr>
                        @endif
                        @foreach ($products as $row)
                            <tr>
                                <td>
                                    <input type="checkbox" name="product_checkbox[]" value="{{ $row['id'] }}" class="product_checkbox">
                                </td>
                                <td>{{ $row['prod_name']}} </td>
                                <td>{{ $row['curr_stock']}} </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Stock History -->

            <div id="product-stock-history">
                @if (session('stock_history_success_status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('stock_history_success_status') }}
                    </div>
                @endif
                
                <!-- Stock History Button Menu -->

                <div class="d-flex flex-row">
                    <div class="d-flex flex-row flex-grow-1">
                    </div>
                    <div class="d-flex flex-row justify-content-end">
                        <form method="post" action="{{ url('clearStockHistory') }}">
                            @method('delete')
                            @csrf
                            <div class="form-group mr-1">
                                <button class="btn btn-clear-history full-spinner-loader">
                                    Clear History
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <table class="table table-bordered shadow-sm">
                    <thead class="thead-dark">
                        <tr>
                            <th>Date</th>
                            <th>Person</th>
                            <th>Stock Status</th>
                            <th>Stock Amount</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @if (count($product_stock_histories) === 0)
                            <tr>
                                <td colspan="100%">No history found</td>
                            </tr>
                        @endif
                        @foreach ($product_stock_histories as $row)
                            <tr>
                                <td>{{ $row['created_at'] }}</td>
                                <td>{{ $row['person_involved'] }}</td>
                                <td>{{ $row['stock_status'] }}</td>
                                <td>{{ $row['amount'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$( document ).ready(function() {
    
    $('.product_bulk_checkbox').change(function() {
        if(this.checked) {
            $('.product_checkbox').prop('checked', true);
        }
        else{
            $('.product_checkbox').prop('checked', false);
        }
    });

    $('#product_bulk_delete_button').on('click', function(e) {

        var subCheckedBoxClass = ".product_checkbox";
        var targetTable = "#product_table";
        var self = $(this);
        var idStr = "product"; 

        sendAjaxMultipleDeleteRequest(self, subCheckedBoxClass, targetTable, idStr);         
    });

});
</script>

<style>

</style>
@endsection