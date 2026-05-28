<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Produto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProdutosController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = Auth::guard('api')->user();

        $query = Produto::query();

        if ($user && (int) $user->empresa_id !== 1) {
            $query->where('empresa_id', $user->empresa_id);
        } elseif ($request->filled('empresa_id')) {
            $query->where('empresa_id', $request->empresa_id);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $searchOnlyNumbers = preg_replace('/\D/', '', $search);

            $query->where(function ($q) use ($search, $searchOnlyNumbers) {
                $q->where('produto', 'like', "%{$search}%")
                    ->orWhere('descricao', 'like', "%{$search}%")
                    ->orWhere('codigo', 'like', "%{$search}%")
                    ->orWhere('codigo_barras', 'like', "%{$search}%")
                    ->orWhere('referencia', 'like', "%{$search}%")
                    ->orWhere('ncm', 'like', "%{$search}%");

                if (! empty($searchOnlyNumbers)) {
                    $q->orWhere('codigo_barras', 'like', "%{$searchOnlyNumbers}%")
                        ->orWhere('ncm', 'like', "%{$searchOnlyNumbers}%");
                }
            });
        }

        if ($request->filled('produto')) {
            $query->where('produto', 'like', '%' . trim($request->produto) . '%');
        }

        if ($request->filled('descricao')) {
            $query->where(function ($q) use ($request) {
                $descricao = trim($request->descricao);
                $q->where('produto', 'like', "%{$descricao}%")
                    ->orWhere('descricao', 'like', "%{$descricao}%");
            });
        }

        if ($request->filled('codigo')) {
            $query->where('codigo', trim($request->codigo));
        }

        if ($request->filled('codigo_barras')) {
            $query->where('codigo_barras', trim($request->codigo_barras));
        }

        if ($request->filled('ncm')) {
            $ncm = trim($request->ncm);
            $ncmOnlyNumbers = preg_replace('/\D/', '', $ncm);

            $query->where(function ($q) use ($ncm, $ncmOnlyNumbers) {
                $q->where('ncm', $ncm);

                if (! empty($ncmOnlyNumbers)) {
                    $q->orWhere('ncm', $ncmOnlyNumbers);
                }
            });
        }

        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
        }

        if ($request->filled('categoria')) {
            $query->where('categoria_id', $request->categoria);
        }

        if ($request->filled('ativo')) {
            $query->where('ativo', filter_var($request->ativo, FILTER_VALIDATE_BOOLEAN));
        }

        $allowedSortFields = [
            'id',
            'produto',
            'descricao',
            'codigo',
            'codigo_barras',
            'ncm',
            'precovenda',
            'preco_venda',
            'precocusto',
            'preco_custo',
            'created_at',
            'updated_at',
        ];

        $sortBy = $request->get('sort_by', 'produto');
        $sortBy = in_array($sortBy, $allowedSortFields, true) ? $sortBy : 'produto';

        $sortOrder = strtolower($request->get('sort_order', 'asc')) === 'desc' ? 'desc' : 'asc';

        $perPage = (int) $request->get('per_page', 15);
        $perPage = max(1, min($perPage, 100));

        $produtos = $query
            ->orderBy($sortBy, $sortOrder)
            ->paginate($perPage);

        return response()->json($produtos);
    }

    public function store(Request $request): JsonResponse
    {
        $user = Auth::guard('api')->user();

        $data = $request->validate($this->rules());
        $data = $this->normalizarDados($data);
        $data = $this->mapearPayloadParaProduto($data, $request, $user);

        $produto = Produto::create($data);

        return response()->json([
            'message' => 'Produto criado com sucesso.',
            'data' => $produto,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $produto = $this->buscarProdutoPermitido($id);

        return response()->json([
            'data' => $produto,
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $produto = $this->buscarProdutoPermitido($id);

        $data = $request->validate($this->rules($produto->id));
        $data = $this->normalizarDados($data);
        $data = $this->mapearPayloadParaProduto($data, $request, Auth::guard('api')->user(), true);

        $produto->update($data);

        return response()->json([
            'message' => 'Produto atualizado com sucesso.',
            'data' => $produto->fresh(),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $produto = $this->buscarProdutoPermitido($id);
        $produto->delete();

        return response()->json([
            'message' => 'Produto removido com sucesso.',
        ]);
    }

    private function rules(?int $produtoId = null): array
    {
        return [
            'empresa_id' => ['nullable', 'integer'],
            'empresa' => ['nullable', 'integer'],

            'categoria_id' => ['nullable', 'integer'],
            'categoria' => ['nullable', 'integer'],

            'codigo' => ['nullable', 'string', 'max:255'],
            'codigo_barras' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('produtos', 'codigo_barras')->ignore($produtoId),
            ],
            'referencia' => ['nullable', 'string', 'max:255'],

            'produto' => ['required_without:descricao', 'string', 'max:255'],
            'descricao' => ['required_without:produto', 'string', 'max:255'],

            'un' => ['nullable', 'string', 'max:20'],
            'unidade' => ['nullable', 'string', 'max:20'],

            'precocusto' => ['nullable', 'numeric', 'min:0'],
            'preco_custo' => ['nullable', 'numeric', 'min:0'],
            'precovenda' => ['nullable', 'numeric', 'min:0'],
            'preco_venda' => ['nullable', 'numeric', 'min:0'],

            'estoque' => ['nullable', 'numeric'],
            'estoque_minimo' => ['nullable', 'numeric'],
            'margem_lucro' => ['nullable', 'numeric'],
            'ativo' => ['nullable', 'boolean'],

            'ncm' => ['required', 'string', 'max:20'],
            'cest' => ['nullable', 'string', 'max:20'],
            'tpProd' => ['nullable', 'string', 'max:20'],

            'cfopinterno' => ['nullable', 'string', 'max:10'],
            'cfopexterno' => ['nullable', 'string', 'max:10'],
            'cfop' => ['nullable', 'string', 'max:10'],

            'cst' => ['nullable', 'string', 'max:10'],
            'cst_csosn' => ['nullable', 'string', 'max:10'],
            'csosn' => ['nullable', 'string', 'max:10'],
            'origem' => ['nullable', 'string', 'max:10'],

            'icms' => ['nullable', 'numeric'],
            'aliquota_icms' => ['nullable', 'numeric'],
            'pis' => ['nullable', 'numeric'],
            'aliquota_pis' => ['nullable', 'numeric'],
            'cofins' => ['nullable', 'numeric'],
            'aliquota_cofins' => ['nullable', 'numeric'],
            'ipi' => ['nullable', 'numeric'],
            'aliquota_ipi' => ['nullable', 'numeric'],

            'cst_pis' => ['nullable', 'string', 'max:10'],
            'cst_cofins' => ['nullable', 'string', 'max:10'],
            'cst_ipi' => ['nullable', 'string', 'max:10'],

            'cst_ibs_cbs' => ['nullable', 'string', 'max:10'],
            'cClassTrib' => ['nullable', 'string', 'max:20'],
            'pIBS' => ['nullable', 'numeric'],
            'pCBS' => ['nullable', 'numeric'],
            'pIS_imposto' => ['nullable', 'numeric'],
        ];
    }

    private function normalizarDados(array $data): array
    {
        if (array_key_exists('ncm', $data) && ! empty($data['ncm'])) {
            $data['ncm'] = preg_replace('/\D/', '', $data['ncm']);
        }

        foreach (['codigo_barras', 'codigo', 'referencia', 'produto', 'descricao', 'un', 'unidade'] as $field) {
            if (array_key_exists($field, $data) && is_string($data[$field])) {
                $data[$field] = trim($data[$field]);
            }
        }

        return $data;
    }

    private function mapearPayloadParaProduto(array $data, Request $request, $user = null, bool $isUpdate = false): array
    {
        $mapped = $data;

        if (array_key_exists('descricao', $data) && ! array_key_exists('produto', $mapped)) {
            $mapped['produto'] = $data['descricao'];
        }

        if (array_key_exists('produto', $data) && ! array_key_exists('descricao', $mapped)) {
            $mapped['descricao'] = $data['produto'];
        }

        if (array_key_exists('categoria', $data)) {
            $mapped['categoria_id'] = $data['categoria'];
        }

        if (array_key_exists('empresa', $data)) {
            $mapped['empresa_id'] = $data['empresa'];
        }

        if (! $isUpdate && $user && ! array_key_exists('empresa_id', $mapped)) {
            $mapped['empresa_id'] = $user->empresa_id;
        }

        if ($user && (int) $user->empresa_id !== 1) {
            $mapped['empresa_id'] = $user->empresa_id;
        }

        if (array_key_exists('un', $data)) {
            $mapped['unidade'] = $data['un'];
        }

        if (array_key_exists('precocusto', $data)) {
            $mapped['preco_custo'] = $data['precocusto'];
        }

        if (array_key_exists('precovenda', $data)) {
            $mapped['preco_venda'] = $data['precovenda'];
        }

        if (array_key_exists('icms', $data)) {
            $mapped['aliquota_icms'] = $data['icms'];
        }

        if (array_key_exists('pis', $data)) {
            $mapped['aliquota_pis'] = $data['pis'];
        }

        if (array_key_exists('cofins', $data)) {
            $mapped['aliquota_cofins'] = $data['cofins'];
        }

        if (array_key_exists('ipi', $data)) {
            $mapped['aliquota_ipi'] = $data['ipi'];
        }

        if (array_key_exists('cst_csosn', $data)) {
            $mapped['csosn'] = $data['cst_csosn'];
        }

        if (array_key_exists('cfopinterno', $data) && ! array_key_exists('cfop', $mapped)) {
            $mapped['cfop'] = $data['cfopinterno'];
        }

        return $mapped;
    }

    private function buscarProdutoPermitido(int $id): Produto
    {
        $user = Auth::guard('api')->user();

        $query = Produto::query();

        if ($user && (int) $user->empresa_id !== 1) {
            $query->where('empresa_id', $user->empresa_id);
        }

        return $query->findOrFail($id);
    }
}