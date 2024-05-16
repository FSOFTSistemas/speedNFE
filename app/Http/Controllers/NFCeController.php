<?php

namespace App\Http\Controllers;

use App\Services\CupomFormaService;
use App\Services\CupomService;
use App\Services\ItemCupomService;
use App\Services\NFCeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NFCeController extends Controller
{
    private $cupomService;
    private $cupomFormaService;
    private $itemCupomService;
    private $nfceService;

    public function __construct(CupomService $cupomService, CupomFormaService $cupomFormaService, ItemCupomService $itemCupomService, NFCeService $nfceService)
    {
        $this->cupomService = $cupomService;
        $this->cupomFormaService = $cupomFormaService;
        $this->itemCupomService = $itemCupomService;
        $this->nfceService = $nfceService;
    }

    public function index()
    {
        try {
            $nfces = $this->nfceService->getCompanyNFCes(Auth::user()->empresa_id);
            return view('nfce.index', ['nfces' => $nfces]);
        } catch (\Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: ' . $e->getMessage());
        }
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        //
    }

}
