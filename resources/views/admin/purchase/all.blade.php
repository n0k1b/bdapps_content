<?php
   $with_domain_status = 0;
  $user_id = Auth::guard('admin')->user()->id;
  $user_role = Auth::guard('admin')->user()->role;
  $role_id = DB::table('roles')->where('name',$user_role)->first()->id;
  $role_permission = DB::table('role_permisiions')->where('role_id',$role_id)->pluck('content_name')->toArray();
 //file_put_contents('role.txt',json_encode($role_permission));


?>

@extends('admin.layout.app')
@section('page_css')
<link href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" rel="stylesheet">
@endsection

@section('content')
<div class="container-fluid">
    @if(Session::has('success'))
    <div class="col-md-10 col-sm-10 col-10 offset-md-1 offset-sm-10 alert alert-success">
        {{Session::get('success')}}
    </div>
    @endif @if ($errors->any())
    <div class="col-md-10 col-sm-10 col-10 offset-md-1 offset-sm-10 alert alert-danger">
        <ul>
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    <div class="row page-titles mx-0">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4>All Purchase</h4>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('admin') }}">Home</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0);">Purchase List</a></li>
            </ol>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="row tab-content">
                <div id="list-view" class="tab-pane fade active show col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title"></h4>
                            @if(in_array('sub_category_add',$role_permission))
                            <a href="{{ route('add-purchase') }}" class="btn btn-primary">+ Add new</a>
                            @else
                            <a href="javascript:void(0);" onclick="access_alert()" class="btn btn-primary">+ Add new</a>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="show-all-purchase" class="display" style="min-width: 845px;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Supplier</th>
                                            <th>Product</th>
                                            <th>Quantity</th>
                                            <th>Purchasing Price</th>
                                            <th>Discount</th>
                                            <th>Vat</th>
                                            <th>Shipping Cost</th>
                                            <th>Purchase Note</th>
                                            <th>Total Price</th>
                                            <th>Action</th>

                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('page_js')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
{{-- <script>
    $("#example3").DataTable({
       ordering: false

   });
</script> --}}

<script type="text/javascript">
    $(function () {

      var table = $('#show-all-purchase').DataTable({
          processing: true,
          serverSide: true,
          ajax: "{{ route('show-all-purchase') }}",
          columns: [
              {data: 'sl_no', name: 'sl_no'},

              {data:'supplier',name:'supplier'},
              {data:'product',name:'product'},
            {
                data: 'product_quantity',
                name: 'product_quantity',

            },

            {

                data: 'unit_purchasing_price',
                name: 'unit_purchasing_price',


            },
            {
                data:'discount',
                name:'discount',
            },

            {
                data:'vat',
                name:'vat',
            },

             {
                data:'shipping_cost',
                name:'shipping_cost',
             },

             {
                data:'purchase_note',
                name:'purchase_note',
             },

             {
                data:'total_price',
                name:'total_price',
             },
             {
                data:'action',
                name:'action',
             }










          ]
      });

    });
  </script>
<script src="{{asset('assets')}}/admin/js/admin.js?{{time()}}"></script>
@endsection
