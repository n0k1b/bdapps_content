<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AppList;
use App\Models\Subscriber;
use Carbon\Carbon;
use DataTables;
use App\Classes\OtpSender;
use App\Classes\VerifyOtp;
use App\Classes\SubscriptionReceiver;
use App\Classes\Subscription;
use App\Classes\SubscriptionException;
use App\Classes\UssdReceiver;
use App\Classes\UssdSender;
use App\Classes\UssdException;
use App\Classes\Logger;
use App\Models\content;
use App\Classes\SMSSender;
class AppsController extends Controller
{
    //
    public function ussd($app_id)
    {   
        

        $production = true;
        if ($production == false) {
            $ussdserverurl = 'http://localhost:7000/ussd/send';
        } else {
            $ussdserverurl = 'https://developer.bdapps.com/ussd/send';
        }
        try {
            $receiver = new UssdReceiver();
            $ussdSender = new UssdSender($ussdserverurl, $this->app_id, $this->app_password);
            $subscription = new Subscription('https://developer.bdapps.com/subscription/send', $this->app_id, $this->app_password);
            // ile_put_contents('text.txt',$receiver->getRequestID());
            //$operations = new Operations();
            //$receiverSessionId  =   $receiver->getSessionId();
            $content = $receiver->getMessage(); // get the message content
            $address = $receiver->getAddress(); // get the ussdSender's address
            $requestId = $receiver->getRequestID(); // get the request ID
            $applicationId = $receiver->getApplicationId(); // get application ID
            $encoding = $receiver->getEncoding(); // get the encoding value
            $version = $receiver->getVersion(); // get the version
            $sessionId = $receiver->getSessionId(); // get the session ID;
            $ussdOperation = $receiver->getUssdOperation(); // get the ussd operation
            //file_put_contents('status.txt',$address);
            $responseMsg = " Thank you for your Subscription.";
            if ($ussdOperation == "mo-init") {
                try {
                    $ussdSender->ussd($sessionId, $responseMsg, $address, 'mt-fin');
                     $subscription->subscribe($address);
                     if($address)
                     {

                     }
                    
                }
                catch(Exception $e) {
                }
            }
        }
        catch(Exception $e) {
          //  file_put_contents('USSDERROR.txt', $e);
        }
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

    public function add_content_ui()
    {
        date_default_timezone_set('Asia/Dhaka');
          $content_types = AppList::select('app_type')->distinct()->get();
        //$content_types = array_unique($content_types);
        // file_put_contents('test.txt',json_encode($content_types));
        $cur_date = date('d-m-Y');
        $app_id = 3;
        $content = content::where('app_id','=',$app_id)->orderBy('date',"DESC")->get();
        if($content->isEmpty())
        {
        date_default_timezone_set('Asia/Dhaka');
        $date = date('d-m-Y');
          
        }
        else{
            $date = $content[0]->date;
            if(strtotime($date) < strtotime($cur_date))
            {
                $date = $cur_date;
            }
            else{
            $date = date('d-m-Y', strtotime($date. '+ 1 days'));
            }
        }
       // file_put_contents('test.txt',$content);
        //return view('dashboard.add_content',);
        return view('admin.content.add',['date'=>$date,'content_types'=>$content_types]);
    }

    public function add_apps(Request $request)
    {

       
        AppList::create($request->except('_token'));
        return redirect()->route('show-all-apps')->with('success','Apps Added Successfully');


    }

    
    public function edit_apps_content_ui(Request $request)
    {
        $id = $request->id;
        $data = AppList::where('id',$id)->first();
        return view('admin.apps.edit_apps_content',['data'=>$data]);

    }
    public function edit_apps_image_ui(Request $request)
    {
        $id = $request->id;
        $data = AppList::where('id',$id)->first();
        return view('admin.apps.edit_apps_image',['data'=>$data]);

    }
    public function update_apps_content(Request $request)
    {
        $id = $request->id;

        AppList::where('id', $id)->update($request->except('_token'));

        return redirect()
            ->route('show-all-apps')
            ->with('success', "Data Updated Successfully");
    }
    public function report()
    {
        $apps = AppList::get();
        return view('admin.report.subscription_report',compact('apps'));
    }
    public function show_subscription_report(Request $request)
    {
        $start_date = Carbon::parse($request->start_date)->toDateTimeString();
        $end_date =  Carbon::parse($request->end_date)->addDays(1)->toDateTimeString();
        $app_id = $request->app_id;
        $type = $request->type;
        if($app_id)
        {
            if($app_id=='all')
            {
                if($type=='all')
                $data = Subscriber::whereBetween('created_at', [$start_date, $end_date])->latest()->get();
                else
                $data = Subscriber::where('subscription_status','LIKE',$type.'%')->whereBetween('created_at', [$start_date, $end_date])->latest()->get();
            }
            else{
                $data = Subscriber::where('subscription_status','LIKE',$type.'%')->where('app_id',$app_id)->whereBetween('created_at', [$start_date, $end_date])->latest()->get();
                
            }
        }
        else
        {
            $data = Subscriber::whereBetween('created_at', [$start_date, $end_date])->latest()->get();
        }
        

       // file_put_contents('test.txt',$start_date.' '.$end_date.' '.$app_id);
       // $data = Subscriber::get();
        $i=1;
        $subscriber = $data->where('subscription_status','Subscriber')->count();
        $unsubscriber = $data->where('subscription_status','Unsubscriber')->count();
        $pending_charge = $data->where('subscription_status','Pending Charge')->count();
      //  file_put_contents('test.txt',json_encode($subscriber));
        foreach($data as $datas)
        {
            //$checked = $datas->status=='1'?'checked':'';
            $datas['sl_no'] = $i++;
            $datas['subscriber'] = $subscriber ;
            $datas['unsubscriber'] = $unsubscriber;
            $datas['pending_charge'] = $pending_charge ;
           

        }

        return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('date', function($data){
                        return Carbon::parse($data->created_at)->format('d-m-Y H:i:s');
         
                     })
                     ->addColumn('app_name', function($data){
                        return $data->apps->app_name;
         
                     })

                    ->make(true);
    }
    

    public function apps_content_delete(Request $request)
    {
        $id = $request->id;
        

       // file_put_contents('test.txt',"hello ".$id);



    }
    public function content()
    {
        return view('admin.content.index'); 
    }

    public function show_all_content(Request $request)
    {

        if ($request->ajax()) {
            $data = content::get();
            $i=1;
                foreach($data as $datas)
                {
                   
                    $datas['sl_no'] = $i++;
                   

                }

            return Datatables::of($data)
                    ->addIndexColumn()
                    
                    ->addColumn('action', function($data){

                        
                        $button = '';
                        $button .= ' <a href="edit_content/'.$data->id.'" class="btn btn-sm btn-primary"><i class="la la-pencil"></i></a>';
                        $button .= ' <a href="javascript:void(0);" class="btn btn-sm btn-danger" onclick="content_delete('.$data->id.')"><i class="la la-trash-o"></i></a>';
                        return $button;
                 })

                
                    ->rawColumns(['action'])
                    ->make(true);
        }

        return view('admin.content.index');

    }
    public function update_data(Request $request)
    {
        $date = $request->date;
        $count = $request->count;
        
        $date = date('d-m-Y', strtotime($date. '+'.$count.'days'));
        
        $data='<tr>
          	<td width = "70%"><textarea  class="form-control cont" id="content" name="contn" rows="6" cols="8" placeholder="Enter Content"></textarea></td>
									
										<td width = "20%"><input type="text" value="'.$date.'" name="date[]" class="form-control" disabled></td>
										<td><a href="javascript:void(0);"  class="remove"><i class="la la-times-circle" style="font-size: 33px;
                                        color: red;"></i></a></td>
        
        </tr>';
        return $data;
    }
    public function save_content(Request $request)
    {
        $content = $request->content;
        $content = explode(",",$content);
        $date = $request->date;
        $date = explode(",",$date);
        $content_type = $request->content_type;
       // $app_id = AppList::select('id')->get();

        for($i=0;$i<sizeof($content);$i++)
        
        {   
            if($content[$i])
             {
                
            content::create(['content'=>$content[$i],'date'=>$date[$i],'content_type'=>$content_type]);
             }
        }
    }

    public function select_app()
    {
        $datas = AppList::select('app_type')->distinct()->get();
        //file_put_contents('test.txt',json_encode($datas));
        return view('admin.content.select_app',compact('datas'));
    }

    public function select_app_regular_content()
    {
        $datas = AppList::get();
        //file_put_contents('test.txt',json_encode($datas));
        return view('admin.content.select_app_regular_content',compact('datas'));
    }
    public function app_type_submit(Request $request)
    {   
        $app_type = $request->app_type;
        //file_put_contents('test.txt',$app_type);
        date_default_timezone_set('Asia/Dhaka');
         // $content_types = AppList::select('app_type')->distinct()->get();
        //$content_types = array_unique($content_types);
        // file_put_contents('test.txt',json_encode($content_types));
        $cur_date = date('d-m-Y');
        $app_id = 3;
        $content = content::where('content_type','=',$app_type)->orderBy('id','DESC')->first();
        
        if($content)
        {
            $date = $content->date;
            if(strtotime($date) < strtotime($cur_date))
            {
                $date = $cur_date;
            }
            else{
            $date = date('d-m-Y', strtotime($date. '+ 1 days'));
            }

          
        }
        else{
          
        $date = date('d-m-Y');
        }
        return view('admin.content.add',compact('app_type','date'));
    }

    public function app_type_submit_regular_content(Request $request)
    {   
        $app_id = $request->app_id;
     
        
        return view('admin.content.send_regular_content',compact('app_id'));
    }

    public function save_content_regular(Request $request)
    {
        $app_id = $request->app_id;
        $content = $request->content;
        $apps = AppList::where('id',$app_id)->first();
        $AppId = $apps->app_id;
        $AppPassword = $apps->app_password;
        $server = 'https://developer.bdapps.com/sms/send';
        $sender = new SMSSender($server,$AppId,$AppPassword);
        $sender->setencoding('8');
        $x = $sender->broadcast($content);
        
        //file_put_contents('test.txt',$app_id.' '.$content);
    }
    


}
