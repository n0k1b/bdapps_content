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
use App\Models\warehouse;
use App\Models\warehouse_product;
use App\Models\area;


class WarehouseController extends Controller
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

    public function show_all_warehouse()
    {
        $datas = warehouse::where('delete_status',0)->get();
        $i=1;
        foreach($datas as $data)
        {
            $data['sl_no'] = $i++;
        }
        $permission = $this->permission();
        return view('admin.warehouse.all',['datas'=>$datas,'permission'=>$permission]);


    }

    public function warehouse_product_active_status(Request $request)
    {
        $id = $request->id;
        $status =warehouse_product::where('id', $id)->first()->status;
        if ($status == 1)
        {
            warehouse_product::where('id', $id)->update(['status' => 0]);
        }
        else
        {
            warehouse_product::where('id', $id)->update(['status' => 1]);
        }
    }

    public function show_warehouse_product(Request $request)
    {
        $warehouse_id = $request->warehouse_id;
        $products = warehouse_product::where('warehouse_id',$warehouse_id)->get();
        $warehouse_name = warehouse::where('id',$warehouse_id)->first()->name;
        $i=1;
        foreach($products as $data)
        {
            $data['sl_no'] = $i++;
        }
        return view('admin.warehouse.product',compact('products','warehouse_name'));
    }


    public function add_warehouse_ui()
    {
        $datas = area::where('status',1)->get();
        return view('admin.warehouse.add',compact('datas'));
    }
    public function add_warehouse(Request $request)
    {
        $rules = [

            'area_id'=>'required',
            'name'=>'required',
            'address'=>'required',
            'contact_no'=>'required|regex:/01[13-9]\d{8}$/',



        ];
    $customMessages = [

        'area_id.required'=>'Area field is required',
        'name.required'=>'Name field is required',
        'address.required'=>'Address field is required',
        'contact_no.required'=>'Contact no field is required',
        'contact_no.regex'=>'Please enter a valid mobile number',




    ];

    $validator = Validator::make( $request->all(), $rules, $customMessages );


    if($validator->fails())
    {
        return redirect()->back()->withInput()->with('errors',collect($validator->errors()->all()));
    }
       warehouse::create($request->all());
        return redirect()->route('show-all-warehouse')->with('success','warehouse Added Successfully');


    }

    public function warehouse_active_status_update(Request $request)
    {
        $id = $request->id;
        $status =warehouse::where('id', $id)->first()->status;
        if ($status == 1)
        {
            warehouse::where('id', $id)->update(['status' => 0]);
        }
        else
        {
            warehouse::where('id', $id)->update(['status' => 1]);
        }
    }
    public function edit_warehouse_content_ui(Request $request)
    {
        $id = $request->id;
        $data = warehouse::where('id',$id)->first();
        $areas = area::where('status',1)->get();
        return view('admin.warehouse.edit',['data'=>$data,'areas'=>$areas]);

    }

    public function update_warehouse_content(Request $request)
    {
        $id = $request->id;

        warehouse::where('id', $id)->update(['area_id'=>$request->area_id,'name' => $request->name,'contact_no'=>$request->contact_no,'address'=>$request->address]);

        return redirect()
            ->route('show-all-warehouse')
            ->with('success', "Data Updated Successfully");
    }


    public function warehouse_content_delete(Request $request)
    {
        $id = $request->id;
        $warehouse_status = warehouse_product::where('warehouse_id',$id)->where('delete_status',0)->first();
        if($warehouse_status)
        {
            echo "warehouse_exist";
        }
        else
        {
        warehouse::where('id', $id)->update(['delete_status'=>1]);
        }

    }

}
