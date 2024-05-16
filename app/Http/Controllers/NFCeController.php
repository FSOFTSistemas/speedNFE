<?php

namespace App\Http\Controllers;

use App\Services\CupomFormaService;
use App\Services\CupomService;
use App\Services\ItemCupomService;
use App\Services\NFCeService;
use Illuminate\Http\Request;

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
        //
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
