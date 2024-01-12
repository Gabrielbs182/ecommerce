<?php

namespace App\Models;

class Pedido extends RModel
{

    protected $table = "pedidos";

    protected $fillable = ['datapedido', 'status', 'usuario_id','status_checkout', 'status_pag', 'status_env', 'cod_checkout', 'cod_entrega', 'cod_rastreamento','endereco_id', 'reference_id', 'total'];

}
