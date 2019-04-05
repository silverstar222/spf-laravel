<?php

namespace App\Model\Basket;

use Illuminate\Database\Eloquent\Model;

class Basket extends Model
{
    protected $table = 'basket';
    protected $dateFormat = 'U';
    protected $fillable = ['id','users_id','product_id','manufacturers_id','count'];

    /**
     * @param $data
     *
     * @return mixed
     */
    public function create_new($data){
        return self::create($data);
    }


    /**
     * @param $users_id
     * @param $product_id
     *
     * @return bool
     */
    public function check_exists($users_id, $product_id){
        $data = self::where('users_id','=',$users_id)
            ->where('product_id','=',$product_id)
            ->select('count','id')
            ->first();
        if(!empty($data)){
            return $data;
        }else{
            return false;
        }
    }

    /**
     * @param $user_id
     *
     * @return mixed
     */
    public function get_product($user_id){
        return self::where('users_id','=',$user_id)->get();
    }

    /**
     * @param $id
     * @param $count
     */
    public function update_count($id, $count ,$man_id){
        self::where('id','=',$id)->update(['count'=>$count,'manufacturers_id'=>$man_id]);
    }

    /**
     * @param $id
     */
    public function remove_product($id){
        self::where('id','=',$id)->delete();
    }

    /**
     * @param $manufacturers_id
     */
    public function delete_basket($manufacturers_id){
        self::where('manufacturers_id','=',$manufacturers_id)->delete();
    }
}
