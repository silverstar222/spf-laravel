<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        \App\Http\Middleware\TrustProxies::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            'throttle:60,1',
            'bindings',
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        '/api/sign_up' => \App\Modules\oAuth\Middleware\Signup::class,
        '/api/sign_in' => \App\Modules\oAuth\Middleware\Signin::class,
        '/api/recovery_password' => \App\Modules\oAuth\Middleware\RecoveryPassowrd::class,
        '/api/manufacturers/create' => \App\Modules\Manufacturers\Middleware\CreateManufacturers::class,
        '/api/manufacturers/delete' => \App\Modules\Manufacturers\Middleware\DeleteManufacturers::class,
        '/api/manufacturers/product/create' => \App\Modules\Manufacturers\Middleware\CreateProductMenufacturers::class,
        '/api/manufacturers/product/delete' => \App\Modules\Manufacturers\Middleware\DeleteManufacturersProduct::class,
        '/api/manufacturers/get_for_admin' => \App\Modules\Manufacturers\Middleware\GetAllManufacturersForAdmin::class,
        '/api/manufacturers/get_by_id' => \App\Modules\Manufacturers\Middleware\GetManufacturersById::class,
        '/api/manufacturers/products/get_by_id' => \App\Modules\Manufacturers\Middleware\GetManufacturersProductsById::class,
        '/api/manufacturers/products/get_all' => \App\Modules\Manufacturers\Middleware\GetAllProductsByManufacturersId::class,
        '/api/manufacturers/edit' => \App\Modules\Manufacturers\Middleware\ManufacturersEdit::class,
        '/api/buyers/admin/get_all'=> \App\Modules\Buyers\Middleware\GetAllBuyers::class,
        '/api/buyers/invite_by_email' => \App\Modules\Buyers\Middleware\InviteBuyers::class,
        '/api/buyers/delete'=> \App\Modules\Buyers\Middleware\DeleteBuyers::class,
        '/api/buyers/edit' => \App\Modules\Buyers\Middleware\EditBuyers::class,
        '/api/buyers/admin/search'=> \App\Modules\Buyers\Middleware\SearchBuyers::class,
        '/api/buyers/admin/get_count_of_new' => \App\Modules\Buyers\Middleware\CountOfNewBuyers::class,
        '/api/buyers/permissions/edit' => \App\Modules\Buyers\Middleware\EditBuyersPermissions::class,
        '/api/buyers/admin/get_by_id' => \App\Modules\Buyers\Middleware\GetBuyersById::class,
        '/api/manufacturers/get_for_user' => \App\Modules\Manufacturers\Middleware\GetAllManufacturersForUser::class,
        '/api/basket/add_product'=> \App\Modules\Basket\Middleware\AddToBasket::class,
        '/api/basket/user/get'=>\App\Modules\Basket\Middleware\GetUserBasket::class,
        '/api/basket/user/remove_product' => \App\Modules\Basket\Middleware\RemoveProductFromBasket::class,
        '/api/buyers/status/set' => \App\Modules\Buyers\Middleware\SetStatusBuyers::class,
        '/api/manufacturers/slider/set' => \App\Modules\Manufacturers\Middleware\ManufacturersSliderSet::class,
        '/api/buyers/payment/card/add' => \App\Modules\Buyers\Middleware\AddCard::class,
        '/api/buyers/payment/card/delete'=>\App\Modules\Buyers\Middleware\DeleteCard::class,
        '/api/basket/pay' => \App\Modules\Basket\Middleware\Pay::class,
        '/api/user/orders/get' => \App\Modules\Orders\Middleware\GetOrders::class,
        '/api/user/orders/get_by_id' => \App\Modules\Orders\Middleware\GetById::class,
        '/api/admin/orders/get' => \App\Modules\Orders\Middleware\GetOrdersFromAdmin::class,
        '/api/admin/orders/document/add' => \App\Modules\Orders\Middleware\UploadDocuments::class,
        '/api/admin/orders/document/remove'=> \App\Modules\Orders\Middleware\RemoveDocuments::class,
        '/api/chat/message/get' => \App\Modules\Chat\Middleware\GetMessages::class,
        '/api/chat/admin/get'=>\App\Modules\Chat\Middleware\GetChatForAdmin::class,
        '/api/chat/message/send'=>\App\Modules\Chat\Middleware\SendMessage::class,
    ];
}
