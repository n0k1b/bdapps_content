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
use App\Models\banner;

class BannerController extends Controller
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


    public function show_all_banner()
    {
        $datas = banner::orderBy('order')->where('delete_status',0)->get();
        $i=1;

        foreach($datas as $data)
        {
            $data['sl_no'] = $i++;
        }
        $permission = $this->permission();
        return view('admin.banner.all',['datas'=>$datas,'role_permission'=>$permission]);


    }

    public function add_banner_ui()
    {
        return view('admin.banner.add');
    }
    public function add_banner(Request $request)
    {
        $rules = [

            'image'=>'required'


        ];
    $customMessages = [

        'image.required'=>'Banner image field is required'


    ];

    $validator = Validator::make( $request->all(), $rules, $customMessages );


    if($validator->fails())
    {
        return redirect()->back()->withInput()->with('errors',collect($validator->errors()->all()));
    }

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
        banner::where('id', $id)->update(['delete_status'=>1]);

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

}
