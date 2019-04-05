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

    class DeleteCard
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
                $this->find_card($data['card_id']);
                return $next($request);
            } catch (Exception $e) {
                return response()->json(['success' => false, "message" => $e->getMessage()], 400);
            }
        }


        /**
         * @param $id
         *
         * @throws Exception
         */
        private function find_card($id){
            $data = DB::table('stripe_card')->where('id','=',$id)
                ->exists();
            if(empty($data)){
                throw new Exception('card does not found!');
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
            $array_required = ['token','card_id'];
            foreach ($array_required as $required) {
                if(empty($data[$required])){
                    throw new Exception("empty ".$required, 404);
                }
            }
        }

    }