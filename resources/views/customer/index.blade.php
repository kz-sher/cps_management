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
                        <a href="{{ url('/customers') }}">Customers</a>
                    </li>
                </ol>
            </nav>

            <!-- Customer list -->
                
            <div id="customer_list">
                @if (session('customer_success_status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('customer_success_status') }}
                    </div>
                @endif

                @if(count($errors) > 0)
                    <div class="alert alert-danger" role="alert">
                        @foreach($errors->all() as $error)
                            {{ $error }} <br>
                        @endforeach
                    </div>
                @endif

                <!-- Customer List Table Button Menu -->

                <div class="d-flex flex-row">
                    <div class="d-flex flex-row align-items-center">
                        <div class="form-group h4">Customer List</div>
                    </div>
                    <div class="d-flex flex-row flex-grow-1">
                    </div>
                    <div class="d-flex flex-row justify-content-end">
                        <div class="form-group mr-1">
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#add_customer_modal_box" data-whatever="@getbootstrap">
                                <i class="fa fa-fw fa-plus"></i>
                            </button>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-danger" id="customer_bulk_delete_button">
                                <i class="fa fa-fw fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Customer Modal Box - Add -->

                <div class="modal fade" id="add_customer_modal_box" tabindex="-1" role="dialog" aria-labelledby="add_customer_modal_box_label" aria-hidden="true">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="add_customer_modal_box_label">New Customer</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                      <div class="modal-body">
                        <form method="post" action="{{ url('customers') }}" id="add_customer_form">
                          {{ csrf_field() }}
                          <div class="form-group">
                            <label for="customer_name" class="col-form-label">Customer Name:</label>
                            <input type="text" name="name" class="form-control" id="customer_name">
                          </div>
                        </form>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" onclick="document.getElementById('add_customer_form').submit();"class="btn btn-primary full-spinner-loader">Add</button>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Customer Modal Box - Update -->

                <div class="modal fade" id="update_customer_modal_box" tabindex="-1" role="dialog" aria-labelledby="update_customer_modal_box_label" aria-hidden="true">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title">Edit Customer - <span id="update_customer_modal_box_label"></span></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                      <div class="modal-body">
                        <form method="post" id="update_customer_form">
                          @method('patch')
                          @csrf
                          <div class="form-group">
                            <label for="update_customer_name" class="col-form-label">Customer Name:</label>
                            <input type="text" name="name" class="form-control" id="update_customer_name">
                          </div>
                        </form>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" onclick="document.getElementById('update_customer_form').submit();"class="btn btn-success full-spinner-loader">Update</button>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Customer List Table -->

                <form method=post action="{{ url('customerDeleteSelected') }}" id="customer_list_form">
                    @method('delete')
                    @csrf
                    <table class="table table-bordered table-hover table-responsive-lg shadow-sm" id="customer_list_table">
                        <thead class="thead-dark">
                            <tr>
                                <th width="5%">
                                    <input type="checkbox" name="customer_bulk_delete" class="customer_bulk_checkbox">
                                </th>
                                <th width="80%">Customers</th>
                                <th width="15%">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            @if (count($customers) === 0)
                                <tr>
                                    <td colspan="100%">No customer found</td>
                                </tr>
                            @endif
                            @foreach ($customers as $row)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="customer_checkbox[]" value="{{ $row['id'] }}" class="customer_checkbox">
                                    </td>
                                    <td id="customer_name{{ $row['id'] }}">{{ $row['name']}}</td>
                                    <td>
                                        <!-- <a class="btn btn-success update_customer_modal_button" data-toggle="modal" data-target="#update_customer_modal_box" data-whatever="@getbootstrap" data-id="{{ $row['id'] }}" data-url="{{ action('CustomerController@update', $row['id']) }}">
                                            <i class="fas fa-edit text-white"></i>
                                        </a> -->
                                        <a class="btn btn-purple update_customer_modal_button" href="{{ action('CustomerController@show', $row['id']) }}">
                                            <i class="fas fa-chart-line text-white"></i>
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
<script>
$( document ).ready(function() {
    
    // Main checkbox toggle that either selects or de-selects all sub-checkboxes
    $('.customer_bulk_checkbox').change(function() {
        if(this.checked) {
            $('.customer_checkbox').prop('checked', true);
        }
        else{
            $('.customer_checkbox').prop('checked', false);
        }
    });

    // Delete button outside the form
    $('#customer_bulk_delete_button').on('click', function(e) {
        if( $('.customer_checkbox:checked').length > 0){
            if(confirm('Are you sure you want to delete all selected data?')){
                $('.full-spinner-loader').click();
                $('#customer_list_form').submit();
            }
        }
        else{
            alert('Please select at least one customer!');
        }
    });

    // Disable delete button if no entry found
    if( $('#customer_list_table').find('.customer_checkbox').length === 0 ){
        $('#customer_bulk_delete_button').attr('disabled', true);
    }

    // Update button that open the update modal box
    $('.update_customer_modal_button').click(function(){
        var id = $(this).attr('data-id');
        var url = $(this).attr('data-url');
        var customer_name = $('#customer_name' + id).html();

        $('#update_customer_name').val(customer_name);
        $('#update_customer_modal_box_label').html(customer_name);
        $('#update_customer_form').attr('action', url);
    });

});
</script>
@endsection