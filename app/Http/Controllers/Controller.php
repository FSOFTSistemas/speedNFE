<?php

namespace App\Http\Controllers;

use App\Utils\TextoUtil;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use RealRashid\SweetAlert\Facades\Alert;

class Controller extends BaseController
{
    public function __construct()
    {
        $this->middleware(function($request, $next){
            if (session('success')) {
                Alert::success(TextoUtil::utf8Seguro(session('success')));
            }

            if (session('error')) {
                Alert::error(TextoUtil::utf8Seguro(session('error')));
            }

            // if(session('alert')) {
            //     Alert::warning(session('alert'));
            // }

            return $next($request);
        });

    }

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
