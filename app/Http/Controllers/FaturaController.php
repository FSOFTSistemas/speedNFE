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
        $history = Http::get('https://financeiro.f-softsistemas.com.br/api/customer/' . $customerCpfCnpj . '/payment-history');
        $signature = json_decode($response->body()) ?: new \stdClass();
        $paymentsObj  = json_decode($history->body());
        $paymentsArr  = is_array($paymentsObj) ? $paymentsObj : ($paymentsObj->data ?? []);
        // compat: algumas views esperam $signature->payments
        $signature->payments = $paymentsArr;

        return view('faturas.signature', [
            'signature' => $signature,
            'payments'  => $paymentsArr,
        ]);
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
