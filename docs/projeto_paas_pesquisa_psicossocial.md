# 🧩 Projeto PaaS — Pesquisa Psicossocial NR-01

## 📘 Visão Geral

**Objetivo:** Criar uma plataforma PaaS que permita empresas aplicarem pesquisas psicossociais conforme exigência da NR-01, com foco na flexibilidade de criação de questionários, coleta segura de respostas e relatórios gerenciais.

**Tecnologias:**
- Backend: PHP Laravel 12
- Frontend: Vue.js (com shadcn/tailwind)
- Banco de dados: PostgreSQL
- Arquitetura: SaaS multi-tenant
- Segurança: Autenticação 2FA, LGPD compliance

---

## 🚦 Faseamento do Projeto

### 🔹 Fase 1 — MVP: Cadastro de Questionários e Coleta de Respostas (4 semanas)
#### Entregas:
- Cadastro de empresas (clientes do PaaS)
- Cadastro de usuários por empresa (admin, colaborador)
- Módulo de questionário:
  - CRUD de questionários
  - Tipos de perguntas: múltipla escolha, escala (Likert), texto aberto
- Módulo de aplicação:
  - Geração de link único por colaborador
  - Resposta anônima ou identificada
- Dashboard básico:
  - Total de respondentes
  - Percentual de conclusão

### 🔹 Fase 2 — Validação e Relatórios Básicos (3 semanas)
#### Entregas:
- Dashboard por questionário
  - Gráficos de distribuição por pergunta
- Exportação de resultados (CSV/PDF)
- Filtros por cargo/setor/área
- Acesso a relatórios por perfil de usuário
- Notificações e lembretes automáticos (e-mail)

### 🔹 Fase 3 — Conformidade NR-01 e Aderência Legal (2 semanas)
#### Entregas:
- Termo de consentimento (LGPD)
- Registro de aceite
- Templates de questionários conforme modelos NR-01
- Registro de auditoria (quem acessou o quê e quando)

### 🔹 Fase 4 — Multi-Tenant e Painel da Plataforma (3 semanas)
#### Entregas:
- Separação lógica de dados por empresa (multi-tenant)
- Painel de superadmin:
  - Gestão de empresas cadastradas
  - Limites de uso por plano
- Controle de planos e assinaturas (API Pagamentos)

### 🔹 Fase 5 — Analytics e IA (opcional) (4 semanas)
#### Entregas:
- Identificação de padrões psicossociais (clusters de risco)
- Análises preditivas com IA
- Sugestões automáticas de ações preventivas

---

## 🗂️ Estrutura de Módulos

### 1. Auth & Gestão de Acesso
- Login / Logout
- Registro por convite
- Permissões por papel (admin, gestor, colaborador)

### 2. Módulo de Questionários
- Criação de questionários dinâmicos
- Banco de perguntas reutilizáveis
- Grupos de perguntas (blocos temáticos)

### 3. Aplicação de Pesquisa
- Disparo por e-mail ou link
- Questionário responsivo e acessível
- Registro de progresso (incompleto vs completo)

### 4. Coleta e Armazenamento de Respostas
- Validação por token/link
- Anonimato opcional
- Armazenamento em formato JSON normalizado

### 5. Relatórios e Visualizações
- Gráficos (chart.js/recharts)
- Filtros de análise (empresa, setor, cargo)
- Exportações

### 6. Conformidade e Segurança
- Criptografia de dados sensíveis
- Logs de auditoria
- Termos de consentimento (assinados)

---

## 📅 Cronograma Resumido

| Fase | Nome                              | Duração | Entregáveis Principais                         |
|------|-----------------------------------|---------|------------------------------------------------|
| 1    | MVP Questionário e Respostas      | 4 sem   | CRUD + aplicação básica                        |
| 2    | Relatórios e Visualizações        | 3 sem   | Dashboards e filtros básicos                   |
| 3    | Conformidade Legal                | 2 sem   | LGPD, termos, auditoria                        |
| 4    | Multi-Tenant e Gestão de Contas   | 3 sem   | Isolamento de dados, plano SaaS                |
| 5    | Analytics/IA (opcional)           | 4 sem   | Análise automatizada                           |

---

## 📌 Observações Importantes

- LGPD: desde o início, garantir compliance nos fluxos de coleta e uso dos dados.
- Escalabilidade: projetar o banco e arquitetura para suportar múltiplas empresas com grande volume de respostas.
- Modularização: questionários e perguntas devem ser componentes reutilizáveis.
- Auditoria: registrar ações críticas como edições, acessos e respostas.
