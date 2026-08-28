<?php

namespace App\Models;

use App\Services\UsersService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Empresa extends Model
{
    use HasFactory;

    protected $fillable = [
        'razao',
        'fantasia',
        'ramo_atividade',
        'cpf_cnpj',
        'endereco_id',
        'rg_ie',
        'celular',
        'contador',
        'sequenciaCupom',
        'ultimaNFe',
        'ultimaNFCe',
        'ultimaMDFe',
        'ultimaNFCom',
        'ultimaCTe',
        'ultimaNFSe',
        'ultimaDPS',
        'serie',
        'serieNFSe',
        'certificado',
        'certificado_conteudo',
        'senhaCertificado',
        'ambiente',
        'status',
        'csc',
        'idCsc',
        'limClientes',
        'limProdutos',
        'limNFes',
        'limNFCes',
        'limNFSe',
        'limMDFes',
        'crt',
        'inscricao_municipal',
        'lancar_nfe_nfce_fluxo_caixa',
    ];

    protected $casts = [
        'senhaCertificado' => 'encrypted',
        'certificado_conteudo' => 'encrypted',
        'lancar_nfe_nfce_fluxo_caixa' => 'boolean',
    ];

    protected $hidden = [
        'senhaCertificado',
        'certificado',
        'certificado_conteudo',
    ];

    public function endereco()
    {
        return $this->hasOne(Endereco::class, 'id', 'endereco_id');
    }

    public static function ultimoNumeroNFe()
    {
        $sUsers = new UsersService;
        $empresa = $sUsers->getEmpresa(Auth::id());

        $empresa = Empresa::findOrFail($empresa->empresa_id);

        return $empresa->ultimoNumeroNFe + 1;
    }

    public static function salvar($nome, $fantasia, $cpf_cnpj, $endereco_id, $rg_ie, $telefone, $contador, $nfe, $nfce, $mdfe, $serie, $certificado, $senha, $ambiente, $csc, $idCsc, $limNFes, $limMDFes, $limNFCes, $clientes, $produtos, $crt, $lancarNFeNFCeFluxoCaixa = true, $nfse = 0, $dps = 0, $serieNFSe = null, $limNFSe = 0, $inscricaoMunicipal = null)
    {
        $response = Empresa::create([
            'razao' => $nome,
            'fantasia' => $fantasia,
            'cpf_cnpj' => $cpf_cnpj,
            'endereco_id' => $endereco_id,
            'rg_ie' => $rg_ie,
            'inscricao_municipal' => $inscricaoMunicipal,
            'celular' => $telefone,
            'contador' => $contador,
            'sequenciaCupom' => 1,
            'ultimaNFe' => $nfe,
            'ultimaNFCe' => $nfce,
            'ultimaMDFe' => $mdfe,
            'ultimaNFSe' => $nfse,
            'ultimaDPS' => $dps,
            'serie' => $serie,
            'serieNFSe' => $serieNFSe ?: $serie,
            'certificado' => null,
            'certificado_conteudo' => $certificado,
            'senhaCertificado' => $senha,
            'ambiente' => $ambiente,
            'status' => 1,
            'csc' => $csc,
            'idCsc' => $idCsc,
            'limClientes' => $clientes,
            'limProdutos' => $produtos,
            'limNFes' => $limNFes,
            'limNFCes' => $limNFCes,
            'limNFSe' => $limNFSe,
            'limMDFes' => $limMDFes,
            'crt' => $crt,
            'lancar_nfe_nfce_fluxo_caixa' => $lancarNFeNFCeFluxoCaixa,
        ]);

        return $response;
    }

    public static function ambientes()
    {
        return [
            '1' => 'Produção',
            '2' => 'Homologação',
        ];
    }

    public static function getCUF($uf)
    {
        $ufs = [
            'RO' => '11',
            'AC' => '12',
            'AM' => '13',
            'RR' => '14',
            'PA' => '15',
            'AP' => '16',
            'TO' => '17',
            'MA' => '21',
            'PI' => '22',
            'CE' => '23',
            'RN' => '24',
            'PB' => '25',
            'PE' => '26',
            'AL' => '27',
            'SE' => '28',
            'BA' => '29',
            'MG' => '31',
            'ES' => '32',
            'RJ' => '33',
            'SP' => '35',
            'PR' => '41',
            'SC' => '42',
            'RS' => '43',
            'MS' => '50',
            'MT' => '51',
            'GO' => '52',
            'DF' => '53',
        ];

        return $ufs[$uf];
    }

    public function nfces()
    {
        return $this->hasMany(NFCe::class, 'empresa_id', 'id');
    }

    public function nfses()
    {
        return $this->hasMany(NFSe::class, 'empresa_id', 'id');
    }
}
