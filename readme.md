1) create project: composer create-project --prefer-dist laravel/laravel passport

2) mysql bug fix: Open AppServiceProvider.php

    use Illuminate\Support\Facades\Schema;

    public function boot()
    {
        Schema::defaultStringLength(191);
    }

3) php artisan migrate

4) php artisan make:auth

5) composer require laravel/passport
install passport

6) php artisan migrate
This step will install additional tables regarding oAuth2

7)php artisan passport:install

Encryption keys generated successfully.
Personal access client created successfully.
Client ID: 1
Client Secret: ultuLIm6yRejclyezRn6IZc4KH7Yy156GDOAbGz3
Password grant client created successfully.
Client ID: 2
Client Secret: iHVyD1CqfiU4icX0QnAGdb5Kwoo8Cbei8jHiFjar

8) Next, you should call the Passport::routes method within the boot method of your  
AuthServiceProvider.php. 

This method will register the routes necessary to issue access tokens and revoke access tokens, 
clients, and personal access tokens:

Open AuthServiceProvider.php file:
add Passport::routes(); to it

public function boot()
    {
        $this->registerPolicies();

        Passport::routes();
    }

9) Finally, in your config/auth.php configuration file, you should set the driver option of the  
api authentication guard to passport. This will instruct your application to use Passport's  
TokenGuard when authenticating incoming API requests:
 under Authentication Guards, change 'driver' => 'token' to 'driver' => 'passport',

 'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'api' => [
            'driver' => 'passport',
            'provider' => 'users',
        ],
    ],

10) php artisan serve

then in postman, do a get request on http://localhost:8000/api/user

it will return a login/password page.

in postman, create header key - Accept and set the value to application/json and hit send.

you should receive:

{
    "message": "Unauthenticated."
}

this is because you need an auth token to access that api

11) In our case, our Laravel app called passport (http://localhost:8000) is both Resource Server and Authorization Server 

Client application can be any other application like one we will make in react. You can take postman as client too.

Resource owner will be ourselves

12) Goto http://localhost/api/user in a browser

register -> and create a user

then login with this user to make sure it works

13) in postman, make post request to this api:
http://localhost/token/oauth

click on form-data radio button:

----------key -> Value

grant_type    -> password 

client_secret -> k6Qldl43MuuhFF6lSG1BnsJC7RJmlmYAErHKcZnS   (this one is in oauth_clients table, under "Laravel Password Grant Client".. it was created in step 7 during php artisan passport:install )
client_id     -> 2 (this is hard coded value from oauth_clients)
username      -> anshu@accessa.mu (from user created in last step)
password      -> 123456 (from user created in last step)

this will return the token save that token 

14) from step 13, take the token and login directly 
create header key - Accept and set the value to application/json and hit send.
create header key - Authorization and set the value to Bearer <the_token_from_prev_step> and hit send.
you will now get the credentials


-----------------------------------------
We will now do the steps 13 and 14 in one go-- i.e. when a user registers, he must get the token in one go (and save it in localStorage)

15) php artisan make:controller Api\AuthController

16) install Guzzle
composer require guzzlehttp/guzzle

17)


