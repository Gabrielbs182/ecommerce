<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\ItensPedido;
use App\Models\Endereco;
use App\Models\Pedido;
use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class VendaService
{

    public function finalizarVenda($prods = [], Usuario $user){
        try {
            DB::beginTransaction();
            $pedido = new Pedido();
            $dtHoje = new DateTime();
            $pedido->datapedido = $dtHoje->format("Y-m-d H:i:s");
            $pedido->status = "PEN";
            $pedido->usuario_id = $user->id;
            $pedido->save();
            foreach ($prods as $p) {
                $itens = new ItensPedido();
                $itens->quantidade = 1;
                $itens->valor = $p->valor;
                $itens->dt_item = $dtHoje->format("Y-m-d H:i:s");
                $itens->produto_id = $p->id;
                $itens->pedido_id = $pedido->id;
                $itens->save();
                # code...
            }
            DB::commit();
            return ['status' => 'ok', 'message' => 'Venda finalizada com sucesso', 'idpedido' => $pedido->id];
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("ERRO: Venda service", ['message' => $e->getMessage()]);
            return ['status' => 'err', 'message' => 'Venda não pode ser finalizada'];
        }
    }
}