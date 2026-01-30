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
		'cpf_cnpj',
		'endereco_id',
		'rg_ie',
		'celular',
		'contador',
		'sequenciaCupom',
		'ultimaNFe',
		'ultimaNFCe',
		'ultimaMDFe',
		'serie',
		'certificado',
		'senhaCertificado',
		'ambiente',
		'status',
		'csc',
		'idCsc',
		'limClientes',
		'limProdutos',
		'limNFes',
		'limNFCes',
		'limMDFes',
		'crt'
	];

	public function endereco()
	{
		return $this->hasOne(Endereco::class, 'id', 'endereco_id');
	}

	public static function ultimoNumeroNFe()
	{
		$sUsers = new UsersService();
		$empresa = $sUsers->getEmpresa(Auth::id());

		$empresa = Empresa::findOrFail($empresa->empresa_id);

		return $empresa->ultimoNumeroNFe + 1;
	}

	public static function salvar($nome, $fantasia, $cpf_cnpj, $endereco_id, $rg_ie, $telefone, $contador, $nfe, $nfce, $mdfe, $serie, $certificado, $senha, $ambiente, $csc, $idCsc, $limNFes, $limMDFes, $limNFCes, $clientes, $produtos, $crt)
	{
		$response = Empresa::create([
			'razao' => $nome,
			'fantasia' => $fantasia,
			'cpf_cnpj' => $cpf_cnpj,
			'endereco_id' => $endereco_id,
			'rg_ie' => $rg_ie,
			'celular' => $telefone,
			'contador' => $contador,
			'sequenciaCupom' => 1,
			'ultimaNFe' => $nfe,
			'ultimaNFCe' => $nfce,
			'ultimaMDFe' => $mdfe,
			'serie' => $serie,
			'certificado' => $certificado,
			'senhaCertificado' => $senha,
			'ambiente' => $ambiente,
			'status' => 1,
			'csc' => $csc,
			'idCsc' => $idCsc,
			'limClientes' => $clientes,
			'limProdutos' => $produtos,
			'limNFes' => $limNFes,
			'limNFCes' => $limNFCes,
			'limMDFes' => $limMDFes,
			'crt' => $crt
		]);
		return $response;
	}

	public static function ambientes()
	{
		return [
			'1' => 'Produção',
			'2' => 'Homologação'
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
			'DF' => '53'
		];
		return $ufs[$uf];
	}

	public function nfces()
	{
		return $this->hasMany(NFCe::class, 'empresa_id', 'id');
	}
}
