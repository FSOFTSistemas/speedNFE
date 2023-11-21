<?php

namespace App\Services;

use App\Models\Veiculo;
use Exception;
use Flasher\Laravel\Http\Request;
use Illuminate\Support\Facades\DB;

class VeiculosService
{
    public function __construct()
    {

    }

    public function salvar($request)
    {
       return Veiculo::create($request->all());
    }
}