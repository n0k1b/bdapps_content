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

class SubCategoryController extends Controller
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

    public function show_all_sub_category(Request $request)
    {

        if ($request->ajax()) {
            $data = sub_category::where('category_id','!=',NULL)->where('delete_status',0)->get();
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
                        $permission = $this->permission();
                        $button = '';
                        if(in_array('sub_category_edit',$permission))
                        $button .= ' <a href="edit_sub_category_content/'.$data->id.'" class="btn btn-sm btn-primary"><i class="la la-pencil"></i></a>';
                        else
                        $button .= ' <a href="javascript:void(0);" onclick="access_alert()" class="btn btn-sm btn-primary"><i class="la la-pencil"></i></a>';
                        $button .= '&nbsp;&nbsp;';
                        if(in_array('sub_category_delete',$permission))
                        $button .= ' <a href="javascript:void(0);" class="btn btn-sm btn-danger" onclick="sub_category_content_delete('.$data->id.')"><i class="la la-trash-o"></i></a>';
                        else
                        $button .= ' <a href="javascript:void(0);" class="btn btn-sm btn-danger" onclick="access_alert()"><i class="la la-trash-o"></i></a>';
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

        $rules = [
            'name'=>'required',

            'category_id'=>'required',
            'image'=>'required',

        ];
    $customMessages = [
        'name.required' => 'Sub Category field is required.',

        'category_id.required' => 'Category field is required.',
        'image.required'=>'Image field is required'


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
            product::where('sub_category_id',$id)->update(['status' => 0]);
        }
        else
        {
            sub_category::where('id', $id)->update(['status' => 1]);
             product::where('sub_category_id',$id)->update(['status' => 1]);
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

        sub_category::where('id', $id)->update(['name' => $request->name,'category_id'=>$request->category_id]);

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
        $product_status = product::where('sub_category_id',$id)->where('delete_status',0)->first();
        if($product_status)
        {
            echo "product_exist";
        }
        else
        {
        sub_category::where('id', $id)->update(['delete_status'=>1]);
        }

    }


}
