# 📘 Documento de Requisitos — PaaS Pesquisa Psicossocial (NR-01)

> Stack: **Laravel 12**, **PostgreSQL**, **Vue + shadcn/Tailwind** — Arquitetura **SaaS multi-tenant** (row-level + RLS PostgreSQL).

## 1. Contexto e Objetivo
Plataforma SaaS multi-tenant para criação, aplicação e análise de pesquisas psicossociais (NR-01), com:
- Templates flexíveis (seções, perguntas, opções, pesos).
- Coleta segura (identificada/ anônima) via link/token.
- Relatórios analíticos e sintéticos com enquadramento por risco.
- Governança de dados (LGPD), auditoria e escalabilidade.

## 2. Escopo
- **In Scope:** tenants, usuários e papéis; builder de questionários; aplicação; respostas; scoring; relatórios; auditoria; notificações; planos/assinaturas; painel superadmin.
- **Out of Scope (Fase 1):** app mobile nativo; SSO corporativo; IA preditiva avançada.

## 3. Personas e Papéis
- **Superadmin:** gerencia tenants, planos, limites, billing e incidentes.
- **Admin do Tenant:** configura organização, usuários, papéis, LGPD/termos, planos.
- **Gestor:** cria templates e pesquisas, acompanha dashboards e relatórios.
- **Analista:** analisa resultados, exporta, elabora diagnósticos complementares.
- **Respondente:** responde via link/token (opção anônima).
- **Auditor (opcional):** leitura de logs, relatórios e evidências.

## 4. Requisitos Funcionais (FR)

### 4.1. Multi-Tenancy
- **FR-MT-01**: Isolar dados por `tenant_id` em todas as entidades (row-level).
- **FR-MT-02**: Resolver tenant por **subdomínio** e suportar **domínio customizado**.
- **FR-MT-03**: Prefixo de cache/filas/storage por tenant.
- **FR-MT-04**: Painel **Superadmin** para gestão de tenants/planos/consumo/saúde.

### 4.2. Acesso e Segurança
- **FR-AC-01**: Autenticação com **2FA** (e-mail/app).
- **FR-AC-02**: **RBAC por tenant** (owner/admin/gestor/analista/respondente/auditor).
- **FR-AC-03**: Convite por e-mail com expiração e aceite de termos.
- **FR-AC-04**: Rate limiting por usuário/tenant; bloqueio por tentativas.

### 4.3. Templates e Questionários
- **FR-TQ-01**: CRUD de **Templates** (título, descrição, versão, ativo).
- **FR-TQ-02**: **Seções** com **peso** por seção.
- **FR-TQ-03**: **Perguntas**: `multiple_choice`, `likert`, `text`, `multi_select` (opc.).
- **FR-TQ-04**: **Opções** por pergunta (label, score/peso, ordem).
- **FR-TQ-05**: **Validações**: obrigatoriedade, mínimo/máximo, consistência de pesos.
- **FR-TQ-06**: **Versionamento** (lock após uso; alterações geram nova versão).

### 4.4. Aplicação da Pesquisa
- **FR-AP-01**: Criar **Instância de Pesquisa** de um template.
- **FR-AP-02**: **Assignments** a respondentes internos/externos (e-mail).
- **FR-AP-03**: Geração de **token único** por respondente; opção **anônima**.
- **FR-AP-04**: Janelas **abre/fecha**; lembretes automáticos.
- **FR-AP-05**: **Progresso** (salvar rascunho) e prevenção de duplicidade.

### 4.5. Respostas
- **FR-RS-01**: Capturar respostas (opção marcada, numérico, texto).
- **FR-RS-02**: **Likert** configurável (1–5/1–7) e pesos.
- **FR-RS-03**: Proteção contra reenvio indevido (token ou status).
- **FR-RS-04**: **Anonimização** quando ativada (sem PII).

### 4.6. Scoring, Enquadramento e Diagnóstico
- **FR-SC-01**: Motor de **scoring** com **Strategy** por tipo de pergunta.
- **FR-SC-02**: **Pesos hierárquicos** (seção/pergunta/opção) e normalização.
- **FR-SC-03**: **Thresholds** por template/escopo → **níveis de risco**.
- **FR-SC-04**: **Regras de diagnóstico** (condições → mensagem/recomendação).
- **FR-SC-05**: Persistir **scores** por pergunta, seção e geral; status `scored`.

### 4.7. Relatórios e Dashboards
- **FR-RL-01**: **Analítico** (distribuição por pergunta; gráficos).
- **FR-RL-02**: **Sintético** (índice geral; enquadramento NR-01; recomendações).
- **FR-RL-03**: **Comparativo** entre rodadas do mesmo template.
- **FR-RL-04**: **Filtros** por área/setor/cargo (quando houver metadados).
- **FR-RL-05**: Export **CSV/PDF**; **link assinado** com expiração.
- **FR-RL-06**: Dashboard de progresso e taxas de conclusão.

### 4.8. Auditoria e LGPD
- **FR-AU-01**: **Audit log** (quem/que/quando/onde).
- **FR-AU-02**: Registro de **aceite de termos** e base legal.
- **FR-AU-03**: **Retenção** configurável; export/delete sob solicitação.
- **FR-AU-04**: **Consentimento** explícito (quando aplicável).

### 4.9. Notificações
- **FR-NT-01**: E-mails transacionais (convite, lembrete, encerramento).
- **FR-NT-02**: Templates de e-mail customizáveis por tenant.
- **FR-NT-03**: Webhooks/API para notificações externas (opcional).

### 4.10. Planos e Assinaturas
- **FR-PL-01**: **Planos** com limites (usuários, pesquisas, storage).
- **FR-PL-02**: Cobrança recorrente e status do tenant (ativo/inadimplente).
- **FR-PL-03**: **Feature flags** por plano (módulos/limites/export).

### 4.11. Admin e Operações
- **FR-AD-01**: Painel Superadmin: saúde, filas, jobs, falhas.
- **FR-AD-02**: Reprocessar scoring/relatórios; regenerar PDFs.
- **FR-AD-03**: Export do tenant para offboarding.

## 5. Requisitos Não Funcionais (NFR)

### 5.1. Segurança
- **NFR-SE-01**: **RLS PostgreSQL** + **Global Scopes** (defesa em profundidade).
- **NFR-SE-02**: Criptografia **em trânsito** (TLS 1.2+) e **em repouso** (storage + PII).
- **NFR-SE-03**: OWASP Top 10; CSP; proteção CSRF/XSS/SQLi/SSRF.
- **NFR-SE-04**: 2FA configurável por política do tenant.
- **NFR-SE-05**: Logs segregados e mascaramento de PII.

### 5.2. Privacidade e LGPD
- **NFR-LG-01**: Princípios LGPD (finalidade, minimização, transparência).
- **NFR-LG-02**: Consentimento registrável; atendimento a direitos do titular.
- **NFR-LG-03**: Registro e comunicação de incidentes quando aplicável.

### 5.3. Desempenho e Escalabilidade
- **NFR-DE-01**: p95 API < 400ms (CRUD) e < 800ms (relatórios simples).
- **NFR-DE-02**: 1.000 tenants; 50k respondentes/mês; auto-scaling horizontal.
- **NFR-DE-03**: Índices por `(tenant_id, ...)`, cache e paginação.
- **NFR-DE-04**: Jobs longos assíncronos com DLQ.

### 5.4. Disponibilidade e Continuidade
- **NFR-DI-01**: **SLA 99,5%** mensal.
- **NFR-DI-02**: Backups; **RPO ≤ 15 min**, **RTO ≤ 2 h**.
- **NFR-DI-03**: Deploy blue/green ou zero-downtime (migrações compatíveis).

### 5.5. Observabilidade
- **NFR-OB-01**: Logs estruturados com `tenant_id`, `survey_id`.
- **NFR-OB-02**: Métricas (latência, throughput, duração de jobs, erro por tenant).
- **NFR-OB-03**: Tracing distribuído (A/B test opcional).

### 5.6. Manutenibilidade e Qualidade
- **NFR-MQ-01**: Cobertura de testes ≥ 70% (unit + feature + e2e crítico).
- **NFR-MQ-02**: PSR/Lint; CI/CD com gates de qualidade.
- **NFR-MQ-03**: Documentação OpenAPI; guias de uso e operação.

### 5.7. Usabilidade e Acessibilidade
- **NFR-US-01**: UI responsiva; **WCAG 2.1 AA**.
- **NFR-US-02**: Idiomas: pt-BR (F1), en/es (F2+).
- **NFR-US-03**: Design system consistente (Tailwind/shadcn).

### 5.8. Interoperabilidade
- **NFR-IN-01**: APIs REST com **JWT**; webhooks para eventos.
- **NFR-IN-02**: Import **CSV** de respondentes; export **CSV/PDF**.

### 5.9. Restrições Técnicas
- **NFR-RT-01**: Laravel 12; PostgreSQL; Vue + shadcn/Tailwind.
- **NFR-RT-02**: Cloud com storage S3-compatível; filas Redis/RabbitMQ.

## 6. Regras de Negócio
- **RN-01**: Template usado → **lock**; edições criam **nova versão**.
- **RN-02**: Pesquisa **anônima** não armazena PII/IP (configurável).
- **RN-03**: Thresholds/diagnósticos configuráveis por template; presets “NR-01”.
- **RN-04**: Inadimplência → modo leitura com aviso.

## 7. Fluxos Críticos (resumo)
1. **Onboarding do Tenant** → criação, plano, convite admin, aceite.
2. **Criação de Template** → seções, perguntas, opções, pesos, publicar.
3. **Aplicação** → instância, assignments, abertura, coleta, fechamento.
4. **Scoring** → cálculo por pergunta/seção/geral; thresholds; diagnóstico.
5. **Relatórios** → analítico/sintético; URL assinada; auditoria.

## 8. Relatórios (detalhes)
- **Analítico:** tabelas por pergunta; gráficos de distribuição; export CSV/PDF.
- **Sintético:** índice geral; riscos por seção; diagnóstico; recomendações.
- **Comparativos:** evolução temporal; cortes por área/setor (quando houver).

## 9. Integrações
- **E-mail**: SMTP/SES/Sendgrid.
- **Pagamentos**: gateway de assinaturas.
- **Webhooks**: eventos (survey_opened/closed/scored).

## 10. Critérios de Aceite (exemplos)
- **CA-01**: Gestor cria template com ≥2 seções, ≥5 perguntas; validações ativas.
- **CA-02**: Instância aberta aceita respostas com salvamento de progresso; token único.
- **CA-03**: Scoring gera nível de risco coerente com thresholds configurados.
- **CA-04**: Sintético exibe índice geral + diagnóstico + recomendações.
- **CA-05**: Isolamento: Tenant A não enxerga dados do Tenant B (testes).

## 11. Métricas de Sucesso
- Taxa de conclusão; tempo até relatório; zero incidentes de isolamento; NPS.

## 12. Roadmap/Fases
- **F1 – MVP**: tenancy, templates, aplicação, respostas, relatório básico.
- **F2 – Relatórios**: analítico/sintético robustos; export; comparativos.
- **F3 – Conformidade**: LGPD avançada; auditoria; termos.
- **F4 – Billing/Planos**: planos, limites, superadmin.
- **F5 – Analytics/IA**: clusters; recomendações assistidas.

---

## 📑 Anexo A — OpenAPI (esqueleto)
Veja o arquivo `openapi-paas-nr1.yaml` incluído neste pacote para os principais endpoints e schemas (tenant-aware).