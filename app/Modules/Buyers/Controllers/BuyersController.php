<?php

namespace App\Modules\Buyers\Controllers;

use App\Http\Controllers\Controller;
use App\Model\Admins\Admins;
use App\Model\Manufacturers\ManufacturersProducts;
use App\Model\Manufacturers\ManufacturersProductsFiles;
use App\Model\Sessions;
use App\Model\Users\StripeCards;
use App\Model\Users\StripeLocal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Model\Users\Users;
use App\Model\Manufacturers\Manufacturers;
use Stripe\Stripe;
class BuyersController extends Controller{

    private $data,$buyers,$manufacturers,$stripeLocal;

    /**
     * LoginController constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request) {
        $this->data = $request->post();
        $this->buyers = new Users();
        $this->manufacturers = new Manufacturers();
        $this->stripeLocal = new StripeLocal();
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_all_buyers_by_admin(){
        $buyers_data = $this->buyers->orderBy('is_new','DESC')->get();
        foreach ($buyers_data as $buyers_datum) {
            $manufacturers_data = $this->manufacturers->select('company_name','logo','id')->get();
            $permissions = explode(',',$buyers_datum->permissions);
            if(!empty($manufacturers_data)) {
                foreach ($manufacturers_data as $manufacturers_datum) {
                    if(in_array($manufacturers_datum->id,$permissions)){
                        $manufacturers_datum->checked = 1;
                    }else{
                        $manufacturers_datum->checked = 0;
                    }

                }
            }
            $cards = StripeCards::where('users_id','=',$buyers_datum->id)
                ->select('card_number','exp_month','exp_year','cvc')
                ->get();
            $buyers_datum->cards = $cards;
            $buyers_datum->permissions = $manufacturers_data;
        }
        $this->buyers->where('is_new','=','1')->update([
            'is_new'=>0
        ]);
        return response()->json(['success'=>true,'buyers_data'=>$buyers_data],200);
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit_buyers_permissions(){
        $buyers_data = $this->buyers->where('id','=',$this->data['buyers_id'])
            ->select('permissions')
            ->first();
        $permissions = explode(',',$buyers_data->permissions);
        if($this->data['value']==1){
            if(!in_array($this->data['manufacturers_id'],$permissions)){
                $permissions[]=$this->data['manufacturers_id'];
            }
        }else{
            $key = array_search($this->data['manufacturers_id'],$permissions);
            if(array_key_exists($key,$permissions)) {
                unset($permissions[$key]);
            }
        }
        $permissions = implode(',',$permissions);
        $this->buyers->update_data(['permissions'=>$permissions],[[
            'id','=',$this->data['buyers_id']
        ]]);
        return response()->json(['success'=>true,'message'=>'success edit'],200);
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_count_of_new_buyers_and_messages(){
        $count = $this->buyers->where('is_new','=','1')->count();

        return response()->json(['success'=>true,'new_buyers_count'=>$count,'new_message_count'=>0],200);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_buyers_by_id(){
        $buyers_data = $this->buyers->where('id','=',$this->data['buyers_id'])->first();
        $manufacturers_data = $this->manufacturers->select('company_name','logo','id')->get();
        $cards = StripeCards::where('users_id','=',$this->data['buyers_id'])
            ->select('card_number','exp_month','exp_year','cvc','brand')
            ->get();
        $permissions = explode(',',$buyers_data->permissions);
        if(!empty($manufacturers_data)) {
            foreach ($manufacturers_data as $manufacturers_datum) {
                if(in_array($manufacturers_datum->id,$permissions)){
                    $manufacturers_datum->checked = 1;
                }else{
                    $manufacturers_datum->checked = 0;
                }

            }
        }
        $buyers_data->permissions = $manufacturers_data;
        $buyers_data->cards = $cards;
        $this->buyers->where('is_new','=','1')->update([
            'is_new'=>0
        ]);
        return response()->json(['success'=>true,'buyers_data'=>$buyers_data],200);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function invite_by_email(Request $request){
        Mail::send('emails.invite_buyers', ['text_message'=>$this->data['message']],function ($m) use ($request)  {
            $m->from('spf@yobibyte.in.ua', 'SPF');

            $m->to($request->email)->subject('Inviting');
        });
        return response()->json(['success'=>true,'message'=>'success invited'],200);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete_buyers(){
        $this->buyers->delete_buyers($this->data['buyers_id']);
        return response()->json(['success'=>true,'message'=>'success deleted'],200);
    }


    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit_buyers(Request $request){
        $insert_data = [];
        $fields_optional = ['company_name','email','phone_number','business_name','delivery_address','manager_name'];
        foreach ($fields_optional as $field) {
            if(!empty($this->data[$field])){
                $insert_data = array_merge($insert_data,[$field=>$this->data[$field]]);
            }
        }
        if(!empty($this->data['password'])){
            $insert_data = array_merge($insert_data,['password'=>md5($this->data['password'])]);
        }
        if($request->file('company_logo')){
            $buyers_data = $this->buyers->where('id','=',$this->data['buyers_id'])->select('company_logo')->first();
            if(!empty($buyers_data->company_logo)){
                $this->manufacturers->delete_file($buyers_data->company_logo);
            }
            $buyers_logo = $this->manufacturers->upload_file($request->file('company_logo'),'/company_logo/');
            $insert_data = array_merge($insert_data,['company_logo'=>$buyers_logo]);
        }

        if(!empty($insert_data)){
            $this->buyers->update_data($insert_data,[[
                'id','=',$this->data['buyers_id']
            ]]);
        }

        return response()->json(['success'=>true,'message'=>'success edited'],200);

    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function search_buyers_by_admin(){
        $buyers_data = $this->buyers->where('company_name','LIKE','%'.$this->data['word'].'%')->get();
        return response()->json(['success'=>true,'buyers_data'=>$buyers_data],200);

    }


    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit_buyers_status(){
        if(empty($this->data['active'])){
            $active = 0;
        }else{
            $active = 1;
        }
        $this->buyers->update_data(['is_active'=>$active],[['id','=',$this->data['buyers_id']]]);
        return response()->json(['success'=>true,'message'=>'success changed status'],200);
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function add_buyers_card(){
        $users_id = Sessions::get_users_id_by_token($this->data['token']);
        try {
            $customer_id = $this->stripeLocal->get_stripe_customer_id($users_id);
            if(empty($customer_id)){
                throw new \Exception('invalid customer id');
            }
            $customer = $this->stripeLocal->retrieve($customer_id);
            $card = $this->stripeLocal->create_token_card($this->data);
            $card_data = $customer->sources->create(array("source" => $card->id));
            $card = StripeCards::create([
                "users_id"=>$users_id,
                "stripe_card_id"=>$card_data->id,
                "card_number" => $this->data['card_number'],
                "exp_month" => $this->data['exp_month'],
                "exp_year" => $this->data['exp_year'],
                "cvc" => $this->data['cvc'],
                "brand"=>$card_data->brand
            ]);
        }catch (\Exception $e) {
            return response()->json(['success'=>false,'message'=>$e->getMessage()],200);
        }
        return response()->json(['success'=>true,'message'=>'success added','card_data'=>$card],200);
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete_card(){
        $users_id = Sessions::get_users_id_by_token($this->data['token']);
        try {
            $customer_id = $this->stripeLocal->get_stripe_customer_id($users_id);
            $customer = $this->stripeLocal->retrieve($customer_id);
            $card_stripe_id = $this->stripeLocal->get_stripe_card_id($this->data['card_id']);
            $this->stripeLocal->delete_card($customer,$card_stripe_id);
            if(empty($customer_id)){
                throw new \Exception('invalid customer id');
            }
            StripeCards::where('id','=',$this->data['card_id'])->delete();
        }catch (\Exception $e){
            return response()->json(['success'=>false,'message'=>$e->getMessage()],200);
        }
        return response()->json(['success'=>true,'message'=>'success deleted'],200);
    }

    /**
     * Destruct
     */
    public function __destruct(){
        unset($this->manufacturers);
        unset($this->buyers);
        unset($this->data);
        unset($this->stripeLocal);
    }
}

