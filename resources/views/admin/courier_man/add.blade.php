@extends('admin.layout.app')
@section('page_css')
<link rel="stylesheet" href="{{asset('assets')}}/admin/css/image_preview.css?{{time()}}">
<link rel="stylesheet" href="{{asset('assets')}}/admin/css/select2.min.css?{{time()}}" />
<link rel="stylesheet" href="{{asset('assets')}}/admin/css/select2_custom.css?{{time()}}" />
<link href="{{asset('assets')}}/admin/vendor/bootstrap-select/dist/css/bootstrap-select.min.css" rel="stylesheet">
@endsection
@section('content')
<div class="container-fluid">
    @if (count($errors)>0)
    <div class="col-md-10 col-sm-10 col-10 offset-md-1 offset-sm-10 alert alert-danger" >
        <ul>
            @foreach($errors->all() as $error)
                <li style="display: list-item;list-style-type:disc">{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif


				<div class="row page-titles mx-0">
                    <div class="col-sm-6 p-md-0">
                        <div class="welcome-text">
                            <h4>Add Courier Man</h4>
                        </div>
                    </div>
                    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('admin') }}">Home</a></li>

                            <li class="breadcrumb-item active"><a href="javascript:void(0);">Add Courier Man</a></li>
                        </ol>
                    </div>
                </div>

				<div class="row">
					<div class="col-xl-12 col-xxl-12 col-sm-12">
                        <div class="card">

							<div class="card-body">
                                <form action="{{route('add-courier')}}" method="post" enctype="multipart/form-data">
                                @csrf
									<div class="row">


									 <div class="col-lg-12 col-md-12 col-sm-12">
											<div class="form-group">
												<label class="form-label">Name</label>
												<input type="text" class="form-control" name="name" placeholder="User Name" value="{{ old('name') }}">
											</div>
									    </div>

                                        <div class="col-lg-12 col-md-12 col-sm-12">
											<div class="form-group">
												<label class="form-label">Mobile</label>
												<input type="text" class="form-control" name="contact_no" placeholder="018..." value="{{ old('contact_no') }}">
											</div>
									    </div>
                                        <div class="col-lg-12 col-md-12 col-sm-12">
											<div class="form-group">
												<label class="form-label">Reference Name</label>
												<input type="text" class="form-control" name="reference_name" placeholder="Reference Name" value="{{ old('reference_name') }}">
											</div>
									    </div>

                                        <div class="col-lg-12 col-md-12 col-sm-12">
											<div class="form-group">
												<label class="form-label">Address</label>
												<input type="text" class="form-control" name="address" placeholder="Address" value="{{ old('address') }}">
											</div>
									    </div>

                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                            <div class="form-group">
                                                <label>Area</label>
                                                <select  multiple class="form-control" id="sel1" name="area_id[]">

                                                    @foreach($areas as $data)
                                                    <option value="{{$data->id}}">{{$data->name}}</option>
                                                   @endforeach
                                                </select>
                                        </div>
									    </div>

                                        <div class="col-lg-12 col-md-12 col-sm-12">
											<div class="form-group">
												<div class="field" align="left">
													<label class="form-label">User Document Front(NID/Birth Certificate)</label>
													<input type="file" id="files" name="personal_document_front" />
												  </div>
											</div>
										</div>


                                        <div class="col-lg-12 col-md-12 col-sm-12">
											<div class="form-group">
												<div class="field" align="left">
													<label class="form-label">User Document Back(NID/Birth Certificate)</label>
													<input type="file" id="files3" name="personal_document_back" />
												  </div>
											</div>
										</div>

                                        <div class="col-lg-12 col-md-12 col-sm-12">
											<div class="form-group">
												<div class="field" align="left">
													<label class="form-label">User Image</label>
													<input type="file" id="files2" name="user_image" />
												  </div>
											</div>
										</div>

                                        <div class="col-lg-12 col-md-12 col-sm-12">
											<div class="form-group">
												<label class="form-label">Default Password</label>
												<input type="password" class="form-control" name="password">
											</div>
									    </div>

                                        <div class="col-lg-12 col-md-12 col-sm-12">
											<div class="form-group">
												<label class="form-label">Retype Password</label>
												<input type="password" class="form-control" name="password_confirmation">
											</div>
									    </div>















										<div class="col-lg-12 col-md-12 col-sm-12">
											<button type="submit" class="btn btn-primary">Submit</button>
											<button type="submit" class="btn btn-light">Cancel</button>
										</div>
									</div>
								</form>
                            </div>
                        </div>
                    </div>
				</div>

            </div>
@endsection

@section('page_js')
<script src="{{asset('assets')}}/admin/js/single_image_preview.js?{{time()}}"></script>
<script src="{{ asset('assets') }}/admin/vendor/bootstrap-select/dist/js/bootstrap-select.min.js"></script>
<script src="{{asset('assets')}}/admin/js/select2.full.js"></script>
<script src="{{asset('assets')}}/admin/js/advanced-form-element.js"></script>
@endsection
