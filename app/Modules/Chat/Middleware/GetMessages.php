<?php
    /**
     * Created by PhpStorm.
     * User: Bogdan
     * Date: 13.11.17
     * Time: 19:54
     */
    namespace App\Modules\Chat\Middleware;
    use App\Http\Middleware\Api\MainMiddleware;
    use Illuminate\Support\Facades\DB;
    use Closure;
    use Exception;

    class GetMessages
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
                $this->find_chat($data['chat_id']);
                return $next($request);
            } catch (Exception $e) {
                return response()->json(['success' => false, "message" => $e->getMessage()], 400);
            }
        }

        private function find_chat($id){
            $data = DB::table('chat')->where('id','=',$id)->exists();
            if(empty($data)){
                throw new Exception('chat was not found!');
            }
        }


        /**
         * @param $data
         *
         * @throws Exception
         */
        private function check_input_data($data){
            $array_required = ['token','chat_id'];
            foreach ($array_required as $required) {
                if(empty($data[$required])){
                    throw new Exception("empty ".$required, 404);
                }
            }
        }

    }