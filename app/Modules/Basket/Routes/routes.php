<?php
    Route::group( [ 'namespace' => 'App\Modules\Basket\Controllers',
        'as' => 'basket.',
        'https' => true,
    ], function() {

        //Add To Basket
        Route::post('/api/basket/add_product',[
            'middleware'=>'/api/basket/add_product',
            'uses'=>'BasketController@add_product'
        ]);

        //Get Basket
        Route::post('/api/basket/user/get',[
            'middleware'=>'/api/basket/user/get',
            'uses'=>'BasketController@get_user_basket'
        ]);

        //Remove Product In Basket
        Route::post('/api/basket/user/remove_product',[
            'middleware'=>'/api/basket/user/remove_product',
            'uses'=>'BasketController@remove_product'
        ]);


        Route::post('/api/basket/pay',[
//            'middleware'=>'/api/basket/pay',
            'uses'=>'BasketController@pay'
        ]);

    });
