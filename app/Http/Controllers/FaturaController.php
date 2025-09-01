<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class FaturaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customerCpfCnpj = auth()->user()->empresa->cpf_cnpj;
        $response = Http::get('https://financeiro.f-softsistemas.com.br/api/customer/' . $customerCpfCnpj);
        $body = json_decode($response->body());
        return view('faturas.signature', ['signature' => $body]);
    }

    public function paymentHistory()
    {

        $customerCpfCnpj = auth()->user()->empresa->cpf_cnpj;
        $response = Http::get('https://financeiro.f-softsistemas.com.br/api/customer/' . $customerCpfCnpj . '/payment-history');
        $body = json_decode($response->body());
        return view('faturas.payment-history', ['response' => $body]);

    }

    public function paymentMethods()
    {
        return view('faturas.payment-methods');
    }

}
