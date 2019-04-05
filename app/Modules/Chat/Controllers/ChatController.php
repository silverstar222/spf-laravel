<?php

namespace App\Modules\Chat\Controllers;

use App\Http\Controllers\Controller;
use App\Model\Admins\Admins;
use App\Model\Chat\Chat;
use App\Model\Chat\ChatMessages;
use App\Model\Manufacturers\Manufacturers;
use App\Model\Orders\Orders;
use App\Model\Sessions;
use App\Model\Users\StripeCards;
use App\Model\Users\StripeLocal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Model\Users\Users;
class ChatController extends Controller{

    private $data,$chat,$chat_messages;

    /**
     * LoginController constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request) {
        $this->data = $request->post();
        $this->chat = new Chat();
        $this->chat_messages = new ChatMessages();
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_message(){
        if(!empty($this->data['last_message_id'])){
            if(!$this->chat_messages->find_message($this->data['last_message_id'])){
                return response()->json(['success'=>false,'message'=>'last_message_id was not found'],200);
            }else{
                $messages = $this->chat_messages->where('id','>',$this->data['last_message_id'])->get();
            }
        }else{
            $messages = $this->chat_messages->where('chat_id','=',$this->data['chat_id'])->get();
        }
        return response()->json(['success'=>true,'messages'=>$messages],200);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_chat_for_admin(){
        $chats = $this->chat->get_all_chats();
        foreach ($chats as $chat) {
            $man_data = Manufacturers::where('id','=',$chat->manufacturers_id)->select('logo','company_name')->first();
            if(!empty($man_data)) {
                $chat->manufacturers_logo = $man_data->logo;
                $chat->manufacturers_company_name = $man_data->company_name;
            }else{
                $chat->manufacturers_logo = null;
                $chat->manufacturers_company_name = null;
            }
        }
        return response()->json(['success'=>true,'chats_data'=>$chats],200);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function message_send(Request $request){
        $acc_from = $request->acc_id;
        $acc_to = null;
        $acc_type_from = $request->type_acc;
        $acc_type_to = null;
        switch ($request->type_acc){
            case 'admin':
                $chat_data = $this->chat->where('id','=',$this->data['chat_id'])->select('orders_id')->first();
                $orders_data = Orders::where('id','=',$chat_data->orders_id)->select('users_id')->first();
                $acc_type_to = 'user';
                $acc_to = $orders_data->users_id;
            break;

            case 'user':
                $acc_type_to = 'admin';
                $acc_to = 1;
            break;
        }
        $message = '';
        switch ($this->data['type_message']){
            case 'file':
                $message = $this->chat->upload_file($request->file('message'));
            break;

            case 'text':
                $message = $this->data['message'];
            break;

            default:
                $message = $this->data['message'];
            break;
        }

        $message_data = $this->chat_messages->create([
            'chat_id'=>$this->data['chat_id'],
            'from'=>$acc_from,
            'to'=>$acc_to,
            'from_type'=>$acc_type_from,
            'to_type'=>$acc_type_to,
            'type_message'=>$this->data['type_message'],
            'message'=>$message
        ]);

        return response()->json(['success'=>true,'message_data'=>$message_data],200);
    }

    /**
     * Destruct
     */
    public function __destruct(){
        unset($this->data);
        unset($this->chat);
        unset($this->chat_messages);
    }
}

