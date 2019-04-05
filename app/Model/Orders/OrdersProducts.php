<?php

namespace App\Model\Orders;

use Illuminate\Database\Eloquent\Model;

class OrdersProducts extends Model
{
    protected $table = 'orders_products';
    protected $dateFormat = 'U';
    protected $fillable = ['id','orders_id','product_id','count','price'];


    /**
     * @param $orders_id
     * @param $prod_id
     * @param $count
     * @param $price
     *
     * @return mixed
     */
    public function create_new($orders_id, $prod_id, $count,$price){
        return self::create([
            'orders_id'=> $orders_id,
            'product_id'=>$prod_id,
            'count'=>$count,
            'price'=>$price
        ]);
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function get_orders_product($id){
        return self::where('orders_id','=',$id)->get();
    }
}
