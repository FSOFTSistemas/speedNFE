<?php

namespace App\Http\Controllers;

use App\Services\EmpresasService;
use App\Services\NotificacaoService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificacaoController extends Controller
{
    private NotificacaoService $notificacaoService;
    private EmpresasService $empresaServices;

    public function __construct(NotificacaoService $notificacaoService, EmpresasService $empresaServices)
    {
        $this->notificacaoService = $notificacaoService;
        $this->empresaServices = $empresaServices;
    }

    public function index(Request $request)
    {
        $notificacoes = $this->notificacaoService->listarEnviadas([
            'data_inicio' => $request->get('data_inicio'),
            'data_fim' => $request->get('data_fim'),
        ]);
        $empresas = $this->empresaServices->todas();

        return view('notificacoes.central', compact('notificacoes', 'empresas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'mensagem' => 'required|string',
            'tipo' => 'required|in:info,aviso,urgente',
            'destino' => 'required|in:todas,empresa',
            'empresa_id' => 'required_if:destino,empresa|nullable|exists:empresas,id',
        ], [
            'required' => 'O campo :attribute é obrigatório!',
            'required_if' => 'Selecione a empresa de destino.',
        ]);

        try {
            $empresaId = $request->destino === 'empresa' ? $request->empresa_id : null;

            $this->notificacaoService->enviar(
                $request->titulo,
                $request->mensagem,
                $request->tipo,
                $empresaId,
                Auth::id()
            );

            return redirect()->route('notificacoes.index')->with('success', 'Notificação enviada com sucesso!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Erro ao enviar notificação: ' . $e->getMessage());
        }
    }

    public function minhas()
    {
        $leituras = $this->notificacaoService->paraUsuario(Auth::id());

        return view('notificacoes.minhas', compact('leituras'));
    }

    public function widget()
    {
        $leituras = $this->notificacaoService->paraUsuario(Auth::id(), true, 5);
        $naoLidas = $this->notificacaoService->contarNaoLidas(Auth::id());

        return response()->json([
            'label' => $naoLidas > 0 ? (string) $naoLidas : null,
            'label_color' => 'danger',
            'dropdown' => view('notificacoes._dropdown', compact('leituras'))->render(),
        ]);
    }

    public function marcarLida(Request $request, $id)
    {
        $this->notificacaoService->marcarComoLida($id, Auth::id());

        return back();
    }

    public function marcarTodasLidas(Request $request)
    {
        $this->notificacaoService->marcarTodasComoLidas(Auth::id());

        return back();
    }
}
