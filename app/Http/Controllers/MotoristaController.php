<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;

class MotoristaController extends Controller
{

    public function __construct()
    {

    }

    public function index()
    {
        try {
            return view('motoristas.index');
        } catch (Exception $e) {
            return back()->with('Ocorreu um erro inesperado, tente novamente em outro momento! Erro: ' . $e);
        }
    }

}
