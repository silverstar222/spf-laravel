<?php
    Route::group( [ 'namespace' => 'App\Modules\Orders\Controllers',
        'as' => 'orders.',
//        'middleware' => 'oauth',
        'https' => true,
    ], function() {

        //Get User Orders
        Route::post('/api/user/orders/get',[
            'middleware'=>'/api/user/orders/get',
            'uses'=>'OrdersController@get_orders'
        ]);

        //Get Orders For Admin
        Route::post('/api/admin/orders/get',[
            'middleware'=>'/api/admin/orders/get',
            'uses'=>'OrdersController@get_orders_for_admin'
        ]);

        //Get Orders By Id
        Route::post('/api/user/orders/get_by_id',[
            'middleware'=>'/api/user/orders/get_by_id',
            'uses'=>'OrdersController@get_orders_by_id'
        ]);


        //Upload Documents
        Route::post('/api/admin/orders/document/add',[
            'middleware'=>'/api/admin/orders/document/add',
            'uses'=>'OrdersController@add_documents_to_orders'
        ]);


        //Remove Documents
        Route::post('/api/admin/orders/document/remove',[
            'middleware'=>'/api/admin/orders/document/remove',
            'uses'=>'OrdersController@remove_documents'
        ]);

    });
