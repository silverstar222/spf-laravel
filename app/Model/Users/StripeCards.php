<?php

namespace App\Model\Users;

use Illuminate\Database\Eloquent\Model;

class StripeCards extends Model
{
    protected $table = 'stripe_card';
    protected $hidden = ['stripe_card_id'];
    protected $fillable=['users_id','card_number','exp_month','exp_year','brand'
        ,'cvc','stripe_card_id'];
    protected $dateFormat = 'U';


    /**
     * @param $data
     *
     * @return mixed
     */
    public function create_new($data){
        return self::create($data);
    }
}
