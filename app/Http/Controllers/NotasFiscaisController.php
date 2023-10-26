<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Services\UsersService;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use NFePHP\DA\NFe\Danfe;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ZipArchive;

class NotasFiscaisController extends Controller
{
    public function show(){
        $users = new UsersService();
        $empresa = $users->getEmpresa(Auth::id());

        $fullPath = public_path();
        array_map('unlink', glob("$fullPath/*.zip"));

        if ($empresa->empresa_id == 1){
            $empresa = '%';
        } else {
            $empresa = $empresa->empresa_id;
        }

        $notas = DB::table('pedidos')
        ->where('empresa_id', 'like', $empresa)
        ->where('chave', '!=', '')
        ->whereRaw('MONTH(created_at) = MONTH(CURRENT_DATE)')
        ->whereRaw('YEAR(created_at) = YEAR(CURRENT_DATE)')
        ->simplePaginate(10);

        return view('notas.todos', ['notas' => $notas]);
    }

    public function visualizarPdf($chave){
        try{
            $venda = Pedido::get()->where('chave', '=', $chave);
            $empresa = Empresa::findOrFail($venda->empresa_id);
            return $empresa;

            $xml = file_get_contents(public_path($empresa->fantasia.'/'.date_format($venda->created_at, 'Y').'/'.date_format($venda->created_at, 'm').'/notas/Autorizadas/').$venda->chave.'.xml');
            return $xml;
			$danfe = new Danfe($xml);
			$pdf = $danfe->render();
			return response($pdf)
			->header('Content-Type', 'application/pdf');
		}catch(\Exception $e){
			session()->flash("erro", $e->getMessage());
			return redirect()->back();
		}
    }

    public function downloadXml($chave){
        try{
			$xml = public_path('xml_nfe/').$chave.'.xml';
			return response()->download($xml);
		}catch(\Exception $e){
			session()->flash("erro", $e->getMessage());
			return redirect()->back();
		}
    }

    public function zip(Request $request){
        $rootPath = realpath('FSOFT SISTEMAS/'.DateTime::createFromFormat('Y-m', $request->periodo)->format('Y').'/'.DateTime::createFromFormat('Y-m', $request->periodo)->format('m'));

        if($rootPath){

            $zip = new ZipArchive;
            $zip->open('FSOFT SISTEMAS '.DateTime::createFromFormat('Y-m', $request->periodo)->format('m').'-'.DateTime::createFromFormat('Y-m', $request->periodo)->format('Y').'.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);

            /** @var SplFileInfo[] $files */
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($rootPath),
                RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $name => $file){
                if(!$file->isDir()){
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($rootPath)+1);

                    $zip->addFile($filePath, $relativePath);
                }
            }

            $zip->close();

            return response()->download(public_path('FSOFT SISTEMAS '.DateTime::createFromFormat('Y-m', $request->periodo)->format('m').'-'.DateTime::createFromFormat('Y-m', $request->periodo)->format('Y').'.zip'));
        }
        return redirect('/notas')->with('alert', 'Não foram encontradas notas para o período solicitado.');
    }
}
