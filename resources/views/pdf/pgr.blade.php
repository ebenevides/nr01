<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #222; }
    h1 { font-size: 16px; text-align: center; margin-bottom: 4px; }
    h2 { font-size: 13px; border-bottom: 2px solid #333; padding-bottom: 4px; margin-top: 20px; }
    h3 { font-size: 11px; margin-bottom: 4px; }
    table { width: 100%; border-collapse: collapse; margin-top: 8px; font-size: 9px; }
    th { background: #2d4a7a; color: #fff; padding: 5px 6px; text-align: left; }
    td { padding: 4px 6px; border-bottom: 1px solid #ddd; }
    tr:nth-child(even) td { background: #f5f7fa; }
    .badge { display: inline-block; padding: 2px 6px; border-radius: 4px; font-size: 8px; font-weight: bold; }
    .nivel-aceitavel { background: #d1fae5; color: #065f46; }
    .nivel-toleravel { background: #fef3c7; color: #92400e; }
    .nivel-inaceitavel { background: #fee2e2; color: #991b1b; }
    .cover { text-align: center; padding: 60px 0 40px; }
    .cover .subtitle { font-size: 12px; color: #555; }
    .info-box { background: #f0f4ff; border: 1px solid #c7d2fe; padding: 10px 14px; border-radius: 4px; margin: 8px 0; }
    .page-break { page-break-after: always; }
    footer { position: fixed; bottom: 0; width: 100%; font-size: 8px; color: #999; text-align: center; border-top: 1px solid #eee; padding-top: 4px; }
</style>
</head>
<body>

<footer>PGR — {{ $empresa->razao_social }} — Gerado em {{ now()->format('d/m/Y H:i') }} — Confidencial</footer>

<!-- Capa -->
<div class="cover">
    <h1>PROGRAMA DE GERENCIAMENTO DE RISCOS</h1>
    <p class="subtitle">NR-01 — Portaria MTE nº 1.419/2024</p>
    <br><br>
    <p><strong>{{ $empresa->razao_social }}</strong></p>
    <p>CNPJ: {{ $empresa->cnpj }}</p>
    @if($empresa->cnae)<p>CNAE: {{ $empresa->cnae }}</p>@endif
    <p>Grau de Risco: {{ $empresa->grau_risco }}</p>
    @if($empresa->responsavel_tecnico)<p>Responsável Técnico: {{ $empresa->responsavel_tecnico }}</p>@endif
    <br>
    <p>Versão gerada em: {{ now()->format('d/m/Y') }}</p>
</div>
<div class="page-break"></div>

<!-- Inventário por estabelecimento/GHE -->
<h2>1. Inventário de Riscos Ocupacionais</h2>

@foreach($empresa->estabelecimentos as $est)
<h3>Estabelecimento: {{ $est->nome }}{{ $est->cidade ? ' — ' . $est->cidade . '/' . $est->uf : '' }}</h3>

@foreach($est->ghes as $ghe)
<p><strong>GHE:</strong> {{ $ghe->nome }} | Trabalhadores expostos: {{ $ghe->num_trabalhadores }}</p>

@foreach($ghe->inventariosRisco as $inv)
<div class="info-box">
    Data avaliação: {{ $inv->data_avaliacao?->format('d/m/Y') }} | Responsável: {{ $inv->responsavel }} | Status: {{ $inv->status?->label() }}
</div>

@if($inv->perigos->count() > 0)
<table>
    <thead>
        <tr>
            <th>Grupo</th>
            <th>Perigo</th>
            <th>Fonte Geradora</th>
            <th>P</th>
            <th>S</th>
            <th>Nível</th>
            <th>Expostos</th>
            <th>Medidas Existentes</th>
        </tr>
    </thead>
    <tbody>
        @foreach($inv->perigos as $p)
        @php
            $nivelClass = $p->nivel_risco <= 2 ? 'aceitavel' : ($p->nivel_risco <= 4 ? 'toleravel' : 'inaceitavel');
            $nivelLabel = $p->nivel_risco <= 2 ? 'Aceitável' : ($p->nivel_risco <= 4 ? 'Tolerável' : 'Inaceitável');
        @endphp
        <tr>
            <td>{{ $p->grupo_risco?->label() }}</td>
            <td>{{ $p->descricao_perigo }}</td>
            <td>{{ $p->fonte_geradora ?? '—' }}</td>
            <td>{{ $p->probabilidade }}</td>
            <td>{{ $p->severidade }}</td>
            <td><span class="badge nivel-{{ $nivelClass }}">{{ $p->nivel_risco }} — {{ $nivelLabel }}</span></td>
            <td>{{ $p->trabalhadores_expostos }}</td>
            <td>{{ $p->medidas_existentes ?? '—' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@else
<p style="color:#888; font-style:italic;">Nenhum perigo registrado neste inventário.</p>
@endif

@endforeach
@endforeach
@endforeach

<!-- Planos de Ação -->
<div class="page-break"></div>
<h2>2. Planos de Ação</h2>

@foreach($empresa->estabelecimentos as $est)
@foreach($est->ghes as $ghe)
@foreach($ghe->inventariosRisco as $inv)
@if(isset($inv->planoAcao) && $inv->planoAcao->count() > 0)
<h3>GHE: {{ $ghe->nome }}</h3>
<table>
    <thead>
        <tr>
            <th>Ação</th>
            <th>Tipo Controle</th>
            <th>Responsável</th>
            <th>Prazo</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($inv->planoAcao as $acao)
        <tr>
            <td>{{ $acao->descricao_acao }}</td>
            <td>{{ $acao->tipo_controle?->label() }}</td>
            <td>{{ $acao->responsavel }}</td>
            <td>{{ $acao->prazo?->format('d/m/Y') }}</td>
            <td>{{ $acao->status?->label() }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif
@endforeach
@endforeach
@endforeach

</body>
</html>
