<?php

namespace App\Services;

use App\Models\Notificacao;
use App\Models\NotificacaoLeitura;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class NotificacaoService
{
    public function enviar($titulo, $mensagem, $tipo, $empresaId, $criadoPor): Notificacao
    {
        return DB::transaction(function () use ($titulo, $mensagem, $tipo, $empresaId, $criadoPor) {
            $notificacao = Notificacao::create([
                'titulo' => $titulo,
                'mensagem' => $mensagem,
                'tipo' => $tipo,
                'empresa_id' => $empresaId,
                'criado_por' => $criadoPor,
            ]);

            $usuarios = User::where('empresa_id', '!=', 1)
                ->when($empresaId, fn ($query) => $query->where('empresa_id', $empresaId))
                ->pluck('id');

            $agora = now();
            $leituras = $usuarios->map(fn ($userId) => [
                'notificacao_id' => $notificacao->id,
                'user_id' => $userId,
                'lida_em' => null,
                'created_at' => $agora,
                'updated_at' => $agora,
            ])->all();

            if (!empty($leituras)) {
                NotificacaoLeitura::insert($leituras);
            }

            return $notificacao;
        });
    }

    public function listarEnviadas(array $filtros = [])
    {
        $query = Notificacao::with(['empresa', 'criador'])
            ->withCount([
                'leituras',
                'leituras as lidas_count' => fn ($query) => $query->whereNotNull('lida_em'),
            ]);

        if (!empty($filtros['data_inicio']) && !empty($filtros['data_fim'])) {
            $query->whereBetween('created_at', [$filtros['data_inicio'], $filtros['data_fim']]);
        }

        return $query->orderByDesc('created_at')->get();
    }

    public function paraUsuario($userId, bool $apenasNaoLidas = false, ?int $limit = null)
    {
        $query = NotificacaoLeitura::where('user_id', $userId)
            ->with('notificacao')
            ->orderByDesc('created_at');

        if ($apenasNaoLidas) {
            $query->whereNull('lida_em');
        }

        if ($limit) {
            return $query->limit($limit)->get();
        }

        return $query->paginate(15);
    }

    public function contarNaoLidas($userId): int
    {
        return NotificacaoLeitura::where('user_id', $userId)->whereNull('lida_em')->count();
    }

    public function marcarComoLida($leituraId, $userId): void
    {
        NotificacaoLeitura::where('id', $leituraId)
            ->where('user_id', $userId)
            ->whereNull('lida_em')
            ->update(['lida_em' => now()]);
    }

    public function marcarTodasComoLidas($userId): void
    {
        NotificacaoLeitura::where('user_id', $userId)
            ->whereNull('lida_em')
            ->update(['lida_em' => now()]);
    }
}
