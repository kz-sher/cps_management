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
                        <a href="{{ url('/customers') }}">Customers</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <a href="{{ url('/customers/'.$customer->id) }}">{{ $customer['name']}}</a>
                    </li>
                </ol>
            </nav>

            <!-- Customer Transaction Table -->
                
            <div id="customer_transactions">
                @if (session('customer_transaction_success_status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('customer_transaction_success_status') }}
                    </div>
                @endif

                @if(count($errors) > 0)
                    <div class="alert alert-danger" role="alert">
                        @foreach($errors->all() as $error)
                            {{ $error }} <br>
                        @endforeach
                    </div>
                @endif

                <!-- Customer Transaction Button Menu -->

                <div class="d-flex flex-row">
                    <div class="d-flex flex-row align-items-center">
                        <div class="form-group h4">Customer Transactions</div>
                    </div>
                    <div class="d-flex flex-row flex-grow-1">
                    </div>
                    <div class="d-flex flex-row justify-content-end">
                        <div class="form-group mr-1">
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#add_customer_transaction_modal_box" data-whatever="@getbootstrap">
                                <i class="fa fa-fw fa-plus"></i>
                            </button>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-danger" id="customer_transaction_bulk_delete_button">
                                <i class="fa fa-fw fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Customer Transaction Modal Box - Add -->

                <div class="modal fade" id="add_customer_transaction_modal_box" tabindex="-1" role="dialog" aria-labelledby="add_customer_transaction_modal_box_label" aria-hidden="true">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="add_customer_transaction_modal_box_label">New Transaction</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                      <div class="modal-body">
                        <form method="post" action="{{ action('CustomerTransactionController@store', $customer['id']) }}" id="add_customer_transaction_form">
                          {{ csrf_field() }}
                          <div class="form-group"> 
                            <label class="col-form-label" for="date">Date</label>
                            <div class="input-group">
                                <input class="form-control datepicker" name="customer_transaction_date" placeholder="DD-MM-YYYY" type="text"/>
                                <div class="input-group-append">
                                    <a class="btn btn-secondary calendar-btn">
                                        <i class="fas fa-calendar text-white"></i>
                                    </a>
                                </div>
                            </div>
                          </div>
                          <div class="form-group">
                            <label for="customer_transaction_prod" class="col-form-label">Product:</label>
                            <select type="text" name="customer_transaction_prod" class="form-control" id="customer_transaction_prod">
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
                            <label for="customer_transaction_desc" class="col-form-label">Description:</label>
                            <input type="text" name="customer_transaction_desc" class="form-control" id="customer_transaction_desc">
                          </div>
                          <div class="form-group">
                            <label for="customer_transaction_qty" class="col-form-label">Quantity:</label>
                            <input type="number" name="customer_transaction_qty" class="form-control" id="customer_transaction_qty">
                          </div>
                          <div class="form-group">
                            <label for="customer_transaction_rate" class="col-form-label">Rate:</label>
                            <input type="number" name="customer_transaction_rate" class="form-control" id="customer_transaction_rate">
                          </div>
                          <div class="form-group">
                            <label for="customer_transaction_status" class="col-form-label">Status:</label>
                            <select type="text" name="customer_transaction_status" class="form-control" id="customer_transaction_status">
                                <option selected disabled>Select Status</option>
                                <option>Rent</option>
                                <option>Return</option>
                            </select>
                          </div>
                        </form>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" onclick="document.getElementById('add_customer_transaction_form').submit();"class="btn btn-primary full-spinner-loader">Add</button>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Customer Transaction Modal Box - Update -->

                <div class="modal fade" id="update_customer_transaction_modal_box" tabindex="-1" role="dialog" aria-labelledby="update_customer_transaction_modal_box_label" aria-hidden="true">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title">Customer Transaction #<span id="update_customer_transaction_modal_box_label"></span></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                      <div class="modal-body">
                        <form method="post" id="update_customer_transaction_form">
                          @method('patch')
                          @csrf
                          <div class="form-group"> 
                            <label class="col-form-label" for="date">Date</label>
                            <div class="input-group">
                                <input class="form-control datepicker" id="update_customer_transaction_date" name="update_customer_transaction_date" placeholder="DD-MM-YYYY" type="text"/>
                                <div class="input-group-append">
                                    <a class="btn btn-secondary calendar-btn">
                                        <i class="fas fa-calendar text-white"></i>
                                    </a>
                                </div>
                            </div>
                          </div>
                          <div class="form-group">
                            <label for="update_customer_transaction_prod" class="col-form-label">Product:</label>
                            <select type="text" name="update_customer_transaction_prod" class="form-control" id="update_customer_transaction_prod">
                                @if (count($products) === 0)
                                    <option selected disabled>No option</option>
                                @endif
                                @foreach ($products as $product)
                                    <option>{{ $product['prod_name'] }}</option>
                                @endforeach
                            </select>
                          </div>
                          <div class="form-group">
                            <label for="update_customer_transaction_desc" class="col-form-label">Description:</label>
                            <input type="text" name="update_customer_transaction_desc" class="form-control" id="update_customer_transaction_desc">
                          </div>
                          <div class="form-group">
                            <label for="update_customer_transaction_qty" class="col-form-label">Quantity:</label>
                            <input type="number" name="update_customer_transaction_qty" class="form-control" id="update_customer_transaction_qty">
                          </div>
                          <div class="form-group">
                            <label for="update_customer_transaction_rate" class="col-form-label">Rate:</label>
                            <input type="number" name="update_customer_transaction_rate" class="form-control" id="update_customer_transaction_rate">
                          </div>
                          <div class="form-group">
                            <label for="update_customer_transaction_status" class="col-form-label">Status:</label>
                            <select type="text" name="update_customer_transaction_status" class="form-control" id="update_customer_transaction_status">
                                <option>Rent</option>
                                <option>Return</option>
                            </select>
                          </div>
                        </form>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" onclick="document.getElementById('update_customer_transaction_form').submit();"class="btn btn-success full-spinner-loader">Update</button>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Transaction Table -->

                <form method=post action="{{ url('customerTransactionDeleteSelected') }}" id="customer_transaction_list_form">
                    @method('delete')
                    @csrf
                    <table class="table table-bordered table-hover table-responsive-lg shadow-sm" id="customer_transaction_table">
                        <thead class="thead-dark">
                            <tr>
                                <th width="5%">
                                    <input type="checkbox" name="customer_transaction_bulk_delete" class="customer_transaction_bulk_checkbox">
                                </th>
                                <th>Time</th>
                                <th>Product</th>
                                <th>Description</th>
                                <th>Quantity</th>
                                <th>Rate</th>
                                <th>Status</th>
                                <th width="15%">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            @if (count($customer_transactions) === 0)
                                <tr>
                                    <td colspan="100%">No transaction found</td>
                                </tr>
                            @endif
                            @foreach ($customer_transactions as $row)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="customer_transaction_checkbox[]" value="{{ $row['id'] }}" class="customer_transaction_checkbox">
                                    </td>
                                    <td id="customer_transaction_date{{ $row['id'] }}">{{ $row['date']}}</td>
                                    <td id="customer_transaction_prod{{ $row['id'] }}">{{ $row['product']}}</td>
                                    <td id="customer_transaction_desc{{ $row['id'] }}">{{ $row['description']}}</td>
                                    <td id="customer_transaction_qty{{ $row['id'] }}">{{ $row['quantity']}}</td>
                                    <td id="customer_transaction_rate{{ $row['id'] }}">{{ $row['rate']}}</td>
                                    <td id="customer_transaction_status{{ $row['id'] }}">{{ $row['status']}}</td>
                                    <td>
                                        <a class="btn btn-success update_customer_transaction_modal_button" data-toggle="modal" data-target="#update_customer_transaction_modal_box" data-whatever="@getbootstrap" data-id="{{ $row['id'] }}" data-url="{{ action('CustomerTransactionController@update', [$customer['id'], $row['id']], $row['id']) }}">
                                            <i class="fas fa-edit text-white"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table> 
                </form> 
            </div>

            <!-- Row Spacer -->
            
            <div class="pb-3"></div>

            <!-- Customer Rent/Return -->
            
            <div id="customer_rent_return">
                
                <!-- Customer Rent/Return Table Title -->

                <div class="d-flex flex-row">
                    <div class="d-flex flex-row align-items-center">
                        <div class="form-group h4">Customer Rent/Return Details</div>
                    </div>
                </div>

                <!-- Customer Rent/Return Table -->
                
                <table class="table table-bordered table-hover shadow-sm col-md-6" id="customer_rent_return_table">
                    <thead class="thead-dark">
                        <tr>
                            <th width="50%">Products</th>
                            <th width="50%">Amount Rented</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @if (count($customer_rent_return_details) === 0)
                            <tr>
                                <td colspan="100%">No customer details found</td>
                            </tr>
                        @endif
                        @foreach ($customer_rent_return_details as $row)
                            <tr>
                                <td>{{ $row['product'] }}</td>
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
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
$( document ).ready(function() {
    
    // Main checkbox toggle that either selects or de-selects all sub-checkboxes
    $('.customer_transaction_bulk_checkbox').change(function() {
        if(this.checked) {
            $('.customer_transaction_checkbox').prop('checked', true);
        }
        else{
            $('.customer_transaction_checkbox').prop('checked', false);
        }
    });

    // Delete button outside the form
    $('#customer_transaction_bulk_delete_button').on('click', function(e) {
        if( $('.customer_transaction_checkbox:checked').length > 0){
            if(confirm('Are you sure you want to delete all selected data?')){
                $('.full-spinner-loader').click();
                $('#customer_transaction_list_form').submit();
            }
        }
        else{
            alert('Please select at least one transaction!');
        }
    });

    // Disable delete button if no entry found
    if( $('#customer_transaction_table').find('.customer_transaction_checkbox').length === 0 ){
        $('#customer_transaction_bulk_delete_button').attr('disabled', true);
        $('#customer_transaction_bulk_delete_button').css('cursor', 'not-allowed');
    }

    // Update button that open the update modal box
    $('.update_customer_transaction_modal_button').click(function(){
        var id = $(this).attr('data-id');
        var url = $(this).attr('data-url');
        var customer_transaction_date = $('#customer_transaction_date' + id).html();
        var customer_transaction_prod = $('#customer_transaction_prod' + id).html();
        var customer_transaction_desc = $('#customer_transaction_desc' + id).html();
        var customer_transaction_qty = $('#customer_transaction_qty' + id).html();
        var customer_transaction_rate = $('#customer_transaction_rate' + id).html();
        var customer_transaction_status = $('#customer_transaction_status' + id).html();

        $('#update_customer_transaction_date').val(customer_transaction_date);
        $('#update_customer_transaction_prod').val(customer_transaction_prod);
        $('#update_customer_transaction_desc').val(customer_transaction_desc);
        $('#update_customer_transaction_qty').val(customer_transaction_qty);
        $('#update_customer_transaction_rate').val(customer_transaction_rate);
        $('#update_customer_transaction_status').val(customer_transaction_status);
        $('#update_customer_transaction_modal_box_label').html(id);
        $('#update_customer_transaction_form').attr('action', url);
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