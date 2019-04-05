<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #d43f3a;
                font-family: 'Raleway', sans-serif;
                font-weight: 100;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 12px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            <div class="content">
                <h1>
                    Test view for avatar upload
                </h1>
                <div class="links">
                    {{--<form enctype="multipart/form-data" method="POST" action="/api/sign_up">--}}
                        {{--<input type="file" name="company_logo"/>--}}
                        {{--<input type="hidden" value="user2@gmail.com" name="email">--}}
                        {{--<input type="hidden" value="company name2" name="company_name">--}}
                        {{--<input type="hidden" value="Pass1234" name="password">--}}
                        {{--<input type="hidden" value="+380660679012" name="phone_number">--}}
                        {{--<input type="hidden" value="Business name" name="business_name">--}}
                        {{--<input type="hidden" value="Delivery address" name="delivery_address">--}}
                        {{--<input type="hidden" value="Manager name" name="manager_name">--}}
                        {{--<input type="submit"/>--}}
                    {{--</form>--}}
                    {{--<form enctype="multipart/form-data" method="POST" action="/api/manufacturers/create">--}}
                        {{--<input type="file" name="logo"/>--}}
                        {{--<input type="file" name="attachments" multiple>--}}
                        {{--<input type="hidden" value="1" name="admins_id">--}}
                        {{--<input type="hidden" value="company name2" name="company_name">--}}
                        {{--<input type="hidden" name="token" value="$2y$10$HrkMGS8k1iEVjE/ip3uUAOYubdXBS6qmLVsWzqOHPBfNY3sQn3UxG">--}}
                        {{--<input type="hidden" value="location" name="location">--}}
                        {{--<input type="hidden" value="website" name="website">--}}
                        {{--<input type="submit"/>--}}
                    {{--</form>--}}

                    {{--<form enctype="multipart/form-data" method="POST" action="/api/manufacturers/product/create">--}}
                    {{--<input type="file" name="logo"/>--}}
                    {{--<input type="file" name="attachments[]" multiple>--}}
                    {{--<input type="hidden" value="1121212" name="title">--}}
                    {{--<input type="hidden" value="5" name="manufacturers_id">--}}
                    {{--<input type="hidden" name="token" value="$2y$10$HrkMGS8k1iEVjE/ip3uUAOYubdXBS6qmLVsWzqOHPBfNY3sQn3UxG">--}}
                    {{--<input type="hidden" value="123" name="price">--}}
                    {{--<input type="hidden" value="description" name="description">--}}
                    {{--<input type="submit"/>--}}
                    {{--</form>--}}


                    {{--<form enctype="multipart/form-data" method="POST" action="/api/manufacturers/edit">--}}
                    {{--<input type="file" name="logo"/>--}}
                    {{--<input type="hidden" value="6" name="manufacturers_id">--}}
                    {{--<input type="hidden" name="token" value="$2y$10$HrkMGS8k1iEVjE/ip3uUAOYubdXBS6qmLVsWzqOHPBfNY3sQn3UxG">--}}
                    {{--<input type="submit"/>--}}
                    {{--</form>--}}

                    {{--<form enctype="multipart/form-data" method="POST" action="/api/buyers/edit">--}}
                    {{--<input type="file" name="company_logo"/>--}}
                    {{--<input type="hidden" value="1" name="buyers_id">--}}
                    {{--<input type="hidden" name="token" value="$2y$10$HrkMGS8k1iEVjE/ip3uUAOYubdXBS6qmLVsWzqOHPBfNY3sQn3UxG">--}}
                    {{--<input type="submit"/>--}}
                    {{--</form>--}}


                    <form enctype="multipart/form-data" method="POST" action="/api/admin/orders/document/add">
                        <input type="file" name="file"/>
                        <input type="hidden" value="5" name="orders_id">
                        <input type="hidden" name="token" value="$2y$10$HrkMGS8k1iEVjE/ip3uUAOYubdXBS6qmLVsWzqOHPBfNY3sQn3UxG">
                        <input type="submit"/>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>
