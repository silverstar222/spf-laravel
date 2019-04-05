<?php
    Route::group( [ 'namespace' => 'App\Modules\Buyers\Controllers',
        'as' => 'buyers.',
//        'middleware' => 'oauth',
        'https' => true,
    ], function() {


        //Get All Buyers
        Route::post('/api/buyers/admin/get_all',[
            'middleware'=>'/api/buyers/admin/get_all',
            'uses'=>'BuyersController@get_all_buyers_by_admin'
        ]);

        //Get Buyers By Id
        Route::post('/api/buyers/admin/get_by_id',[
            'middleware'=>'/api/buyers/admin/get_by_id',
            'uses'=>'BuyersController@get_buyers_by_id'
        ]);

        //Get Count Of New Buyers
        Route::post('/api/admin/get_count_of_new',[
            'middleware'=>'/api/buyers/admin/get_count_of_new',
            'uses'=>'BuyersController@get_count_of_new_buyers_and_messages'
        ]);

        //Search Buyers
        Route::post('/api/buyers/admin/search',[
            'middleware'=>'/api/buyers/admin/search',
            'uses'=>'BuyersController@search_buyers_by_admin'
        ]);

        //Invite Buyers By Email
        Route::post('/api/buyers/invite_by_email',[
            'middleware'=>'/api/buyers/invite_by_email',
            'uses'=>'BuyersController@invite_by_email'
        ]);

        //Delete Buyers
        Route::post('/api/buyers/delete',[
            'middleware'=>'/api/buyers/delete',
            'uses'=>'BuyersController@delete_buyers'
        ]);

        //Edit Buyers
        Route::post('/api/buyers/edit',[
            'middleware'=>'/api/buyers/edit',
            'uses'=>'BuyersController@edit_buyers'
        ]);

        //Edit Buyers Permissions
        Route::post('/api/buyers/permissions/edit',[
            'middleware'=>'/api/buyers/permissions/edit',
            'uses'=>'BuyersController@edit_buyers_permissions'
        ]);

        //(In)active buyers
        Route::post('/api/buyers/status/set',[
            'middleware'=>'/api/buyers/status/set',
            'uses'=>'BuyersController@edit_buyers_status'
        ]);

        Route::post('/api/buyers/payment/card/add',[
            'middleware'=>'/api/buyers/payment/card/add',
            'uses'=>'BuyersController@add_buyers_card'
        ]);

        Route::post('/api/buyers/payment/card/delete',[
            'middleware'=>'/api/buyers/payment/card/delete',
            'uses'=>'BuyersController@delete_card'
        ]);

    });
