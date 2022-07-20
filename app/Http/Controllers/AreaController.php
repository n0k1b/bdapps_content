<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\category;
use App\Models\sub_category;
use App\Models\product;
use App\Models\area;
use DB;
use DataTables;
use Auth;
use Illuminate\Support\Facades\Validator;


class AreaController extends Controller
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

    public function show_all_area()
    {
        $datas = area::where('delete_status',0)->get();
        $i=1;
        foreach($datas as $data)
        {
            $data['sl_no'] = $i++;
        }
        $permission = $this->permission();
        return view('admin.area.all',['datas'=>$datas,'role_permission'=>$permission]);


    }

    public function add_area_ui()
    {
        $datas = area::get();
        return view('admin.area.add',compact('datas'));
    }
    public function add_area(Request $request)
    {
    //     $validator = Validator::make($request->all(), [
    //         'mobile_number' => ['required', 'regex:/01[13-9]\d{8}$/'],
    //      ]);
    // if($validator->fails())
    // {
    //     return redirect()->back()->with('errors',collect($validator->errors()->all()));
    // }
       area::create($request->all());
        return redirect()->route('show-all-area')->with('success','area Added Successfully');


    }

    public function area_active_status_update(Request $request)
    {
        $id = $request->id;
        $status =area::where('id', $id)->first()->status;
        if ($status == 1)
        {
            area::where('id', $id)->update(['status' => 0]);
        }
        else
        {
            area::where('id', $id)->update(['status' => 1]);
        }
    }
    public function edit_area_content_ui(Request $request)
    {
        $id = $request->id;
        $data = area::where('id',$id)->first();
        //$areas = area::where('status',1)->get();
        return view('admin.area.edit_content',['data'=>$data]);

    }

    public function update_area_content(Request $request)
    {
        $id = $request->id;

        area::where('id', $id)->update(['name' => $request->name]);

        return redirect()
            ->route('show-all-area')
            ->with('success', "Data Updated Successfully");
    }


    public function area_content_delete(Request $request)
    {
        $id = $request->id;
        area::where('id', $id)->update(['delete_status'=>1]);

    }


}
