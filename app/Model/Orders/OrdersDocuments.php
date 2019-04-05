<?php

namespace App\Model\Orders;

use Illuminate\Database\Eloquent\Model;

class OrdersDocuments extends Model
{
    protected $table = 'orders_documents';
    protected $dateFormat = 'U';
    protected $fillable = ['id','orders_id','name','link'];

    /**
     * @param $id
     *
     * @return mixed
     */
    public function get_documents_by_orders($id){
        return self::where('orders_id','=',$id)->get();
    }


    /**
     * @param $data
     *
     * @return mixed
     */
    public function create_new($data){
        return self::create($data);
    }

    /**
     * @param $file
     *
     * @return string
     */
    public function upload_file($file){
        $path = public_path('/orders');
        $name = str_random(32) . '_' . date(time()) . '.' . $file->extension();
        $file->move($path, $name);
        return asset('public/orders/'.$name);
    }


    /**
     * @param $id
     *
     * @return mixed
     */
    public function get_documents_file($id){
        $data = self::where('id','=',$id)->select('link')->first();
        return $data->link;
    }

    /**
     * @param $link
     */
    public function remove_file($link){
        if(file_exists($link)){
            unset($link);
        }
    }

    /**
     * @param $id
     */
    public function remove($id){
        self::where('id','=',$id)->delete();
    }
}
