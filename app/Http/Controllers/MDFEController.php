<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;

class MDFEController extends Controller
{

    public function __construct()
    {

    }

    public function index()
    {
        try {
            return view('mdfes.index');
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: ' . $e->getMessage());
        }
    }

    public function create()
    {
        try {
            return view('mdfes.create');
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: ' . $e->getMessage());
        }
    }

    public function edit($mdfeId)
    {
        try {
            return view('mdfes.edit');
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: ' . $e->getMessage());
        }
    }

    public function downloadXML()
    {
        try {
            return view('mdfes.download-xml');
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: ' . $e->getMessage());
        }
    }

}
