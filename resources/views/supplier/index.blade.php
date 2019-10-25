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
                        <a href="{{ url('/suppliers') }}">Suppliers</a>
                    </li>
                </ol>
            </nav>
    
            <!-- Supplier list -->
                
            <div id="supplier_list">
                @if (session('supplier_success_status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('supplier_success_status') }}
                    </div>
                @endif

                @if(count($errors) > 0)
                    <div class="alert alert-danger" role="alert">
                        @foreach($errors->all() as $error)
                            {{ $error }} <br>
                        @endforeach
                    </div>
                @endif

                <!-- Supplier Button Menu -->

                <div class="d-flex flex-row">
                    <div class="d-flex flex-row align-items-center">
                        <div class="form-group h4">Supplier List</div>
                    </div>
                    <div class="d-flex flex-row flex-grow-1">
                    </div>
                    <div class="d-flex flex-row justify-content-end">
                        <div class="form-group mr-1">
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#add_supplier_modal_box" data-whatever="@getbootstrap">
                                <i class="fa fa-fw fa-plus"></i>
                            </button>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-danger" id="supplier_bulk_delete_button">
                                <i class="fa fa-fw fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Supplier Modal Box - Add -->

                <div class="modal fade" id="add_supplier_modal_box" tabindex="-1" role="dialog" aria-labelledby="add_supplier_modal_box_label" aria-hidden="true">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="add_supplier_modal_box_label">New Supplier</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                      <div class="modal-body">
                        <form method="post" action="{{ url('suppliers') }}" id="add_supplier_form">
                          {{ csrf_field() }}
                          <div class="form-group">
                            <label for="supplier_name" class="col-form-label">Supplier Name:</label>
                            <input type="text" name="name" class="form-control" id="supplier_name">
                          </div>
                        </form>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" onclick="document.getElementById('add_supplier_form').submit();"class="btn btn-primary full-spinner-loader">Add</button>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Supplier Modal Box - Update -->

                <div class="modal fade" id="update_supplier_modal_box" tabindex="-1" role="dialog" aria-labelledby="update_supplier_modal_box_label" aria-hidden="true">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title">Edit Supplier - <span id="update_supplier_modal_box_label"></span></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                      <div class="modal-body">
                        <form method="post" id="update_supplier_form">
                          @method('patch')
                          @csrf
                          <div class="form-group">
                            <label for="curr_supplier_name" class="col-form-label">Supplier Name:</label>
                            <input type="text" name="name" class="form-control" id="curr_supplier_name">
                          </div>
                        </form>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" onclick="document.getElementById('update_supplier_form').submit();"class="btn btn-success full-spinner-loader">Update</button>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Supplier Table -->

                <form method=post action="{{ url('supplierDeleteSelected') }}" id="supplier_list_form">
                    @method('delete')
                    @csrf
                    <table class="table table-bordered table-hover table-responsive-lg shadow-sm" id="supplier_table">
                        <thead class="thead-dark">
                            <tr>
                                <th width="5%">
                                    <input type="checkbox" name="supplier_bulk_delete" class="supplier_bulk_checkbox">
                                </th>
                                <th width="80%">Suppliers</th>
                                <th width="15%">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            @if (count($suppliers) === 0)
                                <tr>
                                    <td colspan="100%">No supplier found</td>
                                </tr>
                            @endif
                            @foreach ($suppliers as $row)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="supplier_checkbox[]" value="{{ $row['id'] }}" class="supplier_checkbox">
                                    </td>
                                    <td id="supplier_name{{ $row['id'] }}">{{ $row['name']}}</td>
                                    <td>
                                        <!-- <a class="btn btn-success update_supplier_modal_button" data-toggle="modal" data-target="#update_supplier_modal_box" data-whatever="@getbootstrap" data-id="{{ $row['id'] }}" data-url="{{ action('SupplierController@update', $row['id']) }}">
                                            <i class="fas fa-edit text-white"></i>
                                        </a> -->
                                        <a class="btn btn-purple update_supplier_modal_button" href="{{ action('SupplierController@show', $row['id']) }}">
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
    $('.supplier_bulk_checkbox').change(function() {
        if(this.checked) {
            $('.supplier_checkbox').prop('checked', true);
        }
        else{
            $('.supplier_checkbox').prop('checked', false);
        }
    });

    // Delete button outside the form
    $('#supplier_bulk_delete_button').on('click', function(e) {
        if( $('.supplier_checkbox:checked').length > 0){
            if(confirm('Are you sure you want to delete all selected data?')){
                $('.full-spinner-loader').click();
                $('#supplier_list_form').submit();
            }
            else{
                return false;
            }
        }
        else{
            alert('Please select at least one supplier!');
        }
    });

    // Disable delete button if no entry found
    if( $('#supplier_table').find('.supplier_checkbox').length === 0 ){
        $('#supplier_bulk_delete_button').attr('disabled', true);
    }

    // Update button that open the update modal box
    $('.update_supplier_modal_button').click(function(){
        var id = $(this).attr('data-id');
        var url = $(this).attr('data-url');
        var supplier_name = $('#supplier_name' + id).html();

        $('#curr_supplier_name').val(supplier_name);
        $('#update_supplier_modal_box_label').html(supplier_name);
        $('#update_supplier_form').attr('action', url);
    });

});
</script>
@endsection