<?php

namespace App\Models;

class Pagamento extends RModel
{
    protected $table = "transporte";
    protected $fillable = ['token','status', 'pedido_id'];
}