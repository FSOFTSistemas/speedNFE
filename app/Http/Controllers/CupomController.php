<?php

namespace App\Http\Controllers;

use App\Services\CupomService;
use Illuminate\Http\Request;

class CupomController extends Controller
{

    private $cupomService;

    public function __construct(CupomService $cupomService)
    {
        $this->cupomService = $cupomService;
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
