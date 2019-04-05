<?php

namespace App\Model\Manufacturers;

use Illuminate\Database\Eloquent\Model;

class ManufacturersProducts extends Model
{
    protected $table = 'manufacturers_products';
    protected $dateFormat = 'U';
    protected $fillable = ['id','manufacturers_id','title','logo','price','description','is_active'];

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
    public function delete_product($id){
        self::where('id','=',$id)->delete();
    }
}
