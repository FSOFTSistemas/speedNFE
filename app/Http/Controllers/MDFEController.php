<?php

namespace App\Http\Controllers;

use App\Services\MDFeService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MDFEController extends Controller
{

    private MDFeService $MDFeService;

    public function __construct(MDFeService $MDFeService)
    {
        $this->MDFeService = $MDFeService;
    }

    public function index()
    {
        try {
            $MDFes = $this->MDFeService->buscarMDFes(Auth::user()->empresa_id);
            return view('mdfes.index', ['mdfes' => $MDFes]);
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
