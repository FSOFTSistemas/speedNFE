<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Models\CClassTrib;
use App\Models\CstIbsCbs;
use App\Models\Ncm;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/** Tabelas fiscais usadas no cadastro de produto (mesmas fontes das telas web). */
class FiscalController extends ApiController
{
    public function cstIbsCbs(): JsonResponse
    {
        return $this->success(CstIbsCbs::orderBy('codigo')->get(['codigo', 'descricao']));
    }

    /** Espelha a rota web /api/cclasstrib/{cst}. */
    public function cClassTrib(Request $request): JsonResponse
    {
        $cst = (string) $request->query('cst', '');

        $query = CClassTrib::query()->select('codigo', 'descricao')->orderBy('codigo');

        if ($cst !== '') {
            $query->where(fn ($q) => $q->where('cst_compativel', $cst)->orWhere('cst_compativel', intval($cst)));
        }

        if ($request->filled('search')) {
            $search = trim($request->query('search'));
            $query->where(fn ($q) => $q->where('codigo', 'like', "%{$search}%")->orWhere('descricao', 'like', "%{$search}%"));
        }

        return $this->success($query->limit(100)->get());
    }

    public function ncms(Request $request): JsonResponse
    {
        $query = Ncm::query()->select('ncm', 'descricao')->orderBy('ncm');

        if ($request->filled('search')) {
            $search = trim($request->query('search'));
            $digits = preg_replace('/\D/', '', $search);

            $query->where(function ($q) use ($search, $digits) {
                $q->where('descricao', 'like', "%{$search}%");
                if ($digits !== '') {
                    $q->orWhere('ncm', 'like', "{$digits}%");
                }
            });
        }

        return $this->success($query->limit(50)->get());
    }
}
