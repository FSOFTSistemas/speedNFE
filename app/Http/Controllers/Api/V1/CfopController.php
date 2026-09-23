<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Models\cfop;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CfopController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = cfop::query();

        if ($request->filled('q')) {
            $termo = $request->get('q');
            $query->where(function ($q) use ($termo) {
                $q->where('cfop', 'like', "%{$termo}%")
                    ->orWhere('natureza', 'like', "%{$termo}%");
            });
        }

        return $this->success($query->orderBy('cfop')->get());
    }
}
