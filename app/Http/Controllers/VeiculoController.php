<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;

class VeiculoController extends Controller
{
    public function __construct()
    {

    }

    public function index()
    {
        try {
            return view('veiculos.index');
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: ' . $e->getMessage());
        }
    }

}
