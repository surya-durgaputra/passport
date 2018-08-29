<?php

namespace App;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable;
    use HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table = 'users';

    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    private function result($msg, $status){
        $result = [];
        $result['result'] = $msg;
        $result['status'] = $status;
        return $result;
    }

    public function createNewUser($request){
        $data = [];
        $data['name'] = $request->name;
        $data['email'] = $request->email;
        $data['password'] = bcrypt($request->password);
        
        $result = NULL;
        try {
            //first check if user already exists
            $user = $this->where('email', '=',$request->email)->first();
            if(is_null($user)){
                $user = $this->create($data);
                $result = $this->result($user,config('cust_constants.success'));
            } else {
                $result = $this->result(config('cust_constants.user_create_error'),config('cust_constants.error'));
            }
        }
        catch (\Exception $e) {
            $result = $this->result($e, config('cust_constants.error'));
        }
        
        return $result;
    }

    public function getToken($request){
        try {
            //note Guzzle Http client does not work with localhost (it hangs)
            //hence doing the request manually
            $request->request->add([
                'grant_type'    => 'password',
                'client_id'     => config('cust_constants.client_id'),
                'client_secret' => config('cust_constants.client_secret'),
                'username' => $request->email,
                'password' => $request->password,
                'scope' => ''
            ]);
            $tokenRequest = Request::create('/oauth/token','post');
            $content = json_decode(Route::dispatch($tokenRequest)->getContent());
            $result = $this->result($content,config('cust_constants.success'));
        }
        catch (\Exception $e) {
           $result = $this->result($e, config('cust_constants.error'));
        }
        return $result;
    }

    public function checkCredentials($request){
        $result = NULL;
        try {
            //first check if user already exists
            $user = $this->where('email', '=',$request->email)->first();
            if(is_null($user)){
                $result = $this->result(config('cust_constants.user_exist_error'), config('cust_constants.error'));
                return $result;
            }
            //dd($user);
            if(Hash::check($request->password,$user->password)){
                $result = $this->result("Password matches", config('cust_constants.success'));
            } else {
                $result = $this->result(config('cust_constants.user_login_error'), config('cust_constants.error'));
            }
            
        }
        catch (\Exception $e) {
            $result = $this->result($e, config('cust_constants.error'));
        }
        
        return $result;
    }
}
