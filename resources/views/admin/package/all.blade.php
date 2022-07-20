@extends('admin.layout.app')
@section('page_css')


@endsection
@section('content')



<div class="container-fluid">
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
                            <h4>All Package</h4>
                        </div>
                    </div>
                    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('admin') }}">Home</a></li>

                            <li class="breadcrumb-item active"><a href="javascript:void(0);">Package</a></li>
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
                                        @if(in_array('homepage_content_edit',$permission))
										<a href="{{ route('add-package') }}" class="btn btn-primary">+ Add new</a>
                                        @else
                                        <a href="javascript:void(0);" onclick="access_alert()" class="btn btn-primary">+ Add new</a>
                                        @endif
									</div>
									<div class="card-body">
										<div class="table-responsive">
											<table id="example3" class="table" style="min-width: 845px">
												<thead>
													<tr>
														<th>#</th>
                                                        <th>Package Name</th>
														<th>Image</th>
                                                        <th>Discount Percentage</th>
                                                        <th>Total Price</th>
                                                        <th>Discount Price</th>
														<th>Active Status</th>
                                                        <th>Action</th>
                                                        <th>Image Edit</th>
                                                        <th></th>

													</tr>
												</thead>
												<tbody  class="row_position">

                                                    @foreach($datas as $data)
													<tr id="{{ $data->id }}">
													<?php
													$checked = $data->status=='1'?'checked':'';
													?>
														<td><strong>{{$data->sl_no}}</strong></td>
														<td>{{$data->package_name}}</td>



                                                        <td><img  width="100" src="../{{$data->package_image}}"  alt="Not Available"></td>
                                                        <td>{{$data->discount_percentage}}</td>
                                                        <td>{{$data->total_price}}</td>
                                                        <td>{{$data->discount_price}}</td>
														<td> <label class="switch">
															<input type="checkbox"  onclick="package_active_status({{$data->id}})" {{$checked}}>
																<span class="slider round"></span>
															</label></td>
														<td>
                                                            @if(in_array('homepage_content_edit',$permission))
															<a href="edit_package_content/{{$data->id}}" class="btn btn-sm btn-primary"><i class="la la-pencil"></i></a>
                                                            @else
                                                            <a href="javascript:void(0);" onclick="access_alert()" class="btn btn-sm btn-primary"><i class="la la-pencil"></i></a>
                                                            @endif
                                                            @if(in_array('homepage_content_delete',$permission))

															<a href="javascript:void(0);" class="btn btn-sm btn-danger" onclick="package_content_delete({{$data->id}})"><i class="la la-trash-o"></i></a>
                                                            @else
                                                            <a href="javascript:void(0);" class="btn btn-sm btn-danger" onclick="access_alert()"><i class="la la-trash-o"></i></a>
                                                            @endif

                                                        </td>
                                                        <td>
															<a href="edit_package_image/{{$data->id}}" class="btn btn-sm btn-info"><i class="la la-pencil"></i></a>

                                                        </td>
                                                        <td><button class="btn btn-primary" onclick="location.href='product-add-to-package/{{ $data->id }}'">Show product</button></td>
													</tr>

												@endforeach


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

<script type="text/javascript">

    $("#example3").DataTable({
        ordering: false

    });




  </script>
@endsection
