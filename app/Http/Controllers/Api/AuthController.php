<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use GuzzleHttp\Client;

class AuthController extends Controller
{
    public function register(Request $request, User $user, Client $http){
        //if validation fails, it will return validation error and wont hit the next line
        $validateData = $request->validate([
            'email'=>'required',
            'name'=>'required',
            'password'=>'required'
        ]);

        
        $result = $user->createNewUser($request);
        
        if($result['status'] != config('cust_constants.success')){
            return null; // return some error 
        }

        $newUser = $result['result'];
        $result = [];
        //make a guzzle http request
        $response = $http->post(config('cust_constants.url').'oauth/token', [
            'form_params' => [
                'grant_type' => 'password',
                'client_id' => config('cust_constants.client_id'),
                'client_secret' => config('cust_constants.client-secret'),
                'username' => $request->email,
                'password' => $request->password,
                'scope' => '',
            ],
        ]);
        return json_decode((string) $response->getBody(), true);
    }

    public function login(){

    }
}
