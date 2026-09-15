<?php

namespace App\Services;

use App\Exceptions\AlreadyExistException;
use App\Models\Cliente;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ClientesService
{
    public function listarApi(array $filters, $user = null): LengthAwarePaginator
    {
        $query = Cliente::query()->with('endereco');

        $this->aplicarEscopoEmpresaApi($query, $filters, $user);
        $this->aplicarFiltrosApi($query, $filters);

        $allowedSortFields = [
            'id',
            'codigo',
            'nome',
            'apelido',
            'cpf_cnpj',
            'tipo',
            'limite',
            'created_at',
            'updated_at',
        ];

        $sortBy = $filters['sort_by'] ?? 'nome';
        $sortBy = in_array($sortBy, $allowedSortFields, true) ? $sortBy : 'nome';
        $sortOrder = strtolower($filters['sort_order'] ?? 'asc') === 'desc' ? 'desc' : 'asc';
        $perPage = (int) ($filters['per_page'] ?? 15);
        $perPage = max(1, min($perPage, 100));

        return $query->orderBy($sortBy, $sortOrder)->paginate($perPage);
    }

    public function buscarPermitidoApi(int $id, $user = null): Cliente
    {
        $query = Cliente::query()->with('endereco');

        if ($user && (int) $user->empresa_id !== 1) {
            $query->where('empresa_id', $user->empresa_id);
        }

        return $query->findOrFail($id);
    }

    public function criarApi(array $data, $user = null): Cliente
    {
        return DB::transaction(function () use ($data, $user) {
            $clienteData = $this->mapearPayloadClienteApi($data, $user);

            if (! $this->empresaPodeCadastrarClienteApi($clienteData['empresa_id'] ?? null)) {
                throw new AlreadyExistException('Limite de clientes atingido para esta empresa.');
            }

            $enderecoId = DB::table('enderecos')->insertGetId($this->mapearPayloadEnderecoApi($data));
            $clienteData['endereco_id'] = $enderecoId;

            return Cliente::create($clienteData)->fresh('endereco');
        });
    }

    public function atualizarApi(Cliente $cliente, array $data, $user = null): Cliente
    {
        return DB::transaction(function () use ($cliente, $data, $user) {
            $cliente->update($this->mapearPayloadClienteApi($data, $user, true));

            $enderecoData = $this->mapearPayloadEnderecoApi($data);

            if (! empty($cliente->endereco_id)) {
                DB::table('enderecos')->where('id', $cliente->endereco_id)->update($enderecoData);
            } else {
                $enderecoId = DB::table('enderecos')->insertGetId($enderecoData);
                $cliente->update(['endereco_id' => $enderecoId]);
            }

            return $cliente->fresh('endereco');
        });
    }

    public function removerApi(Cliente $cliente): bool
    {
        return (bool) $cliente->delete();
    }

    public function cpfCnpjExisteApi(string $cpfCnpj, array $filters, $user = null): bool
    {
        $query = Cliente::query()
            ->where('cpf_cnpj', preg_replace('/\D/', '', $cpfCnpj))
            ->where('situacao', 0);

        if ($user && (int) $user->empresa_id !== 1) {
            $query->where('empresa_id', $user->empresa_id);
        } elseif (! empty($filters['empresa_id'])) {
            $query->where('empresa_id', $filters['empresa_id']);
        }

        if (! empty($filters['ignore_id'])) {
            $query->where('id', '!=', $filters['ignore_id']);
        }

        return $query->exists();
    }

    private function aplicarEscopoEmpresaApi(Builder $query, array $filters, $user = null): void
    {
        if ($user && (int) $user->empresa_id !== 1) {
            $query->where('empresa_id', $user->empresa_id);

            return;
        }

        if (! empty($filters['empresa_id'])) {
            $query->where('empresa_id', $filters['empresa_id']);
        } elseif (! empty($filters['empresa'])) {
            $query->where('empresa_id', $filters['empresa']);
        }
    }

    private function aplicarFiltrosApi(Builder $query, array $filters): void
    {
        if (! empty($filters['search'])) {
            $search = trim($filters['search']);
            $searchOnlyNumbers = preg_replace('/\D/', '', $search);

            $query->where(function ($q) use ($search, $searchOnlyNumbers) {
                $q->where('nome', 'like', "%{$search}%")
                    ->orWhere('apelido', 'like', "%{$search}%")
                    ->orWhere('cpf_cnpj', 'like', "%{$search}%")
                    ->orWhere('rg_ie', 'like', "%{$search}%")
                    ->orWhere('telefone', 'like', "%{$search}%")
                    ->orWhere('celular', 'like', "%{$search}%");

                if (! empty($searchOnlyNumbers)) {
                    $q->orWhere('cpf_cnpj', 'like', "%{$searchOnlyNumbers}%")
                        ->orWhere('telefone', 'like', "%{$searchOnlyNumbers}%")
                        ->orWhere('celular', 'like', "%{$searchOnlyNumbers}%");
                }
            });
        }

        foreach (['nome', 'apelido'] as $field) {
            if (! empty($filters[$field])) {
                $query->where($field, 'like', '%'.trim($filters[$field]).'%');
            }
        }

        if (! empty($filters['cpf_cnpj'])) {
            $query->where('cpf_cnpj', preg_replace('/\D/', '', $filters['cpf_cnpj']));
        }

        foreach (['tipo', 'situacao'] as $field) {
            if (array_key_exists($field, $filters) && $filters[$field] !== null && $filters[$field] !== '') {
                $query->where($field, $filters[$field]);
            }
        }
    }

    private function mapearPayloadClienteApi(array $data, $user = null, bool $isUpdate = false): array
    {
        $mapped = [
            'codigo' => $data['codigo'] ?? null,
            'nome' => $data['nome'] ?? null,
            'apelido' => $data['apelido'] ?? null,
            'cpf_cnpj' => $data['cpf_cnpj'] ?? null,
            'rg_ie' => $data['rg_ie'] ?? null,
            'tipo' => $data['tipo'] ?? null,
            'telefone' => $data['telefone'] ?? null,
            'celular' => $data['celular'] ?? null,
            'limite' => $data['limite'] ?? null,
            'situacao' => $data['situacao'] ?? null,
            'empresa_id' => $data['empresa_id'] ?? ($data['empresa'] ?? null),
        ];

        $mapped['contribuinte'] = ! empty($mapped['rg_ie']) ? 1 : 0;

        if (! $isUpdate && $user && empty($mapped['empresa_id'])) {
            $mapped['empresa_id'] = $user->empresa_id;
        }

        if ($user && (int) $user->empresa_id !== 1) {
            $mapped['empresa_id'] = $user->empresa_id;
        }

        return array_filter($mapped, fn ($value) => $value !== null);
    }

    private function mapearPayloadEnderecoApi(array $data): array
    {
        return array_filter([
            'rua' => $data['rua'] ?? null,
            'numero' => $data['numero'] ?? null,
            'bairro' => $data['bairro'] ?? null,
            'cidade' => $data['cidade'] ?? null,
            'uf' => $data['uf'] ?? null,
            'codigoIBGE' => $data['ibge'] ?? ($data['codigoIBGE'] ?? null),
            'cep' => $data['cep'] ?? null,
            'complemento' => $data['complemento'] ?? null,
        ], fn ($value) => $value !== null);
    }

    private function empresaPodeCadastrarClienteApi(?int $empresaId): bool
    {
        if (empty($empresaId) || (int) $empresaId === 1) {
            return true;
        }

        $empresa = DB::table('empresas')->where('id', $empresaId)->first();

        if (! $empresa || ! isset($empresa->limClientes)) {
            return true;
        }

        return Cliente::query()->where('empresa_id', $empresaId)->count() < (int) $empresa->limClientes;
    }

    public function um($id)
    {
        return Cliente::select('clientes.*', 'empresas.razao', 'enderecos.rua', 'enderecos.numero', 'enderecos.bairro', 'enderecos.cidade',
            'enderecos.cep', 'enderecos.codigoIBGE', 'enderecos.uf')
            ->join('empresas', 'empresas.id', 'clientes.empresa_id')
            ->join('enderecos', 'enderecos.id', 'clientes.endereco_id')
            ->where('clientes.id', $id)
            ->first();
    }

    public function todos($id_empresa)
    {
        if ($id_empresa == 1) {
            $id_empresa = '%';
        }

        return DB::table('clientes')
            ->select('clientes.*', 'empresas.fantasia')
            ->join('empresas', 'empresas.id', '=', 'clientes.empresa_id')
            ->where('clientes.empresa_id', 'like', $id_empresa)
            ->get();
    }

    public function todosClientes()
    {
        return Cliente::select('clientes.*', 'empresas.fantasia')
            ->join('empresas', 'empresas.id', '=', 'clientes.empresa_id')
            ->get();
    }

    public function salvar($codigo, $nome, $apelido, $cpf_cnpj, $rg_ie, $telefone, $celular, $tipo, $limite, $empresa, $endereco)
    {
        $contribuinte = 0;
        if ($rg_ie) {
            $contribuinte = 1;
        }
        if (Cliente::where('cpf_cnpj', $cpf_cnpj)->where('empresa_id', $empresa)->exists()) {
            throw new AlreadyExistException('O CPF/CNPJ já está em uso!');
        }

        return Cliente::create([
            'codigo' => $codigo,
            'nome' => $nome,
            'apelido' => $apelido,
            'cpf_cnpj' => $cpf_cnpj,
            'rg_ie' => $rg_ie,
            'telefone' => $telefone,
            'celular' => $celular,
            'tipo' => $tipo,
            'situacao' => 0,
            'limite' => $limite,
            'contribuinte' => $contribuinte,
            'empresa_id' => $empresa,
            'endereco_id' => $endereco,
        ]);
    }

    public function excluir($id)
    {
        $cliente = Cliente::findOrFail($id);

        return $cliente->delete();
    }

    public function editar($id, $tipo, $nome, $apelido, $cpf_cnpj, $rg_ie, $telefone, $celular, $limite, $empresa_id)
    {
        $cliente = Cliente::find($id);
        if (Cliente::where('cpf_cnpj', $cpf_cnpj)->where('empresa_id', $empresa_id)->where('id', '!=', $cliente->id)->exists()) {
            throw new AlreadyExistException('O CPF/CNPJ já está em uso!');
        }
        $cliente->update([
            'tipo' => $tipo,
            'nome' => $nome,
            'apelido' => $apelido,
            'cpf_cnpj' => $cpf_cnpj,
            'rg_ie' => $rg_ie,
            'telefone' => $telefone,
            'celular' => $celular,
            'limite' => $limite,
        ]);

        return $cliente;
    }

    public function contagemClientes($id_empresa)
    {
        return Cliente::where('empresa_id', '=', $id_empresa)
            ->whereRaw('MONTH(created_at) = MONTH(CURRENT_DATE)')
            ->whereRaw('YEAR(created_at) = YEAR(CURRENT_DATE)')
            ->count();
    }
}
