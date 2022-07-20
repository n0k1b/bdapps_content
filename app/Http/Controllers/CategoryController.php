<?php

namespace App\Http\Controllers;

use App\Models\AppList;
use Illuminate\Http\Request;
use App\Models\apps;
use App\Models\sub_apps;
use App\Models\product;
use DB;
use DataTables;
use Auth;
use Illuminate\Support\Facades\Validator;

class appsController extends Controller
{
    //

//     public function permission ()
// {

//     $user_id = Auth::guard('admin')->user()->id;
//     $user_role = Auth::guard('admin')->user()->role;
//     $role_id = DB::table('roles')->where('name',$user_role)->first()->id;
//     $role_permission = DB::table('role_permisiions')->where('role_id',$role_id)->pluck('content_name')->toArray();
//     return $role_permission;

// }


    public function show_all_apps(Request $request)
    {

        if ($request->ajax()) {
            $data = AppList::get();
            $i=1;
                foreach($data as $datas)
                {
                   
                    $datas['sl_no'] = $i++;

                    

                }

            return Datatables::of($data)
                    ->addIndexColumn()
                    
                    ->addColumn('action', function($data){

                        
                        $button = '';
                        $button .= ' <a href="edit_apps_content/'.$data->id.'" class="btn btn-sm btn-primary"><i class="la la-pencil"></i></a>';
                        $button .= ' <a href="javascript:void(0);" class="btn btn-sm btn-danger" onclick="apps_content_delete('.$data->id.')"><i class="la la-trash-o"></i></a>';
                        return $button;
                 })

                
                    ->rawColumns(['action'])
                    ->make(true);
        }

        return view('admin.apps.all_apps');

    }

    public function add_apps_ui()
    {
        return view('admin.apps.add_apps');
    }

    public function add_apps(Request $request)
    {

        $rules = [
                'name'=>'required',
                'image'=>'required'


            ];
        $customMessages = [
            'name.required' => 'apps field is required.',
            'image.required'=>'Image filed is required'


        ];

        $validator = Validator::make( $request->all(), $rules, $customMessages );


        if($validator->fails())
        {
            return redirect()->back()->withInput()->with('errors',collect($validator->errors()->all()));
        }


        if($request->image)
        {
        $image = time() . '.' . request()->image->getClientOriginalExtension();

    $request
        ->image
        ->move(public_path('../image/apps_image') , $image);
    $image = "image/apps_image/" . $image;
    apps::create(['name'=>$request->name,'image'=>$image]);
        }
        else
        {
            apps::create(['name'=>$request->name]);
        }
       //file_put_contents('test.txt',$request->name." ".$request->image);


        return redirect()->route('show-all-apps')->with('success','apps Added Successfully');


    }

    public function apps_active_status_update(Request $request)
    {
        $id = $request->id;
        $status =apps::where('id', $id)->first()->status;
        if ($status == 1)
        {
            apps::where('id', $id)->update(['status' => 0]);
            sub_apps::where('apps_id',$id)->update(['status'=>0]);
            product::where('apps_id',$id)->update(['status'=>0]);


        }
        else
        {
            apps::where('id', $id)->update(['status' => 1]);
            sub_apps::where('apps_id',$id)->update(['status'=>1]);
            product::where('apps_id',$id)->update(['status'=>1]);
        }
    }
    public function edit_apps_content_ui(Request $request)
    {
        $id = $request->id;
        $data = apps::where('id',$id)->first();
        return view('admin.apps.edit_apps_content',['data'=>$data]);

    }
    public function edit_apps_image_ui(Request $request)
    {
        $id = $request->id;
        $data = apps::where('id',$id)->first();
        return view('admin.apps.edit_apps_image',['data'=>$data]);

    }
    public function update_apps_content(Request $request)
    {
        $id = $request->id;

        apps::where('id', $id)->update(['name' => $request->name]);

        return redirect()
            ->route('show-all-apps')
            ->with('success', "Data Updated Successfully");
    }
    public function update_apps_image(Request $request)
    {
        $id = $request->id;
        $previous_image = apps::where('id',$id)->first()->image;
        if($previous_image)
        {

           if(file_exists($previous_image))
           {
                unlink( base_path($previous_image));
           }


        }
        $image = time() . '.' . request()->image->getClientOriginalExtension();

        $request->image->move(public_path('../image/apps_image') , $image);
        $image = "image/apps_image/" . $image;

        apps::where('id',$id)->update(['image'=>$image]);
        return redirect()->route('show-all-apps')->with('success','Image Updated Successfully');

    }

    public function apps_content_delete(Request $request)
    {
        $id = $request->id;
        $sub_apps_status = sub_apps::where('apps_id',$id)->where('delete_status',0)->first();
        $product_status = product::where('apps_id',$id)->where('delete_status',0)->first();
        if($sub_apps_status)
        {
            echo "sub_apps_exist";
        }
        else if($product_status)
        {
            echo "product_exist";
        }

        else
        {
            apps::where('id', $id)->update(['delete_status'=>1]);
        }

       // file_put_contents('test.txt',"hello ".$id);



    }




}
