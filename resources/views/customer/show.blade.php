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
                        <a href="{{ url('/main/customer') }}">Customers</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <a href="{{ url('/main/customer') }}">{{ $customer['name']}}</a>
                    </li>
                </ol>
            </nav>

            <!-- Customer Transaction Table -->
                
            <div id="customer_transactions">
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

                <!-- Customer Transaction Button Menu -->

                <div class="d-flex flex-row">
                    <div class="d-flex flex-row flex-grow-1">
                    </div>
                    <div class="d-flex flex-row justify-content-end">
                        <div class="form-group mr-1">
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#add_transaction_modal_box" data-whatever="@getbootstrap">
                                <i class="fa fa-fw fa-plus"></i>
                            </button>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-danger" id="transaction_bulk_delete_button">
                                <i class="fa fa-fw fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Transaction Modal Box - Add -->

                <div class="modal fade" id="add_transaction_modal_box" tabindex="-1" role="dialog" aria-labelledby="add_transaction_modal_box_label" aria-hidden="true">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="add_transaction_modal_box_label">New Transaction</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                      <div class="modal-body">
                        <form method="post" action="{{ url('transaction') }}" id="add_transaction_form">
                          {{ csrf_field() }}
                          <div class="form-group">
                            <label for="transaction_name" class="col-form-label">Transaction Name:</label>
                            <input type="text" name="name" class="form-control" id="transaction_name">
                          </div>
                        </form>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" onclick="document.getElementById('add_transaction_form').submit();"class="btn btn-primary">Add</button>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- transaction Modal Box - Update -->

                <div class="modal fade" id="update_transaction_modal_box" tabindex="-1" role="dialog" aria-labelledby="update_transaction_modal_box_label" aria-hidden="true">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="update_transaction_modal_box_label">Current Transaction</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                      <div class="modal-body">
                        <form method="post" id="update_transaction_form">
                          @method('patch')
                          @csrf
                          <div class="form-group">
                            <label for="curr_transaction_name" class="col-form-label">Transaction Name:</label>
                            <input type="text" name="name" class="form-control" id="curr_transaction_name">
                          </div>
                        </form>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" onclick="document.getElementById('update_transaction_form').submit();"class="btn btn-success">Update</button>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Transaction Table -->

                <form method=post action="{{ url('customerDeleteSelected') }}" id="transaction_list_form">
                    @method('delete')
                    @csrf
                    <table class="table table-bordered table-hover shadow-sm" id="transaction_table">
                        <thead class="thead-dark">
                            <tr>
                                <th width="5%">
                                    <input type="checkbox" name="transaction_bulk_delete" class="transaction_bulk_checkbox">
                                </th>
                                <th width="80%">Transactions</th>
                                <th width="15%">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                                <tr>
                                    <td colspan="100%">No transaction found</td>
                                </tr>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="transaction_checkbox[]" value="" class="transaction_checkbox">
                                    </td>
                                    <td id="transaction_name"></td>
                                    <td>
                                        <a class="btn btn-success update_transaction_modal_button" data-toggle="modal" data-target="#update_transaction_modal_box" data-whatever="@getbootstrap" data-id="" data-url="">
                                            <i class="fas fa-edit text-white"></i>
                                        </a>
                                        <a class="btn btn-report update_transaction_modal_button" href="">
                                            <i class="fas fa-chart-line text-white"></i>
                                        </a>
                                    </td>
                                </tr>
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
    $('.transaction_bulk_checkbox').change(function() {
        if(this.checked) {
            $('.transaction_checkbox').prop('checked', true);
        }
        else{
            $('.transaction_checkbox').prop('checked', false);
        }
    });

    // Delete button outside the form
    $('#transaction_bulk_delete_button').on('click', function(e) {
        if( $('.transaction_checkbox:checked').length > 0){
            $('#transaction_list_form').submit();
        }
        else{
            alert('Please select at least one transaction!');
        }
    });

    // Disable delete button if no entry found
    if( $('#transaction_table').find('.transaction_checkbox').length === 0 ){
        $('#transaction_bulk_delete_button').attr('disabled', true);
        $('#transaction_bulk_delete_button').css('cursor', 'not-allowed');
    }

    // Update button that open the update modal box
    $('.update_transaction_modal_button').click(function(){
        var id = $(this).attr('data-id');
        var url = $(this).attr('data-url');
        var transaction_name = $('#transaction_name' + id).html();

        $('#curr_transaction_name').val(transaction_name);
        $('#update_transaction_modal_box_label').html(transaction_name);
        $('#update_transaction_form').attr('action', url);
    });

});
</script>
@endsection