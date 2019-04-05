<?php

namespace App\Model\Users;

use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
    protected $table = 'users';
    protected $hidden = ['password','temporary_password','customer_stripe_id'];
    protected $fillable=['email','company_name','password','company_logo'
        ,'phone_number','business_name','delivery_address','customer_stripe_id','manager_name','permissions','is_active','is_paid','is_new','temporary_password'];
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

    /**
     * @param $id
     */
    public function delete_buyers($id){
        self::where('id','=',$id)->delete();
    }

    /**
     * @param $avatar
     *
     * @return string
     */
    public function upload_file($avatar){
        $path = public_path('/company_logo');
        $name = str_random(32) . '_' . date(time()) . '.' . $avatar->extension();
        $avatar->move($path, $name);
        return asset('public/company_logo/'.$name);
    }
}
