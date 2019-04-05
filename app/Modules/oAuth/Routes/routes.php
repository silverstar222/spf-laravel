<?php
    Route::group( [ 'namespace' => 'App\Modules\oAuth\Controllers',
        'as' => 'oauth.',
//        'middleware' => 'oauth',
        'https' => true,
    ], function() {

        //Sign Up
        Route::post('/api/sign_up',[
            'middleware'=>'/api/sign_up',
            'uses'=>'oAuthController@sign_up'
        ]);

        //Sign in
        Route::post('/api/sign_in',[
            'middleware'=>'/api/sign_in',
            'uses'=>'oAuthController@sign_in'
        ]);

        //Recovery password
        Route::post('/api/recovery_password',[
            'middleware'=>'/api/recovery_password',
            'uses'=>'oAuthController@recovery_password'
        ]);

        //Get Manufacturers Logos
        Route::post('/api/slider/get_manufacturers_logos',[
            'uses'=>'oAuthController@get_manufacturers_logos'
        ]);


    });
