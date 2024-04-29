<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class mdl_Users extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = ['username','password','StudentName','StudentId','email','role'];
    protected $table      = 'users';
    protected $primaryKey = 'id';
    public $timestamps    = false;

      public function getAuthPassword() {
        return $this->password;
    }

    public function AauthAcessToken(){
        return $this->hasMany('\App\Models\mdl_OauthAccessToken','user_id','id');
    }

}
