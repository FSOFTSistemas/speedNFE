<?php

namespace App\Http\Livewire;

use App\Enum\TipoDocumentoEnum;
use App\Enum\UfEnum;
use Livewire\Component;

class MDFe extends Component
{
    public $tipoDocumento;
    public $localDescarregamento;
    public $cidade;
    public $valorTotal;
    public $peso;
    public $chave;

    public $tiposDocumentos = [];
    public $ufs = [];

    public function mount()
    {
        $this->tiposDocumentos = TipoDocumentoEnum::cases();
        $this->ufs = UfEnum::cases();
    }

    public function atualizar()
    {
        dd('oi');
    }

    public function render()
    {
        return view('livewire.m-d-fe');
    }
}
