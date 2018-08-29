<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use App\User;

class AuthController extends Controller
{
    public function register(Request $request, User $user){
        //if validation fails, it will return validation error and wont hit the next line
        $validateData = $request->validate([
            'email'=>'required',
            'name'=>'required',
            'password'=>'required'
        ]);
        
        //init result
        $result = [];
        $result = $user->createNewUser($request);
        
        if($result['status'] != config('cust_constants.success')){
            return response()->json($result,500); // return some error 
        }

        //init result
        $result = [];
        $result = $user->getToken($request);
        
        if($result['status'] != config('cust_constants.success')){
            return response()->json($result,500); // return some error 
        }
        
        return response()->json($result,200); 
        
    }

    public function login(Request $request, User $user){
        //if validation fails, it will return validation error and wont hit the next line
        $validateData = $request->validate([
            'email'=>'required',
            'password'=>'required'
        ]);

        //init result
        $result = [];
        $result = $user->checkCredentials($request);
        if($result['status'] != config('cust_constants.success')){
            return response()->json($result,500); // return some error 
        }

        //init result
        $result = [];
        $result = $user->getToken($request);
        if($result['status'] != config('cust_constants.success')){
            return response()->json($result,500); // return some error 
        }
        
        return response()->json($result,200);
    }
}
