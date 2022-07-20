<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use DataTables;
use Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\package;
use App\Models\package_product;
use App\Models\product;

class PackageController extends Controller
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

    public function add_product_to_package(Request $request)
    {
        $product_id = $request->product_id;
        $unit_quantity = $request->unit_quantity;
        $package_id = $request->package_id;
        $package = package::where('id',$package_id)->first();
        $total_price = $package->total_price;
        $discount = $package->discount_percentage;
        $product_price = product::where('id',$product_id)->first()->price;
        $total_price = $total_price+$product_price*$unit_quantity;
        $discount_price = $total_price- floor(($total_price *  $discount)/100);

        package::where('id',$package_id)->update(['total_price'=>$total_price,'discount_price'=>$discount_price]);


        package_product::create(['package_id'=>$package_id,'product_id'=>$product_id,'unit_quantity'=>$unit_quantity]);
    }

    public function add_package_ui()
    {
        return view('admin.package.add');
    }

    public function add_package(Request $request)
    {
        $rules = [
            'name'=>'required',
            'image'=>'required',
            'discount_percentage'=>'required'


            ];
        $customMessages = [
            'name.required' => 'Package name field is required.',
            'image.required'=>'Package image field is required',
            'discount_percentage.required'=>'Discount percentage filed is required',


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
        ->move(public_path('../image/package_image') , $image);
    $image = "image/package_image/" . $image;

        }
        package::create(['package_name'=>$request->name,'package_image'=>$image,'discount_percentage'=>$request->discount_percentage]);

        return redirect()->route('show_all_package')->with('success','Package Added Successfully');



    }

    public function delete_product_from_package(Request $request)
    {
        $id = $request->id;
        $package_product= package_product::where('id',$id)->first();
        $package = package::where('id',$package_product->package_id)->first();
        $total_price = $package->total_price;
        $discount = $package->discount_percentage;
        $total_price = $total_price-($package_product->product->price*$package_product->unit_quantity);
        //file_put_contents('test.txt',$total_price." ".$package_product->product->price." ".$package_product->unit_quantity);
        $discount_price = $total_price - floor(($total_price *  $discount)/100);

        package::where('id',$package_product->package_id)->update(['total_price'=>$total_price,'discount_price'=>$discount_price]);
        package_product::where('id', $id)->update(['delete_status'=>1]);



    }
    public function show_all_package()
    {
        $datas = package::where('delete_status',0)->get();
        //echo "hello";
        $i=1;
        $permission = $this->permission();
        foreach($datas as $data)
        {
            $data['sl_no'] = $i++;
        }
        //echo "hello";
        return view('admin.package.all',['datas'=>$datas,'permission'=>$permission]);

    }

    public function product_add_to_package_ui($id)
    {
        $product_list = product::where('delete_status',0)->get();
        foreach($product_list as $product)
        {
            $avail = package_product::where('package_id',$id)->where('product_id',$product->id)->first();
            if($avail)
            {
                $product['avail'] = 1;
            }
            else
            {
                $product['avail'] = 0;
            }
        }
        return view('admin.package.package_product',compact('product_list','id'));
    }

    public function edit_package_content_ui(Request $request)
    {
        $id = $request->id;
        $data = package::where('id',$id)->first();
        return view('admin.package.edit_content',['data'=>$data]);

    }
    public function package_content_delete(Request $request)
    {
        $id = $request->id;
        package::where('id', $id)->update(['delete_status'=>1]);

    }

    public function edit_package_image_ui($id)
    {
        return view('admin.package.edit_image',['id'=>$id]);
    }

    public function update_package_image(Request $request)
    {
        $id = $request->id;
        $previous_image = package::where('id',$id)->first()->package_image;
        if($previous_image)
        {

           if(file_exists($previous_image))
           {
                unlink( base_path($previous_image));
           }


        }
        $image = time() . '.' . request()->image->getClientOriginalExtension();

        $request->image->move(public_path('../image/package_image') , $image);
        $image = "image/package_image/" . $image;

        package::where('id',$id)->update(['package_image'=>$image]);
        return redirect()->route('show_all_package')->with('success','Image Updated Successfully');
    }

    public function update_package_content(Request $request)
    {
        $id = $request->id;
        //file_put_contents('test.txt',$id);
        $package = package::where('id',$id)->first();
        $total_price = $package->total_price;
        $discount_price = $total_price-floor(($total_price*$request->discount_percentage)/100);
        package::where('id', $id)->update(['package_name' => $request->name,'discount_percentage'=>$request->discount_percentage,'discount_price'=>$discount_price]);

        return redirect()
            ->route('show_all_package')
            ->with('success', "Data Updated Successfully");
    }

    public function package_active_status_update(Request $request)
    {
        $id = $request->id;
        $status =package::where('id', $id)->first()->status;
        if ($status == 1)
        {
            package::where('id', $id)->update(['status' => 0]);
        }
        else
        {
            package::where('id', $id)->update(['status' => 1]);
        }
    }

    public function update_product_to_package(Request $request)
    {
            $product_id = $request->product_id;
            $unit_quantity = $request->unit_quantity;
            $package_product= package_product::where('id',$product_id)->first();
            $existing_quantity = $package_product->unit_quantity;

            $package = package::where('id',$package_product->package_id)->first();
            $total_price = $package->total_price;
            //file_put_contents('test.txt',$total_price);
        $discount = $package->discount_percentage;

        $total_price = $total_price-($package_product->product->price*$existing_quantity);
        $total_price = $total_price+($package_product->product->price*$unit_quantity);

        $discount_price = $total_price - floor(($total_price *  $discount)/100);

        package::where('id',$package_product->package_id)->update(['total_price'=>$total_price,'discount_price'=>$discount_price]);

            package_product::where('id',$product_id)->update(['unit_quantity'=>$unit_quantity]);

            //homepage_product_list::update(['homepage_section_id'=>$homepage_section_id,'product_list'=>$product_id,'discount_percentage'=>$discount_percentage]);

    }

    public function get_all_package_product($id)
    {
      $product_list =   package_product::where('package_id',$id)->where('delete_status',0)->get();
      //file_put_contents('test.txt',json_encode($product_list));
        $data = '';

        foreach($product_list as $product)
        {

            $after_discount_price = 10;
            $data.='
            <div class="col-xl-3 col-xxl-4 col-lg-4 col-md-6 col-sm-6">
                <div class="card">
                    <img class="img" src="../../'.$product->product->thumbnail_image.'" alt=""  height="200px">
                    <div class="card-body">
                        <h4>'.$product->product->name.'</h4>
                        <ul class="list-group mb-3 list-group-flush">

                            <li class="list-group-item px-0 border-top-0 d-flex justify-content-between"><span class="mb-0 text-muted">Product Price</span>
                               <strong>'.$product->product->price.'</strong></li>

                               <li class="list-group-item px-0 border-top-0 d-flex justify-content-between"><span class="mb-0 text-muted">Unit Quantity</span>
                               <strong>'.$product->unit_quantity.'</strong></li>


                               <li class="list-group-item px-0 border-top-0 d-flex justify-content-between"><span class="mb-0 text-muted">Price</span>
                               <strong>'.$product->unit_quantity * $product->product->price.'</strong></li>


                        </ul>
                        <a href="javascript:;" onclick = "edit_quantity_modal('.$product->unit_quantity.','.$product->id.')" class="btn btn-primary">Edit Product Quantity</a>
                        <a  href="javascript:void(0);" class="btn btn-lg btn-danger" onclick="delete_product_from_package('.$product->id.')"><i class="la la-trash-o"></i></a>
                    </div>
                </div>
            </div>
    ';
        }

        $all_product = '<label>Select Product</label>
        <select class="form-control select2" id="product_id" name="product_id">
            <option>Select Product</option>';
        $product_list = product::where('delete_status',0)->get();
        foreach($product_list as $product)
        {
            $avail = package_product::where('package_id',$id)->where('product_id',$product->id)->where('delete_status',0)->first();
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


}
