<?php

namespace App\Modules\Basket\Controllers;

use App\Http\Controllers\Controller;
use App\Model\Basket\Basket;
use App\Model\Chat\Chat;
use App\Model\Manufacturers\Manufacturers;
use App\Model\Manufacturers\ManufacturersProducts;
use App\Model\Orders\Orders;
use App\Model\Orders\OrdersProducts;
use App\Model\Sessions;
use App\Model\Users\StripeCards;
use App\Model\Users\StripeLocal;
use Illuminate\Http\Request;


class BasketController extends Controller{

    private $data,$basket,$orders,$orders_product,$chat;

    /**
     * LoginController constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request) {
        $this->data = $request->post();
        $this->basket = new Basket();
        $this->orders = new Orders();
        $this->orders_product = new OrdersProducts();
        $this->chat = new Chat();
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function add_product(){
        $product_data = ManufacturersProducts::where('id','=',$this->data['product_id'])
            ->select('manufacturers_id')->first();
        $users_id = Sessions::get_users_id_by_token($this->data['token']);
        if($data = $this->basket->check_exists($users_id,$this->data['product_id'])){
            $this->basket->update_count($data->id,$this->data['count'],$product_data->manufacturers_id);
            return response()->json(['success'=>true,'message'=>'success edited basket'],200);

        }else{
            $this->basket->create_new([
                'users_id'=>$users_id,
                'product_id'=>$this->data['product_id'],
                'count'=>$this->data['count'],
                'manufacturers_id'=>$product_data->manufacturers_id
            ]);
            return response()->json(['success'=>true,'message'=>'success added to basket'],200);

        }
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_user_basket(){
        $users_id = Sessions::get_users_id_by_token($this->data['token']);
        $baskets_data = $this->basket->where('users_id','=',$users_id)
            ->get();
        $total_count = 0;
        foreach ($baskets_data as $basket) {
            $manufacturers_data = Manufacturers::where('manufacturers.id','=',$basket->manufacturers_id)->select('company_name')->first();
            $product_data = ManufacturersProducts::where('id','=',$basket->product_id)->select('price')->first();
            $total_count+=(int)$basket->count*(double)$product_data->price;
            $basket->manufacturers_title = $manufacturers_data->company_name;
        }
        return response()->json(['success'=>true,'total_count'=>$total_count,'baskets_data'=>$baskets_data],200);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function remove_product(){
        $this->basket->remove_product($this->data['basket_id']);
        return response()->json(['success'=>true,'message'=>'success removed'],200);

    }


    public function pay(){
        $total_amount = 0;
        $stripeLocal = new StripeLocal();
        $users_id = Sessions::get_users_id_by_token($this->data['token']);
        $baskets_data = $this->basket->get_product($users_id);
        $baskets = [];
        //Get Cards Data
        $stripe_cards_data = StripeCards::where('id','=',$this->data['card_id'])->first();
        $card_insert = [
            'card_number'=>$stripe_cards_data->card_number,
            'exp_month'=>$stripe_cards_data->exp_month,
            'exp_year'=>$stripe_cards_data->exp_year,
            'cvc'=>$stripe_cards_data->cvc
        ];
//        $card_insert = [
//            'card_number'=>'4000000000000077',
//            'exp_month'=>'4',
//            'exp_year'=>'2019',
//            'cvc'=>'314'
//        ];
//        $card_obj = $stripeLocal->create_token_card($card_insert);
//        $stripeLocal->charge_create($card_obj->id,20000);
//
        //Create Card Token
        try {
            $card_obj = $stripeLocal->create_token_card($card_insert);
        }catch (\Exception $e){
            return response()->json(['success'=>false,'message'=>$e->getMessage()],200);
        }

        //Work with baskets data array
        foreach ($baskets_data as $basket){
            $baskets[$basket->manufacturers_id][] = $basket;
        }

        if(!empty($baskets)) {
            //Get Sum
            $total_f = 0;
            foreach ($baskets as $key => $array) {
                $manufacturers_data = Manufacturers::where('id', '=', $key)->select('dop_amount', 'stripe_acc_id')->first();
                //Get Count For Manufacturers
                foreach ($array as $product) {
                    $product_data = ManufacturersProducts::where('id', '=', $product->product_id)->select('price')->first();
                    $total_f += $product_data->price * $product->count;
                }
                $price = ceil($total_f * 100);
                $amount_proc = $price*0.03+20 + $manufacturers_data->dop_amount * 100;
                $price += $amount_proc;
                $total_amount += $price;
            }
            //Pay
            try {
//                throw new \Exception();
                $stripeLocal->charge_create($card_obj->id, $total_amount);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'insufficient funds'], 200);
            }
            //Transfer
            foreach ($baskets as $key => $array) {
                $total = 0;
                $manufacturers_data = Manufacturers::where('id', '=', $key)->select('dop_amount', 'stripe_acc_id')->first();
                //Get Summ with proc and dop_ammout
                foreach ($array as $product) {
                    $product_data = ManufacturersProducts::where('id', '=', $product->product_id)->select('price')->first();
                    $total += (double)$product_data->price * (int)$product->count;
                }
                $price = ceil($total * 100);
                $amount_proc = $price*0.03+20 + $manufacturers_data->dop_amount * 100;
                $price += $amount_proc;
                //try pay
                try {
                    //Transfer to manufacturers account
                    $stripeLocal->transfer_amount($manufacturers_data->stripe_acc_id, $price);
                    //Transfer procent
                    $stripeLocal->transfer_amount('acct_1CGQKbBI4oLHjfpa', $amount_proc);
                    $order_data = $this->orders->create_new($users_id, $key);
                    foreach ($array as $product) {
                        $this->orders_product->create_new($order_data->id, $product->product_id, $product->count, $product_data->price);
                    }
                    $this->orders->update_data(['total_count' => $total], [['id', '=', $order_data->id]]);
                    $this->chat->create_new($order_data->id, 'Order#' . $order_data->id, $key);
                } catch (\Exception $e) {
                    return response()->json(['success' => false, 'message' => $e->getMessage()], 200);
                }
                $this->basket->delete_basket($key);
            }
            return response()->json(['success'=>true,'message'=>'success paid'],200);
        }else{
            return response()->json(['success'=>true,'message'=>'basket is empty'],200);

        }

    }


    /**
     * Destruct
     */
    public function __destruct(){
        unset($this->data);
        unset($this->basket);
        unset($this->orders);
        unset($this->orders_product);
    }
}

