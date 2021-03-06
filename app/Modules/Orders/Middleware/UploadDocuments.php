<?php
    /**
     * Created by PhpStorm.
     * User: Bogdan
     * Date: 13.11.17
     * Time: 19:54
     */
    namespace App\Modules\Orders\Middleware;
    use App\Http\Middleware\Api\MainMiddleware;
    use Illuminate\Support\Facades\DB;
    use Closure;
    use Exception;

    class UploadDocuments
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
                if(empty($request->file('file'))){
                    throw new Exception('file is empty');
                }
                $this->find_orders_id($data['orders_id']);
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
        private function find_orders_id($id){
            $data = DB::table('orders')->where('id','=',$id)->exists();
            if(empty($data)){
                throw new Exception('orders was not found');
            }
        }

        /**
         * @param $data
         *
         * @throws Exception
         */
        private function check_input_data($data){
            $array_required = ['token','orders_id'];
            foreach ($array_required as $required) {
                if(empty($data[$required])){
                    throw new Exception("empty ".$required, 404);
                }
            }
        }

    }