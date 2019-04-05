<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
    use Illuminate\Support\Facades\Artisan;
    Route::get('/', function () {
        return view('welcome');
    });

    Route::get('/test', function () {
//        \App\Model\Admins\Admins::create([
//           'email'=>'admin@gmail.com',
//           'password'=>'823da4223e46ec671a10ea13d7823534'
//        ]);
        return view('avatar');
    });

    Route::get('/s',function (){
       return view('test');
    });

    Route::get('/migrate',function () {
        Artisan::call('migrate');
        return response()->json(['success'=>true],200);
    });
