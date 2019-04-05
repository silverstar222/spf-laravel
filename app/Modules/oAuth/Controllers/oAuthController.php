<?php

namespace App\Modules\oAuth\Controllers;

use App\Http\Controllers\Controller;
use App\Model\Admins\Admins;
use App\Model\Manufacturers\Manufacturers;
use App\Model\Sessions;
use App\Model\Users\StripeCards;
use App\Model\Users\StripeLocal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Model\Users\Users;
class oAuthController extends Controller{

    private $data,$users,$admins,$stripeLocal;

    /**
     * LoginController constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request) {
        $this->data = $request->post();
        $this->users = new Users();
        $this->admins = new Admins();
        $this->stripeLocal = new StripeLocal();
    }


    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sign_in(Request $request){
        $token = Sessions::generate_token_key();
        $response = [];
        switch ($request->type){
            case 'admin':
                Sessions::set_token_key($token,NULL,$request->acc_id);
                $response = $this->admins->where('id','=',$request->acc_id)->first();
                $this->admins->update_data(['temporary_password'=>NULL],[['id','=',$request->acc_id]]);
            break;

            case 'user':
                Sessions::set_token_key($token,$request->acc_id);
                $response = $this->users->where('id','=',$request->acc_id)->first();
                $this->users->update_data(['temporary_password'=>NULL],[['id','=',$request->acc_id]]);
            break;
        }
        return response()->json(['success' => true,'account_type'=>$request->type,'account_data' => $response,'token'=>$token],200);
    }


    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sign_up(Request $request){
        $company_logo = NULL;
        $card = NULL;
        $insert_data = [
            'email'=>$this->data['email'],
            'company_name'=>$this->data['company_name'],
            'password'=>md5($this->data['password']),
            'phone_number'=>$this->data['phone_number'],
            'business_name'=>$this->data['business_name'],
            'delivery_address'=>$this->data['delivery_address'],
            'manager_name'=>$this->data['manager_name']
        ];
        if($request->file('company_logo')) {
            $link = $this->users->upload_file($request->file('company_logo'));
            $insert_data = array_merge($insert_data,['company_logo'=>$link]);
        }
        $customer_data = $this->stripeLocal->create_customer($this->data['email']);
        $customer = $this->stripeLocal->retrieve($customer_data->id);
        $insert_data = array_merge($insert_data,['customer_stripe_id'=>$customer_data->id]);
        if(!empty($this->data['card_number']) && !empty($this->data['exp_month']) && !empty($this->data['exp_year']) && !empty($this->data['cvc'])){
            try {
                $card = $this->stripeLocal->create_token_card($this->data);
                $card_data = $customer->sources->create(["source" => $card->id]);
            }catch (\Exception $e){
                return response()->json(['success'=>false,'message'=>$e->getMessage()],200);
            }
        }
        $users_data = $this->users->create_new($insert_data);
        if(!empty($card_data)){
            StripeCards::create([
                "users_id"=>$users_data->id,
                "stripe_card_id"=>$card_data->id,
                "card_number" => $this->data['card_number'],
                "exp_month" => $this->data['exp_month'],
                "exp_year" => $this->data['exp_year'],
                "cvc" => $this->data['cvc'],
                "brand" => $card_data->brand
            ]);
        }
        $token = Sessions::generate_token_key();
        Sessions::set_token_key($token,$users_data->id);
        return response()->json(['success'=>true,'token'=>$token,'message'=>'Success created!','users_data'=>$users_data],200);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function recovery_password(Request $request){
        $password_rnd = str_random(10);
        switch ($request->type){
            case 'admin':
                $this->admins->update_data(['temporary_password'=>md5($password_rnd)],[['id','=',$request->acc_id]]);
                break;
            case 'user':
                $this->users->update_data(['temporary_password'=>md5($password_rnd)],[['id','=',$request->acc_id]]);
            break;
        }
        Mail::send('emails.recovery_password', ['password' => $password_rnd], function ($m) use ($request) {
            $m->from('spf@yobibyte.in.ua', 'SPF');

            $m->to($request->email)->subject('Your Temporary Password!');
        });
        return response()->json(['success' => true,'message' => 'temporary password was send to your email'],200);

    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_manufacturers_logos(){
        $logos_data = [];
        $logos = Manufacturers::where('is_active','=','1')->select('logo')->get();
        foreach ($logos as $logo) {
            $logos_data[] = $logo->logo;
        }
        return response()->json(['success'=>true,'logos'=>$logos_data],200);
    }

    /**
     * Destruct
     */
    public function __destruct(){
        unset($this->users);
        unset($this->admins);
        unset($this->data);
    }
}

