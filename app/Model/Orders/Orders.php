<?php

namespace App\Model\Orders;

use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    protected $table = 'orders';
    protected $dateFormat = 'U';
    protected $fillable = ['id','users_id','manufacturers_id','is_active','total_count'];


    /**
     * @param $users_id
     * @param $man_id
     *
     * @return mixed
     */
    public function create_new($users_id, $man_id){
        return self::create([
            'users_id'=>$users_id,
            'manufacturers_id'=>$man_id
        ]);
    }

    /**
     * @param $data
     * @param $where_data
     */
    public function update_data($data, $where_data){
        self::where($where_data)->update($data);
    }


    /**
     * @param $id
     *
     * @return mixed
     */
    public function get_orders_by_users_id($id){
        return self::where('users_id','=',$id)->orderBy('created_at','desc')->get();
    }


    /**
     * @return mixed
     */
    public function get_all_orders(){
        return self::orderBy('created_at','desc')->get();
    }


    /**
     * @param $id
     *
     * @return mixed
     */
    public function get_orders_by_id($id){
        return self::where('id','=',$id)->first();
    }
}
