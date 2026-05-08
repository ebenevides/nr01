# SDD — PaaS Pesquisa Psicossocial NR-01

## Objetivo
Plataforma SaaS multi-tenant para aplicação de pesquisas psicossociais conforme NR-01.

## Stack
- Laravel 12
- PostgreSQL (RLS)
- Vue 3 + Tailwind

## Arquitetura
Tenant → Template → Aplicação → Respostas → Scoring → Relatório

## Regras
- Template versionado
- Suporte anonimato
- LGPD compliance

## Scoring
Score = Σ (resposta * peso)

## Relatórios
- Analítico
- Sintético
