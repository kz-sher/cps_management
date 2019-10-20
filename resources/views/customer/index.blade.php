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
                        <a href="{{ url('/main/customer') }}">Customers</a>
                    </li>
                </ol>
            </nav>
    
            <!-- Customer list -->
                
            <div id="customer-list">
                @if (session('customer_success_status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('customer_success_status') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if(count($errors) > 0)
                    <div class="alert alert-danger" role="alert">
                        @foreach($errors->all() as $error)
                            {{ $error }} <br>
                        @endforeach
                    </div>
                @endif

                <!-- Customer Button Menu -->

                <div class="d-flex flex-row">
                    <div class="d-flex flex-row flex-grow-1">
                    </div>
                    <div class="d-flex flex-row justify-content-end">
                        <div class="form-group mr-1">
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#add_customer_modal_box" data-whatever="@getbootstrap">
                                <i class="fa fa-fw fa-plus"></i>
                            </button>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-danger" id="customer_bulk_delete_button" data-url="{{ url('customerDeleteSelected') }}">
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
                        <form method="post" action="{{ url('customer') }}" id="add_customer_form">
                          {{ csrf_field() }}
                          <div class="form-group">
                            <label for="customer_name" class="col-form-label">Customer Name:</label>
                            <input type="text" name="full_name" class="form-control" id="customer_name">
                          </div>
                        </form>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" onclick="document.getElementById('add_customer_form').submit();"class="btn btn-primary">Submit</button>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Customer Table -->

                <table class="table table-bordered table-hover shadow-sm" id="customer_table">
                    <thead class="thead-dark">
                        <tr>
                            <th width="5%">
                                <input type="checkbox" name="customer_bulk_delete" class="customer_bulk_checkbox">
                            </th>
                            <th width="95%">Customers</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @if (count($customers) === 0)
                            <tr>
                                <td colspan="2">No customer found</td>
                            </tr>
                        @endif
                        @foreach ($customers as $row)
                            <tr>
                                <td>
                                    <input type="checkbox" name="customer_checkbox[]" value="{{ $row['id'] }}" class="customer_checkbox">
                                </td>
                                <td>{{ $row['full_name']}} </td>
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
    
    $('.customer_bulk_checkbox').change(function() {
        if(this.checked) {
            $('.customer_checkbox').prop('checked', true);
        }
        else{
            $('.customer_checkbox').prop('checked', false);
        }
    });

    $('#customer_bulk_delete_button').on('click', function(e) {

        var subCheckedBoxClass = ".customer_checkbox";
        var targetTable = "#customer_table";
        var self = $(this);
        var idStr = "customer"; 

        sendAjaxMultipleDeleteRequest(self, subCheckedBoxClass, targetTable, idStr);          
    });
    

});
</script>
@endsection