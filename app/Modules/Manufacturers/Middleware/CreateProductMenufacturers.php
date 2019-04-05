<?php
    /**
     * Created by PhpStorm.
     * User: Bogdan
     * Date: 13.11.17
     * Time: 19:54
     */
    namespace App\Modules\Manufacturers\Middleware;
    use App\Http\Middleware\Api\MainMiddleware;
    use Illuminate\Support\Facades\DB;
    use Closure;
    use Exception;

    class CreateProductMenufacturers
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
//                if(!$request->file('logo')) {
//                    throw new Exception('logo is empty');
//                }
                $this->check_exists($data['title']);
                $this->check_manufacturers($data['manufacturers_id']);
                $request->admins_id = $res['id'];
                return $next($request);
            } catch (Exception $e) {
                return response()->json(['success' => false, "message" => $e->getMessage()], 400);
            }
        }

        /**
         * @param $manufacturers_id
         *
         * @throws Exception
         */
        private function check_manufacturers($manufacturers_id){
            $data = DB::table('manufacturers')->where('id','=',$manufacturers_id)->exists();
            if(empty($data)){
                throw new Exception('manufacturers was not found!');
            }
        }


        /**
         * @param $name
         *
         * @throws Exception
         */
        private function check_exists($name){
            $data = DB::table('manufacturers_products')->where('title','=',$name)->exists();
            if(!empty($data)){
                throw new Exception('product with name "'.$name.'" is already exists!');
            }
        }


        /**
         * @param $data
         *
         * @throws Exception
         */
        private function check_input_data($data){
            $array_required = ['title','price','description','token','manufacturers_id'];
            foreach ($array_required as $required) {
                if(empty($data[$required])){
                    throw new Exception("empty ".$required, 404);
                }
            }
        }

    }