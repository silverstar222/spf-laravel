<?php

namespace App\Http\Middleware\Api;
use App\Model\Sessions;
use Illuminate\Support\Facades\DB;
use Closure;

class MainMiddleware
{
    public static function check_exists_token($token){
        $data = Sessions::where('token','=',$token)->first();
        if(empty($data)){
            throw new \Exception('Token was not found!');
        }else{
            if(!empty($data->admins_id)){
                return ['type_acc'=>'admin','id'=>$data->admins_id];
            }else{
                return ['type_acc'=>'user','id'=>$data->users_id];
            }
        }
    }
}
