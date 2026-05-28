<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;

class ClientesController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = Auth::guard('api')->user();

        $query = Cliente::query();

        if ($user && (int) $user->empresa_id !== 1) {
            $query->where('empresa_id', $user->empresa_id);
        } elseif ($request->filled('empresa_id')) {
            $query->where('empresa_id', $request->empresa_id);
        } elseif ($request->filled('empresa')) {
            $query->where('empresa_id', $request->empresa);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
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

        if ($request->filled('nome')) {
            $query->where('nome', 'like', '%' . trim($request->nome) . '%');
        }

        if ($request->filled('apelido')) {
            $query->where('apelido', 'like', '%' . trim($request->apelido) . '%');
        }

        if ($request->filled('cpf_cnpj')) {
            $cpfCnpj = preg_replace('/\D/', '', $request->cpf_cnpj);
            $query->where('cpf_cnpj', $cpfCnpj);
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('situacao')) {
            $query->where('situacao', $request->situacao);
        }

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

        $sortBy = $request->get('sort_by', 'nome');
        $sortBy = in_array($sortBy, $allowedSortFields, true) ? $sortBy : 'nome';

        $sortOrder = strtolower($request->get('sort_order', 'asc')) === 'desc' ? 'desc' : 'asc';

        $perPage = (int) $request->get('per_page', 15);
        $perPage = max(1, min($perPage, 100));

        $clientes = $query
            ->orderBy($sortBy, $sortOrder)
            ->paginate($perPage);

        return response()->json($clientes);
    }

    public function store(Request $request): JsonResponse
    {
        $user = Auth::guard('api')->user();

        $data = $request->validate($this->rules());
        $data = $this->normalizarDados($data);
        $clienteData = $this->mapearPayloadParaCliente($data, $user);
        $enderecoData = $this->mapearPayloadParaEndereco($data);

        DB::beginTransaction();

        try {
            if (! $this->empresaPodeCadastrarCliente($clienteData['empresa_id'] ?? null)) {
                DB::rollBack();

                return response()->json([
                    'message' => 'Limite de clientes atingido para esta empresa.',
                ], 422);
            }

            if (! empty($enderecoData)) {
                $enderecoId = DB::table('enderecos')->insertGetId($enderecoData);
                $clienteData['endereco_id'] = $enderecoId;
            }

            $cliente = Cliente::create($clienteData);

            DB::commit();

            return response()->json([
                'message' => 'Cliente criado com sucesso.',
                'data' => $cliente->fresh(),
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Erro ao criar cliente.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        $cliente = $this->buscarClientePermitido($id);

        return response()->json([
            'data' => $cliente,
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $cliente = $this->buscarClientePermitido($id);
        $user = Auth::guard('api')->user();

        $data = $request->validate($this->rules($cliente->id));
        $data = $this->normalizarDados($data);
        $clienteData = $this->mapearPayloadParaCliente($data, $user, true);
        $enderecoData = $this->mapearPayloadParaEndereco($data);

        DB::beginTransaction();

        try {
            $cliente->update($clienteData);

            if (! empty($enderecoData)) {
                if (! empty($cliente->endereco_id)) {
                    DB::table('enderecos')
                        ->where('id', $cliente->endereco_id)
                        ->update($enderecoData);
                } else {
                    $enderecoId = DB::table('enderecos')->insertGetId($enderecoData);
                    $cliente->update(['endereco_id' => $enderecoId]);
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Cliente atualizado com sucesso.',
                'data' => $cliente->fresh(),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Erro ao atualizar cliente.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        $cliente = $this->buscarClientePermitido($id);
        $cliente->delete();

        return response()->json([
            'message' => 'Cliente removido com sucesso.',
        ]);
    }

    public function consultarCnpj(string $cnpj): JsonResponse
    {
        $cnpj = preg_replace('/\D/', '', $cnpj);

        if (strlen($cnpj) !== 14) {
            return response()->json([
                'message' => 'CNPJ inválido.',
            ], 422);
        }

        try {
            $response = Http::withoutVerifying()
                ->timeout(15)
                ->get("https://publica.cnpj.ws/cnpj/{$cnpj}");

            if (! $response->successful()) {
                return response()->json([
                    'message' => 'CNPJ não encontrado ou serviço indisponível.',
                ], $response->status());
            }

            $dados = $response->json();
            $estabelecimento = $dados['estabelecimento'] ?? [];
            $cidade = $estabelecimento['cidade'] ?? [];
            $estado = $estabelecimento['estado'] ?? [];
            $inscricoes = $estabelecimento['inscricoes_estaduais'] ?? [];

            return response()->json([
                'data' => [
                    'nome' => $dados['razao_social'] ?? null,
                    'apelido' => $estabelecimento['nome_fantasia'] ?? null,
                    'cpf_cnpj' => $cnpj,
                    'rg_ie' => $inscricoes[0]['inscricao_estadual'] ?? null,
                    'rua' => $estabelecimento['logradouro'] ?? null,
                    'numero' => $estabelecimento['numero'] ?? null,
                    'bairro' => $estabelecimento['bairro'] ?? null,
                    'cidade' => $cidade['nome'] ?? null,
                    'uf' => $estado['sigla'] ?? null,
                    'cep' => isset($estabelecimento['cep']) ? preg_replace('/\D/', '', $estabelecimento['cep']) : null,
                    'complemento' => $estabelecimento['complemento'] ?? null,
                    'telefone' => $estabelecimento['telefone1'] ?? null,
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Erro ao consultar CNPJ.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function verificarCpfCnpj(Request $request): JsonResponse
    {
        $request->validate([
            'cpf_cnpj' => ['required', 'string'],
        ]);

        $user = Auth::guard('api')->user();
        $cpfCnpj = preg_replace('/\D/', '', $request->cpf_cnpj);

        $query = Cliente::query()
            ->where('cpf_cnpj', $cpfCnpj)
            ->where('situacao', 0);

        if ($user && (int) $user->empresa_id !== 1) {
            $query->where('empresa_id', $user->empresa_id);
        } elseif ($request->filled('empresa_id')) {
            $query->where('empresa_id', $request->empresa_id);
        }

        if ($request->filled('ignore_id')) {
            $query->where('id', '!=', $request->ignore_id);
        }

        return response()->json([
            'exists' => $query->exists(),
        ]);
    }

    private function rules(?int $clienteId = null): array
    {
        return [
            'empresa_id' => ['nullable', 'integer'],
            'empresa' => ['nullable', 'integer'],

            'codigo' => ['required', 'string', 'max:255'],
            'nome' => ['required', 'string', 'max:255'],
            'apelido' => ['required', 'string', 'max:255'],
            'cpf_cnpj' => [
                'required',
                'string',
                Rule::unique('clientes', 'cpf_cnpj')->ignore($clienteId),
            ],
            'rg_ie' => ['required', 'string', 'max:255'],
            'tipo' => ['required', 'string', 'max:255'],
            'telefone' => ['required', 'string', 'max:255'],
            'celular' => ['nullable', 'string', 'max:255'],
            'limite' => ['required', 'numeric'],
            'situacao' => ['nullable'],

            'rua' => ['required', 'string', 'max:255'],
            'numero' => ['required', 'string', 'max:255'],
            'bairro' => ['required', 'string', 'max:255'],
            'cidade' => ['required', 'string', 'max:255'],
            'uf' => ['required', 'string', 'max:2'],
            'ibge' => ['required', 'string', 'max:20'],
            'cep' => ['required', 'string', 'max:20'],
            'complemento' => ['nullable', 'string', 'max:255'],
        ];
    }

    private function normalizarDados(array $data): array
    {
        foreach (['cpf_cnpj', 'telefone', 'celular', 'cep'] as $field) {
            if (array_key_exists($field, $data) && ! empty($data[$field])) {
                $data[$field] = preg_replace('/\D/', '', $data[$field]);
            }
        }

        foreach ($data as $field => $value) {
            if (is_string($value)) {
                $data[$field] = trim($value);
            }
        }

        if (array_key_exists('uf', $data) && ! empty($data['uf'])) {
            $data['uf'] = strtoupper($data['uf']);
        }

        return $data;
    }

    private function mapearPayloadParaCliente(array $data, $user = null, bool $isUpdate = false): array
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
        ];

        if (array_key_exists('situacao', $data)) {
            $mapped['situacao'] = $data['situacao'];
        }

        if (array_key_exists('empresa', $data)) {
            $mapped['empresa_id'] = $data['empresa'];
        }

        if (array_key_exists('empresa_id', $data)) {
            $mapped['empresa_id'] = $data['empresa_id'];
        }

        if (! $isUpdate && $user && ! array_key_exists('empresa_id', $mapped)) {
            $mapped['empresa_id'] = $user->empresa_id;
        }

        if ($user && (int) $user->empresa_id !== 1) {
            $mapped['empresa_id'] = $user->empresa_id;
        }

        return array_filter($mapped, fn ($value) => $value !== null);
    }

    private function mapearPayloadParaEndereco(array $data): array
    {
        $endereco = [];

        foreach (['rua', 'numero', 'bairro', 'cidade', 'uf', 'ibge', 'cep', 'complemento'] as $field) {
            if (array_key_exists($field, $data)) {
                $endereco[$field] = $data[$field];
            }
        }

        return $endereco;
    }

    private function buscarClientePermitido(int $id): Cliente
    {
        $user = Auth::guard('api')->user();

        $query = Cliente::query();

        if ($user && (int) $user->empresa_id !== 1) {
            $query->where('empresa_id', $user->empresa_id);
        }

        return $query->findOrFail($id);
    }

    private function empresaPodeCadastrarCliente(?int $empresaId): bool
    {
        if (empty($empresaId) || (int) $empresaId === 1) {
            return true;
        }

        $empresa = DB::table('empresas')->where('id', $empresaId)->first();

        if (! $empresa || ! isset($empresa->limClientes)) {
            return true;
        }

        $totalClientes = Cliente::query()
            ->where('empresa_id', $empresaId)
            ->count();

        return $totalClientes < (int) $empresa->limClientes;
    }
}