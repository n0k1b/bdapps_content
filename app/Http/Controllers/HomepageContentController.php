<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\product;
use DB;
use DataTables;
use Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\homepage_section;
use App\Models\homepage_product_list;

class HomepageContentController extends Controller
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

    public function show_all_homepage_section()
    {
        //echo "hello";
                //
        $datas = homepage_section::orderBy('section_order')->where('delete_status',0)->get();
        //echo "hello";
        $i=1;
        $permission = $this->permission();
        foreach($datas as $data)
        {
            $data['sl_no'] = $i++;
        }
        //echo "hello";
        return view('admin.homepage_section.all',['datas'=>$datas,'permission'=>$permission]);


    }

    public function add_homepage_section_ui()
    {
        return view('admin.homepage_section.add');
    }


    public function add_homepage_section(Request $request)
    {
        $rules = [
            'name'=>'required',
            'image'=>'required'


        ];
    $customMessages = [
        'name.required' => 'Section name field is required.',
        'image.required'=>'Section image field is required'


    ];

    $validator = Validator::make( $request->all(), $rules, $customMessages );


    if($validator->fails())
    {
        return redirect()->back()->withInput()->with('errors',collect($validator->errors()->all()));
    }

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
        homepage_section::where('id', $id)->update(['delete_status'=>1]);

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

    public function product_add_to_section_ui($id)
    {
        $product_list = product::where('delete_status',0)->get();
        foreach($product_list as $product)
        {
            $avail = homepage_product_list::where('homepage_section_id',$id)->where('product_list',$product->id)->where('delete_status',0)->first();
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
      $product_list =   homepage_product_list::where('homepage_section_id',$id)->where('delete_status',0)->get();
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
            $avail = homepage_product_list::where('homepage_section_id',$id)->where('product_list',$product->id)->where('delete_status',0)->first();
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
        homepage_product_list::where('id', $id)->update(['delete_status'=>1]);

    }

    public function get_all_product_list($id)
    {
        $product_list = product::where('delete_status',0)->get();
        foreach($product_list as $product)
        {
            $avail = homepage_product_list::where('homepage_section_id',$id)->where('product_list',$product->id)->where('delete_status',0)->first();
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
}
