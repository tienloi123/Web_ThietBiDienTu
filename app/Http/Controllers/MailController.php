<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mail;
use Carbon\Carbon;
use App\Coupon;
use App\Customer;
class MailController extends Controller
{
    public function send_coupon($coupon_time,$coupon_condition,$coupon_number,$coupon_code){
        $customer = Customer::where('customer_vip','=',NULL)->get();
        $coupon = Coupon::where('coupon_code',$coupon_code)->first();
        $start_coupon = $coupon->coupon_date_start;
        $end_coupon = $coupon->coupon_date_end;

        $now = Carbon::now('Asia/Ho_Chi_Minh')->format('d-m-Y H:i:s');
        $title_mail = "Mã khuyến mãi ngày".''.$now;
        
        $data = [];
        foreach($customer as $normal){
             $data['email'][] = $normal->customer_email;
        }
        $coupon = array(
            'start_coupon'=> $start_coupon,
            'end_coupon'=> $end_coupon,
            'coupon_time'=> $coupon_time,
            'coupon_condition'=> $coupon_condition,
            'coupon_number'=> $coupon_number,
            'coupon_code'=>$coupon_code
           );
        
        Mail::send('pages.send_coupon', ['coupon'=>$coupon], function($message) use ($title_mail,$data){
             $message->to($data['email'])->subject($title_mail);
             $message->from($data['email'],$title_mail);
        });
        return redirect()->back()->with('message','Gửi mã khuyến mãi khách hàng thường thành công');
 
         
     }
    public function send_coupon_vip($coupon_time,$coupon_condition,$coupon_number,$coupon_code){
       $customer_vip = Customer::where('customer_vip',1)->get();
       $coupon = Coupon::where('coupon_code',$coupon_code)->first();
       $start_coupon = $coupon->coupon_date_start;
       $end_coupon = $coupon->coupon_date_end;
       $now = Carbon::now('Asia/Ho_Chi_Minh')->format('d-m-Y H:i:s');
       $title_mail = "Mã khuyến mãi ngày".''.$now;
       
       $data = [];
       foreach($customer_vip as $vip){
            $data['email'][] = $vip->customer_email;
       }
       $coupon = array(
        'start_coupon'=> $start_coupon,
        'end_coupon'=> $end_coupon,
        'coupon_time'=> $coupon_time,
        'coupon_condition'=> $coupon_condition,
        'coupon_number'=> $coupon_number,
        'coupon_code'=>$coupon_code
       );
       
       Mail::send('pages.send_coupon_vip', ['coupon'=>$coupon], function($message) use ($title_mail,$data){
            $message->to($data['email'])->subject($title_mail);
            $message->from($data['email'],$title_mail);
       });
       return redirect()->back()->with('message','Gửi mã khuyến mãi khách hàng vip thành công');

        
    }
    public function mail_example(){
        return view('pages.send_coupon');
    }
   
}
