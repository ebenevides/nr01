<?php

namespace App\Http\Controllers;

use App\Models\InventarioRisco;
use App\Models\ParticipanteTreinamento;
use App\Models\Perigo;
use App\Models\PlanoAcao;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $empresaId = Auth::user()->empresa_id;

        $totalPerigos = Perigo::count();

        $perigosPorNivel = Perigo::selectRaw("
            CASE
                WHEN nivel_risco <= 2 THEN 'Aceitável'
                WHEN nivel_risco <= 4 THEN 'Tolerável'
                ELSE 'Inaceitável'
            END as nivel,
            COUNT(*) as total
        ")->groupByRaw('nivel')->pluck('total', 'nivel');

        $acoesVencidas = PlanoAcao::whereNotIn('status', ['concluido', 'cancelado'])
            ->where('prazo', '<', now())
            ->count();

        $acoesPorStatus = PlanoAcao::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $inventariosPorStatus = InventarioRisco::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $treinamentosVencendo = ParticipanteTreinamento::whereBetween('valido_ate', [now(), now()->addDays(30)])
            ->count();

        $treinamentosVencidos = ParticipanteTreinamento::where('valido_ate', '<', now())
            ->count();

        $totalAcoes = PlanoAcao::count();
        $acoesConcluidas = PlanoAcao::where('status', 'concluido')->count();
        $percentualConformidade = $totalAcoes > 0
            ? round(($acoesConcluidas / $totalAcoes) * 100)
            : 0;

        $acoesPorControle = PlanoAcao::selectRaw('tipo_controle, COUNT(*) as total')
            ->groupBy('tipo_controle')
            ->pluck('total', 'tipo_controle');

        return Inertia::render('Dashboard', [
            'metricas' => [
                'total_perigos' => $totalPerigos,
                'acoes_vencidas' => $acoesVencidas,
                'treinamentos_vencendo' => $treinamentosVencendo,
                'treinamentos_vencidos' => $treinamentosVencidos,
                'percentual_conformidade' => $percentualConformidade,
                'total_acoes' => $totalAcoes,
                'acoes_concluidas' => $acoesConcluidas,
            ],
            'graficos' => [
                'perigos_por_nivel' => $perigosPorNivel,
                'acoes_por_status' => $acoesPorStatus,
                'inventarios_por_status' => $inventariosPorStatus,
                'acoes_por_controle' => $acoesPorControle,
            ],
        ]);
    }
}
