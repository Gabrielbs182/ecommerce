<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Auth\Passwords\CanResetPassword as CanResetPasswordTrait;

class Usuario extends RModel implements Authenticatable, MustVerifyEmail, CanResetPassword
{
    use MustVerifyEmailTrait, Notifiable, Authorizable, CanResetPasswordTrait;

    protected $table = "usuarios";
    protected $fillable = ['email', 'cpf', 'password', 'nome', 'isAdmin', 'ativo', 'telefone','email_verified_at'];

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
        return null;
    }

    public function setRememberToken($value)
    {
        // Your implementation, if needed
    }

    public function getRememberTokenName()
    {
        return null;
    }

    /**
     * Get the email address that should be used for verification.
     *
     * @return string
     */
    public function getEmailForVerification()
    {
        return $this->email;
    }
}
