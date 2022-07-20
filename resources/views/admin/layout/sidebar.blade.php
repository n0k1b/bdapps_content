<div class="dlabnav">


            <div class="dlabnav-scroll">
                <ul class="metismenu" id="menu">
                   
                <li><a class="ai-icon" href="{{url('admin')}}" aria-expanded="false">
                        <i class="la la-bar-chart"></i>
                        <span class="nav-text">Dashboard</span>
                    </a>
                </li>
            

			
					<li><a class="ai-icon" href="{{ route('show-all-apps') }}" aria-expanded="false">
							<i class="la la-list"></i>
							<span class="nav-text">Apps</span>
						</a>
					</li>
             

                

                <li><a class="has-arrow" href="javascript:void()" aria-expanded="false">
                    <i class="la la-shopping-cart"></i>
                    <span class="nav-text">Report</span>
                    </a>
                    <ul aria-expanded="false">

                        <li><a href="{{url('admin/report/order')}}">Order Report</a></li>
                        <li><a href="{{url('admin/report/courier_man')}}">Courier Man</a></li>
                        <li><a href="{{url('admin/report/user')}}">User Report</a></li>
                        {{-- <li><a href="#">Purchase Report</a></li>
                        <li><a href="#">Exense Report</a></li> --}}



                    </ul>
                </li>







				</ul>
            </div>
        </div>
