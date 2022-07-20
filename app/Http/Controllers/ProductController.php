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
use App\Imports\ProductImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\product_brand;
use App\Models\product_color;
use App\Models\product_size;
use App\Models\product_unit;
use App\Models\product_stock;
use App\Models\product_detail_image;
use App\Models\warehouse;
use App\Models\warehouse_product;
use App\Models\product_required_filed;
use App\Models\homepage_product_list;

class ProductController extends Controller
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
    public function get_category()
    {
        $categories = category::get();
        $data = '<option disabled selected>Select Category</option>';
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
        $data = '<option disabled selected>Select Sub Category</option>';
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
            //file_put_contents('test.txt',$category_id);


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
            $product_unit_type = $product->unit_type;
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
            $product_unit_quantity = $product->unit_quantity;
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

          <script src="../assets/admin/js/select2.full.js?'.time().'"></script>
        <script src="../assets/admin/js/advanced-form-element.js?'.time().'"></script>
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
            $category_id = $request->category;
           // $brand_id = 1;

           //file_put_contents('test.txt',$sub_category_id." ".$category_id);

           product::where('id',$product_id)->update(['category_id'=>$category_id,'sub_category_id'=>$sub_category_id]);

        }
        if($column_name == 'sub_category')
        {
            $sub_category_id = $request->sub_category;

            //$brand_id = 1;
           // file_put_contents('test.txt',$product_id." ".$brand_id." ".$sub_category_id);
            product::where('id',$product_id)->update(['sub_category_id'=>$sub_category_id]);

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
            product::where('id',$product_id)->update(['unit_type'=>$input_value]);
        }

        if($column_name == 'product_unit_quantity')
        {
            file_put_contents('test.txt',$input_value.' '.$product_id);
            product::where('id',$product_id)->update(['unit_quantity'=>$input_value]);
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

    public function get_all_product(Request $request)
    {
        //file_put_contents('test.txt',$request." ".time());
        if ($request->ajax()) {
            $datas = product::with('category:id,name','sub_category:id,category_id,name','unit:id,unit_type,unit_quantity')->where('delete_status',0)->select(['*']);


            $i=1;
                foreach($datas as $data)
                {
                  $checked = $data->status=='1'?'checked':'';
                    $data->sl_no= $i++;
                   // $data['category_name'] = $data->sub_category->category->name;

                    $data->chekced =$checked;

                }

              //  file_put_contents('test.txt',$datas);

            return Datatables::eloquent($datas)

                    ->addIndexColumn()

                    ->addColumn('status', function($datas){

                            if($datas->status == 1)
                           $switch = "<label class='switch'> <input onclick='product_active_status(".$datas->id.")' type='checkbox' checked /> <span class='slider round'></span> </label>";
                           else
                           $switch = "<label class='switch'> <input onclick='product_active_status(".$datas->id.")' type='checkbox' /> <span class='slider round'></span> </label>";

                            return $switch;
                    })

                    ->addColumn('category_name', function($datas){
                        $permission = $this->permission();

                        if(in_array('product_edit',$permission))

                        $column = '<p onclick='.'edit('. $datas->id.',"category")'.'>'. $datas->category->name .'</p>';
                        else
                        $column = '<p >'. $datas->sub_category->category->name .'</p>';
                         return $column;
                 })

                 ->addColumn('sub_category_name', function($datas){

                    $permission = $this->permission();

                    if(in_array('product_edit',$permission))
                    $column = '<p onclick='.'edit('. $datas->id.',"sub_category")'.'>'. $datas->sub_category->name.'</p>';
                    else
                    $column = '<p >'. $datas->sub_category->name.'</p>';

                     return $column;
                 })
                 ->addColumn('product_name', function($datas){
                    $permission = $this->permission();

                    if(in_array('product_edit',$permission))
                    $column = '<p onclick='.'edit('. $datas->id.',"product_name")'.'>'. $datas->name .'</p>';
                    else
                    $column = '<p>'. $datas->name .'</p>';

                     return $column;
                 })



                //  ->addColumn('warehouse', function($datas){

                //     $column = '<p onclick='.'edit('. $datas->id.',"warehouse")'.'>'. $datas->warehouse ? $datas->warehouse->warehouse->name : 'No Name' .'</p>';

                //      return $column;
                //  })

                 ->addColumn('product_image', function($datas){
                    $permission = $this->permission();
                    // $url = $datas->thumbnail_image;
                    // $type = pathinfo($url, PATHINFO_EXTENSION);
                    // if($url)
                    // {
                    // $image = file_get_contents($url);
                    // $base64 = 'data:image/' . $type . ';base64,' . base64_encode($image);
                    // }
                    // else
                    // {
                    //     $base64 = '';
                    // }

                    if(in_array('product_edit',$permission))
                    {
                    $column = '<img  onclick='.'edit('. $datas->id.',"product_image")'.' alt="image not found" src="../'.$datas->thumbnail_image.'"  width="100px" class="img-thumbnail product-image lazy" />';
                    }
                    else
                    $column = '<img alt="image not found"  src="../'.$datas->thumbnail_image.'" width="100px" class="img-thumbnail lazy" />';
                     return $column;
                 })
                 ->addColumn('product_price', function($datas){
                    $permission = $this->permission();

                    if(in_array('product_edit',$permission))
                    $column = '<p onclick='.'edit('. $datas->id.',"product_price")'.'>'. $datas->price .'</p>';
                    else
                    $column = '<p>'. $datas->price .'</p>';

                     return $column;
                 })
                 ->addColumn('product_unit_type', function($datas){
                    $permission = $this->permission();

                    if(in_array('product_edit',$permission))
                    $column = '<p onclick='.'edit('. $datas->id.',"product_unit_type")'.'>'. $datas->unit_type .'</p>';
                    else
                    $column = '<p >'. $datas->unit->unit_type .'</p>';

                     return $column;
                 })
                 ->addColumn('product_unit_quantity', function($datas){

                    $column = '<p onclick='.'edit('. $datas->id.',"product_unit_quantity")'.'>'. $datas->unit_quantity .'</p>';

                     return $column;
                 })
                 ->addColumn('produc_stock_amount', function($datas){
                    $permission = $this->permission();

                    if(in_array('product_edit',$permission))
                    $column = '<p >'. $datas->stock .'</p>';
                    else
                    $column = '<p >'. $datas->stock .'</p>';
                     return $column;
                 })
                 ->addColumn('action', function($data){

                    $permission = $this->permission();
                    $button = '';

                    if(in_array('category_delete',$permission))
                    $button .= ' <a href="javascript:void(0);" class="btn btn-sm btn-danger" onclick="product_content_delete('.$data->id.')"><i class="la la-trash-o"></i></a>';
                    else
                    $button .= ' <a href="javascript:void(0);" class="btn btn-sm btn-danger" onclick="access_alert()"><i class="la la-trash-o"></i></a>';
                    return $button;
             })




                    ->rawColumns(['status','category_name','sub_category_name','product_name','product_image','product_price','product_unit_type','product_unit_quantity','produc_stock_amount','action'])
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

    public function product_import(Request $request)
    {

        Excel::import(new ProductImport, $request->file('products'));

        return redirect()->back()->with('success', 'Product Update Successfully');
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
        $warehouses = warehouse::where('status',1)->where('delete_status',0)->get();
        file_put_contents('test.txt',json_encode($warehouses));
        return view('admin.product.add',['description_status'=>$description_status,'detail_image_status'=>$detail_image_status,'brand_status'=>$brand_status,'color_status'=>$color_status,'size_status'=>$size_status,'warehouses'=>$warehouses]);
    }
    public function add_product(Request $request)

    {
        $rules = ['category_id'=>'required',
                 'warehouse_id'=>'required',
                 'name'=>'required',
                 'price'=>'required',
                 'thumbnail_image'=>'required',
                 'unit_type'=>'required',
                 'unit_quantity'=>'required',

                 'net_weight'=>'required',

            ];
        $customMessages = [
            'category_id.required' => 'Category field is required.',
            'warehouse_id.required' => 'Warehouse field is required.',
            'name.required' => 'Product name  field is required.',
            'price.required' => 'Product price field is required.',
            'thumbnail_image.required' => 'Product image field is required.',
            'unit_type.required' => 'Product unit type field is required.',
            'unit_quantity.required' => 'Product unit quantity field is required.',

            'net_weight.required' => 'Product net weight field is required.',



        ];
        $validator = Validator::make( $request->all(), $rules, $customMessages );

        // $validator = Validator::make($request->all(), [
        //     'category_id' => ['required'],
        //     'warehouse_id'=>['required'],
        //  ]);
    if($validator->fails())
    {
        return redirect()->back()->withInput()->with('errors',collect($validator->errors()->all()));
    }
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
        $user_role = Auth::guard('admin')->user()->role;

        if($user_role == 'Admin' || $user_role == 'admin')
        {
            $product = product::create(['category_id'=>$request->category_id,'sub_category_id'=>$request->sub_category_id,'brand_id'=>$request->brand_id,'name'=>$request->name,'price'=>$request->price,'thumbnail_image'=>$image,'description'=>$description,'net_weight'=>$request->net_weight,'unit_type'=>$request->unit_type,'unit_quantity'=>$request->unit_quantity]);

        }
        else
        {
            $product = product::create(['category_id'=>$request->category_id,'sub_category_id'=>$request->sub_category_id,'brand_id'=>$request->brand_id,'name'=>$request->name,'price'=>$request->price,'thumbnail_image'=>$image,'description'=>$description,'net_weight'=>$request->net_weight,'status'=>0,'unit_type'=>$request->unit_type,'unit_quantity'=>$request->unit_quantity]);
        }

        //$product = product::create(['category_id'=>$request->category_id,'sub_category_id'=>$request->sub_category_id,'brand_id'=>$request->brand_id,'name'=>$request->name,'price'=>$request->price,'thumbnail_image'=>$image,'description'=>$description,'net_weight'=>$request->net_weight]);
       $product_id = $product->id;
       if($request->color)
       {
       product_color::create(['product_id'=>$product_id,'color'=>$request->color]);
       }
       if($request->size)
       {
       product_size::create(['product_id'=>$product_id,'size'=>$request->size]);
       }
       //product_unit::create(['product_id'=>$product_id,'unit_quantity'=>$request->unit_quantity,'unit_type'=>$request->unit_type]);
     // product_stock::create(['product_id'=>$product_id,'stock_amount'=>'0']);
       //warehouse_product::create(['product_id'=>$product_id,'warehouse_id'=>$request->warehouse_id]);



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
            homepage_product_list::where('product_list',$id)->update(['status'=>0]);
        }
        else
        {
            product::where('id', $id)->update(['status' => 1]);
            homepage_product_list::where('product_list',$id)->update(['status'=>1]);
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
        product::where('id', $id)->update(['delete_status'=>1]);

    }



}
