# Cinema — Instruções de inicialização

## Visão geral
Repositório monorepo com:
- `backend/`: aplicação Laravel (PHP)
- `frontend/`: aplicação Next.js
- Arquivos Docker: `docker/` e `docker-compose.yml`

## Pré-requisitos
- Git instalado
- Docker e Docker Compose instalados
- (Opcional) GitHub CLI `gh` para criar repositório remoto

## Clonar o repositório
```bash
git clone https://github.com/SEU_USUARIO/NOME_REPO.git
cd NOME_REPO
```

## Rodando com Docker Compose (recomendado)
1. Construir e subir containers:
```bash
docker-compose up -d --build
```
2. Verificar containers:
```bash
docker ps
```
3. Acessar o container do backend (exemplo):
```bash
docker exec -it <nome_ou_id_do_container_backend> sh
# dentro do container você pode usar: php artisan, composer, etc.
```

Observações:
- Se for a primeira vez, pode ser necessário rodar `composer install` e `npm install` dentro dos containers ou localmente conforme a sua configuração.

## Backend (Laravel) — passos comuns
Dentro do container backend ou localmente na pasta `backend/`:
```bash
cd backend
composer install
cp .env.example .env    # ajuste variáveis se necessário
php artisan key:generate
php artisan migrate
php artisan db:seed      # (se existir seeder configurado)
php artisan serve --host=0.0.0.0 --port=8000   # só se não estiver usando Docker
```

## Frontend (Next.js) — passos comuns
Dentro da pasta `frontend/`:
```bash
cd frontend
npm install
npm run dev
# ou com pnpm/yarn conforme preferência
```

## Variáveis de ambiente
- Não comite arquivos `.env` com segredos.
- Use `.env.example` como modelo. Confirme as variáveis de conexão com o banco no `docker-compose.yml` e em `backend/.env`.

## Testes
- Backend (PHPUnit): dentro de `backend/`:
```bash
php artisan test
# ou vendor/bin/phpunit
```
- Frontend: use os comandos configurados em `frontend/package.json` (ex.: `npm run test`).

## Primeiro commit e envio ao GitHub
1. Inicializar git (se ainda não existir):
```bash
git init
```
2. Revisar `backend/.gitignore` e adicionar/excluir arquivos sensíveis.
3. Adicionar e commitar:
```bash
git add .
git commit -m "chore: commit inicial do projeto (backend + frontend + docker)"
```
4. Criar repositório remoto e mandar o `main`:
```bash
# com GitHub CLI
gh repo create SEU_USUARIO/NOME_REPO --public --source=. --remote=origin

git branch -M main
git push -u origin main
```
Ou crie o repositório pelo site do GitHub e depois:
```bash
git remote add origin https://github.com/SEU_USUARIO/NOME_REPO.git
git branch -M main
git push -u origin main
```

## Comandos úteis
- Subir containers: `docker-compose up -d --build`
- Parar containers: `docker-compose down`
- Entrar no backend: `docker exec -it <container> sh`
- Rodar migrations: `php artisan migrate`

## Arquivos importantes
- Código backend: `backend/`
- Código frontend: `frontend/`
- Docker Compose: `docker-compose.yml`
- Contexto do projeto: `contexto_projeto.md`

---
