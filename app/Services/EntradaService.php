<?php

namespace App\Services;

use App\Exceptions\AlreadyExistException;
use App\Models\Entrada;

class EntradaService
{

    public function createEntrada($request, $empresaId)
    {
        return Entrada::create([
            'dataEmissao' => $request->dhEmi,
            'dataEntrada' => $request->dhSaiEnt,
            'numeroNota' => $request->nNF,
            'fornecedor' => $request->fornecedor . ' / ' . $request->CNPJ,
            'chave' => $request->chNFe,
            'xml' => $request->xml,
            'valor' => $request->vNF,
            'empresa_id' => $empresaId
        ])->id;
    }

    public function getInput($entradaId)
    {
        return Entrada::with('itens.produto')->find($entradaId);
    }

    public function getEntradas($empresaId)
    {
        return Entrada::whereEmpresaId($empresaId)->with('itens.produto')->get();
    }

    public function entradaExist($chNFe)
    {
        if (Entrada::whereChave($chNFe)->exists()) {
            throw new AlreadyExistException("Nota (" . $chNFe . ") já importada anteriormente!");
        }
        return false;
    }
}
