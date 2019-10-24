@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="row col-md-12 justify-content-center">
            <div class="col-md-3 m-3 embed-responsive embed-responsive-1by1 text-center shadow hover-grow full-spinner-loader" id="customers">
                <div class="embed-responsive-item d-flex justify-content-center flex-column">
                    <i class="fas fa-users fa-7x"></i>
                    <h4 class="mt-3">Customers</h4>
                </div>
            </div>
            <!-- <div class="col-md-1"></div> -->
            <div class="col-md-3 m-3 embed-responsive embed-responsive-1by1 text-center shadow hover-grow full-spinner-loader" id="products">
                <div class="embed-responsive-item d-flex justify-content-center flex-column">
                    <i class="fas fa-box-open fa-7x"></i>
                    <h4 class="mt-3">Products</h4>
                </div>
            </div>
            <!-- <div class="col-md-1"></div> -->
            <div class="col-md-3 m-3 embed-responsive embed-responsive-1by1 text-center shadow hover-grow full-spinner-loader" id="suppliers">
                <div class="embed-responsive-item d-flex justify-content-center flex-column">
                    <i class="fas fa-truck fa-7x"></i>
                    <h4 class="mt-3">Suppliers</h4>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function() {

    $('#customers').click(function(){
        window.location.href='./customers';
    });

    $('#products').click(function(){
        window.location.href='./products';
    });

    $('#suppliers').click(function(){
        window.location.href='./suppliers';
    });

});
</script>

<style>
#customers, #products, #suppliers{
    border-radius: 20px;
    color: lightgray;
    cursor: pointer;
}

#customers, #products, #suppliers{
    background-image: url('images/triangle-mix2.png');
}
</style>
@endsection