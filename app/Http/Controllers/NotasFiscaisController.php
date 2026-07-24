<?php

namespace App\Http\Controllers;

use App\Mail\EmailXmlContador;
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

class NotasFiscaisController extends Controller
{

    private UsersService $userServices;
    private PedidosService $pedidoServices;

    public function __construct(UsersService $userServices, PedidosService $pedidoServices)
    {
        $this->userServices = $userServices;
        $this->pedidoServices = $pedidoServices;
    }

    public function show(Request $request)
    {
        try {
            $empresa = $this->userServices->getEmpresa(Auth::id());
            $fullPath = public_path();
            array_map('unlink', glob("$fullPath/*.zip"));

            $dataInicio = $request->filled('data_inicio')
                ? $request->input('data_inicio')
                : now()->subMonths(2)->startOfDay()->format('Y-m-d');
            $dataFim = $request->filled('data_fim')
                ? $request->input('data_fim')
                : now()->endOfDay()->format('Y-m-d');

            $filtros = [
                'data_inicio' => $dataInicio,
                'data_fim' => $dataFim,
                'cliente' => $request->input('cliente'),
                'chassi' => $request->input('chassi'),
                'estado' => $request->input('estado'),
            ];

            $notas = $this->pedidoServices->buscarPedidos($empresa->empresa_id, $filtros);

            return view('notas.todos', [
                'notas' => $notas,
                'empresa' => Auth::user()->empresa_id,
                'filtros' => $filtros,
            ]);
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: ' . $e->getMessage());
        }
    }

    public function visualizarPdf($chave)
    {
        try {
            $venda = Pedido::where('chave', $chave)->firstOrFail();
            $xmlRow = $venda->xmlAutorizado;
            if (!$xmlRow) {
                session()->flash("erro", 'XML da nota não encontrado.');
                return redirect()->back();
            }
            $danfe = new Danfe($xmlRow->xml);
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
            $venda = Pedido::findOrFail($request->pedido_id);

            $xmlRow = $venda->xmlAutorizado ?? $venda->xmlCancelado ?? $venda->xmlCce;
            if (!$xmlRow) {
                return back()->with('error', 'XML não encontrado para esta nota.');
            }

            return response($xmlRow->xml, 200, [
                'Content-Type' => 'text/xml',
                'Content-Disposition' => 'attachment; filename="' . $venda->chave . '.xml"',
            ]);
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: ' . $e->getMessage());
        }
    }

    public function zip(Request $request)
    {
        $company = Auth::user()->empresa;
        $data = DateTime::createFromFormat('Y-m', $request->periodo);
        $ano = $data->format('Y');
        $mes = $data->format('m');

        $pedidos = Pedido::where('empresa_id', $company->id)
            ->where('chave', '!=', '')
            ->whereYear('data', $ano)
            ->whereMonth('data', $mes)
            ->with(['xmlAutorizado', 'xmlCancelado', 'xmlCce'])
            ->get();

        $archives = collect();
        foreach ($pedidos as $pedido) {
            if ($pedido->xmlAutorizado) {
                $archives->push((object) ['chave' => $pedido->chave, 'xml' => $pedido->xmlAutorizado->xml]);
            }
            if ($pedido->xmlCancelado) {
                $archives->push((object) ['chave' => $pedido->chave . '-cancelado', 'xml' => $pedido->xmlCancelado->xml]);
            }
            if ($pedido->xmlCce) {
                $archives->push((object) ['chave' => $pedido->chave . '-cce', 'xml' => $pedido->xmlCce->xml]);
            }
        }

        if ($archives->isEmpty()) {
            return redirect('/notas')->with('alert', 'Não foram encontradas notas para o período solicitado.');
        }

        $zipPath = ZipArchiveUtil::zip($archives, $company->id);
        if (!$zipPath) {
            return redirect('/notas')->with('alert', 'Não foi possível gerar o arquivo ZIP.');
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

public function enviarXmlsContador(Request $request)
{
    try {
        $request->validate([
            'month' => 'required',
            'accountant' => 'required|email'
        ]);

        $company = Auth::user()->empresa;
        $ano = date('Y', strtotime($request->month));
        $mes = date('m', strtotime($request->month));

        $pedidos = Pedido::where('empresa_id', $company->id)
            ->where('chave', '!=', '')
            ->whereYear('data', $ano)
            ->whereMonth('data', $mes)
            ->with('xmlAutorizado')
            ->get()
            ->filter(fn ($p) => $p->xmlAutorizado);

        if ($pedidos->isEmpty()) {
            return back()->with('warning', "Nenhuma nota autorizada encontrada para o período {$mes}/{$ano}.");
        }

        $archives = $pedidos->map(fn ($p) => (object) [
            'chave' => $p->chave,
            'xml' => $p->xmlAutorizado->xml,
        ]);

        $zipPath = ZipArchiveUtil::zip($archives, $company->id);
        if (!$zipPath) {
            return back()->with('warning', 'Não foi possível gerar o arquivo ZIP.');
        }

        Mail::to($request->accountant)
            ->send(new EmailXmlContador($company, $zipPath, $request->month));

        ZipArchiveUtil::deleteArchive($zipPath);

        return back()->with('success', 'E-mail enviado com sucesso para: ' . $request->accountant);

    } catch (\Exception $e) {
        return back()->with('error', 'Erro ao processar: ' . $e->getMessage());
    }
}
}
