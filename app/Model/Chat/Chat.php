<?php

namespace App\Model\Chat;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $table = 'chat';
    protected $dateFormat = 'U';
    protected $fillable = ['id','orders_id','chat_name','manufacturers_id','is_active'];

    /**
     * @param $orders_id
     * @param $name
     * @param $man_id
     *
     * @return mixed
     */
    public function create_new($orders_id, $name, $man_id){
        return self::create([
            'orders_id'=> $orders_id,
            'chat_name'=>$name,
            'manufacturers_id'=>$man_id
        ]);
    }


    /**
     * @return mixed
     */
    public function get_all_chats(){
        return self::get();
    }


    /**
     * @param $file
     *
     * @return string
     */
    public function upload_file($file){
        $path = public_path('/chat_files');
        $name = str_random(32) . '_' . date(time()) . '.' . $file->extension();
        $file->move($path, $name);
        return asset('public/chat_files/'.$name);
    }
}
