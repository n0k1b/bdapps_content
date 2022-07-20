@extends('admin.layout.app')
@section('page_css')
<link rel="stylesheet" href="{{asset('assets')}}/lightbox/css/lightbox.css?{{time()}}">
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
                            <h4>All Courier Man</h4>
                        </div>
                    </div>
                    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('admin') }}">Home</a></li>

                            <li class="breadcrumb-item active"><a href="javascript:void(0);">Courier Man</a></li>
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
                                        @if(in_array('courier_man_add',$permission))
										<a href="{{ route('add-courier') }}" class="btn btn-primary">+ Add new</a>
                                        @else
                                        <a href="javascript:void(0);" onclick="access_alert()" class="btn btn-primary">+ Add new</a>
                                        @endif

									</div>
									<div class="card-body">
										<div class="table-responsive">
											<table id="example3" class="display" style="min-width: 845px">
												<thead>
													<tr>
														<th>#</th>
														<th>Name</th>
														<th>Mobile Number</th>
                                                        <th>User Document Front</th>
                                                        <th>User Document Back</th>
                                                        <th>User Image</th>
                                                        <th>Address</th>
                                                        <th>Area</th>
                                                        <th>Reference Name</th>
                                                        <th>Active Status</th>
                                                        <th>Action</th>


													</tr>
												</thead>
												<tbody>

                                                    @foreach($datas as $data)
													<tr>
													<?php
													$checked = $data->status=='1'?'checked':'';
													?>
														<td><strong>{{$data->sl_no}}</strong></td>
                                                        <td>{{$data->user->name}}</td>
                                                        <td>{{$data->user->contact_no}}</td>
                                                        <td><a href="../{{$data->personal_document_front }}" data-lightbox="image-1" > <img src='../{{$data->personal_document_front }}' width='100px' class='img-thumbnail' /></a></td>
                                                        <td><a href="../{{$data->personal_document_back }}" data-lightbox="image-1" > <img src='../{{$data->personal_document_back }}' width='100px' class='img-thumbnail' /></a></td>
                                                        {{-- <td><img id="myImg" src='../{{$data->personal_document }}' width='100px' class='img-thumbnail' /></td> --}}

                                                        <td><a href="../{{$data->user_image }}" data-lightbox="image-1" > <img  src='../{{$data->user_image }}' width='100px' class='img-thumbnail' /></a></td>

                                                        <td>{{ $data->address }}</td>

                                                        <td>{{ $data->area_name }}</td>
                                                        <td>{{ $data->reference_name }}</td>
														<td> <label class="switch">
															<input type="checkbox"  onclick="courier_man_active_status({{$data->id}})" {{$checked}}>
																<span class="slider round"></span>
															</label></td>
														<td>
                                                            <div class="btn-group">
                                                                <button id="btnGroupDrop1" type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">Action</button>
                                                                <div class="dropdown-menu"><a href="reset_courier_man_password/{{$data->id}}" class="dropdown-item">Reset Password</a>
                                                                    <a class="dropdown-item" href="edit_courierman_information/{{$data->id}}">Edit Information</a>
                                                                    <a class="dropdown-item" href="edit_courierman_area/{{$data->id}}">Edit Area</a>
                                                                    <a class="dropdown-item" href="edit_courierman_document/{{$data->id}}">Edit Document</a>
                                                                    <a class="dropdown-item" href="javascript:void()">Edit Image</a>
                                                                </div>
                                                            </div>
                                                        </td>


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
<script>
    $("#example3").DataTable({
       ordering: false

   });

</script>
<script src="{{asset('assets')}}/admin/js/admin.js?{{time()}}"></script>
<script src="{{asset('assets')}}/lightbox/js/lightbox.js?{{time()}}"></script>
@endsection
