<?php

namespace App\Http\Livewire;

use App\Enum\TipoCargaEnum;
use App\Enum\TipoDocumentoEnum;
use App\Enum\UfEnum;
use App\Services\MotoristaService;
use App\Services\VeiculosService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MDFe extends Component
{

    public $tipoDocumento = null;
    public $localDescarregamento = null;
    public $cidade = null;
    public $valorTotal = null;
    public $peso = null;
    public $chave = null;

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

    public $tiposDocumentos = [];
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

        $this->tiposDocumentos = TipoDocumentoEnum::cases();
        $this->tiposCarga = TipoCargaEnum::cases();
        $this->ufs = UfEnum::cases();
        $this->dataInicio = now()->format('Y-m-d');
        $this->veiculosTracao = $veiculoService->buscarVeiculos();
        $this->veiculosReboque = $veiculoService->buscarVeiculos();
        $this->motoristas = $motoristaService->buscarMotoristas(Auth::user()->empresa_id);
    }

    public function salvarDocumento()
    {
        if ($this->tipoDocumento && $this->localDescarregamento && $this->cidade && $this->valorTotal && $this->peso && $this->chave) {
            $this->emit('fecharModal');
            $this->serie = $this->chave[0];
            $this->numero = $this->chave[1];
        }
    }

    public function render()
    {
        return view('livewire.m-d-fe');
    }
}
