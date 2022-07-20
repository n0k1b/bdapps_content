<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\domain;
use App\Models\category;
use App\Models\sub_category;
use App\Models\product;
use App\Models\product_brand;
use App\Models\product_color;
use App\Models\product_size;
use App\Models\product_unit;
use App\Models\product_stock;
use App\Models\product_detail_image;
use App\Models\homepage_section;
use App\Models\homepage_product_list;
use App\Models\banner;
use App\Models\order;
use App\Models\order_details;
use App\Models\user;
use App\Models\courier_man;
use App\Models\warehouse;
use App\Models\warehouse_product;
use App\Models\area;
use App\Models\company_info;
use App\Models\delivery_charge;
use Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\role;
use App\Models\role_permisiion;

use DB;
use DataTables;
use Auth;

use App\Models\product_required_filed;

class AdminController extends Controller
{
    
    //login start
    public function login(Request $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {

            return redirect('admin');

         }
         else
         {
            return back()->with('error','Email and Password not match');
         }
    }

    public function logout()
    {
        auth()->logout();
        return redirect()->to('admin_login');
    }

    //login end
    
    //role start
      public function show_permission_form(Request $request)
    {
        $id = $request->id;
        $role_permission = role_permisiion::where('role_id',$id)->pluck('content_name')->toArray();
       // file_put_contents('test.txt',json_encode($role_permission));

        return view('admin.role.permission',compact('role_permission','id'));
    }
    
    public function show_all_role()
    {
        $datas = role::get();
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
        role::where('id', $id)->delete();

    }

    public function set_permission(Request $request)
    {
        $role_id = $request->role_id;
        file_put_contents('test.txt',$role_id);

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
    
    
     //User Start
    public function show_all_user()
    {
        $datas = user::where('role','<>','')->where('role','<>','courier_man')->get();
        $i=1;
        foreach($datas as $data)
        {
            $data['sl_no'] = $i++;
        }
        return view('admin.user.all',['datas'=>$datas]);


    }

    public function add_user_ui()
    {
        $datas = role::get();
        return view('admin.user.add',compact('datas'));
    }
    public function add_user(Request $request)
    {
    //     $validator = Validator::make($request->all(), [
    //         'mobile_number' => ['required', 'regex:/01[13-9]\d{8}$/'],
    //      ]);
    // if($validator->fails())
    // {
    //     return redirect()->back()->with('errors',collect($validator->errors()->all()));
    // }
        $user = new user();
        $user->name = $request->name;
        $user->contact_no = $request->contact_no;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->role = $request->role;;
        $user->save();


        return redirect()->route('show-all-user')->with('success','User Added Successfully');


    }

    public function user_active_status(Request $request)
    {
        $id = $request->id;
        $status =user::where('id', $id)->first()->status;
        if ($status == 1)
        {
            user::where('id', $id)->update(['status' => 0]);
        }
        else
        {
            user::where('id', $id)->update(['status' => 1]);
        }
    }
    public function edit_user_content_ui(Request $request)
    {
        $id = $request->id;
        $data = user::where('id',$id)->first();
        $datas = role::get();
        return view('admin.user.edit',['data'=>$data,'datas'=>$datas]);

    }

    public function update_user_content(Request $request)
    {
        $id = $request->id;
      //  file_put_contents('test.txt',$request->name." ".$request->contact_no." ".$request->email." ".$request->role);

        user::where('id', $id)->update(['name' => $request->name,'contact_no'=>$request->contact_no,'email'=>$request->email,'role'=>$request->role]);

        return redirect()
            ->route('show-all-user')
            ->with('success', "Data Updated Successfully");
    }


    public function user_content_delete(Request $request)
    {
        $id = $request->id;
        user::where('id', $id)->delete();

    }
    //User End


    //dashboard start
    public function show_dashboard()
    {
        $total_product = product::where('status',1)->get()->count();
        $total_category = category::where('status',1)->get()->count();
        $total_sub_category = sub_category::where('status',1)->get()->count();
        $total_order = order::get()->count();
        return view('admin.dashboard.index',compact('total_product','total_category','total_sub_category','total_order'));

    }


    //dashboard end

    //domain start
    public function show_all_domain()
    {
        $datas = domain::get();
        $i=1;
        foreach($datas as $data)
        {
            $data['sl_no'] = $i++;
        }
        return view('admin.domain.all_domain',['datas'=>$datas]);


    }

    public function add_domain_ui()
    {
        return view('admin.domain.add_domain');
    }
    public function add_domain(Request $request)
    {
        if($request->image)
        {
        $image = time() . '.' . request()->image->getClientOriginalExtension();

    $request
        ->image
        ->move(public_path('../image/domain_image') , $image);
    $image = "image/domain_image/" . $image;
    domain::create(['name'=>$request->name,'image'=>$image]);
        }
       //file_put_contents('test.txt',$request->name." ".$request->image);

       else
       {
        domain::create(['name'=>$request->name]);
       }
        return redirect()->route('show-all-domain')->with('success','Domain Added Successfully');


    }

    public function domain_active_status_update(Request $request)
    {
        $id = $request->id;
        $status =domain::where('id', $id)->first()->status;
        if ($status == 1)
        {
            domain::where('id', $id)->update(['status' => 0]);
        }
        else
        {
            domain::where('id', $id)->update(['status' => 1]);
        }
    }
    public function edit_domain_content_ui(Request $request)
    {
        $id = $request->id;
        $data = domain::where('id',$id)->first();
        return view('admin.domain.edit_domain_content',['data'=>$data]);

    }
    public function edit_domain_image_ui(Request $request)
    {
        $id = $request->id;
        $data = domain::where('id',$id)->first();
        return view('admin.domain.edit_domain_image',['data'=>$data]);

    }
    public function update_domain_content(Request $request)
    {
        $id = $request->id;

        domain::where('id', $id)->update(['name' => $request->name]);

        return redirect()
            ->route('show-all-domain')
            ->with('success', "Data Updated Successfully");
    }
    public function update_domain_image(Request $request)
    {
        $id = $request->id;
        $previous_image = domain::where('id',$id)->first()->image;
        if($previous_image)
        {
           if(file_exists($previous_image))
           {
                unlink( base_path($previous_image));
           }
        }
        $image = time() . '.' . request()->image->getClientOriginalExtension();

        $request->image->move(public_path('../image/domain_image') , $image);
        $image = "image/domain_image/" . $image;

        domain::where('id',$id)->update(['image'=>$image]);
        return redirect()->route('show-all-domain')->with('success','Image Updated Successfully');

    }

    public function domain_content_delete(Request $request)
    {
        $id = $request->id;
        domain::where('id', $id)->delete();

    }

    //domain end

    //category start

    // public function show_all_category()
    // {
    //     $datas = category::get();
    //     $i=1;
    //     foreach($datas as $data)
    //     {
    //         $data['sl_no'] = $i++;
    //     }
    //     return view('admin.category.all_category',['datas'=>$datas]);


    // }

    public function show_all_category(Request $request)
    {

        if ($request->ajax()) {
            $data = category::get();
            $i=1;
                foreach($data as $datas)
                {
                    $checked = $datas->status=='1'?'checked':'';
                    $datas['sl_no'] = $i++;

                    $datas['checked'] =$checked;

                }

            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('status', function($data){

                           $switch = "<label class='switch'> <input onclick='category_active_status(".$data->id.")' type='checkbox'".$data->checked."  /> <span class='slider round'></span> </label>";

                            return $switch;
                    })
                    ->addColumn('action', function($data){

                        $button = ' <a href="edit_category_content/'.$data->id.'" class="btn btn-sm btn-primary"><i class="la la-pencil"></i></a>';
                        $button .= '&nbsp;&nbsp;';
                        $button .= ' <a href="javascript:void(0);" class="btn btn-sm btn-danger" onclick="category_content_delete('.$data->id.')"><i class="la la-trash-o"></i></a>';
                        return $button;
                 })

                 ->addColumn('image_edit', function($data){


                     $button = ' <a href="edit_category_image/'.$data->id.'" class="btn btn-sm btn-primary"><i class="la la-pencil"></i></a>';
                    return $button;
             })
                    ->rawColumns(['status','action','image_edit'])
                    ->make(true);
        }

        return view('admin.category.all_category');

    }


    public function add_category_ui()
    {
        return view('admin.category.add_category');
    }
    public function add_category(Request $request)
    {
        if($request->image)
        {
        $image = time() . '.' . request()->image->getClientOriginalExtension();

    $request
        ->image
        ->move(public_path('../image/category_image') , $image);
    $image = "image/category_image/" . $image;
    category::create(['name'=>$request->name,'image'=>$image]);
        }
       //file_put_contents('test.txt',$request->name." ".$request->image);

        else
        {
            category::create(['name'=>$request->name]);
        }
        return redirect()->route('show-all-category')->with('success','category Added Successfully');


    }

    public function category_active_status_update(Request $request)
    {
        $id = $request->id;
        $status =category::where('id', $id)->first()->status;
        if ($status == 1)
        {
            category::where('id', $id)->update(['status' => 0]);
        }
        else
        {
            category::where('id', $id)->update(['status' => 1]);
        }
    }
    public function edit_category_content_ui(Request $request)
    {
        $id = $request->id;
        $data = category::where('id',$id)->first();
        return view('admin.category.edit_category_content',['data'=>$data]);

    }
    public function edit_category_image_ui(Request $request)
    {
        $id = $request->id;
        $data = category::where('id',$id)->first();
        return view('admin.category.edit_category_image',['data'=>$data]);

    }
    public function update_category_content(Request $request)
    {
        $id = $request->id;

        category::where('id', $id)->update(['name' => $request->name]);

        return redirect()
            ->route('show-all-category')
            ->with('success', "Data Updated Successfully");
    }
    public function update_category_image(Request $request)
    {
        $id = $request->id;
        $previous_image = category::where('id',$id)->first()->image;
        if($previous_image)
        {

           if(file_exists($previous_image))
           {
                unlink( base_path($previous_image));
           }


        }
        $image = time() . '.' . request()->image->getClientOriginalExtension();

        $request->image->move(public_path('../image/category_image') , $image);
        $image = "image/category_image/" . $image;

        category::where('id',$id)->update(['image'=>$image]);
        return redirect()->route('show-all-category')->with('success','Image Updated Successfully');

    }

    public function category_content_delete(Request $request)
    {
        $id = $request->id;
        category::where('id', $id)->delete();

    }
    //category end


    //All courier man Start
    public function show_all_courier_man()
    {
        $datas = courier_man::get();
        $i=1;
        foreach($datas as $data)
        {
            $data['sl_no'] = $i++;
        }
        return view('admin.courier_man.all',['datas'=>$datas]);


    }

    public function add_courier_man_ui()
    {
        return view('admin.courier_man.add');
    }
    public function add_courier_man(Request $request)
    {
    //     $validator = Validator::make($request->all(), [
    //         'mobile_number' => ['required', 'regex:/01[13-9]\d{8}$/'],
    //      ]);
    // if($validator->fails())
    // {
    //     return redirect()->back()->with('errors',collect($validator->errors()->all()));
    // }
        $user = new user();
        $user->name = $request->name;
        $user->contact_no = $request->contact_no;
        $user->password = Hash::make($request->password);
        $user->role = 'courier_man';
        $user->save();

        $personal_document = time() . '.' . request()->personal_document->getClientOriginalExtension();

        $request
            ->personal_document
            ->move(public_path('../image/courier_man_document') , $personal_document);
        $personal_document = "image/courier_man_document/" . $personal_document;

        $courier_man_image = time() . '.' . request()->user_image->getClientOriginalExtension();

        $request
            ->user_image
            ->move(public_path('../image/courier_man_image') , $courier_man_image);
        $courier_man_image = "image/courier_man_image/" . $courier_man_image;
        //user::create(['name'=>$request->name,'contact_no'=>$request->contact_no,'password'=>Hash::make($request->password),'role'=>'courier_man']);

       courier_man::create(['user_id'=>$user->id,'personal_document'=>$personal_document,'user_image'=>$courier_man_image,'address'=>$request->address,'reference_name'=>$request->reference_name]);
        return redirect()->route('show-all-courier')->with('success','courier_man Added Successfully');


    }

      public function courier_man_active_status(Request $request)
    {
        $id = $request->id;
        $status =courier_man::where('id', $id)->first()->status;
        if ($status == 1)
        {
            courier_man::where('id', $id)->update(['status' => 0]);
        }
        else
        {
            courier_man::where('id', $id)->update(['status' => 1]);
        }
    }
    public function edit_courier_man_content_ui(Request $request)
    {
        $id = $request->id;
        $data = courier_man::where('id',$id)->first();
        return view('admin.courier_man.edit',['data'=>$data]);

    }

    public function update_courier_man_content(Request $request)
    {
        $id = $request->id;

        courier_man::where('id', $id)->update(['name' => $request->name]);

        return redirect()
            ->route('show-all-courier_man')
            ->with('success', "Data Updated Successfully");
    }


    public function courier_man_content_delete(Request $request)
    {
        $id = $request->id;
        courier_man::where('id', $id)->delete();

    }
    //All courier_man End

    //Warehouse start
   public function show_all_warehouse()
    {
        $datas = warehouse::get();
        $i=1;
        foreach($datas as $data)
        {
            $data['sl_no'] = $i++;
        }
        return view('admin.warehouse.all',['datas'=>$datas]);


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
    //     $validator = Validator::make($request->all(), [
    //         'mobile_number' => ['required', 'regex:/01[13-9]\d{8}$/'],
    //      ]);
    // if($validator->fails())
    // {
    //     return redirect()->back()->with('errors',collect($validator->errors()->all()));
    // }
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
        warehouse::where('id', $id)->delete();

    }


    //warehouse end
    //area start
    public function show_all_area()
    {
        $datas = area::get();
        $i=1;
        foreach($datas as $data)
        {
            $data['sl_no'] = $i++;
        }
        return view('admin.area.all',['datas'=>$datas]);


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
        area::where('id', $id)->delete();

    }

    //area end

    //category with domain  start

    public function show_all_category_with_domain()
    {
        $datas = category::where('domain_id','!=',NULL)->get();
        $i=1;
        foreach($datas as $data)
        {
            $data['sl_no'] = $i++;
        }
        return view('admin.category_with_domain.all_category_with_domain',['datas'=>$datas]);


    }

    public function add_category_with_domain_ui()
    {
        $datas = domain::get();
        return view('admin.category_with_domain.add_category_with_domain',['datas'=>$datas]);
    }
    public function add_category_with_domain(Request $request)
    {
        if($request->image)
        {
        $image = time() . '.' . request()->image->getClientOriginalExtension();

    $request
        ->image
        ->move(public_path('../image/category_image') , $image);
    $image = "image/category_image/" . $image;
    category::create(['name'=>$request->name,'image'=>$image,'domain_id'=>$request->domain_id]);
        }
        else{
            category::create(['name'=>$request->name,'domain_id'=>$request->domain_id]);
        }
       //file_put_contents('test.txt',$request->name." ".$request->image);


        return redirect()->route('show-all-category-with-domain')->with('success','category Added Successfully');


    }

    public function category_with_domain_active_status_update(Request $request)
    {
        $id = $request->id;
        $status =category::where('id', $id)->first()->status;
        if ($status == 1)
        {
            category::where('id', $id)->update(['status' => 0]);
        }
        else
        {
            category::where('id', $id)->update(['status' => 1]);
        }
    }
    public function edit_category_with_domain_content_ui(Request $request)
    {
        $id = $request->id;
        $content = category::where('id',$id)->first();
        $datas = domain::get();
        return view('admin.category_with_domain.edit_category_with_domain_content',['content'=>$content,'datas'=>$datas]);

    }
    public function edit_category_with_domain_image_ui(Request $request)
    {
        $id = $request->id;
        $data = category::where('id',$id)->first();
        return view('admin.category_with_domain.edit_category_with_domain_image',['data'=>$data]);

    }
    public function update_category_with_domain_content(Request $request)
    {
        $id = $request->id;

        category::where('id', $id)->update(['name' => $request->name,'domain_id'=>$request->domain_id]);

        return redirect()
            ->route('show-all-category-with-domain')
            ->with('success', "Data Updated Successfully");
    }
    public function update_category_with_domain_image(Request $request)
    {
        $id = $request->id;
        $previous_image = category::where('id',$id)->first()->image;
        if($previous_image)
        {
           if(file_exists($previous_image))
           {
                unlink( base_path($previous_image));
           }
        }
        $image = time() . '.' . request()->image->getClientOriginalExtension();

        $request->image->move(public_path('../image/category_image') , $image);
        $image = "image/category_image/" . $image;

        category::where('id',$id)->update(['image'=>$image]);
        return redirect()->route('show-all-category-with-domain')->with('success','Image Updated Successfully');

    }

    public function category_with_domain_content_delete(Request $request)
    {
        $id = $request->id;
        category::where('id', $id)->delete();

    }

    //category with domain end

    //subcategory start

    // public function show_all_sub_category()
    // {
    //     $datas = sub_category::where('category_id','!=',NULL)->get();
    //     $i=1;
    //     foreach($datas as $data)
    //     {
    //         $data['sl_no'] = $i++;
    //     }
    //     return view('admin.sub_category.all2',['datas'=>$datas]);


    // }

    public function show_all_sub_category(Request $request)
    {

        if ($request->ajax()) {
            $data = sub_category::where('category_id','!=',NULL)->get();
            $i=1;
                foreach($data as $datas)
                {
                    $checked = $datas->status=='1'?'checked':'';
                    $datas['sl_no'] = $i++;
                    $datas['category_name'] = $datas->category->name;
                    $datas['checked'] =$checked;

                }

            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('status', function($data){

                           $switch = "<label class='switch'> <input onclick='sub_category_active_status(".$data->id.")' type='checkbox'".$data->checked."  /> <span class='slider round'></span> </label>";

                            return $switch;
                    })
                    ->addColumn('action', function($data){

                        $button = ' <a href="edit_sub_category_content/'.$data->id.'" class="btn btn-sm btn-primary"><i class="la la-pencil"></i></a>';
                        $button .= '&nbsp;&nbsp;';
                        $button .= ' <a href="javascript:void(0);" class="btn btn-sm btn-danger" onclick="sub_category_content_delete('.$data->id.')"><i class="la la-trash-o"></i></a>';
                        return $button;
                 })

                 ->addColumn('image_edit', function($data){


                    $button = ' <a href="edit_sub_category_image/'.$data->id.'" class="btn btn-sm btn-primary"><i class="la la-pencil"></i></a>';
                    return $button;
             })
                    ->rawColumns(['status','action','image_edit'])
                    ->make(true);
        }

        return view('admin.sub_category.all2');

    }

    public function add_sub_category_ui()
    {
        $datas = category::get();
        return view('admin.sub_category.add',['datas'=>$datas]);
    }
    public function add_sub_category(Request $request)
    {
        if($request->image)
        {
        $image = time() . '.' . request()->image->getClientOriginalExtension();

    $request
        ->image
        ->move(public_path('../image/sub_category_image') , $image);
    $image = "image/sub_category_image/" . $image;
    sub_category::create(['name'=>$request->name,'image'=>$image,'category_id'=>$request->category_id]);
        }
        else{
            sub_category::create(['name'=>$request->name,'domain_id'=>$request->domain_id]);
        }
       //file_put_contents('test.txt',$request->name." ".$request->image);


        return redirect()->route('show-all-sub-category')->with('success','category Added Successfully');


    }

    public function sub_category_active_status_update(Request $request)
    {
        $id = $request->id;
        $status =sub_category::where('id', $id)->first()->status;
        if ($status == 1)
        {
            sub_category::where('id', $id)->update(['status' => 0]);
        }
        else
        {
            sub_category::where('id', $id)->update(['status' => 1]);
        }
    }
    public function edit_sub_category_content_ui(Request $request)
    {
        $id = $request->id;
        $content = sub_category::where('id',$id)->first();
        $datas = category::get();
        return view('admin.sub_category.edit_content',['content'=>$content,'datas'=>$datas]);

    }
    public function edit_sub_category_image_ui(Request $request)
    {
        $id = $request->id;
        $data = sub_category::where('id',$id)->first();
        return view('admin.sub_category.edit_image',['data'=>$data]);

    }
    public function update_sub_category_content(Request $request)
    {
        $id = $request->id;

        sub_category::where('id', $id)->update(['name' => $request->name,'domain_id'=>$request->domain_id]);

        return redirect()
            ->route('show-all-sub-category')
            ->with('success', "Data Updated Successfully");
    }
    public function update_sub_category_image(Request $request)
    {
        $id = $request->id;
        $previous_image = sub_category::where('id',$id)->first()->image;
        if($previous_image)
        {
           if(file_exists($previous_image))
           {
                unlink( base_path($previous_image));
           }
        }
        $image = time() . '.' . request()->image->getClientOriginalExtension();

        $request->image->move(public_path('../image/sub_category_image') , $image);
        $image = "image/sub_category_image/" . $image;

        sub_category::where('id',$id)->update(['image'=>$image]);
        return redirect()->route('show-all-sub-category')->with('success','Image Updated Successfully');

    }

    public function sub_category_content_delete(Request $request)
    {
        $id = $request->id;
        sub_category::where('id', $id)->delete();

    }
    //subcategory end

    //brand start
    public function show_all_brand()
    {

        $datas = product_brand::where('sub_category_id','!=',NULL)->get();
        $i=1;
        foreach($datas as $data)
        {
            $data['sl_no'] = $i++;
        }
        return view('admin.brand.all',['datas'=>$datas]);


    }

    public function add_brand_ui()
    {
        $datas = sub_category::get();
        return view('admin.brand.add',['datas'=>$datas]);
    }
    public function add_brand(Request $request)
    {
        if($request->image)
        {
        $image = time() . '.' . request()->image->getClientOriginalExtension();

    $request
        ->image
        ->move(public_path('../image/brand_image') , $image);
    $image = "image/brand_image/" . $image;
    product_brand::create(['brand_name'=>$request->name,'image'=>$image,'sub_category_id'=>$request->sub_category_id]);
        }
        else{
            product_brand::create(['brand_name'=>$request->name,'sub_category_id'=>$request->sub_category_id]);
        }
       //file_put_contents('test.txt',$request->name." ".$request->image);


        return redirect()->route('show-all-brand')->with('success','category Added Successfully');


    }

    public function brand_active_status_update(Request $request)
    {
        $id = $request->id;
        $status =product_brand::where('id', $id)->first()->status;
        if ($status == 1)
        {
            product_brand::where('id', $id)->update(['status' => 0]);
        }
        else
        {
            product_brand::where('id', $id)->update(['status' => 1]);
        }
    }
    public function edit_brand_content_ui(Request $request)
    {
        $id = $request->id;
        $content = product_brand::where('id',$id)->first();
        $datas = category::get();
        return view('admin.brand.edit_content',['content'=>$content,'datas'=>$datas]);

    }
    public function edit_brand_image_ui(Request $request)
    {
        $id = $request->id;
        $data = product_brand::where('id',$id)->first();
        return view('admin.brand.edit_image',['data'=>$data]);

    }
    public function update_brand_content(Request $request)
    {
        $id = $request->id;

        product_brand::where('id', $id)->update(['brand_name' => $request->brand_name,'sub_category_id'=>$request->sub_category_id]);

        return redirect()
            ->route('show-all-brand')
            ->with('success', "Data Updated Successfully");
    }
    public function update_brand_image(Request $request)
    {
        $id = $request->id;
        $previous_image = product_brand::where('id',$id)->first()->image;
        if($previous_image)
        {
           if(file_exists($previous_image))
           {
                unlink( base_path($previous_image));
           }
        }
        $image = time() . '.' . request()->image->getClientOriginalExtension();

        $request->image->move(public_path('../image/brand_image') , $image);
        $image = "image/brand_image/" . $image;

        product_brand::where('id',$id)->update(['image'=>$image]);
        return redirect()->route('show-all-brand')->with('success','Image Updated Successfully');

    }

    public function brand_content_delete(Request $request)
    {
        $id = $request->id;
        product_brand::where('id', $id)->delete();

    }

    //brand end

    //product start

    public function get_all_product(Request $request)
    {
       if ($request->ajax()) {
            $datas = product::get();
            $i=1;
                foreach($datas as $data)
                {
                    $checked = $data->status=='1'?'checked':'';
                    $data['sl_no'] = $i++;
                   // $data['category_name'] = $data->sub_category->category->name;



                    $data['checked'] =$checked;

                }

            return Datatables::of($datas)
                    ->addIndexColumn()
                    ->addColumn('status', function($datas){

                           $switch = "<label class='switch'> <input onclick='category_active_status(".$datas->id.")' type='checkbox'".$datas->checked."  /> <span class='slider round'></span> </label>";

                            return $switch;
                    })

                    ->addColumn('category_name', function($datas){

                        $column = '<p onclick='.'edit('. $datas->id.',"category")'.'>'. $datas->sub_category->category->name .'</p>';

                         return $column;
                 })

                 ->addColumn('sub_category_name', function($datas){

                    $column = '<p onclick='.'edit('. $datas->id.',"sub_category")'.'>'. $datas->sub_category->name.'</p>';

                     return $column;
                 })
                 ->addColumn('product_name', function($datas){

                    $column = '<p onclick='.'edit('. $datas->id.',"product_name")'.'>'. $datas->name .'</p>';

                     return $column;
                 })

                 ->addColumn('warehouse', function($datas){

                    $column = '<p onclick='.'edit('. $datas->id.',"warehouse")'.'>'. $datas->warehouse->warehouse->name .'</p>';

                     return $column;
                 })

                 ->addColumn('product_image', function($datas){

                    $column = '<img onclick='.'edit('. $datas->id.',"product_image")'.'  src="../'.$datas->thumbnail_image.'" width="100px" class="img-thumbnail" />';

                     return $column;
                 })
                 ->addColumn('product_price', function($datas){

                    $column = '<p onclick='.'edit('. $datas->id.',"product_price")'.'>'. $datas->price .'</p>';

                     return $column;
                 })
                 ->addColumn('product_unit_type', function($datas){

                    $column = '<p onclick='.'edit('. $datas->id.',"product_unit_type")'.'>'. $datas->unit->unit_type .'</p>';

                     return $column;
                 })
                 ->addColumn('product_unit_quantity', function($datas){

                    $column = '<p onclick='.'edit('. $datas->id.',"product_unit_quantity")'.'>'. $datas->unit->unit_quantity .'</p>';

                     return $column;
                 })
                 ->addColumn('produc_stock_amount', function($datas){

                    $column = '<p onclick='.'edit('. $datas->id.',"produc_stock_amount")'.'>'. $datas->stock->stock_amount .'</p>';

                     return $column;
                 })




                    ->rawColumns(['status','category_name','sub_category_name','product_name','warehouse','product_image','product_price','product_unit_type','product_unit_quantity','produc_stock_amount'])
                    ->make(true);
        }

        return view('admin.product.all2');
    }

    // public function get_all_product()
    // {
    //     $datas = product::get();
    //     $i=1;
    //     $product = '';
    //     foreach($datas as $data)
    //     {
    //         $checked = $data->status=='1'?'checked':'';

    //         $product.='	<tr>



    //             <td><strong>'.$i++.'</strong></td>
    //             <td onclick='.'edit('. $data->id.',"category")'.'>'. $data->sub_category->category->name .'</td>
    //             <td onclick='.'edit('. $data->id.',"sub_category")'.'>'.$data->sub_category->name.'</td>

    //             <td onclick='.'edit('.$data->id.',"product_name")'.'>'.$data->name.'</td>
    //             <td onclick='.'edit('. $data->id.',"product_image")'.' ><img  width="50" src="../'.$data->thumbnail_image.'"  alt="Not Available"></td>
    //             <td onclick='.'edit('. $data->id.',"product_price")'.'>'. $data->price .'</td>

    //             <td onclick='.'edit('. $data->id.',"product_unit_type")'.'>'. $data->unit->unit_type .'</td>
    //             <td onclick='.'edit('. $data->id.',"product_unit_quantity")'.'>'. $data->unit->unit_quantity .'</td>
    //             <td onclick='.'edit('. $data->id.',"produc_stock_amount")'.'>'. $data->stock->stock_amount .'</td>
    //             <td> <label class="switch">
    //                 <input type="checkbox"  onclick="product_active_status('.$data->id.')" '.$checked.'>
    //                     <span class="slider round"></span>
    //                 </label></td>



    //         </tr>';

    //     }


    //     echo $product;




    // }

    public function show_all_product()
    {

        return view('admin.product.all2');


    }

    public function add_product_ui()
    {
       //$field_array = array();
       $description_status = 0;
       $detail_image_status = 0;
       $brand_status = 0;
       $size_status = 0;
       $color_status =0;
       $datas = product_required_filed::get();
        foreach($datas as $data)
        {
            if($data->name == "Product Description" && $data->status == 1)
            {

                $description_status = 1;
            }
           else if($data['name'] =="Product Detail Image" && $data['status']== 1)
            {
                $detail_image_status = 1;
            }
            else if($data['name'] == "Product Brand" && $data['status']== 1)
            {
                $brand_status = 1;
            }
            else if($data['name'] == "Product Size" && $data['status']== 1)
            {
                $size_status = 1;
            }
            else if($data['name'] == "Product Color" && $data['status'] == 1)
            {
                $color_status = 1;
            }


        }
        $warehouses = warehouse::where('status',1)->get();
        return view('admin.product.add',['description_status'=>$description_status,'detail_image_status'=>$detail_image_status,'brand_status'=>$brand_status,'color_status'=>$color_status,'size_status'=>$size_status,'warehouses'=>$warehouses]);
       // return view('admin.product.add',['description_status'=>$description_status,'detail_image_status'=>$detail_image_status,'brand_status'=>$brand_status,'color_status'=>$color_status,'size_status'=>$size_status]);
    }
    public function add_product(Request $request)
    {
        $image = time() . '.' . request()->thumbnail_image->getClientOriginalExtension();

        $request->thumbnail_image->move(public_path('../image/product_image') , $image);
        $image = "image/product_image/" . $image;
        if($request->description)
        {
            $description = $request->description;
        }
        else
        {
            $description = NULL;
        }
       $product = product::create(['category_id'=>$request->category_id,'sub_category_id'=>$request->sub_category_id,'brand_id'=>$request->brand_id,'name'=>$request->name,'price'=>$request->price,'thumbnail_image'=>$image,'description'=>$description,'net_weight'=>$request->net_weight]);
       $product_id = $product->id;
       if($request->color)
       {
       product_color::create(['product_id'=>$product_id,'color'=>$request->color]);
       }
       if($request->size)
       {
       product_size::create(['product_id'=>$product_id,'size'=>$request->size]);
       }
       product_unit::create(['product_id'=>$product_id,'unit_quantity'=>$request->unit_quantity,'unit_type'=>$request->unit_type]);
       product_stock::create(['product_id'=>$product_id,'stock_amount'=>$request->unit_stock]);
     warehouse_product::create(['product_id'=>$product_id,'warehouse_id'=>$request->warehouse_id]);


       if($request->file('detail_image'))
       {
        $increment = 0;
        foreach ($request->file('detail_image') as $detail_image) {
            $filesName = time().$increment.'.'.$detail_image->getClientOriginalExtension();
            $detail_image->move(public_path('../Apartment photoes'), $filesName);
            product_detail_image::create(['product_id'=>$product_id,'image'=>$detail_image]);
            //detailes_image::create(['apartment_id'=>$apartment->id,'image'=>$filesName]);
            //myfile = fopen("courier_manMaskNumbers.txt", "a+") or die("Unable to open file!");
            //fwrite($myfile,$filesName."\n");
            $increment++;
        }
    }
    return redirect()->route('show-all-product')->with('success','Product Added Successfully');

    //     if($request->image)
    //     {
    //     $image = time() . '.' . request()->image->getClientOriginalExtension();

    // $request
    //     ->image
    //     ->move(public_path('../image/product_image') , $image);
    // $image = "image/product_image/" . $image;
    // product::create(['name'=>$request->name,'image'=>$image]);
    //     }
    //    //file_put_contents('test.txt',$request->name." ".$request->image);

    //    else
    //    {
    //     product::create(['name'=>$request->name]);
    //    }
    //     return redirect()->route('show-all-product')->with('success','Product Added Successfully');


    }

    public function product_active_status_update(Request $request)
    {
        $id = $request->id;
        $status =product::where('id', $id)->first()->status;
        if ($status == 1)
        {
            product::where('id', $id)->update(['status' => 0]);
        }
        else
        {
            product::where('id', $id)->update(['status' => 1]);
        }
    }
    public function edit_product_content_ui(Request $request)
    {
        $id = $request->id;
        $data = product::where('id',$id)->first();
        return view('admin.product.edit_content',['data'=>$data]);

    }
    public function edit_product_image_ui(Request $request)
    {
        $id = $request->id;
        $data = product::where('id',$id)->first();
        return view('admin.product.edit_image',['data'=>$data]);

    }
    public function update_product_content(Request $request)
    {
        $id = $request->id;

        product::where('id', $id)->update(['name' => $request->name]);

        return redirect()
            ->route('show-all-product')
            ->with('success', "Data Updated Successfully");
    }
    public function update_product_image(Request $request)
    {
        $id = $request->id;
        $previous_image = product::where('id',$id)->first()->image;
        if($previous_image)
        {
           if(file_exists($previous_image))
           {
                unlink( base_path($previous_image));
           }
        }
        $image = time() . '.' . request()->image->getClientOriginalExtension();

        $request->image->move(public_path('../image/product_image') , $image);
        $image = "image/product_image/" . $image;

        product::where('id',$id)->update(['image'=>$image]);
        return redirect()->route('show-all-product')->with('success','Image Updated Successfully');

    }

    public function product_content_delete(Request $request)
    {
        $id = $request->id;
        product::where('id', $id)->delete();

    }

    public function get_category()
    {
        $categories = category::get();
        $data = '<option>Select Category</option>';
        foreach($categories as $category)
        {

            $data.='<option value='.$category->id.'>'.$category->name.'</option>';
        }
        return $data;
    }

    public function get_sub_category(Request $request)
    {
        $category_id = $request->category_id;
        $sub_categories = sub_category::where('category_id',$category_id)->get();
        $data = '<option>Select Sub Category</option>';
        foreach($sub_categories as $sub_category)
        {
            $data.='<option value='.$sub_category->id.'>'.$sub_category->name.'</option>';
        }
        return $data;
    }

    public function get_brand(Request $request)
    {
        $sub_category_id = $request->sub_category_id;
        $brands = product_brand::where('sub_category_id',$sub_category_id)->get();
        $data = '<option>Selet Brand</option>';
        foreach($brands as $brand)
        {
            $data.='<option value='.$brand->id.'>'.$brand->brand_name.'</option>';
        }
        return $data;
    }

    public function show_all_product_field()
    {
        $datas = product_required_filed::get();
        $i=1;
        foreach($datas as $data)
        {
            $data['sl_no'] = $i++;
        }
        return view('admin.product.product-field',['datas'=>$datas]);
    }

    public function product_required_field_active_status_update(Request $request)
    {
        $id = $request->id;
        $status =product_required_filed::where('id', $id)->first()->status;
        if ($status == 1)
        {
            product_required_filed::where('id', $id)->update(['status' => 0]);
        }
        else
        {
            product_required_filed::where('id', $id)->update(['status' => 1]);
        }

    }

    public function get_product_update_modal(Request $request)
    {

        $column_name = $request->column_name;
        $product_id = $request->product_id;
        $data= product::where('id',$product_id)->first();
        //file_put_contents('test.txt',$column_name);

        $data ='  <div class="modal-header">

                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="product_content">';
        if($column_name == 'category')
        {


            $contents = category::get();
            $data.='<div class="col-lg-12 col-md-12 col-sm-12">
            <div class="form-group">

                <label>Category</label>
                <select class="form-control select2 category" id="value" name="category_id">
                <option>Select Category</option>
                ';
                foreach($contents  as $content)
                {
                    $data.='<option value="'.$content->id.'">'.$content->name.'</option>';
                }
                $data.='

                </select>
            </div>

            <div class="form-group">
                <label>Category</label>
                <select class="form-control select2 sub_category" id="value" name="sub_category_id">
                <option>Select Category First</option>
                </select>
             </div>

        </div>';
        }



        if($column_name == 'sub_category')
        {
            $product = product::where('id',$product_id)->first();
            $category_id = $product->sub_category->category->id;
            file_put_contents('test.txt',$category_id);


            $contents = sub_category::where('category_id',$category_id)->get();


            $data.='<div class="col-lg-12 col-md-12 col-sm-12">

            <div class="form-group">
                <label>Sub Category</label>
                <select class="form-control select2 sub_category " id="value" name="sub_category_id">
                <option>Select Sub Category</option>
                ';
                foreach($contents  as $content)
                {
                    $data.='<option value="'.$content->id.'">'.$content->name.'</option>';
                }
                $data.='
                </select>
            </div>

        </div>';
        }
        if($column_name =='brand_category')
        {
            $product = product::where('id',$product_id)->first();
            $contents = product_brand::where('sub_category_id',$product->sub_category_id)->get();
            $data.='<div class="col-lg-12 col-md-12 col-sm-12">
            <div class="form-group">
                <label>Brand</label>
                <select class="form-control select2 brand" id="value" name="sub_category_id">
                ';
                foreach($contents  as $content)
                {
                    $data.='<option value="'.$content->id.'">'.$content->brand_name.'</option>';
                }
                $data.='

                </select>
            </div>
        </div>';
        }
           if($column_name =='warehouse')
        {
           // $product = product::where('id',$product_id)->first();
            $contents = warehouse::where('status',1)->get();
            $data.='<div class="col-lg-12 col-md-12 col-sm-12">
            <div class="form-group">
                <label>Warehouse</label>
                <select class="form-control select2 warehouse_id" id="value" name="warehouse_id">
                ';
                foreach($contents  as $content)
                {
                    $data.='<option value="'.$content->id.'">'.$content->name.'</option>';
                }
                $data.='

                </select>
            </div>
        </div>';
        }
        if($column_name == 'product_name')
        {
            $product_name = product::where('id',$product_id)->first()->name;
            $data.='<div class="col-lg-12 col-md-12 col-sm-12">
            <div class="form-group">
                <label>Product Name</label>
                <input type="text" class="form-control" id="value" name="value" value="'.$product_name.'"  />
            </div>
        </div>';
        }


        if($column_name == 'product_image')
        {
            $data.='<div class="col-lg-12 col-md-12 col-sm-12">
                         <div class="form-group">
                                    <div class="field" align="left">
                                        <label class="form-label">Product Thumbnail Image</label>
                                        <input type="file" id="files" name="thumbnail_image"  accept="image/*" multiple />
                                    </div>
                            </div>
        </div>
        <script src="../assets/admin/js/single_image_preview.js?'.time().'"></script>
        ';
        }

        if($column_name == 'product_price')
        {
            $product_price = product::where('id',$product_id)->first()->price;
            $data.='<div class="col-lg-12 col-md-12 col-sm-12">
            <div class="form-group">
                <label>Product Unit Price</label>
                <input type="text" class="form-control" id="value" name="name" value="'.$product_price.'"  />
            </div>
        </div>';
        }

        if($column_name == 'product_unit_type')
        {
            $product = product::where('id',$product_id)->first();
            $product_unit_type = $product->unit->unit_type;
            $data.='<div class="col-lg-12 col-md-12 col-sm-12">
            <div class="form-group">
                <label>Product Unit Type</label>
                <input type="text" class="form-control" id="value" name="name" value="'.$product_unit_type.'"  />
            </div>
        </div>';
        }

        if($column_name == 'product_unit_quantity')
        {
            $product = product::where('id',$product_id)->first();
            $product_unit_quantity = $product->unit->unit_quantity;
            $data.='<div class="col-lg-12 col-md-12 col-sm-12">
            <div class="form-group">
                <label>Product Unit Quanity</label>
                <input type="text" class="form-control" id="value" name="name" value="'.$product_unit_quantity.'"  />
            </div>
        </div>';
        }

        if($column_name == 'produc_stock_amount')
        {
            $product = product::where('id',$product_id)->first();
            $product_stock_amount = $product->stock->stock_amount;
            $data.='<div class="col-lg-12 col-md-12 col-sm-12">
            <div class="form-group">
                <label>Product Stock Amount</label>
                <input type="text" class="form-control" id="value" name="name" value="'.$product_stock_amount.'"  />
            </div>
        </div>';
        }


        $data.=' </div>
        <div class="modal-footer">
            <input type="hidden" id="product_id" value="'.$product_id.'">
            <input type="hidden" id="column_name" value="'.$column_name.'">
            <button type="button" onclick="update_product_value()" class="btn btn-primary">Update</button>
        </div>

        <script src="../assets/admin/js/admin.js?'.time().'"></script>
        ';



        return $data;
    }

    public function update_product_value(Request $request)
    {
        $column_name = $request->column_name;
        $product_id = $request->product_id;
        $input_value = $request->input_value;
       // file_put_contents('test.txt','hello');
        if($column_name == 'category')
        {
            $sub_category_id = $request->sub_category;
            $brand_id = 1;

            product::where('id',$product_id)->update(['brand_id'=>$brand_id,'sub_category_id'=>$sub_category_id]);

        }
        if($column_name == 'sub_ategory')
        {
            $sub_category_id = $request->sub_category;
            $brand_id = 1;
           // file_put_contents('test.txt',$product_id." ".$brand_id." ".$sub_category_id);
            product::where('id',$product_id)->update(['brand_id'=>$brand_id,'sub_category_id'=>$sub_category_id]);

        }




        if($column_name =='brand_category')
        {

            $brand_id = $request->brand_id;
           // file_put_contents('test.txt',$product_id." ".$brand_id." ".$sub_category_id);
            product::where('id',$product_id)->update(['sub_category_id'=>$sub_category_id]);
        }
        if($column_name == 'product_name')
        {
            product::where('id',$product_id)->update(['name'=>$input_value]);
        }


        if($column_name == 'product_image')
        {
            $image = time() . '.' . request()->thumbnail_image->getClientOriginalExtension();

            $request->thumbnail_image->move(public_path('../image/product_image') , $image);
            $image = "image/product_image/" . $image;
            product::where('id',$product_id)->update(['thumbnail_image'=>$image]);
        }

        if($column_name == 'product_price')
        {
            product::where('id',$product_id)->update(['price'=>$input_value]);
        }

        if($column_name == 'product_unit_type')
        {
            product_unit::where('product_id',$product_id)->update(['unit_type'=>$input_value]);
        }

        if($column_name == 'product_unit_quantity')
        {
            product_unit::where('product_id',$product_id)->update(['unit_quantity'=>$input_value]);
        }

        if($column_name == 'produc_stock_amount')
        {
            product_stock::where('product_id',$product_id)->update(['stock_amount'=>$input_value]);
        }
         if($column_name == 'warehouse')
        {
            $warehouse_id = $request->warehouse_id;
            warehouse_product::where('product_id',$product_id)->update(['warehouse_id'=>$warehouse_id]);
        }



    }



    //product end


    //homepage section start

    public function show_all_homepage_section()
    {
        $datas = homepage_section::orderBy('section_order')->get();
        $i=1;
        foreach($datas as $data)
        {
            $data['sl_no'] = $i++;
        }
        return view('admin.homepage_section.all',['datas'=>$datas]);


    }

    public function add_homepage_section_ui()
    {
        return view('admin.homepage_section.add');
    }
    public function add_homepage_section(Request $request)
    {
        $homepage_section = homepage_section::get();
        if(sizeof($homepage_section)>0)
        {
            $last_insert_id = homepage_section::orderBy('section_order','DESC')->first()->section_order;

        }
        else
        {
            $last_insert_id = 0;
        }
        if($request->image)
        {
        $image = time() . '.' . request()->image->getClientOriginalExtension();

    $request
        ->image
        ->move(public_path('../image/homepage_section_image') , $image);
    $image = "image/homepage_section_image/" . $image;
    homepage_section::create(['section_name'=>$request->name,'image'=>$image,'section_order'=>$last_insert_id+1]);
        }
       //file_put_contents('test.txt',$request->name." ".$request->image);
        else

        {
            homepage_section::create(['section_name'=>$request->name,'section_order'=>$last_insert_id+1]);
        }

        return redirect()->route('show-all-homepage_section')->with('success','homepage_section Added Successfully');


    }

    public function homepage_section_active_status_update(Request $request)
    {
        $id = $request->id;
        $status =homepage_section::where('id', $id)->first()->status;
        if ($status == 1)
        {
            homepage_section::where('id', $id)->update(['status' => 0]);
        }
        else
        {
            homepage_section::where('id', $id)->update(['status' => 1]);
        }
    }
    public function edit_homepage_section_content_ui(Request $request)
    {
        $id = $request->id;
        $data = homepage_section::where('id',$id)->first();
        return view('admin.homepage_section.edit_content',['data'=>$data]);

    }
    public function edit_homepage_section_image_ui(Request $request)
    {
        $id = $request->id;
        $data = homepage_section::where('id',$id)->first();
        return view('admin.homepage_section.edit_image',['data'=>$data]);

    }
    public function update_homepage_section_content(Request $request)
    {
        $id = $request->id;

        homepage_section::where('id', $id)->update(['section_name' => $request->name]);

        return redirect()
            ->route('show-all-homepage_section')
            ->with('success', "Data Updated Successfully");
    }
    public function update_homepage_section_image(Request $request)
    {
        $id = $request->id;
        $previous_image = homepage_section::where('id',$id)->first()->image;
        if($previous_image)
        {


                if(file_exists($previous_image))
           {
                unlink( base_path($previous_image));
           }



        }
        $image = time() . '.' . request()->image->getClientOriginalExtension();

        $request->image->move(public_path('../image/homepage_section_image') , $image);
        $image = "image/homepage_section_image/" . $image;

        homepage_section::where('id',$id)->update(['image'=>$image]);
        return redirect()->route('show-all-homepage_section')->with('success','Image Updated Successfully');

    }

    public function homepage_section_content_delete(Request $request)
    {
        $id = $request->id;
        homepage_section::where('id', $id)->delete();

    }

    public function update_homepage_content_order(Request $request)
    {
        $position = $request->position;
        //file_put_contents('test.txt',$position[0]);
        $homepage_section = homepage_section::get();
        for($i = 0 ;$i<sizeof($position);$i++)
        {
            homepage_section::where('id',$position[$i])->update(['section_order'=>$i+1]);
        }

    }
    //homepage section end

    //banner start
    public function show_all_banner()
    {
        $datas = banner::orderBy('order')->get();
        $i=1;
        foreach($datas as $data)
        {
            $data['sl_no'] = $i++;
        }
        return view('admin.banner.all',['datas'=>$datas]);


    }

    public function add_banner_ui()
    {
        return view('admin.banner.add');
    }
    public function add_banner(Request $request)
    {
        $banner = banner::get();
        if(sizeof($banner)>0)
        {
            $last_insert_id = banner::orderBy('order','DESC')->first()->order;

        }
        else
        {
            $last_insert_id = 0;
        }

        $image = time() . '.' . request()->image->getClientOriginalExtension();

    $request
        ->image
        ->move(public_path('../image/banner_image') , $image);
    $image = "image/banner_image/" . $image;
    banner::create(['image'=>$image,'order'=>$last_insert_id+1]);

       //file_put_contents('test.txt',$request->name." ".$request->image);


        return redirect()->route('show-all-banner')->with('success','banner Added Successfully');


    }

    public function banner_active_status_update(Request $request)
    {
        $id = $request->id;
        $status =banner::where('id', $id)->first()->status;
        if ($status == 1)
        {
            banner::where('id', $id)->update(['status' => 0]);
        }
        else
        {
            banner::where('id', $id)->update(['status' => 1]);
        }
    }

    public function edit_banner_image_ui(Request $request)
    {
        $id = $request->id;
        return view('admin.banner.edit_image',compact('id'));

    }

    public function update_banner_image(Request $request)
    {
        $id = $request->id;
        $previous_image = banner::where('id',$id)->first()->image;
        if($previous_image)
        {


                if(file_exists($previous_image))
           {
                unlink( base_path($previous_image));
           }



        }
        $image = time() . '.' . request()->image->getClientOriginalExtension();

        $request->image->move(public_path('../image/banner_image') , $image);
        $image = "image/banner_image/" . $image;

        banner::where('id',$id)->update(['image'=>$image]);
        return redirect()->route('show-all-banner')->with('success','Image Updated Successfully');

    }

    public function banner_content_delete(Request $request)
    {
        $id = $request->id;
        banner::where('id', $id)->delete();

    }

    public function update_banner_order(Request $request)
    {
        $position = $request->position;
        //file_put_contents('test.txt',$position[0]);
        $banner = banner::get();
        for($i = 0 ;$i<sizeof($position);$i++)
        {
            banner::where('id',$position[$i])->update(['order'=>$i+1]);
        }

    }
    //banner end

    //product add to section start

    public function product_add_to_section_ui($id)
    {
        $product_list = product::get();
        foreach($product_list as $product)
        {
            $avail = homepage_product_list::where('homepage_section_id',$id)->where('product_list',$product->id)->first();
            if($avail)
            {
                $product['avail'] = 1;
            }
            else
            {
                $product['avail'] = 0;
            }
        }
        return view('admin.homepage_section.product_section_all',compact('product_list','id'));
    }

    public function add_product_to_section(Request $request)
    {
            $product_id = $request->product_id;
            $discount_percentage = $request->discount_percentage;
            $homepage_section_id = $request->homepage_section_id;
            homepage_product_list::create(['homepage_section_id'=>$homepage_section_id,'product_list'=>$product_id,'discount_percentage'=>$discount_percentage]);



    }

    public function update_product_to_section(Request $request)
    {
            $product_id = $request->product_id;
            $product_percentage = $request->product_percentage;
            homepage_product_list::where('id',$product_id)->update(['discount_percentage'=>$product_percentage]);

            //homepage_product_list::update(['homepage_section_id'=>$homepage_section_id,'product_list'=>$product_id,'discount_percentage'=>$discount_percentage]);

    }




    public function get_all_homepage_section_product($id)
    {
      $product_list =   homepage_product_list::where('homepage_section_id',$id)->get();
        $data = '';
        foreach($product_list as $product)
        {
            $after_discount_price = $product->product->price- floor(($product->product->price * $product->discount_percentage)/100);
            $data.='
            <div class="col-xl-3 col-xxl-4 col-lg-4 col-md-6 col-sm-6">
                <div class="card">
                    <img class="img" src="../../'.$product->product->thumbnail_image.'" alt=""  height="200px">
                    <div class="card-body">
                        <h4>'.$product->product->name.'</h4>
                        <ul class="list-group mb-3 list-group-flush">
                            <li class="list-group-item px-0 border-top-0 d-flex justify-content-between"><span class="mb-0 text-muted">Product Price</span>
                               <strong>'.$product->product->price.'</strong></li>

                               <li class="list-group-item px-0 border-top-0 d-flex justify-content-between"><span class="mb-0 text-muted">Discount Percentage</span>
                               <strong>'.$product->discount_percentage.'</strong></li>

                               <li class="list-group-item px-0 border-top-0 d-flex justify-content-between"><span class="mb-0 text-muted">After Discount Price</span>
                               <strong>'.$after_discount_price.'</strong></li>

                        </ul>
                        <a href="javascript:;" onclick = "edit_discount_price_modal('.$product->discount_percentage.','.$product->id.')" class="btn btn-primary">Edit Discount Percentage</a>
                        <a href="javascript:void(0);" class="btn btn-sm btn-danger" onclick="delete_product_from_section('.$product->id.')"><i class="la la-trash-o"></i></a>
                    </div>
                </div>
            </div>
    ';
        }
        $all_product = '<label>Select Product</label>
        <select class="form-control select2" id="product_id" name="product_id">
            <option>Select Product</option>';
        $product_list = product::get();
        foreach($product_list as $product)
        {
            $avail = homepage_product_list::where('homepage_section_id',$id)->where('product_list',$product->id)->first();
            if($avail)
            {
                $product['avail'] = 1;
            }
            else
            {
                $product['avail'] = 0;
            }
        }
        foreach($product_list as $product)
        {
            if($product->avail == 0){
                 $all_product.='


                <option value="'.$product->id.'">'. $product->name.'</option>

       ';
            }
            else{
        $all_product.='


        <option value="'.$product->id .'" disabled>'.$product->name.'</option>

       ';
        }
    }
        $all_product.=' </select>

        <script src="../../assets/admin/js/select2.full.js"></script>
        <script src="../../assets/admin/js/advanced-form-element.js"></script>

        ';


        echo json_encode(['section_product'=>$data,'all_product'=>$all_product]);

    }

    public function delete_product_from_section(Request $request)
    {
        $id = $request->id;
        homepage_product_list::where('id', $id)->delete();

    }

    public function get_all_product_list($id)
    {
        $product_list = product::get();
        foreach($product_list as $product)
        {
            $avail = homepage_product_list::where('homepage_section_id',$id)->where('product_list',$product->id)->first();
            if($avail)
            {
                $product['avail'] = 1;
            }
            else
            {
                $product['avail'] = 0;
            }
        }


    }

    //product add to section end

    //order start
   public function new_order()
    {
        $order = order::where('status','!=','delivered')->get();
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
        $order = order::where('status','delivered')->get();
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
        
        $order_details = order_details::where('order_no',$order_no)->get();
        $i=1;
          foreach($order_details as $data)
        {
            $data['sl_no'] = $i++;
        }
        file_put_contents('test.txt',$order_no." ".json_encode($order_details));
        return view('admin.order.show_product',['datas'=>$order_details]);
    }


    //order end
    
    //company info start
        public function show_all_company_info()
        {
                $data = company_info::first();
                return view('admin.company_info.all',compact('data'));
        }
        public function add_company_info(Request $request)
        {
            company_info::where('id',2)->update($request->except('_token'));
            return redirect()->route('show-all-company-info')->with('success','Information Updated Successfully');
        }
    //company info end
    
      //delivery charge start
    public function show_all_delivery_charge()
    {
            $data = delivery_charge::first();
            return view('admin.delivery_charge.all',compact('data'));
    }
    public function add_delivery_charge(Request $request)
    {
        //delivery_charge::create($request->except('_token'));
       delivery_charge::where('id',1)->update($request->except('_token'));
        return redirect()->route('show-all-delivery-charge')->with('success','Information Updated Successfully');
    }
    //delivery charge end
}
