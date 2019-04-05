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

    class EditBuyersPermissions
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
                if($res['type_acc']=='user'){
                    throw new Exception('Incorrect token');
                }
                $this->find_buyers($data['buyers_id']);
                $this->find_manufacturers($data['manufacturers_id']);
                $this->check_value($data);
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
        private function find_buyers($id){
            $data = DB::table('users')->where('id','=',$id)->exists();
            if(empty($data)){
                throw new Exception('buyers was not found!');
            }
        }


        /**
         * @param $id
         *
         * @throws Exception
         */
        private function find_manufacturers($id){
            $data = DB::table('manufacturers')->where('id','=',$id)->exists();
            if(empty($data)){
                throw new Exception('manufacturers was not found!');
            }
        }

        private function check_value($data){
            $fields = [0,1];
            if(!in_array($data['value'],$fields)){
                throw  new Exception('incorrect value');
            }
        }


        /**
         * @param $data
         *
         * @throws Exception
         */
        private function check_input_data($data){
            $array_required = ['token','buyers_id','manufacturers_id'];
            foreach ($array_required as $required) {
                if(empty($data[$required])){
                    throw new Exception("empty ".$required, 404);
                }
            }
        }

    }