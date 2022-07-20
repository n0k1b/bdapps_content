<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AppList;
use DataTables;

class AppsController extends Controller
{
    //
    public function ussd($app_id)
    {
        file_put_contents('test.txt',$app_id);
    }

    public function show_all_apps(Request $request)
    {

        if ($request->ajax()) {
            $data = AppList::get();
            $i=1;
                foreach($data as $datas)
                {
                   
                    $datas['sl_no'] = $i++;
                    $datas['ussd_url'] = 'https://proappsbd.com/api/ussd/'.$datas->app_name;
                    $datas['sms_url'] = 'https://proappsbd.com/api/sms/'.$datas->app_name;
                    $datas['subscription_notification_url'] = 'https://proappsbd.com/api/subscriptionNotification/'.$datas->app_name;
                    

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

       

        return redirect()->route('show-all-apps')->with('success','apps Added Successfully');


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
