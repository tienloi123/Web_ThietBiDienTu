<?php

namespace App\Http\Controllers;
use App\SocialCustomers;
use Illuminate\Http\Request;
use DB;
use Session;
use App\Social;
use App\Customer;
use Socialite;
use App\Login;
use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;
use Validator;
use App\Rules\Captcha; 
use App\Statistic;
class AdminController extends Controller
{
    public function login_google(){
        return Socialite::driver('google')->redirect();
    }
    public function callback_google(){
            $users = Socialite::driver('google')->stateless()->user(); 
            // // return $users->id;
            // return $users->name;
            // return $users->email;
            $authUser = $this->findOrCreateUser($users,'google');
            $account_name = Login::where('admin_id',$authUser->user)->first();
            Session::put('admin_name',$account_name->admin_name);
            Session::put('admin_id',$account_name->admin_id);
            return redirect('/dashboard')->with('message', 'Đăng nhập Admin thành công');  
    }
    public function findOrCreateUser($users, $provider){
            $authUser = Social::where('provider_user_id', $users->id)->first();
            if($authUser){

                return $authUser;
            }
          
            $hieu = new Social([
                'provider_user_id' => $users->id,
                'provider' => strtoupper($provider)
            ]);

            $orang = Login::where('admin_email',$users->email)->first();

                if(!$orang){
                    $orang = Login::create([
                        'admin_name' => $users->name,
                        'admin_email' => $users->email,
                        'admin_password' => '',
                        'admin_phone' => '',
                        'admin_status' => 1
                        
                    ]);
                }

            $hieu->login()->associate($orang);
                
            $hieu->save();

            $account_name = Login::where('admin_id',$hieu->user)->first();
            Session::put('admin_name',$account_name->admin_name);
            Session::put('admin_id',$account_name->admin_id); 
          
            return redirect('/dashboard')->with('message', 'Đăng nhập Admin thành công');


    }


    public function login_facebook(){
        return Socialite::driver('facebook')->redirect();
    }

    public function callback_facebook(){
        $provider = Socialite::driver('facebook')->user();
        $account = Social::where('provider','facebook')->where('provider_user_id',$provider->getId())->first();
        if($account){
            //login in vao trang quan tri  
            $account_name = Login::where('admin_id',$account->user)->first();
            Session::put('admin_name',$account_name->admin_name);
            Session::put('admin_id',$account_name->admin_id);
            return redirect('/dashboard')->with('message', 'Đăng nhập Admin thành công');
        }else{

            $hieu = new Social([
                'provider_user_id' => $provider->getId(),
                'provider' => 'facebook'
            ]);

            $orang = Login::where('admin_email',$provider->getEmail())->first();

            if(!$orang){
                $orang = Login::create([
                    'admin_name' => $provider->getName(),
                    'admin_email' => $provider->getEmail(),
                    'admin_password' => '',
                    'admin_phone' => ''
                    
                ]);
            }
            $hieu->login()->associate($orang);
            $hieu->save();

            $account_name = Login::where('admin_id',$account->user)->first();
            Session::put('admin_name',$account_name->admin_name);
            Session::put('admin_id',$account_name->admin_id);
            return redirect('/dashboard')->with('message', 'Đăng nhập Admin thành công');
        } 
    }

    public function AuthLogin(){
        $admin_id = Session::get('admin_id');
        if($admin_id){
            return Redirect::to('dashboard');
        }else{
            return Redirect::to('admin')->send();
        }
    }

    public function index(){
    	return view('admin_login');
    }
    public function show_dashboard(){
        $this->AuthLogin();
    	return view('admin.dashboard');
    }
    public function dashboard(Request $request){
        //$data = $request->all();
        $data = $request->validate([
            //validation laravel 
            'admin_email' => 'required',
            'admin_password' => 'required',
           'g-recaptcha-response' => new Captcha(),    //dòng kiểm tra Captcha
        ]);


        $admin_email = $data['admin_email'];
        $admin_password = md5($data['admin_password']);
        $login = Login::where('admin_email',$admin_email)->where('admin_password',$admin_password)->first();
        if($login){
            $login_count = $login->count();
            if($login_count>0){
                Session::put('admin_name',$login->admin_name);
                Session::put('admin_id',$login->admin_id);
                return Redirect::to('/dashboard');
            }
        }else{
                Session::put('message','Mật khẩu hoặc tài khoản bị sai.Làm ơn nhập lại');
                return Redirect::to('/admin');
        }
       

    }
    public function logout(){
        $this->AuthLogin();
        Session::put('admin_name',null);
        Session::put('admin_id',null);
        return Redirect::to('/admin');
    }
    public function login_customer_google(){
        config(['services.google.redirect' => env('GOOGLE_CLIENT_URL')]);
        return Socialite::driver('google')->redirect();
    }
    public function callback_customer_google(){
        config(['services.google.redirect' => env('GOOGLE_CLIENT_URL')]);

        $users = Socialite::driver('google')->stateless()->user(); 

            $authUser = $this->findOrCreateCustomer($users, 'google');

        if($authUser){
        $account_name = Customer::where('customer_id',$authUser->user)->first();
        Session::put('customer_id',$account_name->customer_id);
        Session::put('customer_picture',$account_name->customer_picture);
        Session::put('customer_name',$account_name->customer_name);
    }elseif($customer_new){
        $account_name = Customer::where('customer_id',$authUser->user)->first();
        Session::put('customer_id',$account_name->customer_id);
        Session::put('customer_picture',$account_name->customer_picture);
        Session::put('customer_name',$account_name->customer_name);
    }
        
        return redirect('/dang-nhap')->with('message', 'Đăng nhập bằng tài khoản google <span style="color:red">'.$account_name->customer_email.'</span> thành công');  
    }
        public function findOrCreateCustomer($users, $provider){
            $authUser = SocialCustomers::where('provider_user_id', $users->id)->first();
            if($authUser){

                return $authUser;
            }else{
                $customer_new = new SocialCustomers([
                    'provider_user_id'=>$users->id,
                    'provider_user_email'=>$users->email,
                    'provider' => strtoupper($provider)
                ]);

            $customer = Customer::where('customer_email',$users->email)->first();

                if(!$customer){
                    $customer = Customer::create([
                        'customer_name' => $users->name,
                        
                        'customer_email' => $users->email,
                        'customer_password' => '',
                        'customer_phone' => '',
                        
                    ]);
                }

            $customer_new->customer()->associate($customer);
                
            $customer_new->save();
            return $customer_new;
            }
        }
        public function filter_by_date(Request $request){
            $data = $request->all();

            $from_date = $data['from_date'];

            $to_date = $data['to_date'];

            $get = Statistic::whereBetween('order_date',[$from_date,$to_date])->orderBy('order_date','ASC')->get();
            foreach($get as $key => $val){
                $chart_data[] = array(
                    'period' => $val->order_date,
                    'order' =>$val->total_order,
                    'sales' =>$val->sales,
                    'profit' => $val->profit,
                    'quantity'=>$val->quantity
                );
            }
            echo $data = json_encode($chart_data);
        }
    

}


