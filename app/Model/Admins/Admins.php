<?php

namespace App\Model\Admins;

use Illuminate\Database\Eloquent\Model;

class Admins extends Model
{
    protected $table = 'admins';
    protected $hidden = ['password','temporary_password'];
    protected $fillable=['email','password','type_acc','temporary_password'];
    protected $dateFormat = 'U';

    /**
     * @param $data
     *
     * @return mixed
     */
    public function create_new($data){
        return self::create($data);
    }

    /**
     * @param $data
     * @param $where_data
     *
     * @return mixed
     */
    public function update_data($data, $where_data){
        return self::where($where_data)->update($data);
    }
}
