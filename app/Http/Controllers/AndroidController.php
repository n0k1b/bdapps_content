<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\domain;
use App\Models\category;
use App\Models\sub_category;
use App\Models\product;
use App\Models\product_brand;
use App\Models\product_color;
use App\Models\product_size;
use App\Models\product_unit;
use App\Models\product_stock;
use App\Models\product_detail_image;
use App\Models\homepage_section;
use App\Models\homepage_product_list;
use App\Models\banner;
use App\Models\area;
use App\Models\order;
use App\Models\order_details;
use App\Models\user_address;
use App\Models\user_otp;
use App\Models\courier_man;
use App\Models\user_token;
use App\Models\warehouse;
use App\Models\deposit;
use App\Http\Traits\FirebaseTrait;
use App\Events\OrderNotification;

use Hash;
use DB;


class AndroidController extends Controller
{
    //
    use FirebaseTrait;

    public $base_url = "http://gogoshopbd.com/";
    protected $current_date;

     public function __construct()
    {
        date_default_timezone_set("Asia/Bangkok");
        $this->current_date = date('Y-m-d');
    }

    public function get_homepage_content(Request $request)
    {
 $banner = banner::orderBy('order')->where('status',1)->get();
          foreach($banner as $data)
          {
              $data['image'] = $this->base_url."".$data->image;
          }


        $data = array();
        $product = array();
          $homepage_section_content = homepage_section::where('status',1)->orderBy('section_order')->get();
           foreach($homepage_section_content as $section_product)
           {
             $product = array();
               foreach($section_product->product_list as $product_list)
               {
                    $discount_price =$product_list->product->price- floor(($product_list->product->price*$product_list->discount_percentage)/100);
                   array_push($product,['id'=>$product_list->product->id,'name'=>$product_list->product->name,'image'=>$this->base_url.$product_list->product->thumbnail_image,'unit'=>$product_list->product->unit->unit_quantity." ".$product_list->product->unit->unit_type,'price'=> $product_list->product->price,'discount_price'=>$discount_price,'stock'=>$product_list->product->stock]);
               }

               array_push($data,['section_name'=>$section_product->section_name,'product'=>$product]);
           }

        $response = ["data" =>$data,'banner'=>$banner,'status_code'=>200];
            return response($response, 200);
    }

    public function search_courier_man($area_id)
    {
       // $area_id = $request->area_id;
        $all_courier = courier_man::where('area_id','LIKE','%'.$area_id.'%')->where('status',1)->get();
        $avail_courier_man = 0;

        foreach($all_courier as $courier)
        {
            if(order::where('courier_man',$courier->user_id)->where('status','!=','delivered')->first())
            {
                continue;
            }
            else
            {
               $avail_courier_man = $courier->user_id;
               break;
            }

        }
        if($avail_courier_man == 0)
        {
            $all_courier = courier_man::where('area_id','LIKE','%'.$area_id.'%')->where('status',1)->orderBy(DB::raw('RAND()'))->take(1)->first();
            if($all_courier)
            $avail_courier_man = $all_courier->user_id;
            else
            $avail_courier_man = 0;

        }

            return $avail_courier_man;


    }
    public function token_insertion(Request $request)
    {
         $user_id = auth('api')->user()->id;
         $token = $request->token;
         if(user_token::where('user_id',$user_id)->first())
         {
             user_token::where('user_id',$user_id)->update(['firebase_token'=>$token]);
         }
         else
         {
             user_token::create(['user_id'=>$user_id,'firebase_token'=>$token]);
         }
          $response = ['status_code'=>200];
            return response($response, 200);


    }
     public function submit_order(Request $request)
    {
        //file_put_contents('test.txt',$request);
        $address_id = $request->address_id;
        //file_put_contents('order_id_test.txt',$address_id);

        $carts  = json_decode(json_encode($request->cart));

        $user_id = auth('api')->user()->id;
        $order_no = 'GG'.$user_id.mt_rand(10000,99999);
        $area_id = user_address::where('id',$address_id)->first()->area_id;
        $courier_man =$this->search_courier_man($area_id);
        if($courier_man == 0)
        {
        $response = ['status_code'=>414,'message'=>'No Courier Man Available. Please try again later'];
        return response($response, 200);
        }
        $token = user_token::where('user_id',$courier_man)->first()->firebase_token;
        $text = "New Order";
        $body = "You have a new order";
        $this->sendPushNotification($token,$text,$body);

        foreach($carts as $cart)
        {
         order_details::create(['order_no'=>$order_no,'product_id'=>$cart->id,'unit_quantity'=>$cart->unit,'count'=>$cart->count,'price'=>$cart->price]);
        }

           // $order_no = $order->order_no;
              $order_list = array();
            $order_detail = order_details::where('order_no',$order_no)->get();
            $order_details = array();
            $sub_total = 0;
              for($j=0;$j<sizeof($order_detail);$j++)
            {
                $sub_total+=$order_detail[$j]->price*$order_detail[$j]->count;
                array_push($order_details,['id'=>$order_detail[$j]->id,'price'=>$order_detail[$j]->price,'count'=>$order_detail[$j]->count,'unit'=>$order_detail[$j]->unit_quantity,'name'=>$order_detail[$j]->product->name,'image'=>$this->base_url.$order_detail[$j]->product->thumbnail_image,'total'=>$order_detail[$j]->price*$order_detail[$j]->count]);

            }
            $delivery_fee = 60;
            $order = order::create(['address_id'=>$address_id,'user_id'=>$user_id,'order_no'=>$order_no,'courier_man'=>$courier_man,'status'=>'pending','total_price'=>$sub_total,'delivery_fee'=>$delivery_fee]);

            $order_date =  date("d-m-Y h:i:s", strtotime($order->created_at));




            array_push($order_list,['order_no'=>$order->order_no,'order_date'=>$order_date,'status'=>$order->status,'delivery_fee'=>$delivery_fee,'delivery_address'=>$order->address->address,'subtotal'=>$sub_total+$delivery_fee,'product'=>$order_details]);

            event(new OrderNotification('hello world'));

         $response = ['status_code'=>200,'order'=>$order_list];
        return response($response, 200);

    }
      public function get_category(Request $request)
    {
         $data_category = array();

        $category = category::where('status',1)->get();
      // return $category[1]->sub_category;

        foreach($category as $cat)
           {
            $data_sub_category = array();
               foreach($cat->sub_category as $sub_category)
               {

                   $product_list = array();

                    foreach($sub_category->product as $product)
                    {
                        $discount_avail = homepage_product_list::where('product_list',$product->id)->first();
                        if($discount_avail)
                        {
                             $discount_price =$product->price - floor(($product->price*$discount_avail->discount_percentage)/100);
                        }
                        else
                        {
                            $discount_price = $product->price;
                        }
                    array_push($product_list,['id'=>$product->id,'name'=>$product->name,'image'=>$this->base_url.$product->thumbnail_image,'unit'=>$product->unit->unit_quantity." ".$product->unit->unit_type,'price'=> $product->price,'stock'=>$product->stock,'discount_price'=>$discount_price]);
                    }
                   array_push($data_sub_category,['id'=>$sub_category->id,'name'=>$sub_category->name,'image'=>$this->base_url.$sub_category->image,'product'=>$product_list]);
               }

               array_push($data_category,['id'=>$cat->id,'name'=>$cat->name,'image'=>$this->base_url.$cat->image,'sub_category'=>$data_sub_category]);
           }

           $response = ["category" =>$data_category,'status_code'=>200];
           return response($response, 200);
    }



    public function get_area(Request $request)
    {

        $area = area::where('status',1)->get();//array(['id'=>1,'name'=>'Muradpur'],['id'=>2,'name'=>'Panchalais'],['id'=>3,'name'=>"O.R Nizam Road"],['id'=>4,'name'=>"Bahaddarhat"],['id'=>5,'name'=>"2 no gate"]);
       // $response = ["area" =>$area,'status_code'=>200];
        return response($area, 200);
    }

     public function get_area_delivery_man(Request $request)
    {
        $user_id = auth('api')->user()->id;
         $area_id = courier_man::where('user_id',$user_id)->first()->area_id;
            $selected_area = array();
             if($area_id)
             {
             $selected_area = explode(',',$area_id);
             }
        $area = area::where('status',1)->whereNotIn('id',$selected_area )->get();//array(['id'=>1,'name'=>'Muradpur'],['id'=>2,'name'=>'Panchalais'],['id'=>3,'name'=>"O.R Nizam Road"],['id'=>4,'name'=>"Bahaddarhat"],['id'=>5,'name'=>"2 no gate"]);
       // $response = ["area" =>$area,'status_code'=>200];
        return response($area, 200);
    }

    public function get_address()
    {
           $user_id = auth('api')->user()->id;
           $address = user_address::where('user_id',$user_id)->where('delete_status',0)->orderBy('id')->get();
           $adress_list = array();
           foreach ($address as $data)
           {
               array_push($adress_list,['id'=>$data->id,'area_id'=>$data->area_id,'area'=>$data->area->name,'delivery_address'=>$data->address,'msisdn'=>$data->contact_no,'address_type'=>$data->address_type]);
            //  $data['area_name'] = $data->area->name;
           }
           // $response = ['address'=>$adress_list,'status_code'=>200];
           return response($adress_list, 200);
    }
    public function update_address(Request $request)
    {
        //$user_id = auth('api')->user()->id;
        $area_id = $request->area_id;
        $delivery_address = $request->delivery_address;
        $contact_no = $request->msisdn;
        $address_type = $request->address_type;
        $address = user_address::where('id',$request->id)->update(['area_id'=>$area_id,'address'=>$delivery_address,'contact_no'=>$contact_no,'address_type'=>$address_type]);
        if($address)
        {
            $response = ['status_code'=>200];
           return response($response, 200);
        }
        else
        {
            $response = ['status_code'=>400,'message'=>"Some Error Occured"];
            return response($response, 200);
        }
    }
    public function save_address(Request $request)
    {
         //file_put_contents("test.txt",$request);
        $area_id = $request->area_id;
        $delivery_address = $request->delivery_address;
        $contact_no = $request->msisdn;
        $address_type = $request->address_type;
       $user_id = auth('api')->user()->id;
        $address = user_address::create(['area_id'=>$area_id,'address'=>$delivery_address,'contact_no'=>$contact_no,'address_type'=>$address_type,'user_id'=>$user_id]);


         $response = ['status_code'=>200,'address_id'=>$address->id];
           return response($response, 200);
    }

    public function delete_address(Request $request)
    {
        $address_id = $request->address_id;
        user_address::where('id',$address_id)->update(['delete_status'=>1]);
        $response = ['status_code'=>200];
        return response($response, 200);
    }

    public function search_product(Request $request)
    {
       $input_value = $request->input_value;
        $products = product::where('name','LIKE','%'.$input_value.'%')->where('status',1)->get();
        $product_list = array();

        foreach($products as $product)
        {
            $discount_avail = homepage_product_list::where('product_list',$product->id)->first();
                        if($discount_avail)
                        {
                             $discount_price =$product->price - floor(($product->price*$discount_avail->discount_percentage)/100);
                        }
                        else
                        {
                            $discount_price = $product->price;
                        }
            array_push($product_list,['id'=>$product->id,'name'=>$product->name,'image'=>$this->base_url.$product->thumbnail_image,'unit'=>$product->unit->unit_quantity." ".$product->unit->unit_type,'price'=> $product->price,'stock'=>$product->stock,'discount_price'=>$discount_price]);

        }



            $response = ["product" =>$product_list];
            return response($response, 200);

    }

    public function add_order(Request $request)
    {
        $address_id = $request->address_id;
        $cart = $request->ca;
        //return $req['address_id'];
        //file_put_contents('test.txt',$request->address_id);
    }
    public function logout(Request $request)
{

    $request->user()->token()->revoke();
     $response = ["status_code" =>200,'message'=>'Successfull'];
     return response($response, 200);
}

    public function submit_otp(Request $request)
    {
        $token = $request->otp_token;
        $otp = $request->otp;
        $check = user_otp::where('token',$token)->where('otp',$otp)->first();
         $user = user::where('contact_no', $request->msisdn)->first();
       //  file_put_contents('test.txt',$otp.' '.$token.' '.$request->msisdn);


        if($check)
        {
             if($user)
             {
            $token = $user->createToken('Laravel Password Grant Client')->accessToken;
             $response = ["status_code" =>200,'user_status'=>'old','user'=>$user,'token'=>$token];
             }
             else
             {

                 $response = ["status_code" =>200,'user_status'=>'new'];
             }
            //return response($response, 200);

        }
        else
        {
             $response = ["status_code" =>400,'error_msg'=>"OTP not matched"];
        }

        return response($response, 200);
    }
    public function send_otp($mobile_number,$otp)
    {
        $mobile_number = '88'.$mobile_number;
        $url = "http://gsms.pw/smsapi";
  $data = [
    "api_key" => "C2000343616ff9646b2b18.73522886",
    "type" => "text",
    "contacts" => $mobile_number,
    "senderid" => "8809601001329",
    "msg" => "Your GOGO SHOP OTP is ".$otp,
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

    function str_random($length = 16)
    {
         $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    return substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
    }

        public function login(Request $request)
    {
        $mobile_number = $request->msisdn;
        // $user = user::where('contact_no',$mobile_number)->first();
        // if($user)
        // {
        //      $response = ['status_code'=>202,'message'=>'User Already Registered'];
        //     return response($response, 200);
        // }
        $otp = mt_rand(1000,9999);
        $otp_token = $this->str_random(30);
        $this->send_otp($mobile_number,$otp);
        user_otp::create(['token'=>$otp_token,'otp'=>$otp]);

        $response = ['status_code'=>200,'otp_token'=>$otp_token];
        return response($response, 200);


        // $user = user::where('contact_no', $request->msisdn)->first();
        // if($user)
        // {
        //     $token = $user->createToken('Laravel Password Grant Client')->accessToken;
        //     $response = ['token' => $token,'status_code'=>200];
        //     return response($response, 200);
        // }
        // else
        // {
        //     $response = ["message" =>'User does not exist','status_code'=>422];
        //     return response($response, 422);
        // }
    }

          public function login_delivery_man(Request $request)
    {
        $mobile_number = $request->msisdn;
        $user = user::where('contact_no',$mobile_number)->first();
        if($user)
        {
             $response = ['status_code'=>202,'message'=>'User Already Registered'];
            return response($response, 200);
        }
        $otp = mt_rand(1000,9999);
        $otp_token = $this->str_random(30);
        $this->send_otp($mobile_number,$otp);
        user_otp::create(['token'=>$otp_token,'otp'=>$otp]);

        $response = ['status_code'=>200,'otp_token'=>$otp_token];
        return response($response, 200);


        // $user = user::where('contact_no', $request->msisdn)->first();
        // if($user)
        // {
        //     $token = $user->createToken('Laravel Password Grant Client')->accessToken;
        //     $response = ['token' => $token,'status_code'=>200];
        //     return response($response, 200);
        // }
        // else
        // {
        //     $response = ["message" =>'User does not exist','status_code'=>422];
        //     return response($response, 422);
        // }
    }

    public function registration(Request $request)
    {
        $name = $request->name;
        $mobile_number = $request->msisdn;


        $user = new user();
        $user->name = $name;
        $user->contact_no = $mobile_number;
        $user->save();
        // $user = user::create(['name'=>$name,'contact_no'=>$mobile_number]);

         $token = $user->createToken('Laravel Password Grant Client')->accessToken;
         $response = ['token' => $token,'status_code'=>200];
        // $response = ["status_code" =>200,'token'=>$token];//
            return response($response, 200);


    }

    public function get_order_details(Request $request)
    {
         $user_id = auth('api')->user()->id;
         $order = order::where('user_id',$user_id)->get();
         $order_list = array();
        for($i=0;$i<sizeof($order);$i++)
        {
            $order_no = $order[$i]->order_no;
            $order_detail = order_details::where('order_no',$order_no)->get();
            $order_details = array();
            $sub_total = 0;
            for($j=0;$j<sizeof($order_detail);$j++)
            {
                $sub_total+=$order_detail[$j]->price*$order_detail[$j]->count;
                array_push($order_details,['id'=>$order_detail[$j]->id,'price'=>$order_detail[$j]->price,'count'=>$order_detail[$j]->count,'unit'=>$order_detail[$j]->unit_quantity,'name'=>$order_detail[$j]->product->name,'image'=>$this->base_url.$order_detail[$j]->product->thumbnail_image,'total'=>$order_detail[$j]->price*$order_detail[$j]->count]);
                // array_push($order_details,$order_)
            }
            $order_date =  date("d-m-Y h:i:s", strtotime($order[$i]->created_at));
            $delivery_fee = 60;
            array_push($order_list,['order_no'=>$order[$i]->order_no,'order_date'=>$order_date,'status'=>$order[$i]->status,'delivery_address'=>$order[$i]->address->address,'delivery_fee'=>$delivery_fee,'subtotal'=>$sub_total+$delivery_fee,'product'=>$order_details]);


        }

        $response = ["status_code" =>200,'message'=>'successfull','order'=>$order_list];//
        return response($response, 200);

    }

    public function delivery_fee()
    {
         $response = ["status_code" =>200,'delivery_fee'=>60];//
        return response($response, 200);
    }


    //Delivery man start
        public function delivery_man_login(Request $request)
        {
            $contact_no = $request->msisdn;
            $user = user::where('contact_no',$contact_no)->where('role','courier_man')->first();
            if($user)
            {
                if(courier_man::where('user_id',$user->id)->first()->status == 0)
                {
                     $response = ["code" =>403,'message'=>'User not approved by Adminstrator'];
                     return response($response, 200);
                }

                if(Hash::check($request->password,$user->password))
                {
                   // return 'hello';
                    $token = $user->createToken('Laravel Password Grant Client')->accessToken;
                    unset($user['password']);
                    $courier_man = courier_man::where('user_id',$user->id)->first();
                     $user->address = $courier_man->address;
                    $user->reference_name = $courier_man->reference_name;
                    $user->user_nid_front = $this->base_url.$courier_man->personal_document_front;
                     $user->user_nid_back = $this->base_url.$courier_man->personal_document_back;
                    $user->user_image= $this->base_url.$courier_man->user_image;

                    $response = ["code" =>200,'message'=>'Login Successfull','token'=>$token,'user'=>$user];
                    return response($response, 200);
                }
                else
                {
                    $response = ["code" =>402,'message'=>'Password Not Match'];
                     return response($response, 200);
                }
            }
            else
            {
                $response = ["code" =>404,'message'=>'User Not Registered. Please contact with Administrator'];
                 return response($response, 200);
            }

        }

         public function delivery_man_registration(Request $request)
        {

            $a = json_decode($request->sender_information);

            //$file = $request->file('nid');

            //file_put_contents("test.txt",$request->sender_information);


            $contact_no = $a->msisdn;
             $user = user::where('contact_no',$contact_no)->first();
             if($user)
             {
                 $response = ["code" =>404,'message'=>'User Already Registered'];
                 return response($response, 200);
             }
             else if(user::where('email',$a->email)->first())
             {
                 $response = ["code" =>405,'message'=>'Email Already Registered'];
                 return response($response, 200);
             }
             else
             {
                  $user = new User();
                  $user->name = $a->name;
                $user->email = $a->email;
                  $user->contact_no = $contact_no;
                  $user->password = Hash::make($a->password);
                  $user->role = 'courier_man';
                  $user->save();
                  $courier_man = new courier_man();
                  $courier_man->user_id = $user->id;
                  $courier_man->address = $a->address;
                  $courier_man->reference_name = $a->reference_name;
                  $personal_document_front = 'nid_front_'.$user->id.'_'.time() . '.' . request()->user_nid_front->getClientOriginalExtension();


                    $request
                        ->user_nid_front
                        ->move(public_path('../image/courier_man_document') , $personal_document_front);
                    $personal_document_front = "image/courier_man_document/" . $personal_document_front;


                     $personal_document_back ='nid_back_'.$user->id.'_'. time() . '.' . request()->user_nid_back->getClientOriginalExtension();

                    $request
                        ->user_nid_back
                        ->move(public_path('../image/courier_man_document') , $personal_document_back);
                    $personal_document_back = "image/courier_man_document/" . $personal_document_back;

                    $courier_man_image ='user_image_'.time() . '.' . request()->user_image->getClientOriginalExtension();

                    $request
                        ->user_image
                        ->move(public_path('../image/courier_man_image') , $courier_man_image);
                    $courier_man_image = "image/courier_man_image/" . $courier_man_image;
                    $courier_man->personal_document_front = $personal_document_front;
                    $courier_man->personal_document_back = $personal_document_back;
                    $courier_man->user_image =  $courier_man_image;
                    $courier_man->save();

                    $user->address = $a->address;
                    $user->reference_name = $a->reference_name;
                    $user->personal_document_front = $this->base_url.$personal_document_front;
                     $user->personal_document_back = $this->base_url.$personal_document_back;
                    $user->user_image= $this->base_url.$courier_man_image;
                     $token = $user->createToken('Laravel Password Grant Client')->accessToken;
                      $response = ["code" =>200,'message'=>'Login Successfull','token'=>$token,'user'=>$user];
                    return response($response, 200);



             }


        }

        public function update_area(Request $request)
        {

            $user_id = auth('api')->user()->id;
            $area_id = $request->area_id;
            $area = '';
            for($i = 0;$i<sizeof($area_id);$i++)
            {
                $area.=','.$area_id[$i];
            }
            $area = ltrim($area, ',');
            // file_put_contents('test_text',json_encode($area_id));
            courier_man::where('user_id',$user_id)->update(['area_id'=>$area]);
             $response = ["code" =>200,'message'=>'Area Update Successfully'];
            return response($response, 200);
        }


        public function todays_order(Request $request)
        {
              $user_id = auth('api')->user()->id;
              $todays_order = order::where('courier_man',$user_id)->where('created_at','LIKE',$this->current_date."%")->orderBy(DB::raw('case when status= "pending" then 1 when status= "picked" then 2 when status= "delivered" then 3 end'))->orderBy('id','DESC')->get();
                foreach($todays_order as $order)
                {
                    $product = array();
                    $order_details = order_details::where('order_no',$order->order_no)->get();
                    $order_total = 0;
                    $order->date = date("d-m-Y H:i:s", strtotime($order->created_at));
                    unset($order->created_at);
                    unset($order->updated_at);
                    foreach($order_details as $products)
                    {

                        $products->product->thumbnail_image =   $this->base_url.$products->product->thumbnail_image;
                        $products->product->unit_quantity = $products->unit_quantity;
                        $products->product->count = $products->count;
                         $products->product->price = $products->price;
                        $order_total+= $products->count*$products->price;
                        $warehouse = warehouse::where('status',1)->get();
                        $products->product->warehouse = $warehouse;

                        array_push($product,$products->product);



                    }

                    $order->order_total = $order_total;
                    $order->user_details = $order->user;
                   $order->delivery_address = $order->address->area->name.",".$order->address->address;
                   $order->delivery_contact_no = $order->address->contact_no;
                   $order->product = $product;

                   unset($order->address);
                   unset($order->user);

                }

                // $response = ['order'=>$todays_order,'deposit_availavle'=>0];

                 return response($todays_order, 200);

        }

         public function get_selected_area(Request $request)

        {
             $user_id = auth('api')->user()->id;

             $area = courier_man::where('user_id',$user_id)->first()->area_id;
             if($area)
             {
             $area = explode(',',$area);

             $selected_area = array();
            for($i=0;$i<sizeof($area);$i++)
            {
                $area_name = area::where('id',$area[$i])->first()->name;
                array_push($selected_area,['id'=>$area[$i],'name'=>$area_name]);

            }


             }
             else
             {
                  $selected_area = array();
             }
              return response($selected_area,200);


        }

       public function all_order(Request $request)
        {
              $user_id = auth('api')->user()->id;
              $todays_order = order::where('courier_man',$user_id)->orderBy(DB::raw('case when status= "pending" then 1 when status= "picked" then 2 when status= "delivered" then 3 end'))->get();

                foreach($todays_order as $order)
                {
                    $product = array();
                    $order_details = order_details::where('order_no',$order->order_no)->get();
                    $order_total = 0;
                    $order->date = date("d-m-Y", strtotime($order->created_at));


                    unset($order->updated_at);
                    foreach($order_details as $products)
                    {

                        $products->product->thumbnail_image =   $this->base_url.$products->product->thumbnail_image;
                        $products->product->unit_quantity = $products->unit_quantity;
                        $products->product->count = $products->count;
                         $products->product->price = $products->price;
                        $order_total+= $products->count*$products->price;
                        $warehouse = warehouse::where('status',1)->get();
                        $products->product->warehouse = $warehouse;
                        array_push($product,$products->product);

                    }

                    $order->order_total = $order_total;
               $order->user_details = $order->user;
                   $order->delivery_address = $order->address->area->name.",".$order->address->address;
                   $order->delivery_contact_no = $order->address->contact_no;
                   $order->product = $product;

                   unset($order->address);
                    unset($order->user);

                }

                $todays_order = $todays_order->groupBy('date');

                $all_order = array();
                foreach($todays_order as $key=>$value)
                {
                    foreach($value as $data)
                    {
                        $data->date = date("d-m-Y h:i:s", strtotime($data->created_at));
                         unset($data->created_at);

                    }
                    array_push($all_order,['date'=>$key,'order'=>$value]);
                }

                    usort( $all_order, array( $this, 'date_sort' ) );



                 //$response = ['order'=>$todays_order,'deposit_availavle'=>0];

                 return response($all_order, 200);

        }

       public function date_sort($element1, $element2) {
    $datetime1 = strtotime($element1['date']);
    $datetime2 = strtotime($element2['date']);
    return $datetime2 - $datetime1;
}


        public function order_picked(Request $request)
        {
            $order_no = $request->order_no;
            order::where('order_no',$order_no)->update(['status'=>'picked']);
            $response = ['status_code'=>200];
            return response($response, 200);

        }
         public function order_delivered(Request $request)
        {
            $order_no = $request->order_no;
            order::where('order_no',$order_no)->update(['status'=>'delivered']);

            $order = order::where('order_no',$order_no)->first();
            $order_id = $order->id;
          //  $total_bill = $order->total_price+$order->delivery_fee;
            deposit::create(['order_id'=>$order_id]);

            $response = ['status_code'=>200];
            return response($response, 200);

        }
        public function check_deposit(Request $request)
        {
            $user_id = auth('api')->user()->id;
           $total_earn =  order::where('courier_man',$user_id)->where('status','delivered')->sum('total_price');
           $total_deposit = 0;// deposit::where('courier_man',$user_id)->sum('deposit_amount');
            $deposit =$total_earn-$total_deposit ;
            $response = ['status_code'=>200,'deposit'=>$deposit];
            return response($response, 200);
        }

        public function remarks(Request $request)
        {
            $order_no = $request->order_no;
            $message = $request->message;
            order::where('order_no',$order_no)->update(['remarks'=>$message]);
            $response = ['status_code'=>200];
            return response($response, 200);
        }

        public function delivery_man_dashboard(Request $request)
        {
            $user_id = auth('api')->user()->id;
            //$completed_order = 10;
            $completed_order = order::where('courier_man',$user_id)->where('status','delivered')->get()->count();
            //$pending_order = 20;
            $pending_order = order::where('courier_man',$user_id)->where('status','pending')->get()->count();
            $total_earn = order::where('courier_man',$user_id)->where('status','delivered')->sum('total_price');
            $total_deposit = deposit::where('courier_man',$user_id)->sum('deposit_amount');
            $response = ['status_code'=>200,'completed_order'=>$completed_order,'pending_order'=>$pending_order,'total_earn'=>$total_earn,'total_deposit'=>$total_deposit];
            return response($response, 200);
        }

        public function order_details(Request $request)
        {
            $order_no = $request->order_no;
            $order = order::where('order_no',$order_no)->first();

                    $product = array();
                    $order_details = order_details::where('order_no',$order_no)->get();
                    $order_total = 0;
                    $order->date = date("d-m-Y H:i:s", strtotime($order->created_at));
                    unset($order->created_at);
                    unset($order->updated_at);
                    foreach($order_details as $products)
                    {

                        $products->product->thumbnail_image =   $this->base_url.$products->product->thumbnail_image;
                        $products->product->unit_quantity = $products->unit_quantity;
                        $products->product->count = $products->count;
                         $products->product->price = $products->price;
                        $order_total+= $products->count*$products->price;
                        array_push($product,$products->product);

                    }

                    $order->order_total = $order_total;
                    $order->user_details = $order->user;
                   $order->delivery_address = $order->address->area->name.",".$order->address->address;
                   $order->delivery_contact_no = $order->address->contact_no;
                   $order->product = $product;

                   unset($order->address);
                   unset($order->user);

                 return response($order, 200);



        }


    //Delivery man end

    public function date_test()
    {
         date_default_timezone_set("Asia/Bangkok");
        $date = date('Y-m-d h:i:s a');
        return $date;
    }



}
