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

    class Signup
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
                $this->check_exists_acc($data['email']);
                $this->check_company_name($data['company_name']);
//                if(!$request->file('company_logo')) {
//                    throw new Exception('company logo is empty!');
//                }
                return $next($request);
            } catch (Exception $e) {
                return response()->json(['success' => false, "message" => $e->getMessage()], 400);
            }
        }

        /**
         * @param $type
         * @param $data
         *
         * @throws Exception
         */
        private function check_exists_acc($email){
            $admins_data = DB::table('admins')->where('email','=',$email)->first();
            $users_data = DB::table('users')->where('email','=',$email)->first();
            if(!empty($admins_data) || !empty($users_data)){
                throw new Exception('email is already exists in a system!');
            }
        }

        private function check_company_name($name){
            $users_data = DB::table('users')->where('company_name','=',$name)->first();
            if(!empty($admins_data) || !empty($users_data)){
                throw new Exception('company name is already exists in a system!');
            }
        }


        /**
         * @param $data
         *
         * @throws Exception
         */
        private function check_input_data($data){
            $array_required = ['email','password','company_name','phone_number',
                'business_name','delivery_address','manager_name'];
            foreach ($array_required as $required) {
                if(empty($data[$required])){
                    throw new Exception("empty ".$required, 404);
                }
            }
        }


    }