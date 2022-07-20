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
use App\Models\role;
use App\Models\role_permisiion;

class RoleController extends Controller
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

    public function show_all_role()
    {
        $datas = role::where('delete_status',0)->get();
        $i=1;
        foreach($datas as $data)
        {
            $data['sl_no'] = $i++;
        }
        return view('admin.role.all',['datas'=>$datas]);


    }

    public function add_role_ui()
    {
        $datas = role::get();
        return view('admin.role.add',compact('datas'));
    }
    public function add_role(Request $request)
    {
    //     $validator = Validator::make($request->all(), [
    //         'mobile_number' => ['required', 'regex:/01[13-9]\d{8}$/'],
    //      ]);
    // if($validator->fails())
    // {
    //     return redirect()->back()->with('errors',collect($validator->errors()->all()));
    // }
       role::create($request->all());
        return redirect()->route('show-all-role')->with('success','role Added Successfully');


    }

    public function role_active_status_update(Request $request)
    {
        $id = $request->id;
        $status =role::where('id', $id)->first()->status;
        if ($status == 1)
        {
            role::where('id', $id)->update(['status' => 0]);
        }
        else
        {
            role::where('id', $id)->update(['status' => 1]);
        }
    }
    public function edit_role_content_ui(Request $request)
    {
        $id = $request->id;
        $data = role::where('id',$id)->first();
        //$areas = area::where('status',1)->get();
        return view('admin.role.edit_content',['data'=>$data]);

    }

    public function update_role_content(Request $request)
    {
        $id = $request->id;

        role::where('id', $id)->update(['name' => $request->name,'description'=>$request->description]);

        return redirect()
            ->route('show-all-role')
            ->with('success', "Data Updated Successfully");
    }


    public function role_content_delete(Request $request)
    {
        $id = $request->id;
        role::where('id', $id)->update(['delete_status'=>1]);

    }

    public function set_permission(Request $request)
    {
        $role_id = $request->role_id;
        //file_put_contents('test.txt',$role_id);

        if($request->has('category_view'))
        {
            role_permisiion::updateOrCreate(['content_name'=>'category_view','role_id'=>$role_id]);
        }
        else
        {
            role_permisiion::where('content_name','category_view')->where('role_id',$role_id)->delete();
        }

        if($request->has('category_add'))
        {
            role_permisiion::updateOrCreate(['content_name'=>'category_add','role_id'=>$role_id]);
        }
        else
        {
            role_permisiion::where('content_name','category_add')->where('role_id',$role_id)->delete();
        }


        if($request->has('category_edit'))
        {
            role_permisiion::updateOrCreate(['content_name'=>'category_edit','role_id'=>$role_id]);
        }
        else
        {
            role_permisiion::where('content_name','category_edit')->where('role_id',$role_id)->delete();
        }

        if($request->has('category_delete'))
        {
            role_permisiion::updateOrCreate(['content_name'=>'category_delete','role_id'=>$role_id]);
        }
        else
        {
            role_permisiion::where('content_name','category_delete')->where('role_id',$role_id)->delete();
        }

        if($request->has('sub_category_view'))
        {
            role_permisiion::updateOrCreate(['content_name'=>'sub_category_view','role_id'=>$role_id]);
        }
        else
        {
            role_permisiion::where('content_name','sub_category_view')->where('role_id',$role_id)->delete();
        }

        if($request->has('sub_category_add'))
        {
            role_permisiion::updateOrCreate(['content_name'=>'sub_category_add','role_id'=>$role_id]);
        }
        else
        {
            role_permisiion::where('content_name','sub_category_add')->where('role_id',$role_id)->delete();
        }

        if($request->has('sub_category_edit'))
        {
            role_permisiion::updateOrCreate(['content_name'=>'sub_category_edit','role_id'=>$role_id]);
        }
        else
        {
            role_permisiion::where('content_name','sub_category_edit')->where('role_id',$role_id)->delete();
        }

        if($request->has('sub_category_delete'))
        {
            role_permisiion::updateOrCreate(['content_name'=>'sub_category_delete','role_id'=>$role_id]);
        }
        else
        {
            role_permisiion::where('content_name','sub_category_delete')->where('role_id',$role_id)->delete();
        }

        if($request->has('product_view'))
        {
            role_permisiion::updateOrCreate(['content_name'=>'product_view','role_id'=>$role_id]);
        }
        else
        {
            role_permisiion::where('content_name','product_view')->where('role_id',$role_id)->delete();
        }

        if($request->has('product_add'))
        {
            role_permisiion::updateOrCreate(['content_name'=>'product_add','role_id'=>$role_id]);
        }
        else
        {
            role_permisiion::where('content_name','product_add')->where('role_id',$role_id)->delete();
        }

        if($request->has('product_edit'))
        {
            role_permisiion::updateOrCreate(['content_name'=>'product_edit','role_id'=>$role_id]);
        }
        else
        {
            role_permisiion::where('content_name','product_edit')->where('role_id',$role_id)->delete();
        }

        if($request->has('product_delete'))
        {
            role_permisiion::updateOrCreate(['content_name'=>'product_delete','role_id'=>$role_id]);
        }
        else
        {
            role_permisiion::where('content_name','product_delete')->where('role_id',$role_id)->delete();
        }

        if($request->has('homepage_content_view'))
        {
            role_permisiion::updateOrCreate(['content_name'=>'homepage_content_view','role_id'=>$role_id]);
        }
        else
        {
            role_permisiion::where('content_name','homepage_content_view')->where('role_id',$role_id)->delete();
        }

        if($request->has('homepage_content_add'))
        {
            role_permisiion::updateOrCreate(['content_name'=>'homepage_content_add','role_id'=>$role_id]);
        }
        else
        {
            role_permisiion::where('content_name','homepage_content_add')->where('role_id',$role_id)->delete();
        }

        if($request->has('homepage_content_edit'))
        {
            role_permisiion::updateOrCreate(['content_name'=>'homepage_content_edit','role_id'=>$role_id]);
        }
        else
        {
            role_permisiion::where('content_name','homepage_content_edit')->where('role_id',$role_id)->delete();
        }

        if($request->has('homepage_content_delete'))
        {
            role_permisiion::updateOrCreate(['content_name'=>'homepage_content_delete','role_id'=>$role_id]);
        }
        else
        {
            role_permisiion::where('content_name','homepage_content_delete')->where('role_id',$role_id)->delete();
        }

        if($request->has('banner_view'))
        {
            role_permisiion::updateOrCreate(['content_name'=>'banner_view','role_id'=>$role_id]);
        }
        else
        {
            role_permisiion::where('content_name','banner_view')->where('role_id',$role_id)->delete();
        }

        if($request->has('banner_add'))
        {
            role_permisiion::updateOrCreate(['content_name'=>'banner_add','role_id'=>$role_id]);
        }
        else
        {
            role_permisiion::where('content_name','banner_add')->where('role_id',$role_id)->delete();
        }

        if($request->has('banner_edit'))
        {
            role_permisiion::updateOrCreate(['content_name'=>'banner_edit','role_id'=>$role_id]);
        }
        else
        {
            role_permisiion::where('content_name','banner_edit')->where('role_id',$role_id)->delete();
        }

        if($request->has('banner_delete'))
        {
            role_permisiion::updateOrCreate(['content_name'=>'banner_delete','role_id'=>$role_id]);
        }
        else
        {
            role_permisiion::where('content_name','banner_delete')->where('role_id',$role_id)->delete();
        }

        if($request->has('new_order_view'))
        {
            role_permisiion::updateOrCreate(['content_name'=>'new_order_view','role_id'=>$role_id]);
        }
        else
        {
            role_permisiion::where('content_name','new_order_view')->where('role_id',$role_id)->delete();
        }

        if($request->has('new_order_add'))
        {
            role_permisiion::updateOrCreate(['content_name'=>'new_order_add','role_id'=>$role_id]);
        }
        else
        {
            role_permisiion::where('content_name','new_order_add')->where('role_id',$role_id)->delete();
        }

        if($request->has('new_order_edit'))
        {
            role_permisiion::updateOrCreate(['content_name'=>'new_order_edit','role_id'=>$role_id]);
        }
        else
        {
            role_permisiion::where('content_name','new_order_edit')->where('role_id',$role_id)->delete();
        }
        if($request->has('new_order_delete'))
        {
            role_permisiion::updateOrCreate(['content_name'=>'new_order_delete','role_id'=>$role_id]);
        }
        else
        {
            role_permisiion::where('content_name','new_order_delete')->where('role_id',$role_id)->delete();
        }

        if($request->has('all_order_view'))
        {
            role_permisiion::updateOrCreate(['content_name'=>'all_order_view','role_id'=>$role_id]);
        }
        else
        {
            role_permisiion::where('content_name','all_order_view')->where('role_id',$role_id)->delete();
        }

        if($request->has('all_order_add'))
        {
            role_permisiion::updateOrCreate(['content_name'=>'all_order_add','role_id'=>$role_id]);
        }
        else
        {
            role_permisiion::where('content_name','all_order_add')->where('role_id',$role_id)->delete();
        }

        if($request->has('all_order_edit'))
        {
            role_permisiion::updateOrCreate(['content_name'=>'all_order_edit','role_id'=>$role_id]);
        }
        else
        {
            role_permisiion::where('content_name','all_order_edit')->where('role_id',$role_id)->delete();
        }

        if($request->has('all_order_delete'))
        {
            role_permisiion::updateOrCreate(['content_name'=>'all_order_delete','role_id'=>$role_id]);
        }
        else
        {
            role_permisiion::where('content_name','all_order_delete')->where('role_id',$role_id)->delete();
        }

        if($request->has('courier_man_view'))
        {
            role_permisiion::updateOrCreate(['content_name'=>'courier_man_view','role_id'=>$role_id]);
        }
        else
        {
            role_permisiion::where('content_name','courier_man_view')->where('role_id',$role_id)->delete();
        }


        if($request->has('courier_man_add'))
        {
            role_permisiion::updateOrCreate(['content_name'=>'courier_man_add','role_id'=>$role_id]);
        }
        else
        {
            role_permisiion::where('content_name','courier_man_add')->where('role_id',$role_id)->delete();
        }

        if($request->has('courier_man_edit'))
        {
            role_permisiion::updateOrCreate(['content_name'=>'courier_man_edit','role_id'=>$role_id]);
        }
        else
        {
            role_permisiion::where('content_name','courier_man_edit')->where('role_id',$role_id)->delete();
        }

        if($request->has('courier_man_delete'))
        {
            role_permisiion::updateOrCreate(['content_name'=>'courier_man_delete','role_id'=>$role_id]);
        }
        else
        {
            role_permisiion::where('content_name','courier_man_delete')->where('role_id',$role_id)->delete();
        }

        if($request->has('area_view'))
        {
            role_permisiion::updateOrCreate(['content_name'=>'area_view','role_id'=>$role_id]);
        }
        else
        {
            role_permisiion::where('content_name','area_view')->where('role_id',$role_id)->delete();
        }


        if($request->has('area_add'))
        {
            role_permisiion::updateOrCreate(['content_name'=>'area_add','role_id'=>$role_id]);
        }
        else
        {
            role_permisiion::where('content_name','area_add')->where('role_id',$role_id)->delete();
        }

        if($request->has('area_edit'))
        {
            role_permisiion::updateOrCreate(['content_name'=>'area_edit','role_id'=>$role_id]);
        }
        else
        {
            role_permisiion::where('content_name','area_edit')->where('role_id',$role_id)->delete();
        }


        if($request->has('area_delete'))
        {
            role_permisiion::updateOrCreate(['content_name'=>'area_delete','role_id'=>$role_id]);
        }
        else
        {
            role_permisiion::where('content_name','area_delete')->where('role_id',$role_id)->delete();
        }


        if($request->has('warehouse_view'))
        {
            role_permisiion::updateOrCreate(['content_name'=>'warehouse_view','role_id'=>$role_id]);
        }
        else
        {
            role_permisiion::where('content_name','warehouse_view')->where('role_id',$role_id)->delete();
        }


        if($request->has('warehouse_add'))
        {
            role_permisiion::updateOrCreate(['content_name'=>'warehouse_add','role_id'=>$role_id]);
        }
        else
        {
            role_permisiion::where('content_name','warehouse_add')->where('role_id',$role_id)->delete();
        }


        if($request->has('warehouse_edit'))
        {
            role_permisiion::updateOrCreate(['content_name'=>'warehouse_edit','role_id'=>$role_id]);
        }
        else
        {
            role_permisiion::where('content_name','warehouse_edit')->where('role_id',$role_id)->delete();
        }

        if($request->has('warehouse_delete'))
        {
            role_permisiion::updateOrCreate(['content_name'=>'warehouse_delete','role_id'=>$role_id]);
        }
        else
        {
            role_permisiion::where('content_name','warehouse_delete')->where('role_id',$role_id)->delete();
        }


        if($request->has('delivery_charge_view'))
        {
            role_permisiion::updateOrCreate(['content_name'=>'delivery_charge_view','role_id'=>$role_id]);
        }
        else
        {
            role_permisiion::where('content_name','delivery_charge_view')->where('role_id',$role_id)->delete();
        }


        if($request->has('delivery_charge_add'))
        {
            role_permisiion::updateOrCreate(['content_name'=>'delivery_charge_add','role_id'=>$role_id]);
        }
        else
        {
            role_permisiion::where('content_name','delivery_charge_add')->where('role_id',$role_id)->delete();
        }

        if($request->has('delivery_charge_edit'))
        {
            file_put_contents('test2.txt','delivery charge');
            role_permisiion::updateOrCreate(['content_name'=>'delivery_charge_edit','role_id'=>$role_id]);
        }
        else
        {
            role_permisiion::where('content_name','delivery_charge_edit')->where('role_id',$role_id)->delete();
        }

        if($request->has('delivery_charge_delete'))
        {
            role_permisiion::updateOrCreate(['content_name'=>'delivery_charge_delete','role_id'=>$role_id]);
        }
        else
        {
            role_permisiion::where('content_name','delivery_charge_delete')->where('role_id',$role_id)->delete();
        }


        if($request->has('dashboard_view'))
        {
            role_permisiion::updateOrCreate(['content_name'=>'dashboard_view','role_id'=>$role_id]);
        }
        else
        {
            role_permisiion::where('content_name','dashboard_view')->where('role_id',$role_id)->delete();
        }

        return redirect()->route('show-all-role')->with('success','Permission Updated Successfully');






    }

    //role end
    //permission start
    public function show_permission_form(Request $request)
    {
        $id = $request->id;
        $role_permission = role_permisiion::where('role_id',$id)->pluck('content_name')->toArray();
       // file_put_contents('test.txt',json_encode($role_permission));

        return view('admin.role.permission',compact('role_permission','id'));
    }


}
