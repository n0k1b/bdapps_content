@extends('admin.layout.app')
@section('page_css')
<link rel="stylesheet" href="{{asset('assets')}}/admin/css/single_and_multiple_image_preview.css?{{time()}}" />
<link rel="stylesheet" href="{{asset('assets')}}/admin/css/select2.min.css?{{time()}}" />
<link rel="stylesheet" href="{{asset('assets')}}/admin/css/select2_custom.css?{{time()}}" />



@endsection

@section('content')
<div class="container-fluid">

    <div class="row page-titles mx-0">
        <div class="col-sm-6 col-md-6 col-lg-6 p-md-0">
            <div class="form-group" style="padding-right: 10px;" id="product_list">

            </div>
        </div>

        <div class="col-sm-3 col-md-3 col-lg-3 p-md-0">
            <div class="form-group" style="padding-right: 10px;">
                <label>Unit Quantity</label>
                <input type="text" class="form-control" name="discount_percentage" id="unit_quantity" />
            </div>
        </div>

        <div class="col-sm-2 col-md-2 col-lg-2 p-md-0">
            <div class="form-group">
                <label></label>
                <button  type="button" onclick="add_product_to_section()" class="btn btn-primary" style="margin-top: 30px">Add</button>
            </div>
        </div>



    </div>

    <div class="row" id="all_package_product">





    </div>

</div>

<div class="modal fade" id="edit_quantity_modal">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Quantity</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group" style="padding-right: 10px;">
                    <label>Quantity</label>
                    <input type="text" class="form-control" name="discount_percentage" id="update_unit_quantity" />
                    <input type="hidden" id="update_product_id" >
                </div>
            </div>
            <div class="modal-footer">

                <button type="button" onclick="update_quantity()" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>
@endsection
@section('page_js')
<script>
    var package_id = {{ $id }};
</script>
<script src="{{asset('assets')}}/admin/js/single_and_multiple_image_preview.js?{{time()}}"></script>
{{-- <script src="{{asset('assets')}}/admin/js/bootstrap-select.js"></script> --}}
<script src="{{asset('assets')}}/admin/js/select2.full.js"></script>
<script src="{{asset('assets')}}/admin/js/advanced-form-element.js"></script>

<script src="{{asset('assets')}}/admin/js/product_add_to_package.js?{{time()}}"></script>

@endsection
