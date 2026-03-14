<?php

namespace App\Http\Controllers;

use App\Mail\EmailXmlContador;
use App\Models\Empresa;
use App\Models\Pedido;
use App\Services\PedidosService;
use App\Services\UsersService;
use App\Utils\ZipArchiveUtil;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
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
        $rootPath = realpath($company->fantasia . '/' . DateTime::createFromFormat('Y-m', $request->periodo)->format('Y') . '/' . DateTime::createFromFormat('Y-m', $request->periodo)->format('m'));

        if ($rootPath) {

            $zip = new ZipArchive;
            $zip->open($company->fantasia . ' ' . DateTime::createFromFormat('Y-m', $request->periodo)->format('m') . '-' . DateTime::createFromFormat('Y-m', $request->periodo)->format('Y') . '.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);

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

public function enviarXmlsContador(Request $request)
{
    try {
        $request->validate([
            'month' => 'required',
            'accountant' => 'required|email'
        ]);

        $company = Auth::user()->empresa;
        
        // Monta os caminhos conforme sua estrutura confirmada
        $ano = date('Y', strtotime($request->month));
        $mes = date('m', strtotime($request->month));
        
        // Caminho absoluto para a pasta do mês
        $folderPath = public_path($company->fantasia . '/' . $ano . '/' . $mes);

        if (!file_exists($folderPath)) {
            return back()->with('warning', "A pasta do período {$mes}/{$ano} não foi encontrada no servidor.");
        }

        // Nome do arquivo ZIP temporário
        $zipName = 'XMLs_' . $company->fantasia . '_' . $mes . '_' . $ano . '.zip';
        $zipPath = public_path($zipName);

        $zip = new \ZipArchive;
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
            
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($folderPath),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );

            $hasFiles = false;
            foreach ($files as $name => $file) {
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();
                    $relativePath = basename($filePath);
                    $zip->addFile($filePath, $relativePath);
                    $hasFiles = true;
                }
            }
            $zip->close();

            if (!$hasFiles) {
                unlink($zipPath);
                return back()->with('warning', 'A pasta existe, mas não contém arquivos XML.');
            }
        }

        // ENVIO: O e-mail destino é o $request->accountant (o que você digitou no modal)
        \Illuminate\Support\Facades\Mail::to($request->accountant)
            ->send(new \App\Mail\EmailXmlContador($company, $zipPath, $request->month));

        // Deleta o zip após enviar
        if (file_exists($zipPath)) {
            unlink($zipPath);
        }

        return back()->with('success', 'E-mail enviado com sucesso para: ' . $request->accountant);

    } catch (\Exception $e) {
        return back()->with('error', 'Erro ao processar: ' . $e->getMessage());
    }
}
}
