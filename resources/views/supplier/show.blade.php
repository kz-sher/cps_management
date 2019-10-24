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
                    <li class="breadcrumb-item" aria-current="page">
                        <a href="{{ url('/suppliers') }}">Suppliers</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <a href="{{ url('/suppliers/'.$supplier->id) }}">{{ $supplier['name']}}</a>
                    </li>
                </ol>
            </nav>

            <!-- Supplier Transaction Table -->
                
            <div id="supplier_transactions">
                @if (session('supplier_transaction_success_status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('supplier_transaction_success_status') }}
                    </div>
                @endif

                @if(count($errors) > 0)
                    <div class="alert alert-danger" role="alert">
                        @foreach($errors->all() as $error)
                            {{ $error }} <br>
                        @endforeach
                    </div>
                @endif

                <!-- Supplier Transaction Button Menu -->

                <div class="d-flex flex-row">
                    <div class="d-flex flex-row flex-grow-1">
                    </div>
                    <div class="d-flex flex-row justify-content-end">
                        <div class="form-group mr-1">
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#add_supplier_transaction_modal_box" data-whatever="@getbootstrap">
                                <i class="fa fa-fw fa-plus"></i>
                            </button>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-danger" id="supplier_transaction_bulk_delete_button">
                                <i class="fa fa-fw fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Supplier Transaction Modal Box - Add -->

                <div class="modal fade" id="add_supplier_transaction_modal_box" tabindex="-1" role="dialog" aria-labelledby="add_supplier_transaction_modal_box_label" aria-hidden="true">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="add_supplier_transaction_modal_box_label">New Transaction</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                      <div class="modal-body">
                        <form method="post" action="{{ action('SupplierTransactionController@store', $supplier['id']) }}" id="add_supplier_transaction_form">
                          {{ csrf_field() }}
                          <div class="form-group"> 
                            <label class="col-form-label" for="date">Date</label>
                            <div class="input-group">
                                <input class="form-control datepicker" name="supplier_transaction_date" placeholder="DD-MM-YYYY" type="text"/>
                                <div class="input-group-append">
                                    <a class="btn btn-secondary calendar-btn">
                                        <i class="fas fa-calendar text-white"></i>
                                    </a>
                                </div>
                            </div>
                          </div>
                          <div class="form-group">
                            <label for="supplier_transaction_prod" class="col-form-label">Product:</label>
                            <select type="text" name="supplier_transaction_prod" class="form-control" id="supplier_transaction_prod">
                                @if (count($products) === 0)
                                    <option selected disabled>No option</option>
                                @else
                                    <option selected disabled>Select Product</option>
                                    @foreach ($products as $product)
                                        <option>{{ $product['prod_name'] }}</option>
                                    @endforeach
                                @endif
                            </select>
                          </div>
                          <div class="form-group">
                            <label for="supplier_transaction_desc" class="col-form-label">Description:</label>
                            <input type="text" name="supplier_transaction_desc" class="form-control" id="supplier_transaction_desc">
                          </div>
                          <div class="form-group">
                            <label for="supplier_transaction_qty" class="col-form-label">Quantity:</label>
                            <input type="number" name="supplier_transaction_qty" class="form-control" id="supplier_transaction_qty">
                          </div>
                          <div class="form-group">
                            <label for="supplier_transaction_status" class="col-form-label">Status:</label>
                            <select type="text" name="supplier_transaction_status" class="form-control" id="supplier_transaction_status">
                                <option selected disabled>Select Status</option>
                                <option>Import</option>
                                <option>Return</option>
                            </select>
                          </div>
                        </form>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" onclick="document.getElementById('add_supplier_transaction_form').submit();"class="btn btn-primary full-spinner-loader">Add</button>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- supplier Transaction Modal Box - Update -->

                <div class="modal fade" id="update_supplier_transaction_modal_box" tabindex="-1" role="dialog" aria-labelledby="update_supplier_transaction_modal_box_label" aria-hidden="true">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title">Supplier Transaction #<span id="update_supplier_transaction_modal_box_label"></span></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                      <div class="modal-body">
                        <form method="post" id="update_supplier_transaction_form">
                          @method('patch')
                          @csrf
                          <div class="form-group"> 
                            <label class="col-form-label" for="date">Date</label>
                            <div class="input-group">
                                <input class="form-control datepicker" id="update_supplier_transaction_date" name="update_supplier_transaction_date" placeholder="DD-MM-YYYY" type="text"/>
                                <div class="input-group-append">
                                    <a class="btn btn-secondary calendar-btn">
                                        <i class="fas fa-calendar text-white"></i>
                                    </a>
                                </div>
                            </div>
                          </div>
                          <div class="form-group">
                            <label for="update_supplier_transaction_prod" class="col-form-label">Product:</label>
                            <select type="text" name="update_supplier_transaction_prod" class="form-control" id="update_supplier_transaction_prod">
                                @if (count($products) === 0)
                                    <option selected disabled>No option</option>
                                @endif
                                @foreach ($products as $product)
                                    <option>{{ $product['prod_name'] }}</option>
                                @endforeach
                            </select>
                          </div>
                          <div class="form-group">
                            <label for="update_supplier_transaction_desc" class="col-form-label">Description:</label>
                            <input type="text" name="update_supplier_transaction_desc" class="form-control" id="update_supplier_transaction_desc">
                          </div>
                          <div class="form-group">
                            <label for="update_supplier_transaction_qty" class="col-form-label">Quantity:</label>
                            <input type="number" name="update_supplier_transaction_qty" class="form-control" id="update_supplier_transaction_qty">
                          </div>
                          <div class="form-group">
                            <label for="update_supplier_transaction_status" class="col-form-label">Status:</label>
                            <select type="text" name="update_supplier_transaction_status" class="form-control" id="update_supplier_transaction_status">
                                <option>Import</option>
                                <option>Return</option>
                            </select>
                          </div>
                        </form>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" onclick="document.getElementById('update_supplier_transaction_form').submit();"class="btn btn-success full-spinner-loader">Update</button>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Transaction Table -->

                <form method=post action="{{ url('supplierTransactionDeleteSelected') }}" id="supplier_transaction_list_form">
                    @method('delete')
                    @csrf
                    <table class="table table-bordered table-hover table-responsive-lg shadow-sm" id="supplier_transaction_table">
                        <thead class="thead-dark">
                            <tr>
                                <th width="5%">
                                    <input type="checkbox" name="supplier_transaction_bulk_delete" class="supplier_transaction_bulk_checkbox">
                                </th>
                                <th>Time</th>
                                <th>Product</th>
                                <th>Description</th>
                                <th>Quantity</th>
                                <th>Status</th>
                                <th width="15%">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            @if (count($supplier_transactions) === 0)
                                <tr>
                                    <td colspan="100%">No transaction found</td>
                                </tr>
                            @endif
                            @foreach ($supplier_transactions as $row)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="supplier_transaction_checkbox[]" value="{{ $row['id'] }}" class="supplier_transaction_checkbox">
                                    </td>
                                    <td id="supplier_transaction_date{{ $row['id'] }}">{{ $row['date']}}</td>
                                    <td id="supplier_transaction_prod{{ $row['id'] }}">{{ $row['product']}}</td>
                                    <td id="supplier_transaction_desc{{ $row['id'] }}">{{ $row['description']}}</td>
                                    <td id="supplier_transaction_qty{{ $row['id'] }}">{{ $row['quantity']}}</td>
                                    <td id="supplier_transaction_status{{ $row['id'] }}">{{ $row['status']}}</td>
                                    <td>
                                        <a class="btn btn-success update_supplier_transaction_modal_button" data-toggle="modal" data-target="#update_supplier_transaction_modal_box" data-whatever="@getbootstrap" data-id="{{ $row['id'] }}" data-url="{{ action('SupplierTransactionController@update', [$supplier['id'], $row['id']], $row['id']) }}">
                                            <i class="fas fa-edit text-white"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table> 
                </form> 
            </div> 

        </div>
    </div>
</div>
@endsection

@section('script')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
$( document ).ready(function() {
    
    // Main checkbox toggle that either selects or de-selects all sub-checkboxes
    $('.supplier_transaction_bulk_checkbox').change(function() {
        if(this.checked) {
            $('.supplier_transaction_checkbox').prop('checked', true);
        }
        else{
            $('.supplier_transaction_checkbox').prop('checked', false);
        }
    });

    // Delete button outside the form
    $('#supplier_transaction_bulk_delete_button').on('click', function(e) {
        if( $('.supplier_transaction_checkbox:checked').length > 0){
            if(confirm('Are you sure you want to delete all selected data?')){
                $('.full-spinner-loader').click();
                $('#supplier_transaction_list_form').submit();
            }
        }
        else{
            alert('Please select at least one transaction!');
        }
    });

    // Disable delete button if no entry found
    if( $('#supplier_transaction_table').find('.supplier_transaction_checkbox').length === 0 ){
        $('#supplier_transaction_bulk_delete_button').attr('disabled', true);
        $('#supplier_transaction_bulk_delete_button').css('cursor', 'not-allowed');
    }

    // Update button that open the update modal box
    $('.update_supplier_transaction_modal_button').click(function(){
        var id = $(this).attr('data-id');
        var url = $(this).attr('data-url');
        var supplier_transaction_date = $('#supplier_transaction_date' + id).html();
        var supplier_transaction_prod = $('#supplier_transaction_prod' + id).html();
        var supplier_transaction_desc = $('#supplier_transaction_desc' + id).html();
        var supplier_transaction_qty = $('#supplier_transaction_qty' + id).html();
        var supplier_transaction_status = $('#supplier_transaction_status' + id).html();

        $('#update_supplier_transaction_date').val(supplier_transaction_date);
        $('#update_supplier_transaction_prod').val(supplier_transaction_prod);
        $('#update_supplier_transaction_desc').val(supplier_transaction_desc);
        $('#update_supplier_transaction_qty').val(supplier_transaction_qty);
        $('#update_supplier_transaction_status').val(supplier_transaction_status);
        $('#update_supplier_transaction_modal_box_label').html(id);
        $('#update_supplier_transaction_form').attr('action', url);
    });

    // Date Picker
    $('.datepicker').datepicker({
        dateFormat: "dd-mm-yy",
        autoclose: true
    });

    // Calendar button click event that triggers datepicker form field to be focused
    $('.calendar-btn').click(function(){
        $('.datepicker').focus();
    });

});
</script>
@endsection