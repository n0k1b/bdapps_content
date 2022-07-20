@extends('admin.layout.app')
@section('page_css')
<link rel="stylesheet" href="{{asset('assets')}}/admin/css/select2.min.css?{{time()}}" />
<link rel="stylesheet" href="{{asset('assets')}}/admin/css/image_preview.css?{{time()}}">
<link rel="stylesheet" href="{{asset('assets')}}/admin/css/select2_custom.css?{{time()}}" />
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


                <a hre="get_all_cateogry">ABC</a>

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
										<a href="{{ route('add-product') }}" class="btn btn-primary">+ Add new</a>
									</div>
									<div class="card-body">
										<div class="table-responsive">
											<table id="product_table" class="display" style="min-width: 845px">
												<thead>
													<tr>
														<th>#</th>
														<th>Category Name</th>
                                                        <th>Sub Category Name</th>

                                                        <th>Product Name</th>
                                                        <th>Image</th>
                                                        <th>Unit Price</th>

                                                        <th>Unit Type</th>
                                                        <th>Unit Quantity</th>
                                                        <th>Stock</th>


														<th>Active Status</th>



													</tr>
												</thead>
												<tbody id="product_list">




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
<script>
    $(function() {
        fetch_product()
    })
</script>

<script>
    $('#product_table').dataTable( {
         ordering: false,
         recordsFiltered: 28,
} );

</script>

@endsection
