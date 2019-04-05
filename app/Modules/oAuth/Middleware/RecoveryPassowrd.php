<?php
    /**
     * Created by PhpStorm.
     * User: Bogdan
     * Date: 13.11.17
     * Time: 19:54
     */
    namespace App\Modules\oAuth\Middleware;
    use Illuminate\Support\Facades\DB;
    use Closure;
    use Exception;

    class RecoveryPassowrd
    {
        /**
         * @param $request
         * @param Closure $next
         *
         * @return \Illuminate\Http\JsonResponse|mixed
         */
        public function handle($request, Closure $next)
        {
            try {
                $data = $request->post();
                $this->check_input_data($data);
                $result = $this->find_acc_in_bd_by_email($data['email']);
                $request->type = $result['type_acc'];
                $request->acc_id = $result['acc_id'];
                return $next($request);
            } catch (Exception $e) {
                return response()->json(['success' => false, "message" => $e->getMessage()], 400);
            }
        }


        /**
         * @param $data
         *
         * @throws Exception
         */
        private function check_input_data($data){
            $array_required = ['email'];
            foreach ($array_required as $required) {
                if(empty($data[$required])){
                    throw new Exception("empty ".$required, 404);
                }
            }
        }


        /**
         * @param $email
         *
         * @return array
         * @throws Exception
         */
        private function find_acc_in_bd_by_email($email){
            $users_data = DB::table('users')->where('email', '=', $email)->first();
            $admins_data = DB::table('admins')->where('email', '=', $email)->first();
            if(empty($admins_data) && empty($users_data)){
                throw new Exception('email was not found!');
            }else{
                if(!empty($admins_data)){
                    return ['type_acc' => 'admin', 'acc_id' => $admins_data->id];
                }
                if(!empty($users_data)){
                    return ['type_acc'=>'user','acc_id'=>$users_data->id];
                }
            }
        }

    }