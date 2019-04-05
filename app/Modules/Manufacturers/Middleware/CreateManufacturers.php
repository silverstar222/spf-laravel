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

    class CreateManufacturers
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
                $this->check_exists($data['company_name']);
                $request->admins_id = $res['id'];
                return $next($request);
            } catch (Exception $e) {
                return response()->json(['success' => false, "message" => $e->getMessage()], 400);
            }
        }


        /**
         * @param $name
         *
         * @throws Exception
         */
        private function check_exists($name){
            $data = DB::table('manufacturers')->where('company_name','=',$name)->exists();
            if(!empty($data)){
                throw new Exception('manufacturers with name "'.$name.'" is already exists!');
            }
        }


        /**
         * @param $data
         *
         * @throws Exception
         */
        private function check_input_data($data){
            $array_required = ['company_name','location','website','token','dop_amount'];
            foreach ($array_required as $required) {
                if(empty($data[$required])){
                    throw new Exception("empty ".$required, 404);
                }
            }
        }

    }