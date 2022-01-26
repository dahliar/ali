<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Auth;
use DB;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'username',
        'role',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function isMarketing(){
        if (Auth::check() and (Auth::user()->role == 3)){
            return true;
        }
        return false;
    }
    public function isAdmin(){
        if (Auth::check() and (Auth::user()->role == 1)){
            return true;
        }
        return false;
    }
    public function isProduction(){
        if (Auth::check() and (Auth::user()->role == 2)){
            return true;
        }
        return false;
    }
    public function isHumanResources(){
        if (Auth::check() and (Auth::user()->role == 4)){
            return true;
        }
        return false;
    }


    public function isAuthenticatedUserSameAsUserIdChoosen($userId){
        if (Auth::check() and (Auth::user()->id == $userId)){
            return true;
        }
        return false;
    }
    public function testUser(){
        dump(Auth::user());

        //dd($request->session()->all());
        //echo "123";
        //return false;
    }
            /*
        public function roleGranted($arrRolesGranted){
        dd(Auth::user());

        if(in_array(Auth::user()->role, $arrRolesGranted)){
            return true;
        }
        return false;
        }

            public function positionGranted($arrLevelsGranted){
            if(in_array(Auth::user()->role, $arrLevelsGranted)){
            return true;
        }
        return false;
    }
        */
}
