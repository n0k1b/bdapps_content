<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\user;
use App\Models\courier_man;
use App\Models\area;
use Illuminate\Support\Facades\Validator;
use Auth;
use Hash;
use DB;

class CouriermanController extends Controller
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

    public function reset_courier_man_password(Request $request)
    {
        $id = $request->id;
       $data = courier_man::where('id',$id)->first();
        return view('admin.courier_man.reset_pass',compact('data'));
    }
    public function update_password(Request $request)
    {
            $validator = Validator::make($request->all(), [
            'password' => ['required','confirmed'],
         ]);
    if($validator->fails())
    {
        return redirect()->back()->with('errors',collect($validator->errors()->all()));
    }
    $id = $request->id;
    ///file_put_contents('test.txt',$id);
    $user_id = courier_man::where('id',$id)->first()->user_id;
    user::where('id',$user_id)->update(['password'=>Hash::make($request->password)]);
    return redirect()->route('show-all-courier')->with('success','Password Reset Successfully');
    }

    public function show_all_courier_man()
    {
        $datas = courier_man::where('delete_status',0)->get();
        $i=1;
        $permission = $this->permission();
        $area_name = "";
        foreach($datas as $data)
        {
            $data['sl_no'] = $i++;
            $area_id = explode(',',$data->area_id);

            for($i=0;$i<sizeof($area_id);$i++){
            $area = area::where('id',$area_id[$i])->first();
            if($area)
            $area_name.=$area->name;
            if($i<sizeof($area_id)-1)
            $area_name.=',';

            }
            $data['area_name'] = $area_name;
            $area_name = "";

        }
        return view('admin.courier_man.all',['datas'=>$datas,'permission'=>$permission]);


    }

    public function add_courier_man_ui()
    {
        $areas = area::where('delete_status',0)->where('status',1)->get();
        return view('admin.courier_man.add',compact('areas'));
    }
    public function add_courier_man(Request $request)
    {


        //file_put_contents('test.txt',$a[0]);
        $rules = ['contact_no'=>'required|unique:users|regex:/01[13-9]\d{8}$/',
                'name'=>'required',
                'reference_name'=>'required',
                'address'=>'required',
                'personal_document_front'=>'required',
                'personal_document_back'=>'required',
                'user_image'=>'required',
                'password'=>'required|confirmed',
                'area_id'=>'required'




            ];
        $customMessages = [
            'contact_no.required' => 'Mobile number field is required.',
            'contact_no.unique' => 'Mobile number has already been taken. Try another number',
            'contact_no.regex'=>'Please enter a valid number'



        ];
        $validator = Validator::make( $request->all(), $rules, $customMessages );

    //     $validator = Validator::make($request->all(), [
    //         'mobile_number' => ['required', 'regex:/01[13-9]\d{8}$/'],
    //      ]);
    if($validator->fails())
    {
        return redirect()->back()->withInput()->with('errors',collect($validator->errors()->all()));
    }

    $area = json_decode(json_encode($request->area_id));
    $area_id = '';
    for($i=0;$i<sizeof($area);$i++)
    {
        $area_id.=$area[$i];
        if($i<sizeof($area)-1)
        $area_id.=',';

    }

        $user = new user();
        $user->name = $request->name;
        $user->contact_no = $request->contact_no;
        $user->password = Hash::make($request->password);
        $user->role = 'courier_man';
        $user->save();

        $personal_document_front = 'nid_front_'.$user->id.'_'.time() . '.' . request()->personal_document_front->getClientOriginalExtension();

        $request
            ->personal_document_front
            ->move(public_path('../image/courier_man_document') , $personal_document_front);
        $personal_document_front = "image/courier_man_document/" . $personal_document_front;


        $personal_document_back = 'nid_back_'.$user->id.'_'.time() . '.' . request()->personal_document_back->getClientOriginalExtension();

        $request
            ->personal_document_back
            ->move(public_path('../image/courier_man_document') , $personal_document_back);
        $personal_document_back = "image/courier_man_document/" . $personal_document_back;

        $courier_man_image = time() . '.' . request()->user_image->getClientOriginalExtension();

        $request
            ->user_image
            ->move(public_path('../image/courier_man_image') , $courier_man_image);
        $courier_man_image = "image/courier_man_image/" . $courier_man_image;
        //user::create(['name'=>$request->name,'contact_no'=>$request->contact_no,'password'=>Hash::make($request->password),'role'=>'courier_man']);

       courier_man::create(['user_id'=>$user->id,'area_id'=>$area_id,'personal_document_front'=>$personal_document_front,'personal_document_back'=>$personal_document_back,'user_image'=>$courier_man_image,'address'=>$request->address,'reference_name'=>$request->reference_name]);
        return redirect()->route('show-all-courier')->with('success','Courier Man Added Successfully');


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
    public function edit_courier_content_ui(Request $request)
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
        courier_man::where('id', $id)->update(['delete_status'=>1]);

    }

    public function edit_courierman_information_ui(Request $request)
    {
        $id = $request->id;
        $data = courier_man::where('id',$id)->first();
        return view('admin.courier_man.edit_information',['data'=>$data]);
    }

    public function update_courier_information(Request $request)
    {
        $id = $request->id;

        courier_man::where('id',$id)->update(['address'=>$request->address,'reference_name'=>$request->reference_name]);
        $user_id = courier_man::where('id',$id)->first()->user_id;
        user::where('id',$user_id)->update(['name'=>$request->name,'contact_no'=>$request->contact_no]);
        return redirect()->route('show-all-courier')->with('success','Data Updated Successfully');


    }


    public function edit_courierman_area_ui(Request $request)
    {
        $id = $request->id;

        $data = courier_man::where('id',$id)->first();
        $user_area = explode(',',$data->area_id);
        $areas = area::where('delete_status',0)->where('status',1)->get();
        return view('admin.courier_man.edit_area',['data'=>$data,'areas'=>$areas,'user_area'=>$user_area]);
    }

    public function edit_courierman_document_ui(Request $request)
    {
        $id = $request->id;
        return view('admin.courier_man.edit_document',compact('id'));
    }

    public function update_courier_area(Request $request)
    {
        $id = $request->id;
        $area = json_decode(json_encode($request->area_id));
        $area_id = '';
        for($i=0;$i<sizeof($area);$i++)
        {
            $area_id.=$area[$i];
            if($i<sizeof($area)-1)
            $area_id.=',';

        }
        courier_man::where('id',$id)->update(['area_id'=>$area_id]);

        return redirect()->route('show-all-courier')->with('success','Data Updated Successfully');


    }

    public function update_courier_document(Request $request)
    {
        $id = $request->id;
        $user_id = courier_man::where('id',$id)->first()->user_id;

        $previous_image_front = courier_man::where('id',$id)->first()->personal_document_front;
        if($previous_image_front)
        {

           if(file_exists($previous_image_front))
           {
                unlink( base_path($previous_image_front));
           }


        }

        $previous_image_back = courier_man::where('id',$id)->first()->personal_document_back;
        if($previous_image_back)
        {

           if(file_exists($previous_image_back))
           {
                unlink( base_path($previous_image_back));
           }


        }
        $personal_document_front = 'nid_front_'.$user_id.'_'.time() . '.' . request()->personal_document_front->getClientOriginalExtension();

        $request
            ->personal_document_front
            ->move(public_path('../image/courier_man_document') , $personal_document_front);
        $personal_document_front = "image/courier_man_document/" . $personal_document_front;


        $personal_document_back = 'nid_back_'.$user_id.'_'.time() . '.' . request()->personal_document_back->getClientOriginalExtension();

        $request
            ->personal_document_back
            ->move(public_path('../image/courier_man_document') , $personal_document_back);
        $personal_document_back = "image/courier_man_document/" . $personal_document_back;

        courier_man::where('id',$id)->update(['personal_document_front'=>$personal_document_front,'personal_document_back'=>$personal_document_back]);

        return redirect()->route('show-all-courier')->with('success','Data Updated Successfully');


    }


}
