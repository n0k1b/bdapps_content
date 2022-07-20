@extends('admin.layout.app')
@section('page_css')

<link href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.css" rel="stylesheet">
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
                            <h4>{{ $warehouse_name }}</h4>
                        </div>
                    </div>

                </div>

				<div class="row">

					<div class="col-lg-12">
						<div class="row tab-content">
							<div id="list-view" class="tab-pane fade active show col-lg-12">
								<div class="card">

									<div class="card-body">
										<div class="table-responsive">
											<table id="example3" class="table" style="min-width: 845px">
												<thead>
													<tr>
														<th>#</th>


                                                        <th>Name</th>
														<th>Image</th>
														<th>Active Status</th>
													</tr>
												</thead>
												<tbody>

                                                    @foreach($products as $data)
													<tr>
													<?php
													$checked = $data->status=='1'?'checked':'';
													?>
														<td><strong>{{$data->sl_no}}</strong></td>


                                                        <td>{{ $data->product->name }}</td>

                                                        <td><img  height="80x" width="100" src="../{{$data->product->thumbnail_image}}"  alt="Not Available"></td>
														<td> <label class="switch">
															<input type="checkbox"  onclick="warehouse_product_active_status({{$data->id}})" {{$checked}}>
																<span class="slider round"></span>
															</label></td>



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

<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.js"></script>
<script type="text/javascript">

    $("#example3").DataTable({
        ordering: false

    });





  </script>
@endsection
