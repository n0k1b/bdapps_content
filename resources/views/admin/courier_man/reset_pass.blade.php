@extends('admin.layout.app')
@section('content')
<div class="container-fluid">

    @if (count($errors)>0)
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
                            <h4>Reset Password</h4>
                        </div>
                    </div>
                    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ 'admin' }}">Home</a></li>
                            <li class="breadcrumb-item active"><a href="javascript:void(0);">Courier Man</a></li>
                            <li class="breadcrumb-item active"><a href="javascript:void(0);">Reset Password</a></li>
                        </ol>
                    </div>
                </div>

				<div class="row">
					<div class="col-xl-12 col-xxl-12 col-sm-12">
                        <div class="card">

							<div class="card-body">
                                <form action="{{route('update_password')}}" method="post" enctype="multipart/form-data">
                                @csrf
									<div class="row">
										<div class="col-lg-12 col-md-12 col-sm-12">
                                            <div class="form-group">
                                                <label class="form-label">Password</label>
                                                <input type="hidden" name="id" value="{{ $data->id }}">
                                                <input type="password" class="form-control" name="password">
                                            </div>
										</div>

                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                            <div class="form-group">
                                                <label class="form-label">Re type password</label>

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
