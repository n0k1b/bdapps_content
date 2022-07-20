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
<link rel="stylesheet" href="{{asset('assets')}}/admin/css/select2.min.css?{{time()}}" />
<link rel="stylesheet" href="{{asset('assets')}}/admin/css/image_preview.css?{{time()}}">
<link rel="stylesheet" href="{{asset('assets')}}/admin/css/select2_custom.css?{{time()}}" />
<link href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/buttons/1.7.0/css/buttons.dataTables.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/buttons/1.6.4/css/buttons.bootstrap.css" rel="stylesheet">

<style>
    table.dataTable.display tbody td
    {
        font-size: 13px;
    }
    table.dataTable.display thead th
    {
        font-size: 13px;
    }
    .switch {
    position: relative;
    display: inline-block;
    width: 43px;
    height: 21px;
    }
    .round{
        padding: 10px;
        color:black;
    }
    select2-container {
    z-index:10050;
}
    .slider:before {

    position: absolute;
    content: "";
    height: 19px;
    width: 16px;
    left: 1px;
    bottom: 1px;

    background-color: white;
    -webkit-transition: .4s;
    transition: .4s;
}
</style>
@endsection
@section('content')



<div class="container-fluid">
    <div class="modal fade" id="product_update_modal">
        <div class="modal-dialog" role="document">
            <div class="modal-content" id="product_content">




            </div>
        </div>
    </div>
@if(Session::has('success'))
    <div class="col-md-10 col-sm-10 col-10 offset-md-1 offset-sm-10 alert alert-success" >

        {{Session::get('success')}}

        </div>
    @endif

    @if ($errors->any())
            <div class="col-md-10 col-sm-10 col-10 offset-md-1 offset-sm-10 alert alert-danger" >
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
                            <h4>All Product</h4>
                        </div>
                    </div>
                    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.html">Home</a></li>

                            <li class="breadcrumb-item active"><a href="javascript:void(0);">All product</a></li>
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

                                        @if(in_array('product_add',$role_permission))
                                        <a href="{{ route('add-product') }}" class="btn btn-primary">+ Add new</a>
                                        @else
                                        <a href="javascript:void(0);" onclick="access_alert()" class="btn btn-primary">+ Add new</a>
                                        @endif

									</div>
									<div class="card-body">
										<div class="table-responsive">
                                            {{-- <p>This page took {{ (microtime(true) - LARAVEL_START) }} seconds to render</p> --}}
											<table id="product" class="display" style="min-width: 845px">
												<thead>
													<tr>
														{{-- <th>#</th> --}}
														<th>Category Name</th>
                                                        <th>Sub Category Name</th>

                                                        <th>Product Name</th>
                                                        {{-- <th>Warehouse</th> --}}
                                                        <th>Image</th>
                                                        <th>Unit Price</th>

                                                        <th>Unit Type</th>
                                                        <th>Unit Quantity</th>
                                                        <th>Stock</th>


														<th>Active Status</th>

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

<script src="{{asset('assets')}}/admin/js/admin.js?{{time()}}"></script>

<script src="{{asset('assets')}}/admin/js/select2.full.js"></script>
<script src="{{asset('assets')}}/admin/js/advanced-form-element.js"></script>

<script src="{{asset('assets')}}/admin/js/single_image_preview.js?{{time()}}"></script>
<script src="{{asset('assets')}}/admin/js/select2.full.js"></script>
<script src="{{asset('assets')}}/admin/js/advanced-form-element.js"></script>
{{--
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script> --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.18/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.66/vfs_fonts.js"></script>

<script src="https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.2.4/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.2.4/js/buttons.bootstrap.js"></script>
<script src="https://cdn.datatables.net/buttons/1.2.4/js/buttons.html5.js"></script>
<script src="https://cdn.datatables.net/buttons/1.2.4/js/buttons.print.js"></script>
<script type="text/javascript" src="{{asset('assets')}}/admin/js/lazyloader/jquery.lazy.min.js"></script>

{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script> --}}



<script type="text/javascript">
    $(function () {

        // var lazy = $("table img").Lazy({chainable: false});


        var buttonCommon = {
    //         format: {
    //      body: function(data, column, row) {
    //        data = data.replace(/<div class="flagtext"\">/, '');
    //        data = data.replace(/<.*?>/g, "");
    //        return data;
    //      }
    //    }

    };

      var table = $('#product').DataTable({



        //dom: '<"row"lfB>rtip',
       //dom: 'Blfrtip',
        // buttons: [
        //         {
        //             extend: 'pdf',
        //             text: 'PDF',
        //             exportOptions: {
        //                 columns: ':visible:not(.not-exported)',
        //                 rows: ':visible',

        //             },

        //         },
        //         // {
        //         //     extend: 'csv',
        //         //     text: 'CSV',
        //         //     exportOptions: {
        //         //         columns: ':visible:not(.not-exported)',
        //         //         rows: ':visible',
        //         //         stripHtml: true,
        //         //         format: {
        //         //             body: function ( data, row, column, node ) {
        //         //                 if (column === 0 && (data.toString().indexOf('<img src=') !== -1)) {
        //         //                     var regex = /<img.*?src=['"](.*?)['"]/;
        //         //                     data = regex.exec(data)[1];
        //         //                 }
        //         //                 return data;
        //         //             }
        //         //         }
        //         //     }
        //         // },
        //         {
        //             extend: 'print',
        //             text: 'Print',
        //             exportOptions: {
        //                 columns: ':visible:not(.not-exported)',
        //                 rows: ':visible',
        //                 stripHtml: false,
        //             },

        //         },

        //     ],




            processing: true,
         language: {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '
        },
          serverSide: true,

          ajax: "{{ route('get_all_product') }}",
        //   drawCallback: function() {
        //     // update the images shown on table changes
        //     lazy.update();
        // },
          deferRender: true,
          columns: [
            //   {data: 'sl_no'},

            {data:'category_name',name:'category_name'},
              {data:'sub_category_name',name:'sub_category_name'},
              {data:'product_name',name:'name'},
            //   {data:'warehouse',name:'warehouse'},
            {data:'product_image',name:'product_image'},
              {data:'product_price',name:'product_price'},
              {data:'product_unit_type',name:'product_unit_type'},
              {data:'product_unit_quantity',name:'product_unit_quantity'},
              {data:'produc_stock_amount',name:'produc_stock_amount'},
            {
                data: 'status',
                name: 'status',
            },
                {
                data: 'action',
                name: 'action',
                },

          ],









      });



    });
  </script>





@endsection
