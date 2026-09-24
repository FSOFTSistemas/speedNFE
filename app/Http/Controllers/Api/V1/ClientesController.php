<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\AlreadyExistException;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\V1\StoreClienteRequest;
use App\Http\Requests\Api\V1\UpdateClienteRequest;
use App\Http\Resources\ClienteResource;
use App\Services\ClientesService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class ClientesController extends ApiController
{
    public function __construct(private ClientesService $clientesService) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $clientes = $this->clientesService->listarApi(
            $request->all(),
            Auth::guard('api')->user()
        );

        return ClienteResource::collection($clientes);
    }

    public function store(StoreClienteRequest $request): JsonResponse
    {
        try {
            $cliente = $this->clientesService->criarApi(
                $request->validated(),
                Auth::guard('api')->user()
            );
        } catch (AlreadyExistException $e) {
            return $this->message($e->getMessage(), 422);
        }

        return $this->success(
            new ClienteResource($cliente),
            'Cliente criado com sucesso.',
            201
        );
    }

    public function show(int $id): ClienteResource
    {
        $cliente = $this->clientesService->buscarPermitidoApi(
            $id,
            Auth::guard('api')->user()
        );

        return new ClienteResource($cliente);
    }

    public function update(UpdateClienteRequest $request, int $id): JsonResponse
    {
        $cliente = $this->clientesService->buscarPermitidoApi(
            $id,
            Auth::guard('api')->user()
        );

        $cliente = $this->clientesService->atualizarApi(
            $cliente,
            $request->validated(),
            Auth::guard('api')->user()
        );

        return $this->success(
            new ClienteResource($cliente),
            'Cliente atualizado com sucesso.'
        );
    }

    public function destroy(int $id): JsonResponse
    {
        $cliente = $this->clientesService->buscarPermitidoApi(
            $id,
            Auth::guard('api')->user()
        );

        $this->clientesService->removerApi($cliente);

        return $this->message('Cliente removido com sucesso.');
    }

    public function consultarCnpj(string $cnpj): JsonResponse
    {
        $cnpj = preg_replace('/\D/', '', $cnpj);

        if (strlen($cnpj) !== 14) {
            return $this->message('CNPJ inválido.', 422);
        }

        try {
            $response = Http::withoutVerifying()
                ->timeout(15)
                ->get("https://publica.cnpj.ws/cnpj/{$cnpj}");

            if (! $response->successful()) {
                return $this->message('CNPJ não encontrado ou serviço indisponível.', $response->status());
            }

            $dados = $response->json();
            $estabelecimento = $dados['estabelecimento'] ?? [];
            $cidade = $estabelecimento['cidade'] ?? [];
            $estado = $estabelecimento['estado'] ?? [];
            $inscricoes = $estabelecimento['inscricoes_estaduais'] ?? [];

            // Prefere a IE ativa do mesmo estado do estabelecimento; a lista pode trazer IEs de outras UFs.
            $inscricao = collect($inscricoes)->first(fn ($ie) => ($ie['ativo'] ?? false) && ($ie['estado']['sigla'] ?? null) === ($estado['sigla'] ?? null))
                ?? $inscricoes[0] ?? null;

            // O cnpj.ws devolve o DDD separado do número.
            $telefone = preg_replace('/\D/', '', ($estabelecimento['ddd1'] ?? '').($estabelecimento['telefone1'] ?? ''));

            return $this->success([
                'nome' => $dados['razao_social'] ?? null,
                'apelido' => $estabelecimento['nome_fantasia'] ?? null,
                'cpf_cnpj' => $cnpj,
                'rg_ie' => $inscricao['inscricao_estadual'] ?? null,
                'rua' => $estabelecimento['logradouro'] ?? null,
                'numero' => $estabelecimento['numero'] ?? null,
                'bairro' => $estabelecimento['bairro'] ?? null,
                'cidade' => $cidade['nome'] ?? null,
                'uf' => $estado['sigla'] ?? null,
                'ibge' => isset($cidade['ibge_id']) ? (string) $cidade['ibge_id'] : null,
                'cep' => isset($estabelecimento['cep']) ? preg_replace('/\D/', '', $estabelecimento['cep']) : null,
                'complemento' => $estabelecimento['complemento'] ?? null,
                'telefone' => $telefone !== '' ? $telefone : null,
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

        return response()->json([
            'exists' => $this->clientesService->cpfCnpjExisteApi(
                $request->cpf_cnpj,
                $request->all(),
                Auth::guard('api')->user()
            ),
        ]);
    }
}
