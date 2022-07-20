<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\category;
use App\Models\sub_category;
use App\Models\product;
use DB;
use DataTables;
use Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\order;
use App\Models\order_details;


class OrderController extends Controller
{
    //

    public function permission ()
    {

        $user_id = Auth::guard('admin')->user()->id;
        $user_role = Auth::guard('admin')->user()->role;
        $role_id = DB::table('roles')->where('name',$user_role)->first()->id;
        $role_permission = DB::table('role_permisiions')->where('role_id',$role_id)->pluck('content_name')->toArray();
        return $role_permission;

    }


    public function new_order()
    {
        $order = order::where('status','!=','delivered')->where('status','!=','canceled')->orderBy(DB::raw('case when status= "pending" then 1 when status= "picked" then 2 end'))->orderBy('created_at','DESC')->get();
        $i=1;

         foreach($order as $data)
        {


            $order_no = $data->order_no;
            $order_detail = order_details::where('order_no',$order_no)->get();

            $sub_total = 0;
            for($j=0;$j<sizeof($order_detail);$j++)
            {
                $sub_total+=$order_detail[$j]->price*$order_detail[$j]->count;

                // array_push($order_details,$order_)
            }
          //  $order_date =  date("d-m-Y h:i:s", strtotime($order[$i]->created_at));
          // array_push($order_list,['order_no'=>$order[$i]->order_no,'order_date'=>$order_date,'status'=>$order[$i]->status,'delivery_address'=>$order[$i]->address->address,'subtotal'=>$sub_total,'product'=>$order_details]);



            $data['sl_no'] = $i++;
            $data['total_bill'] = $sub_total;
        }

        return view('admin.order.new',['datas'=>$order]);
    }

       public function all_order()
    {
        $order = order::where('status','!=','pending')->where('status','!=','picked')->where('delete_status',0)->orderBy(DB::raw('case when status= "delivered" then 1 when status= "canceled" then 2 end'))->get();
        $i=1;

         foreach($order as $data)
        {


            $order_no = $data->order_no;
            $order_detail = order_details::where('order_no',$order_no)->get();

            $sub_total = 0;
            for($j=0;$j<sizeof($order_detail);$j++)
            {
                $sub_total+=$order_detail[$j]->price*$order_detail[$j]->count;

                // array_push($order_details,$order_)
            }
          //  $order_date =  date("d-m-Y h:i:s", strtotime($order[$i]->created_at));
          // array_push($order_list,['order_no'=>$order[$i]->order_no,'order_date'=>$order_date,'status'=>$order[$i]->status,'delivery_address'=>$order[$i]->address->address,'subtotal'=>$sub_total,'product'=>$order_details]);



            $data['sl_no'] = $i++;
            $data['total_bill'] = $sub_total;
        }

        return view('admin.order.all',['datas'=>$order]);
    }

    public function show_order_product(Request $request)
    {
        $order_no = $request->order_no;
        $order = order::where('order_no',$order_no)->first();
        $order_details = order_details::where('order_no',$order_no)->get();
        $i=1;
          foreach($order_details as $data)
        {
            $data['sl_no'] = $i++;
        }
       // file_put_contents('test.txt',$order_no." ".json_encode($order_details));
        return view('admin.order.show_product',['datas'=>$order_details,'order'=>$order]);
    }
    public function update_order_status(Request $request)
    {
        $order_id = $request->order_id;
        $order_status = $request->order_status;
        file_put_contents('test.txt',$order_id." ".$order_status);
        order::where('id',$order_id)->update(['status'=>$order_status]);
        return redirect()
        ->route('new-order')
        ->with('success', "Data Updated Successfully");
    }

    public function change_order_status(Request $request)
    {
        $order_id = $request->id;

        //$order = order::where('id',$order_id)->get();

       // file_put_contents('test.txt',$order_no." ".json_encode($order_details));
        return view('admin.order.update_order_status',['order_id'=>$order_id]);
    }

}
