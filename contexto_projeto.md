# Contexto do Projeto — Cinema

Resumo executivo
- Monorepo com backend em Laravel (API) e frontend em Next.js (UI). Configurado para rodar em Docker Compose.
- Objetivo: catálogo de filmes com dados enriquecidos por TMDB e mídia (posters, backdrops, trailers).

Estrutura principal
- `backend/` — Aplicação Laravel (PHP)
  - Modelos: `app/Models/Movie.php`, `Genre.php`, `Media.php`, `User.php`.
  - Serviços: `app/Services/TmdbService.php`, `MediaUrlResolver.php` (resolução de URLs de mídia).
  - Recursos HTTP: `app/Http/Resources/MovieResource.php`, `GenreResource.php`.
  - Migrations: tabelas para `movies`, `genres`, `media` e pivot `genre_movie` em `database/migrations/`.
  - Seeders: `database/seeders/FilmeSeeder.php` (população inicial de filmes).
  - Configs e assets: `routes/`, `public/`, `resources/`.
- `frontend/` — Next.js (TypeScript/React) com componentes e páginas em `src/`.
- `docker/` e `docker-compose.yml` — containers para app, frontend e banco (MySQL), com script de inicialização `docker/mysql/init.sql`.

Banco de dados e dados iniciais
- Migrations já presentes para `movies`, `genres`, `media` e pivot `genre_movie`.
- Há seeders para popular amostra (ver `database/seeders/`).

Integração com TMDB e mídia
- Implementado: `TmdbService` para chamadas à API do TMDB e `MediaUrlResolver` para formar URLs (TMDB ou local).
- `MovieResource` usa `MediaUrlResolver` para expor `poster_url`, `backdrop_url`, `logo_url` e `trailer_url`.

Pendências e recomendações (prioridade)
- Remover chaves sensíveis do repositório e adicionar `backend/.env.example` com placeholders.
- Confirmar que `backend/.gitignore` exclui `vendor/`, `node_modules/`, `.env`, `storage/` e builds.
- Adicionar CI básico (GitHub Actions) para rodar `composer install` e testes PHP/JS.
- Adicionar documentação mínima no `README.md` com passos de execução Docker Compose e variáveis de ambiente necessárias.
- Cobrir com testes automatizados os endpoints principais da API e os serviços `TmdbService`/`MediaUrlResolver`.

O que está sendo feito / evidências de trabalho em andamento
- Integração de mídia/TMDB: presença de `app/Services/TmdbService.php`, `MediaUrlResolver.php` e referências em `MovieResource`.
- Modelagem e migrations recentes: tabelas de `media`/`movies`/`genres` e pivot já versionadas (migrations de 2026-02-12).
- Seeders (p.ex. `FilmeSeeder`) preparados para popular dados de exemplo.

Próximos passos sugeridos (curto prazo)
1. Remover `TMDB_API_KEY` do `backend/.env` e movê-la para `backend/.env.example` (placeholder).
2. Criar branch com a documentação atualizada e commit (eu preparei uma branch local para isso).
3. Adicionar template de PR e instruções de contribution no `README.md`.
4. Implementar CI (workflow GitHub Actions) para validar builds e testes.

Como eu posso entregar os PRs
- Posso criar branches e commits locais com as alterações (ex.: `docs/update-contexto`) e gerar patches (`git format-patch`) caso você ainda não tenha um remote configurado.
- Se você já possui `origin` configurado, posso criar as branches e empurrar (push) e abrir PRs via `gh` CLI (se desejado).

Onde olhar primeiro
- Backend: [backend](backend)
- Frontend: [frontend](frontend)
- Docker / Compose: [docker-compose.yml](docker-compose.yml)

Notas finais
- Mantive a visão geral concisa e priorizei segurança (remover chaves) e entrega incremental (docs + CI + PRs).

Este arquivo serve como referência para o estado atual do projeto e próximos passos.
