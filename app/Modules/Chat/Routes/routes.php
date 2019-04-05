<?php
    Route::group( [ 'namespace' => 'App\Modules\Chat\Controllers',
        'as' => 'chat.',
//        'middleware' => 'oauth',
        'https' => true,
    ], function() {


        //Get Message
        Route::post('/api/chat/message/get',[
            'middleware'=>'/api/chat/message/get',
            'uses'=>'ChatController@get_message'
        ]);


        //Get Chat For Admin
        Route::post('/api/chat/admin/get',[
            'middleware'=>'/api/chat/admin/get',
            'uses'=>'ChatController@get_chat_for_admin'
        ]);


        //Get Chat For Admin
        Route::post('/api/chat/message/send',[
            'middleware'=>'/api/chat/message/send',
            'uses'=>'ChatController@message_send'
        ]);



    });
