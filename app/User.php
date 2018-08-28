<?php

namespace App;

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

    public function createNewUser($request){
        $data = [];
        $data['name'] = $request->name;
        $data['email'] = $request->email;
        $data['password'] = bcrypt($request->password);

        $result = NULL;
        try {
            $result['result'] = $this->create($data);
            $result['status'] = config('cust_constants.success');
        }
        catch (\Exception $e) {
            $result['result'] = config('cust_constants.server_error');
            $result['status'] = config('cust_constants.error');
        }
    }
}
