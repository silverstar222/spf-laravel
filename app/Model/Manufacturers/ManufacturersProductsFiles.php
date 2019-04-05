<?php

namespace App\Model\Manufacturers;

use Illuminate\Database\Eloquent\Model;

class ManufacturersProductsFiles extends Model
{
    protected $table = 'manufacturers_products_files';
    protected $dateFormat = 'U';
    protected $fillable = ['id','manufacturers_products_id','src','is_active'];

    /**
     * @param $data
     *
     * @return mixed
     */
    public function create_new($data){
        return self::create($data);
    }
}
