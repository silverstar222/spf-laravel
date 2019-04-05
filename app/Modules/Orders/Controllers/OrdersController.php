<?php

namespace App\Modules\Orders\Controllers;

use App\Http\Controllers\Controller;
use App\Model\Admins\Admins;
use App\Model\Chat\Chat;
use App\Model\Manufacturers\Manufacturers;
use App\Model\Manufacturers\ManufacturersProducts;
use App\Model\Orders\Orders;
use App\Model\Orders\OrdersDocuments;
use App\Model\Orders\OrdersProducts;
use App\Model\Sessions;
use App\Model\Users\StripeCards;
use App\Model\Users\StripeLocal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Model\Users\Users;
class OrdersController extends Controller{

    private $data,$orders,$orders_products,$orders_documents;

    /**
     * LoginController constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request) {
        $this->data = $request->post();
        $this->orders = new Orders();
        $this->orders_products = new OrdersProducts();
        $this->orders_documents = new OrdersDocuments();
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_orders(){
        $users_id = Sessions::get_users_id_by_token($this->data['token']);
        $orders = $this->orders->get_orders_by_users_id($users_id);
        foreach ($orders as $order) {
            $order->order_date = date('d/m/Y',strtotime($order->created_at));
            $man_data = Manufacturers::where('id','=',$order->manufacturers_id)->select('logo')->first();
            if(!empty($man_data->logo)){
                $order->manufacturers_logo = $man_data->logo;
            }else{
                $order->manufacturers_logo = null;
            }
        }
        return response()->json(['success'=>true,'orders_data'=>$orders],200);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_orders_by_id(){
        $orders_data = $this->orders->get_orders_by_id($this->data['orders_id']);
        $products = $this->orders_products->get_orders_product($this->data['orders_id']);
        $documents = $this->orders_documents->get_documents_by_orders($this->data['orders_id']);
        $man_data = Manufacturers::where('id','=',$orders_data->manufacturers_id)->select('company_name','logo')->first();
        if(!empty($man_data)){
            $orders_data->manufacturers_company_logo =  $man_data->logo;
            $orders_data->manufacturers_company_name =  $man_data->company_name;
        }
        foreach ($products as $product) {
            $prod_data = ManufacturersProducts::where('id','=',$product->product_id)->select('logo')->first();
            if(!empty($prod_data->logo)) {
                $product->product_logo = $prod_data->logo;
            }else{
                $product->product_logo = null;
            }
        }

        $chat_data = Chat::where('orders_id','=',$this->data['orders_id'])->select('id')->first();
        $users_data = Users::where('id','=',$orders_data->users_id)->select('delivery_address')->first();
        $orders_data->chat_id = $chat_data->id;
        $orders_data->delivery_location = $users_data->delivery_address;
        $orders_data->order_date = date('d/m/Y',strtotime($orders_data->created_at));
        $orders_data->products = $products;
        $orders_data->documents = $documents;

        return response()->json(['success'=>true,'orders_data'=>$orders_data],200);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_orders_for_admin(){
        $orders = $this->orders->get_all_orders();
        foreach ($orders as $order) {
            $chat_data = Chat::where('orders_id','=',$order->id)->select('id','chat_name')->first();
            $order->order_date = date('d/m/Y',strtotime($order->created_at));
            $man_data = Manufacturers::where('id','=',$order->manufacturers_id)->select('logo','company_name')->first();
            if(!empty($man_data)){
                $order->manufacturers_company_name = $man_data->company_name;
                $order->manufacturers_logo = $man_data->logo;
            }else{
                $order->manufacturers_company_name = null;
                $order->manufacturers_logo = null;
            }
            $order->chat_id = $chat_data->id;
            $order->order_name = $chat_data->chat_name;
        }
        return response()->json(['success'=>true,'orders_data'=>$orders],200);

    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function add_documents_to_orders(Request $request){
        $link = $this->orders_documents->upload_file($request->file('file'));
        $doc_data = $this->orders_documents->create_new([
            'orders_id'=>$this->data['orders_id'],
            'link'=>$link,
            'name'=>'Invoice order#'.$this->data['orders_id']
        ]);
        return response()->json(['success'=>true,'orders_documents_data'=>$doc_data],200);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function remove_documents(){
        $link = $this->orders_documents->get_documents_file($this->data['documents_id']);
        $this->orders_documents->remove_file($link);
        $this->orders_documents->remove($this->data['documents_id']);
        return response()->json(['success'=>true,'message'=>'success deleted'],200);
    }


    /**
     * Destruct
     */
    public function __destruct(){
        unset($this->data);
        unset($this->orders);
        unset($this->orders_products);
    }
}

