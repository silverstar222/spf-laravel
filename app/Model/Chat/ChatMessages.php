<?php

namespace App\Model\Chat;

use Illuminate\Database\Eloquent\Model;

class ChatMessages extends Model
{
    protected $table = 'chat_messages';
    protected $dateFormat = 'U';
    protected $fillable = ['id','chat_id','from','to','from_type','to_type','type_message','message'];


    /**
     * @param $id
     *
     * @return bool
     */
    public function find_message($id){
        $data = self::where('id','=',$id)->exists();
        if(empty($data)){
            return false;
        }else{
            return true;
        }
    }
}
