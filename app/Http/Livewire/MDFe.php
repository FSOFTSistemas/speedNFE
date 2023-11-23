<?php

namespace App\Http\Livewire;

use App\Enum\TipoDocumentoEnum;
use App\Enum\UfEnum;
use Livewire\Component;

class MDFe extends Component
{
    public $tipoDocumento = null;
    public $localDescarregamento = null;
    public $cidade = null;
    public $valorTotal = null;
    public $peso = null;
    public $chave = null;
    public $teste = null;

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
    public $ufs = [];

    public function mount()
    {
        $this->tiposDocumentos = TipoDocumentoEnum::cases();
        $this->ufs = UfEnum::cases();
    }

    public function salvarDocumento()
    {
        if ($this->tipoDocumento && $this->localDescarregamento && $this->cidade && $this->valorTotal && $this->peso && $this->chave) {
            $this->teste = 'ola';
        }
    }

    public function render()
    {
        return view('livewire.m-d-fe');
    }
}
