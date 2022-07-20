<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Validator;
use Session;
use App\Models\user_otp;
use App\Models\user;

class AuthController extends Controller
{
    //

    function str_random($length = 16)
    {
         $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    return substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
    }
    public function submit_user_information(Request $request)
    {
        $name = $request->name;
        $mobile_number = Session::get('mobile_number');
        $user = new user();
        $user->contact_no = $mobile_number;
        $user->name = $name;
        $user->role = 'customer';
        $user->save();
        Auth::login($user);
        if(session()->has('cart'))
                return redirect()->to('checkout');
                else
                return redirect()->to('/');
        //return redirect()->to('/');

        //user::create(['contact_no'=>$mobile_number,'name'=>$name]);
       // user::where('contact_no',$mobile_number)->update(['name'=>$name]);
    }

    public function submit_otp(Request $request)
    {

        $token = Session::get('otp_token');
        $otp = $request->otp;
        $mobile_number = Session::get('mobile_number');
        $check = user_otp::where('token',$token)->where('otp',$otp)->first();

         $user = user::where('contact_no', $mobile_number)->first();


        if($check)
        {

             if($user)
             {
                Auth::login($user);
                if(session()->has('cart'))
                return redirect()->to('checkout');
                else
                return redirect()->to('/');
             }
             else
             {
                return view('auth.save_name');
             }
            //return response($response, 200);

        }
        else
        {
            return view('auth.otp',['error'=>'Otp Not Matched','mobile_number'=>$mobile_number]);
           /// return redirect()->back()->with('error','Otp Not Matched');
        }

        //return response($response, 200);
    }
    public function send_otp(Request $request)
    {

         $validator = Validator::make($request->all(), [
                'mobile_number' => ['required', 'regex:/01[13-9]\d{8}$/'],
             ]);
        if($validator->fails())
        {
            return redirect()->back()->with('errors',collect($validator->errors()->all()));
        }
        $mobile_number = $request->mobile_number;
        $otp = mt_rand(1000,9999);
        $otp_token = $this->str_random(30);
        Session::put('otp_token',$otp_token);
        Session::put('mobile_number',$mobile_number);
       $this->otp($mobile_number,$otp);

        user_otp::create(['token'=>$otp_token,'otp'=>$otp]);
        return view('auth.otp',compact('mobile_number'));

    }
    public function otp($mobile_number,$otp)
    {
        $mobile_number = '88'.$mobile_number;
        $url = "http://gsms.pw/smsapi";
     $data = [
    "api_key" => "C2000343616ff9646b2b18.73522886",
    "type" => "text",
    "contacts" => $mobile_number,
    "senderid" => "8809601001329",
    "msg" => "Your Urbor OTP is ".$otp,
  ];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  $response = curl_exec($ch);
  curl_close($ch);
  //return $response;

    }
    public function logout()
    {
        auth()->logout();
        return redirect()->to('/');
    }

}
