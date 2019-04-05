<?php

namespace App\Model\Users;

use Illuminate\Database\Eloquent\Model;

class StripeLocal extends Model{
    const stripe_key = 'sk_test_5dFXmIq3nbPmOgPWSf5ugg9r';

    /**
     * StripeLocal constructor.
     */
    public function __construct(){
        \Stripe\Stripe::setApiKey(self::stripe_key);
    }

    /**
     * @param $email
     *
     * @return \Stripe\ApiResource
     */
    public function create_customer($email){
        return \Stripe\Customer::create([
            "email" => $email
        ]);
    }


    /**
     * @param $customer_id
     *
     * @return \Stripe\StripeObject
     */
    public function retrieve($customer_id){
        return \Stripe\Customer::retrieve($customer_id);
    }


    /**
     * @param $id
     *
     * @return mixed
     */
    public function get_stripe_customer_id($id){
        $data = Users::where('id','=',$id)->select('customer_stripe_id')->first();
        return $data->customer_stripe_id;
    }


    /**
     * @return \Stripe\ApiResource
     */
    public function create_account(){
        $data =  \Stripe\Account::create(array(
            "type" => "custom",
            "country" => "US",
//            "dob"=>[
//                "day"=>"10",
//                "month"=>"10",
//                "year"=>"2000"
//            ]

        ));
//        $account = \Stripe\Account::retrieve($data->id);
//        $account->legal_entity->first_name = "name";
//        $account->legal_entity->last_name = "name";
//        $account->legal_entity->account_holder_type = "company";
//        $account->save();
        return $data;
    }

    public function transfer_amount($destination,$amount){
        return \Stripe\Transfer::create(array(
            "amount" => $amount,
            "currency" => "usd",
            "destination" => $destination,
            "transfer_group" => "ORDER_95"
        ));
    }

    public function charge_create($tok,$amount){
        return \Stripe\Charge::create(array(
            "amount" => $amount,
            "currency" => "usd",
            "source" => $tok,
            "description" => "Charge for SPF"
        ));
    }


    /**
     * @param $id
     *
     * @return mixed
     */
    public function get_stripe_card_id($id){
        $data = StripeCards::where('id','=',$id)->select('stripe_card_id')->first();
        return $data->stripe_card_id;
    }


    /**
     * @param $customer
     * @param $card_id
     */
    public function delete_card($customer, $card_id){
        $customer->sources->retrieve($card_id)->delete();
    }


    /**
     * @param $data
     *
     * @return \Stripe\ApiResource
     */
    public function create_token_card($data){
        return \Stripe\Token::create(array(
            "card" => array(
                "number" => $data['card_number'],
                "exp_month" => $data['exp_month'],
                "exp_year" => $data['exp_year'],
                "cvc" => $data['cvc']
            )
        ));
    }
}