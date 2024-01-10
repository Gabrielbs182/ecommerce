<?php

namespace App\Models;

class Endereco extends RModel
{
    protected $table = "enderecos";
    protected $fillable = ['logradouro','bairro','numero', 'complemento', 'cep', 'cidade','uf'];

    public function setCepAttribute($cep)
    {
        $value = preg_replace("/[^0-9]/","",$cep);
        $this->attributes['cep'] = $value;
    }

}
