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

        // compat: algumas views esperam $signature->payments
        $signature->payments = $paymentsObj;


        $payments = collect($paymentsObj ?? [])
            ->flatten(1)  // remove um nível de array ([[obj],[obj]] → [obj,obj])
            ->filter()    // remove nulls, se existirem
            ->values()    // reindexa de 0,1,2...
            ->all();

            // dd($payments);
        return view('faturas.signature', compact('signature', 'payments', 'customerCpfCnpj'));
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
