<?php
    /**
     * Created by PhpStorm.
     * User: Bogdan
     * Date: 13.11.17
     * Time: 19:54
     */
    namespace App\Modules\Basket\Middleware;
    use App\Http\Middleware\Api\MainMiddleware;
    use Illuminate\Support\Facades\DB;
    use Closure;
    use Exception;

    class AddToBasket
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
                    throw new Exception('incorrect token');
                }
                $this->check_exists_product($data['product_id']);
                if(preg_match('@[A-z]@u',$data['count'])){
                    throw new Exception('incorrect count');
                }
                return $next($request);
            } catch (Exception $e) {
                return response()->json(['success' => false, "message" => $e->getMessage()], 400);
            }
        }


        /**
         * @param $product_id
         *
         * @throws Exception
         */
        private function check_exists_product($product_id){
            $data = DB::table('manufacturers_products')->where('id','=',$product_id)->exists();
            if(empty($data)){
                throw new Exception('Product does not exists!');
            }
        }


        /**
         * @param $data
         *
         * @throws Exception
         */
        private function check_input_data($data){
            $array_required = ['token','product_id','count'];
            foreach ($array_required as $required) {
                if(empty($data[$required])){
                    throw new Exception("empty ".$required, 404);
                }
            }
        }

    }