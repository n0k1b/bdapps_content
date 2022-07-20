<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use DataTables;
use Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\supplier;
use App\Models\purchase;
use App\Models\product;

class PurchaseController extends Controller
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

    public function show_all_purchase(Request $request)
    {

        if ($request->ajax()) {
            $data = purchase::get();
            $i=1;
                foreach($data as $datas)
                {
                    //$checked = $datas->status=='1'?'checked':'';
                    $datas['sl_no'] = $i++;
                    $datas->supplier = $datas->supplier->supplier_name;
                    $datas->product = $datas->product->name;
                    //file_put_contents('test.txt',$datas->product->name);

                   // $datas['checked'] =$checked;

                }


            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('supplier',function($data)
                    {
                        return $data->supplier;
                    })
                    ->addColumn('product',function($data)
                    {
                        return $data->product;
                    })
                    ->addColumn('action', function($data){

                        $permission = $this->permission();
                        $button = '';
                        if(in_array('category_edit',$permission))
                        $button .= ' <a href="edit_purchase_content/'.$data->id.'" class="btn btn-sm btn-primary"><i class="la la-pencil"></i></a>';
                        else
                        $button .= ' <a href="javascript:void(0);" onclick="access_alert()" class="btn btn-sm btn-primary"><i class="la la-pencil"></i></a>';
                        $button .= '&nbsp;&nbsp;';
                        if(in_array('category_delete',$permission))
                        $button .= ' <a href="javascript:void(0);" class="btn btn-sm btn-danger" onclick="purchase_content_delete('.$data->id.')"><i class="la la-trash-o"></i></a>';
                        else
                        $button .= ' <a href="javascript:void(0);" class="btn btn-sm btn-danger" onclick="access_alert()"><i class="la la-trash-o"></i></a>';
                        return $button;
                 })


                    ->rawColumns(['action'])
                    ->make(true);
        }

        return view('admin.purchase.all');


    }
    public function edit_purchase_content_ui(Request $request)
    {

        $id = $request->id;

        $datas = purchase::where('id',$id)->first();
        file_put_contents('test.txt',json_encode($datas));

        $suppliers = supplier::get();
        $products = product::where('delete_status',0)->get();
        return view('admin.purchase.edit',compact('datas','products','suppliers'));

    }
    public function update_purchase(Request $request)
    {
        purchase::where('id',$request->id)->update($request->except('_token'));
        return redirect()
        ->route('show-all-purchase')
        ->with('success', "Data Updated Successfully");
    }

    public function add_purchase_ui()
    {
        $products = product::where('delete_status',0)->get();
        $suppliers = supplier::get();
        return view('admin.purchase.add',compact('products','suppliers'));


    }

    public function add_purchase(Request $request)
    {
        $product_id = $request->product_id;
        $supplier_id = $request->supplier_id;
        $purchase_quantity = $request->product_quantity;
        $unit_purchasing_price = $request->unit_purchasing_price;
        $net_price = $purchase_quantity*$unit_purchasing_price;
        $total_price = $net_price+($net_price*($request->vat/100))-($net_price*($request->discount/100))+$request->shipping_cost;
        $purchase_note = $request->purchase_note;
        purchase::create([
            'supplier_id'=>$supplier_id,
            'product_id'=>$product_id,
            'product_quantity'=>$purchase_quantity,
            'unit_purchasing_price'=>$unit_purchasing_price,
            'net_price'=>$net_price,
            'discount'=>$request->discount,
            'vat'=>$request->vat,
            'shipping_cost'=>$request->shipping_cost,
            'purchase_note'=>$purchase_note,
            'total_price'=>$total_price


        ]);

        $remaining_stock = product::where('id',$product_id)->first()->stock;
        $stock = $remaining_stock+$purchase_quantity;
        product::where('id',$product_id)->update(['stock'=>$stock]);

            return back()->with('success','Data Addess Successfully');


    }

}
