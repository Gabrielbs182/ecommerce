<?php

namespace App\Http\Controllers;

use App\Models\Cidade;
use Illuminate\Http\Request;

class CidadeControllador extends Controller
{

    public function buscarCidades($idUf){
        $cidades = Cidade::where('estado_id', '=', $idUf)->get();
        return $cidades->toJson();
    }
}
