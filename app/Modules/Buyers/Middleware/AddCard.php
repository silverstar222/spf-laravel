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

    class AddCard
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
                if($res['type_acc']=='admin'){
                    throw new Exception('Incorrect token');
                }
                $this->find_buyers($res['id']);
                $this->find_card_number($res['id'],$data['card_number']);
                return $next($request);
            } catch (Exception $e) {
                return response()->json(['success' => false, "message" => $e->getMessage()], 400);
            }
        }


        /**
         * @param $user_id
         * @param $number
         *
         * @throws Exception
         */
        private function find_card_number($user_id, $number){
            $data = DB::table('stripe_card')->where('users_id','=',$user_id)
                ->where('card_number','=',$number)
                ->exists();
            if(!empty($data)){
                throw new Exception('card is already exists!');
            }
        }

        /**
         * @param $id
         *
         * @throws Exception
         */
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
            $array_required = ['token','card_number','exp_month','exp_year','cvc'];
            foreach ($array_required as $required) {
                if(empty($data[$required])){
                    throw new Exception("empty ".$required, 404);
                }
            }
        }

    }