<?php

namespace App\Http\Livewire;

use App\Enum\TipoCargaEnum;
use App\Enum\TipoDocumentoEnum;
use App\Enum\UfEnum;
use App\Services\CidadeService;
use App\Services\EmpresasService;
use App\Services\MotoristaService;
use App\Services\VeiculosService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MDFe extends Component
{
    //Dados iniciais
    public $tipoDocumento = null;
    public $localDescarregamento = null;
    public $cidade = null;
    public $peso = null;
    public $valor = null;
    public $chave = null;

    //Especificações da MDFe
    public $veiculoTracao = null;
    public $motorista = null;
    public $veiculoReboque = null;
    public $localCarregamento = null;
    public $percurso = null;
    public $dataInicio = null;
    public $tipoTransporte = null;
    public $numero = null;
    public $serie = null;
    public $produtoPredominante = null;
    public $tipoCarga = null;
    public $valorTotal = 0;
    public $pesoTotal = 0;

    //Dados da NFe
    public $serieNFe = null;
    public $numeroNFe = null;
    public $ufNFe = null;
    public $NFe = [];
    public $NFes = [];

    //Dados para preemcher a teça
    public $tiposDocumentos = [];
    public $cidades = [];
    public $tiposCarga = [];
    public $ufs = [];
    public $veiculosTracao = [];
    public $veiculosReboque = [];
    public $motoristas = [];

    public function mount()
    {
        //Injetar Services
        $motoristaService = new MotoristaService();
        $veiculoService = new VeiculosService();
        $empresaService = new EmpresasService();
        $empresa = $empresaService->buscarEmpresa(Auth::user()->empresa_id);

        $this->numero = "Geração Automática";
        $this->localCarregamento = $empresa->uf . ' - ' . $empresa->cidade;
        $this->tiposDocumentos = TipoDocumentoEnum::cases();
        $this->tiposCarga = TipoCargaEnum::cases();
        $this->ufs = UfEnum::cases();
        $this->dataInicio = now()->format('Y-m-d');
        $this->veiculosTracao = $veiculoService->buscarVeiculosTracao();
        $this->veiculosReboque = $veiculoService->buscarReboques();
        $this->motoristas = $motoristaService->buscarMotoristas(Auth::user()->empresa_id);
    }

    public function buscarCidades()
    {
        // Injetar Service
        $cidadeService = new CidadeService();
        $this->cidades = $cidadeService->buscarCidadesPorUf($this->localDescarregamento);
    }

    public function salvarDocumento()
    {
        if ($this->tipoDocumento && $this->localDescarregamento && $this->cidade && $this->valor && $this->peso && $this->chave) {
            if (!$this->validarChaveNFe($this->chave)) {
                return $this->emit('chaveInvalida');
            }
            $this->NFe = ['tipoDocumento' => $this->tipoDocumento, 'cidade' => $this->cidade, 'ufNFe' => $this->ufNFe, 'valor' => $this->valor, 'peso' => $this->peso, 'chave' => $this->chave, 'serieNFe' => $this->serieNFe, 'numeroNFe' => $this->numeroNFe];
            $this->NFes[] = $this->NFe;
            $this->calcularTotais();
            $this->limparCampos();
            return $this->emit('fecharModal');
        }
    }

    public function calcularTotais()
    {
        $this->valorTotal += $this->valor;
        $this->pesoTotal += $this->peso;
    }

    public function limparCampos()
    {
        $this->cidade = null;
        $this->valor = null;
        $this->peso = null;
        $this->chave = null;
    }

    public function validarChaveNFe($chave)
    {
        if (strlen($chave) != 44) {
            return false;
        }
        $uf = $this->ufNFe(substr($chave, 0, 2));
        // $anoMesEmissao = substr($chave, 2, 4);
        $cnpjEmitente = substr($chave, 6, 14);
        // $modelo = substr($chave, 20, 2);
        $serie = substr($chave, 22, 3);
        $numeroNFe = substr($chave, 25, 9);
        // $tipoEmisao = substr($chave, 34, 1);
        // $codigoNumerico = substr($chave, 35, 8);
        $digitoVerificador = substr($chave, 43, 1);
        if (!$this->validarCNPJ($cnpjEmitente)) {
            return false;
        }
        $dvCalculado = $this->calcularDV(substr($chave, 0, -1));
        if ($dvCalculado != $digitoVerificador) {
            return false;
        }
        $this->ufNFe = $uf;
        $this->serieNFe = $serie;
        $this->numeroNFe = $numeroNFe;
        return true;
    }

    public function validarCNPJ($cnpj)
    {
        return true;
    }

    public function calcularDV($chave)
    {
        $soma = 0;
        $multiplicador = 2;
        for ($i = strlen($chave) - 1; $i >= 0; $i--) {
            $soma += $chave[$i] * $multiplicador;
            $multiplicador++;
            if ($multiplicador > 9) {
                $multiplicador = 2;
            }
        }
        $resto = $soma % 11;
        $dv = $resto == 0 || $resto == 1 ? 0 : 11 - $resto;
        return $dv;
    }

    public function ufNFe($codigo)
    {
        foreach (UfEnum::cases() as $uf) {
            $cod = explode('_', $uf->name)[1];
            if ($cod == $codigo) {
                return $uf;
            }
        }
        return false;
    }

    public function editNFe($nota)
    {
        dd($nota);
        return $this->emit('abrirModalEdit');
    }

    public function addNFe()
    {
        return $this->emit('abrirModal');
    }

    public function render()
    {
        return view('livewire.m-d-fe');
    }

}