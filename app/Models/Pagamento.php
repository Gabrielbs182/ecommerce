<?php

namespace App\Models;

class Pagamento extends RModel
{
    protected $table = "pagamento";
    protected $fillable = ['token','status', 'pedido_id'];
}