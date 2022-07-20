@extends('admin.layout.app')
@section('page_css')
<link rel="stylesheet" href="{{asset('assets')}}/admin/css/image_preview.css?{{time()}}">
@endsection
@section('content')
<div class="container-fluid">

				<div class="row page-titles mx-0">
                    <div class="col-sm-6 p-md-0">
                        <div class="welcome-text">
                            <h4>Company Info</h4>
                        </div>
                    </div>
                    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Home</a></li>

                            <li class="breadcrumb-item active"><a href="javascript:void(0);">Add Category</a></li>
                        </ol>
                    </div>
                </div>

				<div class="row">
					<div class="col-xl-12 col-xxl-12 col-sm-12">
                        <div class="card">

							<div class="card-body">
                                <form action="{{route('add-company-info')}}" method="post" enctype="multipart/form-data">
                                @csrf
									<div class="row">


									 <div class="col-lg-12 col-md-12 col-sm-12">
											<div class="form-group">
												<label class="form-label">Organization Name</label>
												<input type="text" class="form-control" name="company_name" value="{{ $data->company_name ?? '' }}">
											</div>
										</div>

                                        <div class="col-lg-12 col-md-12 col-sm-12">
											<div class="form-group">
												<label class="form-label">Organization Contact No 1</label>
												<input type="text" class="form-control" name="contact_no1" value="{{ $data->contact_no1 ?? '' }}" }}>
											</div>
										</div>

                                        <div class="col-lg-12 col-md-12 col-sm-12">
											<div class="form-group">
												<label class="form-label">Organization Contact No 2</label>
												<input type="text" class="form-control" name="contact_no2" value="{{ $data->contact_no2 ?? '' }}">
											</div>
										</div>



                                        <div class="col-lg-12 col-md-12 col-sm-12">
											<div class="form-group">
												<label class="form-label">Organization Email</label>
												<input type="text" class="form-control" name="email" value="{{ $data->email ?? '' }}">
											</div>
										</div>



                                        <div class="col-lg-12 col-md-12 col-sm-12">
											<div class="form-group">
												<label class="form-label">Organization Address</label>
												<input type="text" class="form-control" name="address" value="{{ $data->address ?? '' }}">
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
@endsection
