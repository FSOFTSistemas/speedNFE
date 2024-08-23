<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FaturaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('faturas.signature');
    }

    public function paymentHistory()
    {
        return view('faturas.payment-history');
    }

    public function paymentMethods()
    {
        return view('faturas.payment-methods');
    }

}
