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
                        <a href="{{ url('/products') }}">Products</a>
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
                            {{ $error }}<br>
                        @endforeach
                    </div>
                @endif

                <!-- Product List Button Menu -->

                <div class="d-flex flex-row">
                    <div class="d-flex flex-row align-items-center">
                        <div class="form-group h4">Product List</div>
                    </div>
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
                            <input type="text" name="new_prod_name" class="form-control" id="new_product_name">
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

                <!-- Product Table -->
                <form method=post action="{{ url('productDeleteSelected') }}" id="product_list_form">
                    @method('delete')
                    @csrf
                    <table class="table table-bordered table-hover table-responsive-lg shadow-sm" id="product_table">
                        <thead class="thead-dark">
                            <tr>
                                <th width="5%">
                                    <input type="checkbox" name="product_bulk_delete" class="product_bulk_checkbox">
                                </th>
                                <th>Products</th>
                                <th>Stock Amount</th>
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
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </form>

                <div class="d-flex flex-row">
                    <div class="d-flex flex-row">

                    </div>
                    <div class="d-flex flex-row flex-grow-1">
                        @if($products->total() !== 0)
                            Showing
                            {{ $products->currentPage()*10-9 }}
                            - 
                            @if($products->currentPage()*10 <= $products->total())
                                {{ $products->currentPage()*10 }}
                            @else
                                {{$products->total()}}
                            @endif
                            result(s)
                            (Out of {{$products->total()}})
                        @else
                            Showing 0 result
                        @endif
                    </div>
                    <div class="d-flex flex-row justify-content-end">
                        {{$products->links('pagination.default')}}
                    </div>
                </div>

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
            if(confirm('Are you sure you want to delete all selected data?')){
                $('.full-spinner-loader').click();
                $('#product_list_form').submit();
            }
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

});
</script>

<style>

</style>
@endsection