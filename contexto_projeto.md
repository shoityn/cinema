# Contexto do Projeto — Cinema

Resumo rápido
- Projeto monorepo com backend em Laravel e frontend em Next.js.
- Ambiente preparado para rodar com Docker / Docker Compose.

Estrutura principal
- `backend/`: Aplicação Laravel (PHP)
  - `app/Models/Filme.php`, `Genero.php`, `User.php`: modelos principais.
  - `database/migrations/`: migrations incluem tabelas de `filmes`, `generos` e tabela pivô `filme_genero`.
  - `routes/`: rotas `api.php` e `web.php`.
  - `public/`, `resources/`: assets e views.
- `frontend/`: Aplicação Next.js (TypeScript/React)
- `docker/` e `docker-compose.yml`: configuração para containers (ex.: MySQL com `docker/mysql/init.sql`).

Banco de dados
- Migrations já presentes para `filmes`, `generos` e pivot. Verificar `backend/database/migrations/`.

Como rodar localmente (com Docker Compose)
1. Verifique que Docker e Docker Compose estão instalados.
2. Na raiz do projeto, rode:

```bash
docker-compose up -d --build
```

3. Acesse containers conforme configuração (ex.: app backend e frontend). Ajuste `.env`/variáveis de ambiente dentro dos serviços conforme necessário.

Passos sugeridos para o primeiro commit e publicação no GitHub
1. Inicializar o repositório Git local (se ainda não existir):

```bash
git init
```

2. Verificar/ajustar `.gitignore` (arquivo `backend/.gitignore` já existe — confirme). 
3. Adicionar todos os arquivos e criar commit inicial:

```bash
git add .
git commit -m "chore: commit inicial do projeto (backend + frontend + docker)"
```

4. Criar repositório remoto no GitHub (via UI do GitHub ou `gh` CLI) e adicionar `origin`:

```bash
# usando GitHub CLI
gh repo create <usuario>/<nome-repo> --public --source=. --remote=origin
# ou manualmente
git remote add origin https://github.com/<usuario>/<nome-repo>.git
```

5. Fazer push para a branch principal (ex.: `main`):

```bash
git branch -M main
git push -u origin main
```

Notas e próximos passos
- Confirme segredos e credenciais não versionadas em arquivos `.env` (use `.env.example`).
- Opcional: adicionar `README.md` com instruções de execução e CI (GitHub Actions).
- Verificar se `backend/.gitignore` cobre `vendor/`, `node_modules/`, `.env` e pastas de build.

Onde olhar no repositório
- Código backend: `backend/`
- Código frontend: `frontend/`
- Docker: `docker/` e `docker-compose.yml`

Mantive este arquivo na raiz com o nome `contexto_projeto.md` para referência rápida.
