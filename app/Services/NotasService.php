<?php

namespace App\Services;

use App\Models\MDFE;
use App\Models\MDFeNota;

class NotasService
{

    public function save($numero, $serie, $data, $uf_inicio, $uf_termino, $uf_percurso, $valor_total,
        $peso, $carga_predominante, $ncm, $tipo_carga, $empresa, $veicTracao, $veicReboque, $motorista) {
        return MDFE::create([
            'numero' => $numero,
            'serie' => $serie,
            'data' => $data,
            'situacao' => 'Pendente',
            'uf_inicio' => $uf_inicio,
            'uf_termino' => $uf_termino,
            'uf_percurso' => $uf_percurso,
            'tipo_documento' => 'MDFe',
            'chave_acesso' => null,
            'valor_total' => $valor_total,
            'peso' => $peso,
            'carga_predominante' => $carga_predominante,
            'ncm' => '95030022',
            'tipo_carga' => $tipo_carga,
            'empresa_id' => $empresa,
            'veiculo_tracao_id' => $veicTracao,
            'veiculo_reboque_id' => $veicReboque,
            'motoristaId' => $motorista,
        ]);
    }

    public function saveNotas($tipoDocumento, $chave, $uf, $municipio, $codMun, $valor, $peso, $serie, $numero, $mdfe)
    {
        return MDFeNota::create([
            'tipo_documento' => $tipoDocumento,
            'chave' => $chave,
            'uf' => $uf,
            'municipio' => $municipio,
            'codMun' => $codMun,
            'valor' => $valor,
            'peso' => $peso,
            'serie' => $serie,
            'numero' => $numero,
            'mdfe_id' => $mdfe
        ]);
    }

    public function buscarMDFes($empresaId)
    {
        return MDFE::select('m_d_f_e_s.*', 'empresas.fantasia')
            ->join('empresas', 'empresas.id', 'm_d_f_e_s.empresa_id')
            ->where('m_d_f_e_s.empresa_id', $empresaId)
            ->get();
    }

}
