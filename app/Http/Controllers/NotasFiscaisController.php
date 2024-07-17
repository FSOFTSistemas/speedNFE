<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Pedido;
use App\Services\PedidosService;
use App\Services\UsersService;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use NFePHP\DA\NFe\Danfe;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ZipArchive;

class NotasFiscaisController extends Controller
{

    private UsersService $userServices;
    private PedidosService $pedidoServices;

    public function __construct(UsersService $userServices, PedidosService $pedidoServices)
    {
        $this->userServices = $userServices;
        $this->pedidoServices = $pedidoServices;
    }

    public function show()
    {
        try {
            $empresa = $this->userServices->getEmpresa(Auth::id());
            $fullPath = public_path();
            array_map('unlink', glob("$fullPath/*.zip"));
            $notas = $this->pedidoServices->buscarPedidos($empresa->empresa_id);
            return view('notas.todos', ['notas' => $notas, 'empresa' => Auth::user()->empresa_id]);
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: ' . $e->getMessage());
        }
    }

    public function visualizarPdf($chave)
    {
        try {
            $venda = Pedido::get()->where('chave', '=', $chave);
            $empresa = Empresa::findOrFail($venda->empresa_id);
            return $empresa;

            $xml = file_get_contents(public_path($empresa->fantasia . '/' . date_format($venda->created_at, 'Y') . '/' . date_format($venda->created_at, 'm') . '/notas/Autorizadas/') . $venda->chave . '.xml');
            return $xml;
            $danfe = new Danfe($xml);
            $pdf = $danfe->render();
            return response($pdf)
                ->header('Content-Type', 'application/pdf');
        } catch (\Exception $e) {
            session()->flash("erro", $e->getMessage());
            return redirect()->back();
        }
    }

    public function downloadXml(Request $request)
    {
        try {
            $estado = $request->estado == 'Autorizado' ? 'Autorizadas' : ($request->estado == 'Cancelado' ? 'Canceladas' : 'CCe');
            $xml = asset($request->empresa . '/' . date('Y', strtotime($request->data)) . '/' . date('m', strtotime($request->data)) . '/notas/' . $estado . '/' . $request->chave . '.xml');
            return response()->download($xml);
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: ' . $e->getMessage());
        }
    }

    public function zip(Request $request)
    {
        $company = Auth::user()->empresa;
        $rootPath = realpath($company->razao . '/' . DateTime::createFromFormat('Y-m', $request->periodo)->format('Y') . '/' . DateTime::createFromFormat('Y-m', $request->periodo)->format('m'));

        if ($rootPath) {

            $zip = new ZipArchive;
            $zip->open($company->razao . ' ' . DateTime::createFromFormat('Y-m', $request->periodo)->format('m') . '-' . DateTime::createFromFormat('Y-m', $request->periodo)->format('Y') . '.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);

            /** @var SplFileInfo[] $files */
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($rootPath),
                RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $name => $file) {
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($rootPath) + 1);

                    $zip->addFile($filePath, $relativePath);
                }
            }

            $zip->close();

            return response()->download(public_path($company->fantasia . ' ' . DateTime::createFromFormat('Y-m', $request->periodo)->format('m') . '-' . DateTime::createFromFormat('Y-m', $request->periodo)->format('Y') . '.zip'));
        }
        return redirect('/notas')->with('alert', 'Não foram encontradas notas para o período solicitado.');
    }
}
