<?php

namespace App\Services;

use App\Models\MDFE;
use App\Models\MDFeNota;
use App\Models\MDFeXml;

class NotasService
{
    public function save(
        $numero,
        $serie,
        $data,
        $uf_inicio,
        $uf_termino,
        $codMunCarregamento,
        $municipioCarregamento,
        $percursos,
        $valor_total,
        $peso,
        $tipo_carga,
        $nNFe,
        $nMDFe,
        $nCTe,
        $empresa,
        $veicTracao,
        $numeroLacre,
        $info_fisco,
        $info_contribuinte
    ) {
        if ($this->qtdeEmitMDFe($empresa->id) < $empresa->limMDFes || $empresa->id == 1) {
            return MDFE::create([
                'numero' => $numero,
                'serie' => $serie,
                'data' => $data,
                'situacao' => 'Pendente',
                'uf_inicio' => $uf_inicio,
                'uf_termino' => $uf_termino,
                'uf_percurso' => $percursos ? implode(' - ', $percursos) : null,
                'codMunCarregamento' => $codMunCarregamento,
                'municipioCarregamento' => $municipioCarregamento,
                'tipo_documento' => 'MDFe',
                'chave_acesso' => null,
                'valor_total' => $valor_total,
                'peso' => $peso,
                'tipo_carga' => $tipo_carga,
                'nNFe' => $nNFe,
                'nMDFe' => $nMDFe,
                'nCTe' => $nCTe,
                'info_fisco' => $info_fisco,
                'info_contribuinte' => $info_contribuinte,
                'numeroLacre' => $numeroLacre,
                'empresa_id' => $empresa->id,
                'veiculo_tracao_id' => $veicTracao,
            ]);
        }
    }

    public function update(
        $numero,
        $serie,
        $data,
        $uf_inicio,
        $uf_termino,
        $codMunCarregamento,
        $municipioCarregamento,
        $percursos,
        $valor_total,
        $peso,
        $tipo_carga,
        $nNFe,
        $nMDFe,
        $nCTe,
        $veicTracao,
        $numeroLacre,
        $info_fisco,
        $info_contribuinte,
        $mdfeId
    ) {
        $mdfe = MDFE::find($mdfeId);

        return $mdfe->update([
            'numero' => $numero,
            'serie' => $serie,
            'data' => $data,
            'situacao' => 'Pendente',
            'uf_inicio' => $uf_inicio,
            'uf_termino' => $uf_termino,
            'uf_percurso' => $percursos ? implode(' - ', $percursos) : null,
            'codMunCarregamento' => $codMunCarregamento,
            'municipioCarregamento' => $municipioCarregamento,
            'tipo_documento' => 'MDFe',
            'chave_acesso' => null,
            'valor_total' => $valor_total,
            'peso' => $peso,
            'tipo_carga' => $tipo_carga,
            'nNFe' => $nNFe,
            'nMDFe' => $nMDFe,
            'nCTe' => $nCTe,
            'info_fisco' => $info_fisco,
            'info_contribuinte' => $info_contribuinte,
            'numeroLacre' => $numeroLacre,
            'veiculo_tracao_id' => $veicTracao,
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
            'mdfe_id' => $mdfe,
        ]);
    }

    public function updateOrCreateNotas($tipoDocumento, $chave, $uf, $municipio, $codMun, $valor, $peso, $serie, $numero, $nota_id, $mdfeId)
    {
        if (! $nota_id) {
            return $this->saveNotas(
                $tipoDocumento,
                $chave,
                $uf,
                $municipio,
                $codMun,
                $valor,
                $peso,
                $serie,
                $numero,
                $mdfeId
            );
        } else {
            $nota = MDFeNota::find($nota_id);

            return $nota->update([
                'chave' => $chave,
                'uf' => $uf,
                'municipio' => $municipio,
                'codMun' => $codMun,
                'valor' => $valor,
                'peso' => $peso,
                'serie' => $serie,
                'numero' => $numero,
            ]);
        }
    }

    public function deleteNotas($notas, $mdfeId)
    {
        $notas_ids = collect($notas)->pluck('nota_id')->toArray();
        $ids = MDFeNota::where('mdfe_id', $mdfeId)->get()->pluck('id')->toArray();
        $notasParaDeletar = array_diff($ids, $notas_ids);

        return MDFeNota::whereIn('id', $notasParaDeletar)->delete();
    }

    public function deleteMDFe($mdfeId)
    {
        $mdfe = MDFE::find($mdfeId);

        return $mdfe->delete();
    }

    public function buscarMDFe($mdfeId)
    {
        return MDFE::with('empresa', 'veiculoTracao.proprietario', 'reboques.reboque', 'motoristas.motorista', 'notas', 'prodPred', 'xmls')->find($mdfeId);
    }

    public function salvarXmlMDFe(MDFE $mdfe, string $tipo, string $xml): MDFeXml
    {
        return MDFeXml::updateOrCreate(
            [
                'mdfe_id' => $mdfe->id,
                'tipo' => $tipo,
            ],
            [
                'xml' => $xml,
            ]
        );
    }

    public function buscarMDFes($empresaId)
    {
        if ($empresaId == 1) {
            $empresaId = '%';
        }

        return MDFE::select('m_d_f_e_s.*', 'empresas.fantasia')
            ->join('empresas', 'empresas.id', 'm_d_f_e_s.empresa_id')
            ->where('m_d_f_e_s.empresa_id', 'like', $empresaId)
            ->get();
    }

    public function qtdeEmitMDFe($empresa_id)
    {
        return MDFE::where('empresa_id', $empresa_id)->count();
    }

    public static function getTotalMDFePerMonth($companyId)
    {
        $query = MDFE::selectRaw('MONTH(data) as mes, SUM(valor_total) as total_vendas')
            ->where('situacao', 'Autorizado')
            ->whereYear('data', date('Y')) // Filtra apenas o ano corrente
            ->groupBy('mes')
            ->orderBy('mes', 'asc'); // Garante a ordenação correta dos meses

        // Se não for a empresa "1", aplica o filtro por empresa
        if ($companyId != 1) {
            $query->where('empresa_id', $companyId);
        }

        return $query->get();
    }
}
