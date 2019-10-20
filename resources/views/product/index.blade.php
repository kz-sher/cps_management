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
                        <a href="{{ url('/product') }}">Products</a>
                    </li>
                </ol>
            </nav>

            <!-- Product Section -->

            <div id="product_inventory">
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
                            <label for="new_product_name" class="col-form-label">Product Name:</label>
                            <input type="text" name="prod_name" class="form-control" id="new_product_name">
                          </div>
                        </form>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" onclick="document.getElementById('add_product_form').submit();"class="btn btn-primary full-spinner-loader">Add</button>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Product Modal Box - Update -->

                <div class="modal fade" id="update_product_modal_box" tabindex="-1" role="dialog" aria-labelledby="update_product_modal_box_label" aria-hidden="true">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title">Product - <span id="update_product_modal_box_label"></span></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                      <div class="modal-body">
                        <form method="post" id="update_product_form">
                          @method('patch')
                          @csrf
                          <div class="form-group">
                            <label for="curr_product_name" class="col-form-label">Product Name:</label>
                            <input type="text" name="prod_name" class="form-control" id="curr_product_name">
                          </div>
                          <div class="form-group">
                            <label for="curr_product_stock_amount" class="col-form-label">Stock Amount:</label>
                            <input type="number" name="curr_stock" class="form-control" id="curr_product_stock_amount" value=0>
                          </div>
                        </form>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" onclick="document.getElementById('update_product_form').submit();"class="btn btn-success full-spinner-loader">Update</button>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Product Supplier Modal Box - Import -->

                <div class="modal fade" id="import_product_modal_box" tabindex="-1" role="dialog" aria-labelledby="import_product_modal_box_label" aria-hidden="true">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title">Import Product - <span id="import_product_modal_box_label"></span></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                      <div class="modal-body">
                        <form method="post" id="import_product_form">
                          @method('patch')
                          @csrf
                          <div class="form-group">
                            <label for="supplier" class="col-form-label">Supplier:</label>
                            <select type="text" name="supplier" class="form-control" id="supplier">
                                @if (count($suppliers) === 0)
                                    <option>No option</option>
                                @endif
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier['id'] }}">{{ $supplier['name'] }}</option>
                                @endforeach
                            </select>
                          </div>
                          <div class="form-group">
                            <label for="import_product_stock_amount" class="col-form-label">Stock Amount:</label>
                            <input type="number" name="import_stock" class="form-control" id="import_product_stock_amount" value=0>
                          </div>
                        </form>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" onclick="document.getElementById('import_product_form').submit();"class="btn btn-purple full-spinner-loader">Save</button>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Product Table -->
                <form method=post action="{{ url('productDeleteSelected') }}" id="product_list_form">
                    @method('delete')
                    @csrf
                    <table class="table table-bordered table-hover shadow-sm" id="product_table">
                        <thead class="thead-dark">
                            <tr>
                                <th width="5%">
                                    <input type="checkbox" name="product_bulk_delete" class="product_bulk_checkbox">
                                </th>
                                <th width="40%">Products</th>
                                <th width="40%">Stock Amount</th>
                                <th width="15%">Actions</th>
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
                                    <td id="product_name{{ $row['id'] }}">{{ $row['prod_name']}}</td>
                                    <td id="product_stock{{ $row['id'] }}">{{ $row['curr_stock']}}</td>
                                    <td>
                                        <a class="btn btn-success update_product_modal_button" data-toggle="modal" data-target="#update_product_modal_box" data-whatever="@getbootstrap" data-id="{{ $row['id'] }}" data-url="{{ action('ProductController@update', $row['id']) }}">
                                            <i class="fas fa-edit text-white"></i>
                                        </a>
                                        <a class="btn btn-purple import_product_modal_button" data-toggle="modal" data-target="#import_product_modal_box" data-whatever="@getbootstrap" data-id="{{ $row['id'] }}" data-url="{{ action('ProductController@importProduct', $row['id']) }}">
                                            <i class="fas fa-truck text-white"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </form>
            </div>

            <!-- Stock History -->

            <div id="product_stock_history">
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
                                <button class="btn btn-orange full-spinner-loader">
                                    Clear History
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <table class="table table-bordered shadow-sm" id="stock_history_table">
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
                            <tr class="history_entry">
                                <td>{{ $row['created_at'] }}</td>
                                <td>{{ $row['person_involved'] }}</td>
                                <td>{{ $row['stock_status'] }}</td>
                                <td>
                                    {{ $row['stock_amount'] }}
                                    @if ($row['stock_amount_status'] === 'up')
                                        <strong class="text-success"> (+)</strong>
                                    @else
                                        <strong class="text-danger"> (-)</strong>
                                    @endif
                                </td>
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
    
    // Main checkbox toggle that either selects or de-selects all sub-checkboxes
    $('.product_bulk_checkbox').change(function() {
        if(this.checked) {
            $('.product_checkbox').prop('checked', true);
        }
        else{
            $('.product_checkbox').prop('checked', false);
        }
    });

    // Delete button outside the form
    $('#product_bulk_delete_button').on('click', function(e) {
        if( $('.product_checkbox:checked').length > 0){
            $('.full-spinner-loader').click();
            $('#product_list_form').submit();
        }
        else{
            alert('Please select at least one product!');
        }
    });

    // Disable delete button if no entry found
    if( $('#product_table').find('.product_checkbox').length === 0 ){
        $('#product_bulk_delete_button').attr("disabled", true);
        $('#product_bulk_delete_button').css('cursor', 'not-allowed');
    }

    // Disable clear history button if no entry found
    if( $('#stock_history_table').find('.history_entry').length === 0 ){
        $('.btn-orange').attr("disabled", true);
        $('.btn-orange').css('cursor', 'not-allowed');
    }

    // Update button that open the update modal box
    $('.update_product_modal_button').click(function(){
        var id = $(this).attr('data-id');
        var url = $(this).attr('data-url');
        var product_name = $('#product_name' + id).html();
        var product_stock = $('#product_stock' + id).html();

        $('#curr_product_name').val(product_name);
        $('#curr_product_stock_amount').val(product_stock);
        $('#update_product_modal_box_label').html(product_name);
        $('#update_product_form').attr('action', url);
    });

    // Import button that open the product supplier modal box
    $('.import_product_modal_button').click(function(){
        var id = $(this).attr('data-id');
        var url = $(this).attr('data-url');
        console.log(url);
        var product_name = $('#product_name' + id).html();

        $('#import_product_modal_box_label').html(product_name);
        $('#import_product_form').attr('action', url);
    });

});
</script>

<style>

</style>
@endsection