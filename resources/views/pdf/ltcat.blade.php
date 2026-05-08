<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #222; }
    h1 { font-size: 16px; text-align: center; }
    h2 { font-size: 13px; border-bottom: 2px solid #333; padding-bottom: 4px; margin-top: 20px; }
    h3 { font-size: 11px; }
    table { width: 100%; border-collapse: collapse; margin-top: 8px; font-size: 9px; }
    th { background: #374151; color: #fff; padding: 5px 6px; text-align: left; }
    td { padding: 4px 6px; border-bottom: 1px solid #ddd; vertical-align: top; }
    tr:nth-child(even) td { background: #f9fafb; }
    .cover { text-align: center; padding: 60px 0 40px; }
    .info-box { background: #f3f4f6; border-left: 3px solid #6b7280; padding: 8px 12px; margin: 8px 0; }
    .page-break { page-break-after: always; }
    .conclusao { background: #eff6ff; border: 1px solid #bfdbfe; padding: 12px 16px; margin-top: 16px; }
    footer { position: fixed; bottom: 0; width: 100%; font-size: 8px; color: #999; text-align: center; border-top: 1px solid #eee; }
</style>
</head>
<body>

<footer>LTCAT — {{ $empresa->razao_social }} — Gerado em {{ now()->format('d/m/Y H:i') }} — Confidencial</footer>

<!-- Capa -->
<div class="cover">
    <h1>LAUDO TÉCNICO DAS CONDIÇÕES AMBIENTAIS DO TRABALHO</h1>
    <p>LTCAT — Lei nº 9.032/1995 | Decreto nº 3.048/1999</p>
    <br><br>
    <p><strong>{{ $empresa->razao_social }}</strong></p>
    <p>CNPJ: {{ $empresa->cnpj }}</p>
    @if($empresa->cnae)<p>CNAE: {{ $empresa->cnae }}</p>@endif
    <p>Grau de Risco: {{ $empresa->grau_risco }}</p>
    @if($empresa->responsavel_tecnico)<p>Responsável Técnico: {{ $empresa->responsavel_tecnico }}</p>@endif
    <br>
    <p>Data: {{ now()->format('d/m/Y') }}</p>
</div>
<div class="page-break"></div>

<!-- Identificação dos estabelecimentos -->
<h2>1. Identificação dos Estabelecimentos</h2>
@foreach($empresa->estabelecimentos as $est)
<div class="info-box">
    <strong>{{ $est->nome }}</strong>
    @if($est->cnpj) | CNPJ: {{ $est->cnpj }} @endif<br>
    @if($est->logradouro){{ $est->logradouro }}, {{ $est->numero }}{{ $est->complemento ? ' — ' . $est->complemento : '' }}, {{ $est->bairro }} — {{ $est->cidade }}/{{ $est->uf }}@endif
</div>
@endforeach

<!-- Avaliação ambiental por GHE -->
<div class="page-break"></div>
<h2>2. Avaliação das Condições Ambientais por GHE</h2>

@foreach($empresa->estabelecimentos as $est)
<h3>Estabelecimento: {{ $est->nome }}</h3>

@foreach($est->ghes as $ghe)
<p><strong>GHE: {{ $ghe->nome }}</strong> | Trabalhadores: {{ $ghe->num_trabalhadores }}</p>
@if($ghe->descricao)<p style="color:#555">{{ $ghe->descricao }}</p>@endif

@foreach($ghe->inventariosRisco as $inv)
@php
    $perigosLtcat = $inv->perigos->whereIn('grupo_risco.value', ['fisico', 'quimico', 'biologico']);
@endphp

@if($inv->perigos->count() > 0)
<table>
    <thead>
        <tr>
            <th>Agente</th>
            <th>Descrição</th>
            <th>Fonte</th>
            <th>Expostos</th>
            <th>Intensidade/Concentração</th>
            <th>Conclusão</th>
        </tr>
    </thead>
    <tbody>
        @foreach($inv->perigos as $p)
        @php
            $nivel = $p->nivel_risco <= 2 ? 'Não nocivo' : ($p->nivel_risco <= 4 ? 'Atenção' : 'Nocivo / Periculoso');
        @endphp
        <tr>
            <td>{{ $p->grupo_risco?->label() }}</td>
            <td>{{ $p->descricao_perigo }}</td>
            <td>{{ $p->fonte_geradora ?? '—' }}</td>
            <td>{{ $p->trabalhadores_expostos }}</td>
            <td>—</td>
            <td>{{ $nivel }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@else
<p style="color:#888; font-style:italic; font-size:9px;">Sem agentes avaliados neste GHE.</p>
@endif
@endforeach

@endforeach
@endforeach

<!-- Conclusão -->
<div class="page-break"></div>
<h2>3. Conclusão Técnica</h2>
<div class="conclusao">
    <p>Com base no levantamento realizado, os Grupos Homogêneos de Exposição (GHEs) da empresa
    <strong>{{ $empresa->razao_social }}</strong> foram avaliados quanto à presença de agentes físicos,
    químicos e biológicos, em conformidade com a NR-01 (Portaria MTE nº 1.419/2024) e legislação previdenciária vigente.</p>
    <br>
    <p>Os resultados constam nas tabelas de avaliação ambiental deste laudo e deverão ser considerados
    para fins de enquadramento de Aposentadoria Especial (Lei nº 9.032/1995) e elaboração do PPP.</p>
    <br>
    <p>Este LTCAT tem validade até a ocorrência de mudanças nos processos produtivos, equipamentos,
    instalações ou agentes de risco que alterem as condições avaliadas.</p>
</div>

<br>
<p>____________________________________</p>
<p>{{ $empresa->responsavel_tecnico ?? '________________________________' }}</p>
<p>Responsável Técnico</p>

</body>
</html>
