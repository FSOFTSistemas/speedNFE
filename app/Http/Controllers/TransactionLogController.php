<?php

namespace App\Http\Controllers;

use App\Models\TransactionLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionLogController extends Controller
{
    public function index()
    {
        $id = Auth::user()->empresa_id;
        $logs = TransactionLog::whereHas('usuario', function ($query) use ($id) {
            $query->where('empresa_id', $id);
        })->with('usuario')->get();
    
        return view('log', compact('logs'));
    }
}
