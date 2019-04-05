<?php

namespace App\Model\Manufacturers;

use Illuminate\Database\Eloquent\Model;

class Manufacturers extends Model
{
    protected $table = 'manufacturers';
    protected $dateFormat = 'U';
    protected $fillable = ['id','merchant_id','dop_amount','stripe_acc_id','is_slider','admins_id','company_name','logo','location','website','is_active'];

    /**
     * @param $data
     *
     * @return mixed
     */
    public function create_new($data){
        return self::create($data);
    }

    /**
     * @param $id
     */
    public function delete_manufacturers($id){
        self::where('id','=',$id)->delete();
    }

    /**
     * @param $logo
     * @param $path_name
     *
     * @return string
     */
    public function upload_file($logo, $path_name){
        $path = public_path($path_name);
        $name = str_random(32) . '_' . date(time()) . '.' . $logo->extension();
        $logo->move($path, $name);
        return asset('public'.$path_name.$name);
    }

    /**
     * @param $file_link
     */
    public function delete_file($file_link){
        if(file_exists($file_link)) {
            unlink($file_link);
        }
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
