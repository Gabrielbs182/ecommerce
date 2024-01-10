<?php

namespace App\Models;

class ItensPedido extends RModel
{

    protected $table = "itens_pedidos";

    protected $fillable = ['quantidade', 'valor', 'produto_id', 'pedido_id', 'numero'];

}
