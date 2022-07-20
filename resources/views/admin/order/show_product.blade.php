@extends('admin.layout.app')
@section('page_css')

    <link href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css" rel="stylesheet">
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
                            <h4>Orderd Product</h4>
                        </div>
                    </div>
                    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('admin') }}">Home</a></li>

                            <li class="breadcrumb-item active"><a href="javascript:void(0);">Ordered Product</a></li>
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

									</div>
									<div class="card-body">
										<div class="table-responsive">
											<table id="example3" class="display table table-bordered table-striped" style="min-width: 845px">
    											<thead  class="thead-dark">
													<tr>
														<th>#</th>

														<th>Product Name</th>
														<th>Quantity</th>
                                                        <th>Unit Price</th>
                                                        <th>Total Price</th>


													</tr>
												</thead>
												<tbody>
                                                    <?php
                                                        $total = 0;

                                                    ?>
                                                    @foreach($datas as $data)

														<td><strong>{{$data->sl_no}}</strong></td>

														<td>{{$data->product->name}}</td>

														<td>{{ $data->count }} x {{$data->unit_quantity}}</td>
                                                        <td>{{ $data->price }}</td>
                                                        <td>{{ $data->count*$data->price }}</td>



													</tr>

                                                    <?php
                                                        $total+= $data->count* $data->price;
                                                    ?>
												@endforeach


												</tbody>
                                                <tfoot class="thead-light">
                                                    <tr>
                                                    <th colspan="3"></th>
                                                    <th style="text-center">Total</th>

                                                    <th>{{ $total }}</th>
                                                    </tr>
                                                </tfoot>
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

<script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.print.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>

<script>

    $("#example3").DataTable({
       ordering: false,
       dom: 'Bfrtip',
        buttons: [
            { extend: 'print', footer: true,  stripHtml:false,

            title: 'Invoice',
             messageTop:"<b>Order Number</b>: "+"{{ $order->order_no }}"+'<br>'+
             "<b>Customer Name</b>: "+"{{ $order->user->name }}"+'<br>'+
            "<b>Contact No</b>: "+ "{{ $order->user->contact_no }}"+'<br>'+
            "<b>Address</b>: "+"{{ $order->address->address }}"+','+"{{ $order->address->area->name }}"+'<br>'+
            "<b>Deliver date & time</b>: "+"{{ $order->delivery_date }}"+' '+"{{ $order->delivery_time }}<br>"


            ,
            customize: function ( win ) {

                        $(win.document.body).find('h1').css('text-align', 'center').css('font-size','25px');
                        $(win.document.body).find('th').css('font-size', '18px');
                        $(win.document.body).find('th').css('padding', '10px');
                        $(win.document.body).css( 'font-size', '20px' );

                        $(win.document.body).find( 'table' )
                        .addClass( 'compact' )
                        .css( 'font-size', 'inherit' )
                        .css('text-align','center');
                }


             },
            { extend: 'pdfHtml5',
                footer: true,



                customize : function(doc){
                    doc.styles.tableHeader.alignment = 'left';
                    doc.content[1].table.widths = [20,'*','*','*','*'];


                }


            }
        ]

   });
</script>




@endsection
