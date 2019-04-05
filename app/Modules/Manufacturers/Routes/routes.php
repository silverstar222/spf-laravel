<?php
    Route::group( [ 'namespace' => 'App\Modules\Manufacturers\Controllers',
        'as' => 'manufacturers.',
//        'middleware' => 'oauth',
        'https' => true,
    ], function() {

        //Create Manufacturers
        Route::post('/api/manufacturers/create',[
            'middleware'=>'/api/manufacturers/create',
            'uses'=>'ManufacturersController@manufacturers_create'
        ]);

        //Create Manufacturers Products
        Route::post('/api/manufacturers/product/create',[
            'middleware'=>'/api/manufacturers/product/create',
            'uses'=>'ManufacturersController@manufacturers_product_create'
        ]);

        //Get All Manufacturers For Admin
        Route::post('/api/manufacturers/get_for_admin',[
            'middleware'=>'/api/manufacturers/get_for_admin',
            'uses'=>'ManufacturersController@get_manufacturers_for_admin'
        ]);

        //Get All Manufacturers For User
        Route::post('/api/manufacturers/get_for_user',[
            'middleware'=>'/api/manufacturers/get_for_user',
            'uses'=>'ManufacturersController@get_manufacturers_for_user'
        ]);

        //Edit Manufacturers
        Route::post('/api/manufacturers/edit',[
            'middleware'=>'/api/manufacturers/edit',
            'uses'=>'ManufacturersController@edit_manufacturers'
        ]);

        //Get Products By  Manufacturers Id
        Route::post('/api/manufacturers/products/get_by_id',[
            'middleware'=>'/api/manufacturers/products/get_by_id',
            'uses'=>'ManufacturersController@get_manufacturers_products_by_id'
        ]);

        //Get Products By  Manufacturers Id
        Route::post('/api/manufacturers/products/get_all',[
            'middleware'=>'/api/manufacturers/products/get_all',
            'uses'=>'ManufacturersController@get_all_manufacturers_products'
        ]);

        //Get Manufacturers By Id
        Route::post('/api/manufacturers/get_by_id',[
            'middleware'=>'/api/manufacturers/get_by_id',
            'uses'=>'ManufacturersController@get_manufacturers_by_id'
        ]);


        //Delete Manufacturers
        Route::post('/api/manufacturers/delete',[
            'middleware'=>'/api/manufacturers/delete',
            'uses'=>'ManufacturersController@manufacturers_delete'
        ]);

        //Delete Manufacturers
        Route::post('/api/manufacturers/product/delete',[
            'middleware'=>'/api/manufacturers/product/delete',
            'uses'=>'ManufacturersController@manufacturers_product_delete'
        ]);


        //Delete Manufacturers
        Route::post('/api/manufacturers/slider/set',[
            'middleware'=>'/api/manufacturers/slider/set',
            'uses'=>'ManufacturersController@manufacturers_slider_set'
        ]);

    });
