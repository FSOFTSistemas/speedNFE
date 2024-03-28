<?php

namespace App\Http\Livewire;

use App\Enums\TipoCargaEnum;
use App\Enums\TipoDocumentoEnum;
use App\Enums\UfEnum;
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
    public $municipio = null;
    public $codMunCarregamento = null;
    public $percurso = null;
    public $dataInicio = null;
    public $tipoTransporte = null;
    public $numero = null;
    public $serie = null;
    public $produtoPredominante = null;
    public $tipoCarga = null;
    public $valorTotal = 0;
    public $pesoTotal = 0;
    public $info_fisco = null;
    public $info_contribuinte = null;
    public $motoristas = [];
    public $reboques = [];

    //Lacres
    public $lacres = [];
    public $numeroLacre = null;

    // Produto Predominante
    public $codGTIN = null;
    public $codNCM = null;
    public $latCarregamento = null;
    public $lonCarregamento = null;
    public $latDescarregamento = null;
    public $lonDescarregamento = null;

    //Dados da NFe
    public $serieNota = null;
    public $numeroNota = null;
    public $ufNota = null;
    public $nota = [];
    public $notas = [];
    public $indexEdit = null;

    //Dados para preemcher a tela
    public $carregamento = null;
    public $numeroNotas = [];
    public $tiposDocumentos = [];
    public $cidadesDescarregamento = [];
    public $cidadesCarregamento = [];
    public $tiposCarga = [];
    public $ufs = [];
    public $percursos = [];
    public $veiculosTracao = [];
    public $veiculosReboqueDisponiveis = [];
    public $motoristasDisponiveis = [];

    public function mount()
    {
        //Injetar Services
        $motoristaService = new MotoristaService();
        $veiculoService = new VeiculosService();
        $empresaService = new EmpresasService();
        $cidadeService = new CidadeService();
        $empresa = $empresaService->buscarEmpresa(Auth::user()->empresa_id);

        $this->numero = "Geração Automática";
        $this->localCarregamento = $empresa->uf;
        $this->carregamento = json_encode($cidadeService->buscarCidade($empresa->cidade));
        $this->carregamento();
        $this->tiposDocumentos = TipoDocumentoEnum::cases();
        $this->tiposCarga = TipoCargaEnum::cases();
        $this->ufs = UfEnum::cases();
        $this->cidadesCarregamento = $cidadeService->buscarCidadesPorUf($this->localCarregamento);
        $this->dataInicio = now()->format('Y-m-d');
        $this->veiculosTracao = $veiculoService->buscarVeiculosTracao();
        $this->veiculosReboqueDisponiveis = $veiculoService->buscarReboques();
        $this->motoristasDisponiveis = $motoristaService->buscarMotoristas(Auth::user()->empresa_id);
        $this->numeroNotas = ['NFe' => 0, 'MDFe' => 0, 'CTe' => 0];
    }

    public function buscarCidades($cargaDescarga)
    {
        // Injetar Service
        $cidadeService = new CidadeService();
        if ($cargaDescarga) {
            $this->cidadesDescarregamento = $cidadeService->buscarCidadesPorUf($this->localDescarregamento);
            return $this->emit('cidades', $this->cidadesDescarregamento);
        } else {
            $this->cidadesCarregamento = $cidadeService->buscarCidadesPorUf($this->localCarregamento);
        }
    }

    public function salvarDocumento()
    {
        if ($this->tipoDocumento && $this->localDescarregamento && $this->cidade && $this->valor && $this->peso && $this->chave) {
            if ($this->buscarChave($this->chave) >= 1) {
                return $this->emit('chaveJaExiste');
            } else if (!$this->validarChaveNFe($this->chave)) {
                return $this->emit('chaveInvalida');
            }
            $local = explode('@', $this->cidade);
            $this->nota = ['tipoDocumento' => $this->tipoDocumento, 'cidade' => $local[0], 'codMun' => $local[1], 'ufNota' => $this->ufNota, 'valor' => $this->valor, 'peso' => $this->peso, 'chave' => $this->chave, 'serieNota' => $this->serieNota, 'numeroNota' => $this->numeroNota];
            $this->notas[] = $this->nota;
            $this->numberNotes($this->tipoDocumento);
            $this->calcularTotais();
            $this->limparCampos();
            $this->emit('btnCancelar');
            return $this->emit('fecharModal');
        }
    }

    public function numberNotes($tpDoc)
    {
        if (array_key_exists($tpDoc, $this->numeroNotas)) {
            $this->numeroNotas[$tpDoc]++;
        }
    }

    public function addMotorista()
    {
        if ($this->motorista && !in_array($this->motorista, $this->motoristas) && count($this->motoristas) <= 3) {
            $this->motoristas[] = $this->motorista;
            $this->motorista = null;
        }
    }

    public function removeMotorista($motorista)
    {
        if (is_numeric($motorista)) {
            unset($this->motoristas[$motorista]);
            $this->motoristas = array_values($this->motoristas);
        }
    }

    public function addReboque()
    {
        if ($this->veiculoReboque && !in_array($this->veiculoReboque, $this->reboques) && count($this->reboques) <= 3) {
            $this->reboques[] = $this->veiculoReboque;
            $this->veiculoReboque = null;
        }
    }

    public function removeReboque($reboque)
    {
        if (is_numeric($reboque)) {
            unset($this->reboques[$reboque]);
            $this->reboques = array_values($this->reboques);
        }
    }

    public function buscarChave($chave)
    {
        $achou = 0;
        foreach ($this->notas as $nota) {
            if ($nota['chave'] == $chave) {
                $achou++;
            }
        }
        return $achou;
    }

    public function calcularTotais()
    {
        $valor = 0;
        $peso = 0;
        foreach ($this->notas as $nota) {
            $valor += $nota['valor'];
            $peso += $nota['peso'];
        }
        $this->valorTotal = $valor;
        $this->pesoTotal = $peso;
    }

    public function limparCampos()
    {
        $this->tipoDocumento = null;
        $this->cidade = null;
        $this->valor = null;
        $this->peso = null;
        $this->chave = null;
        $this->indexEdit = null;
    }

    public function validarChaveNFe($chave)
    {
        if (strlen($chave) != 44) {
            return false;
        }
        $cnpjEmitente = substr($chave, 6, 14);
        $serie = substr($chave, 22, 3);
        $numeroNota = substr($chave, 25, 9);
        $digitoVerificador = substr($chave, 43, 1);
        if (!$this->validarCNPJ($cnpjEmitente)) {
            return false;
        }
        $dvCalculado = $this->calcularDV(substr($chave, 0, -1));
        if ($dvCalculado != $digitoVerificador) {
            return false;
        }
        $this->ufNota = $this->localDescarregamento;
        $this->serieNota = $serie;
        $this->numeroNota = $numeroNota;
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

    public function addPercurso()
    {
        if ($this->percurso && !in_array($this->percurso, $this->percursos)) {
            if ($this->percurso == $this->localCarregamento || $this->percurso == $this->localDescarregamento) {
                return $this->emit('percursoInvalido');
            }
            $this->percursos[] = $this->percurso;
            $this->percurso = null;
            return $this->emit('percursos', $this->percursos);
        }
    }

    public function removePercurso()
    {
        array_pop($this->percursos);
        return $this->emit('percursos', $this->percursos);
    }

    public function carregamento()
    {
        $carregamento = json_decode($this->carregamento);
        $this->municipio = $carregamento->cidade;
        $this->codMunCarregamento = $carregamento->municipio;
    }

    public function editNote($nota, $index)
    {
        $this->tipoDocumento = $nota['tipoDocumento'];
        $this->cidade = $nota['cidade'] . '@' . $nota['codMun'];
        $this->valor = $nota['valor'];
        $this->peso = $nota['peso'];
        $this->chave = $nota['chave'];
        $this->indexEdit = $index;
        return $this->emit('abrirModalEdit');
    }

    public function cancelEdit()
    {
        $this->limparCampos();
        return $this->emit('fecharModalEdit');
    }

    public function updateNote()
    {
        if ($this->tipoDocumento && $this->localDescarregamento && $this->cidade && $this->valor && $this->peso && $this->chave) {
            if ($this->buscarChave($this->chave) > 1) {
                return $this->emit('chaveJaExiste');
            } else if (!$this->validarChaveNFe($this->chave)) {
                return $this->emit('chaveInvalida');
            }
            $local = explode('@', $this->cidade);
            $this->notas[$this->indexEdit] = ['tipoDocumento' => $this->tipoDocumento, 'cidade' => $local[0], 'codMun' => $local[1], 'ufNota' => $this->ufNota, 'valor' => $this->valor, 'peso' => $this->peso, 'chave' => $this->chave, 'serieNota' => $this->serieNota, 'numeroNota' => $this->numeroNota];
            $this->calcularTotais();
            $this->limparCampos();
            return $this->emit('fecharModalEdit');
        }
    }

    public function deleteNote($nota)
    {
        unset($this->notas[$nota]);
        $this->notas = array_values($this->notas);
        return $this->calcularTotais();
    }

    public function addNote()
    {
        $this->emit('atualizarSelect', $this->localDescarregamento);
        return $this->emit('abrirModal');
    }

    public function render()
    {
        return view('livewire.m-d-fe');
    }

}