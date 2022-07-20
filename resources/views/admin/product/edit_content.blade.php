@extends('admin.layout.app')
@section('content')
<div class="container-fluid">

				<div class="row page-titles mx-0">
                    <div class="col-sm-6 p-md-0">
                        <div class="welcome-text">
                            <h4>Edit category</h4>
                        </div>
                    </div>
                    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                            <li class="breadcrumb-item active"><a href="javascript:void(0);">Homepage Content</a></li>
                            <li class="breadcrumb-item active"><a href="javascript:void(0);">Add Content</a></li>
                        </ol>
                    </div>
                </div>

				<div class="row">
					<div class="col-xl-12 col-xxl-12 col-sm-12">
                        <div class="card">

							<div class="card-body">
                                <form action="{{route('update_category_with_domain_content')}}" method="post" enctype="multipart/form-data">
                                @csrf
									<div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                            <div class="form-group">
                                                <label>Category Name</label>
                                                <select class="form-control" id="sel1" name="domain_id">
                                                    @foreach($datas as $data)
                                                    @if($data->id == $content->domain_id)
                                                    <option value="{{$data->id}}" selected>{{$data->name}}</option>
                                                    @else
                                                    <option value="{{$data->id}}">{{$data->name}}</option>
                                                    @endif
                                                   @endforeach
                                                </select>
                                            </div>
                                        </div>


										<div class="col-lg-12 col-md-12 col-sm-12">
											<div class="form-group">
												<label class="form-label">Sub Category Name</label>
                                                <input type="text" class="form-control" name="name" value="{{ $content->name }}" >
                                                <input type="hidden" name='id'  value="{{ $content->id }}">
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
