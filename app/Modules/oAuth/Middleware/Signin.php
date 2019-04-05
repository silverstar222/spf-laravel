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

    class Signin
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
                $result = $this->check_exists_acc($data['password'],$data['email']);
                $request->type = $result['type_acc'];
                $request->acc_id = $result['acc_id'];
                return $next($request);
            } catch (Exception $e) {
                return response()->json(['success' => false, "message" => $e->getMessage()], 400);
            }
        }


        /**
         * @param $password
         * @param $email
         *
         * @return array
         * @throws Exception
         */
        private function check_exists_acc($password, $email){
            $admins_data = DB::table('admins')->where('email','=',$email)->first();
            $users_data = DB::table('users')->where('email','=',$email)->first();
            if(empty($admins_data) && empty($users_data)){
                throw new Exception('email was not found!');
            }else{
                if(!empty($admins_data)){
                    if($admins_data->password==md5($password) || $admins_data->temporary_password==md5($password)) {
                        return ['type_acc' => 'admin', 'acc_id' => $admins_data->id];
                    }else{
                        throw new Exception('incorrect password!');
                    }
                }
                if(!empty($users_data)){
                    if($users_data->password==md5($password) || $users_data->temporary_password==md5($password)) {
                        if($users_data->is_active==0){
                            throw new Exception('account is inactive!');
                        }else {
                            return ['type_acc' => 'user', 'acc_id' => $users_data->id];
                        }
                    }else{
                        throw new Exception('incorrect password!');
                    }
                }
            }
        }


        /**
         * @param $data
         *
         * @throws Exception
         */
        private function check_input_data($data){
            $array_required = ['password','email'];
            foreach ($array_required as $required) {
                if(empty($data[$required])){
                    throw new Exception("empty ".$required, 404);
                }
            }
        }

    }