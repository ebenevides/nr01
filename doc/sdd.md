# SDD — Sistema de Conformidade NR-01

## 1. Visão Geral

Sistema web para gestão de conformidade com a NR-01 (Gerenciamento de Riscos Ocupacionais). Permite que empresas elaborem, mantenham e evidenciem o PGR (Programa de Gerenciamento de Riscos) conforme exigências do MTE.

**Stack:** Laravel 12 · Vue 3 + Inertia.js · PostgreSQL (RLS) · Redis · S3

---

## 2. Domínio

### Entidades principais

| Entidade | Descrição |
|---|---|
| `Empresa` | Tenant raiz. Dados CNPJ, CNAE, grau de risco |
| `Estabelecimento` | Unidade física da empresa (pode ter vários) |
| `GHE` | Grupo Homogêneo de Exposição |
| `InventarioRisco` | Levantamento de perigos por GHE (etapa 1 PGR) |
| `PlanoAcao` | Medidas de controle vinculadas ao inventário |
| `Evidencia` | Arquivos (S3) comprovando execução de ações |
| `Treinamento` | Registro de capacitações dos trabalhadores |
| `Documento` | PGR, LTCAT e demais documentos legais gerados |
| `Usuario` | Acesso ao sistema (pertence a 1+ empresas) |

### Ciclo PGR

```
Identificar Perigos → Avaliar Riscos → Definir Controles → Executar → Monitorar → Revisar
```

---

## 3. Arquitetura

### Multi-tenancy

Isolamento via **PostgreSQL RLS**. Cada tabela principal tem `empresa_id`. Policy garante que queries só retornam dados do tenant ativo.

```sql
-- Exemplo de policy
CREATE POLICY tenant_isolation ON inventarios_risco
  USING (empresa_id = current_setting('app.tenant_id')::uuid);
```

`app.tenant_id` setado no boot do request via middleware Laravel.

### Camadas (Laravel)

```
Http/Controllers      → resposta HTTP, sem lógica de domínio
Http/Middleware       → auth, tenant, RLS set
Actions/              → casos de uso (1 classe = 1 ação)
Models/               → Eloquent + scopes
Services/             → integrações externas (S3, PDF)
Jobs/                 → tarefas assíncronas (Queue/Redis)
```

### Frontend (Vue 3 + Inertia)

- Inertia sem API separada — controllers retornam `Inertia::render()`
- Composables em `resources/js/composables/`
- Páginas em `resources/js/Pages/`
- Componentes compartilhados em `resources/js/Components/`

### Armazenamento

| Dado | Onde |
|---|---|
| Evidências / documentos PDF | S3 |
| Sessões | Redis |
| Cache de queries pesadas | Redis |
| Jobs queue | Redis |

---

## 4. Módulos

### 4.1 Inventário de Riscos
- CRUD de perigos por GHE
- Classificação por grupo de risco (físico, químico, biológico, ergonômico, acidente)
- Avaliação de probabilidade × severidade → nível de risco
- Exportação para PDF (LTCAT / PGR)

### 4.2 Plano de Ação
- Criação de ações corretivas/preventivas vinculadas ao inventário
- Responsável, prazo, status (pendente / em andamento / concluído)
- Upload de evidências → S3

### 4.3 Treinamentos
- Registro de capacitações por trabalhador
- Controle de validade e renovação
- Lista de presença (upload PDF → S3)

### 4.4 Documentos Legais
- Geração de PGR e LTCAT em PDF
- Versionamento de documentos
- Assinatura digital (futuro)

### 4.5 Dashboard
- Indicadores: % conformidade, ações vencidas, treinamentos a vencer
- Gráficos por estabelecimento

---

## 5. Segurança

- Auth via Laravel Sanctum (sessions web)
- Roles: `admin_empresa` · `tecnico_seguranca` · `gestor` · `visualizador`
- RLS no PostgreSQL como segunda camada de isolamento
- Uploads validados por MIME + tamanho antes de enviar ao S3
- Logs de auditoria em tabela `audit_logs` (quem fez o quê, quando)

---

## 6. Infra

```
┌─────────────────────────────────┐
│  Nginx (reverse proxy)          │
├─────────────────────────────────┤
│  PHP-FPM + Laravel 12           │
├──────────────┬──────────────────┤
│  PostgreSQL  │  Redis           │
│  (RLS)       │  (cache/queue)   │
└──────────────┴──────────────────┘
                      │
                   S3 Bucket
                (evidencias, docs)
```

---

## 7. Etapas de Desenvolvimento

- [x] **Etapa 1** — Setup: Laravel 13, Vue 3, Inertia, PostgreSQL, autenticação (Breeze)
- [x] **Etapa 2** — Multi-tenancy: RLS policies, middleware `SetTenant`, trait `BelongsToTenant`
- [x] **Etapa 3** — Cadastros base: Empresa, Estabelecimento, GHE, FK users→empresas
- [x] **Etapa 4** — Inventário de Riscos: CRUD perigos, matriz P×S, nível calculado (stored column)
- [x] **Etapa 5** — Plano de Ação + Evidências: upload S3, `EvidenciaService`, status workflow
- [x] **Etapa 6** — Treinamentos: registro participantes, validade automática, alerta 30 dias
- [x] **Etapa 7** — Geração PDF assíncrona (PGR/LTCAT): DomPDF, job `GerarDocumentoJob`, S3
- [x] **Etapa 8** — Dashboard: 4 KPIs + Chart.js (doughnut + bar), links rápidos
- [x] **Etapa 9** — Auditoria: trait `Auditable`, `audit_logs` com jsonb diff, nav completo

---

## 8. Pendências / Decisões Abertas

- [x] Cadastro de Empresa: EmpresaController + Index/Form/Show.vue + rota resource + middleware role:admin_empresa
- [x] Seed inicial: DatabaseSeeder com Empresa demo, 2 users (admin/tecnico), Estabelecimento, 2 GHEs
- [x] Role middleware: CheckRole registrado como alias `role` — uso: `->middleware('role:admin_empresa')`
- [x] Permissões granulares: policies nativas Laravel (InventarioRiscoPolicy, PlanoAcaoPolicy, TreinamentoPolicy, DocumentoPolicy) + TenantPolicy trait + User#canWrite() + authorize() em todos os controllers
- [ ] Assinatura digital dos documentos: integração com gov.br ou certificado A1/A3
- [ ] S3 região confirmada: `sa-east-1` já no `.env.example` (LGPD)
