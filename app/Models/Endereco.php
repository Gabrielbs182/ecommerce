<?php

namespace App\Models;

class Endereco extends RModel
{
    protected $table = "enderecos";
    protected $fillable = ['logradouro','numero', 'complemento', 'cep', 'cidade_id'];

    public function setCepAttribute($cep)
    {
        $value = preg_replace("/[^0-9]/","",$cep);
        $this->attributes['cep'] = $value;
    }

}
