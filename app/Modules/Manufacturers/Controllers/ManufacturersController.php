<?php

namespace App\Modules\Manufacturers\Controllers;

use App\Http\Controllers\Controller;
use App\Model\Admins\Admins;
use App\Model\Manufacturers\ManufacturersProducts;
use App\Model\Manufacturers\ManufacturersProductsFiles;
use App\Model\Sessions;
use App\Model\Users\StripeLocal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Model\Users\Users;
use App\Model\Manufacturers\Manufacturers;
class ManufacturersController extends Controller{

    private $data,$manufacturers,$products,$products_file;

    /**
     * LoginController constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request) {
        $this->data = $request->post();
        $this->manufacturers = new Manufacturers();
        $this->products = new ManufacturersProducts();
        $this->products_file = new ManufacturersProductsFiles();
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_manufacturers_for_user(){
        $response = [];
        $users_id = Sessions::get_users_id_by_token($this->data['token']);
        $permissions_data = Users::where('id','=',$users_id)->select('permissions')->first();
        $permissions = explode(',',$permissions_data->permissions);
        if(!empty($permissions)){
            $response = $this->manufacturers->whereIn('id',$permissions)->get();
        }
        return response()->json(['success'=>true,'manufacturers_data'=>$response],200);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_manufacturers_for_admin(){
        $manufacturers = $this->manufacturers->get();
        if(!empty($manufacturers)){
            foreach ($manufacturers as $manufacturer) {
                $products = $this->products->where('manufacturers_id','=',$manufacturer->id)->get();
                if(!empty($products)){
                    foreach ($products as $product) {
                        $files_src = [];
                        $files = $this->products_file->where('manufacturers_products_id','=',$product->id)->select('src')->get();
                        if(!empty($files)){
                            foreach ($files as $file) {
                                $files_src[] = $file->src;
                            }
                        }
                        $product->files = $files_src;
                    }
                }
                $manufacturer->products = $products;
            }
        }
        return response()->json(['success'=>true,'manufacturers_data'=>$manufacturers],200);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_manufacturers_products_by_id(){
        $product_data = $this->products->where('id','=',$this->data['product_id'])->first();
        $files = $this->products_file->where('manufacturers_products_id','=',$this->data['product_id'])->select('src')->get();
        $files_src = [];
        if(!empty($files)){
            foreach ($files as $file) {
                $files_src[] = $file->src;
            }
        }
        $product_data->files = $files_src;
        return response()->json(['success'=>true,'product_data'=>$product_data],200);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_all_manufacturers_products(){
        $product_data = $this->products->where('manufacturers_id','=',$this->data['manufacturers_id'])->get();
        if(!empty($product_data)){
            foreach ($product_data as $product) {
                $files_src = [];
                $files = $this->products_file->where('manufacturers_products_id','=',$product->id)->select('src')->get();
                if(!empty($files)){
                    foreach ($files as $file) {
                        $files_src[] = $file->src;
                    }
                }
                $product->files = $files_src;
            }
        }
        return response()->json(['success'=>true,'manufacturers_id'=>$this->data['manufacturers_id'],'products_data'=>$product_data],200);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit_manufacturers(Request $request){
        $insert_data = [];
        $field_optional = ['company_name','location','website','merchant_id'];
        foreach ($field_optional as $field) {
            if(!empty($this->data[$field])){
                $insert_data = array_merge($insert_data,[$field=>$this->data[$field]]);
            }
        }
        if($request->file('logo')) {
            $manufacturers_data = $this->manufacturers->where('id','=',$this->data['manufacturers_id'])->select('logo')->first();
            if(!empty($manufacturers_data->logo)){
                $this->manufacturers->delete_file($manufacturers_data->logo);
            }
            $manufacturers_logo = $this->manufacturers->upload_file($request->file('logo'),'/manufacturers_logo/');
            $insert_data = array_merge($insert_data,['logo'=>$manufacturers_logo]);
        }
        if(!empty($insert_data)){
            $this->manufacturers->update_data($insert_data,[[
                'id','=',$this->data['manufacturers_id']
            ]]);
        }
        return response()->json(['success'=>true,'message'=>'success edit'],200);

    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_manufacturers_by_id(){
        $manufacturers = $this->manufacturers->where('id','=',$this->data['manufacturers_id'])->first();
        $manufacturers->products = [];
        $products = $this->products->where('manufacturers_id','=',$this->data['manufacturers_id'])->get();
        if(!empty($products)){
            foreach ($products as $product) {
                $files_src = [];
                $files = $this->products_file->where('manufacturers_products_id','=',$product->id)->select('src')->get();
                if(!empty($files)){
                    foreach ($files as $file) {
                        $files_src[] = $file->src;
                    }
                }
                $product->files = $files_src;
            }
            $manufacturers->products = $products;
        }
        return response()->json(['success'=>true,'manufacturers_data'=>$manufacturers],200);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function manufacturers_create(Request $request){
        try {
            $stripeLocal = new StripeLocal();
            $insert_data = [
                'admins_id' => $request->admins_id,
                'company_name' => $this->data['company_name'],
                'location' => $this->data['location'],
                'website' => $this->data['website'],
                'dop_amount' => $this->data['dop_amount']
            ];
            if ($request->file('logo')) {
                $manufacturers_logo = $this->manufacturers->upload_file($request->file('logo'), '/manufacturers_logo/');
                $insert_data = array_merge($insert_data, ['logo' => $manufacturers_logo]);
            }
            $stripe_acc = $stripeLocal->create_account();
            $insert_data = array_merge($insert_data, ['stripe_acc_id' => $stripe_acc->id]);
            $response = $this->manufacturers->create_new($insert_data);
            return response()->json(['success' => true, 'message' => 'success created', 'manufacturers_data' => $response], 200);
        }catch (\Exception $e){
            return response()->json(['success' => false, 'message' => $e->getMessage()], 200);

        }
    }

    //        $data = [
//            'card_number'=>'4000000000000077',
//            'exp_month'=>'4',
//            'exp_year'=>'2019',
//            'cvc'=>'314'
//        ];
//        $card_token = $stripeLocal->create_token_card($data);
//        $resp = $stripeLocal->charge_create($card_token->id);
//        $ch = \Stripe\Charge::retrieve($resp->id);
//        $ch->capture();
//        $stripe_acc = $stripeLocal->transfer_amount();

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function manufacturers_product_create(Request $request){
        $insert_data = [
            'title' => $this->data['title'],
            'price' => $this->data['price'],
            'description' => $this->data['description'],
            'manufacturers_id'=>$this->data['manufacturers_id']
        ];
        if($request->file('logo')) {
            $products_logo = $this->manufacturers->upload_file($request->file('logo'),'/products_logo/');
            $insert_data = array_merge($insert_data,['logo'=>$products_logo]);
        }
        $products_data = $this->products->create_new($insert_data);

        if($request->file('attachments')) {
            $files_data = [];
            foreach ($request->file('attachments') as $file) {
                $src = $this->manufacturers->upload_file($file,'/products_files/');
                $files_data = array_merge($files_data,[$src]);
                $this->products_file->create_new([
                    'src' => $src,
                    'manufacturers_products_id' => $products_data->id
                ]);
            }
            $products_data->attachments = $files_data;
        }

        return response()->json(['success'=>true,'message'=>'success added product','products_data'=>$products_data],200);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function manufacturers_product_delete(){
        $products_files = $this->products_file->where('manufacturers_products_id','=',$this->data['manufacturers_products_id'])
            ->select('src')->get();
        $products = $this->products->where('id','=',$this->data['manufacturers_products_id'])
            ->select('logo')
            ->first();
        if(!empty($products_files)){
            foreach ($products_files as $src) {
                $this->manufacturers->delete_file($src->src);
            }
        }
        if(!empty($products->logo)){
            $this->manufacturers->delete_file($products->logo);
        }
        $this->products->delete_product($this->data['manufacturers_products_id']);
        return response()->json(['success'=>true,'message'=>'success deleted product'],200);

    }


    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function manufacturers_delete(){
        $manufacturers_data = $this->manufacturers->where('manufacturers.id','=',$this->data['manufacturers_id'])
            ->join('manufacturers_products','manufacturers.id','=','manufacturers_products.manufacturers_id')
            ->select('manufacturers.logo as manlogo','manufacturers_products.logo as prodlogo')
            ->first();
        $products_files = $this->products_file->where('manufacturers_products_id','=',$this->data['manufacturers_id'])
            ->select('src')->get();
        if(!empty($products_files)){
            foreach ($products_files as $src) {
                $this->manufacturers->delete_file($src);
            }
        }
        if(!empty($manufacturers_data->manlogo)){
           $this->manufacturers->delete_file($manufacturers_data->manlogo);
        }
        if(!empty($manufacturers_data->prodlogo)){
            $this->manufacturers->delete_file($manufacturers_data->prodlogo);
        }
        $this->manufacturers->delete_manufacturers($this->data['manufacturers_id']);
        return response()->json(['success'=>true,'message'=>'success deleted'],200);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function manufacturers_slider_set(){
        if(!empty($this->data['active'])){
            $active = 1;
        }else{
            $active = 0;
        }
        $this->manufacturers->update_data(['is_slider'=>$active],[['id','=',$this->data['manufacturers_id']]]);
        return response()->json(['success'=>true,'message'=>'success edited'],200);
    }


    /**
     * Destruct
     */
    public function __destruct(){
        unset($this->products);
        unset($this->manufacturers);
        unset($this->data);
        unset($this->products_file);
    }
}

