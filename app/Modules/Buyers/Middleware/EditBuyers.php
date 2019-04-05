<?php
    /**
     * Created by PhpStorm.
     * User: Bogdan
     * Date: 13.11.17
     * Time: 19:54
     */
    namespace App\Modules\Buyers\Middleware;
    use App\Http\Middleware\Api\MainMiddleware;
    use Illuminate\Support\Facades\DB;
    use Closure;
    use Exception;

    class EditBuyers
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
                $res = MainMiddleware::check_exists_token($data['token']);
//                if($res['type_acc']=='user'){
//                    throw new Exception('Incorrect token');
//                }
                $this->find_buyers($data['buyers_id']);
                if(!empty($data['email'])) {
                    $this->check_email($data);
                }
                return $next($request);
            } catch (Exception $e) {
                return response()->json(['success' => false, "message" => $e->getMessage()], 400);
            }
        }

        private function check_email($data){
            $data = DB::table('users')->where('id','!=',$data['buyers_id'])
                ->where('email','=',$data['email'])
                ->exists();
            if(!empty($data)){
                throw new Exception('email is already exists in a system!');
            }
        }

        private function find_buyers($id){
            $data = DB::table('users')->where('id','=',$id)->exists();
            if(empty($data)){
                throw new Exception('buyers was not found!');
            }
        }


        /**
         * @param $data
         *
         * @throws Exception
         */
        private function check_input_data($data){
            $array_required = ['token','buyers_id'];
            foreach ($array_required as $required) {
                if(empty($data[$required])){
                    throw new Exception("empty ".$required, 404);
                }
            }
        }

    }