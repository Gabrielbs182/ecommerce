<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;

class Usuario extends RModel implements Authenticatable
{

    protected $table = "usuarios";
    protected $fillable = ['email', 'cpf', 'password', 'nome', 'isAdmin', 'ativo'];

    public function getAuthIdentifierName()
    {
        return "email";
    }
    public function getAuthIdentifier()
    {
        return $this->email;
    }
    public function getAuthPassword()
    {
        return $this->password;
    }
    public function getRememberToken()
    {
    }
    public function setRememberToken($value)
    {
        
    }
    public function getRememberTokenName()
    {
        
    }
}
