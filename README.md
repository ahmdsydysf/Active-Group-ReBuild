# Active Group rebuild

A full rebuild of [activegroup-eg.com](https://activegroup-eg.com/): faster, lighter, fully responsive, bilingual (Arabic / English with RTL), with a brand-new admin dashboard.

## Structure

| Folder | Stack | Purpose |
| --- | --- | --- |
| `backend/` | Laravel 13 (API only), Sanctum, L5-Swagger | REST API (`/api/v1/*`) + Swagger docs |
| `frontend/` | Next.js 16 (App Router), React 19, Tailwind CSS 4, TypeScript | Public website + custom admin dashboard |
| `.claude/skills/` | Claude Code skills | Project skills (see `skills-lock.json`) |

## Getting started

### Backend

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed   # sample content from the current site
php artisan admin:create you@example.com --password='a long password'
php artisan serve            # http://localhost:8000
```

- Health check: `GET /api/v1/health`
- Swagger UI: `http://localhost:8000/api/documentation`
- Regenerate docs: `php artisan l5-swagger:generate`
- Tests: `php artisan test`

### Frontend

```bash
cd frontend
npm install
cp .env.example .env.local   # API_URL and NEXT_PUBLIC_API_URL point at the backend
npm run dev                  # http://localhost:3000
```

The dashboard is at `/admin/`. The public site is static: `npm run build` fetches every page from the API and writes plain HTML to `out/`, so the API must be running during the build.

## Deploying

Every push to `main` runs `.github/workflows/deploy.yml`. It uploads the API to the server over SSH, runs the migrations, then builds the static site from the live API and uploads it to `public_html/`.

The site is static HTML, so a change made in the dashboard shows only after a rebuild. When there are unpublished changes, the dashboard shows a "نشر التعديلات" button. Pressing it runs `.github/workflows/publish.yml`, which rebuilds and uploads the site only. That workflow also runs every night, so posts scheduled for a future date appear on time.

### One-time setup on Namecheap (cPanel)

1. Back up the current `public_html/`. The first deploy replaces its contents.
2. Domains: add the subdomain `api.activegroup-eg.com` with its document root set to `active-api/public`. Turn on SSL for it.
3. MySQL Databases: create a database and a user, and give the user all privileges on it.
4. SSH Access: Manage SSH Keys > Generate a key with no passphrase, then Authorize it. View the private key; it goes into GitHub below. SSH must be enabled for the account (Namecheap uses port 21098).
5. File Manager: create `active-api/.env` with:

   ```
   APP_NAME="Active Group"
   APP_ENV=production
   APP_KEY=
   APP_DEBUG=false
   APP_URL=https://api.activegroup-eg.com
   APP_LOCALE=ar
   APP_FALLBACK_LOCALE=en
   LOG_LEVEL=error
   DB_CONNECTION=mysql
   DB_HOST=localhost
   DB_PORT=3306
   DB_DATABASE=cpaneluser_dbname
   DB_USERNAME=cpaneluser_dbuser
   DB_PASSWORD="database password"
   SESSION_DRIVER=array
   CACHE_STORE=file
   QUEUE_CONNECTION=sync
   FILESYSTEM_DISK=local
   FRONTEND_URL=https://activegroup-eg.com,https://www.activegroup-eg.com
   SANCTUM_EXPIRATION=480
   MAIL_MAILER=smtp
   MAIL_HOST=mail.activegroup-eg.com
   MAIL_PORT=465
   MAIL_SCHEME=smtps
   MAIL_USERNAME=noreply@activegroup-eg.com
   MAIL_PASSWORD="mailbox password"
   MAIL_FROM_ADDRESS=noreply@activegroup-eg.com
   MAIL_FROM_NAME="Active Group"
   PUBLISH_GITHUB_TOKEN=
   PUBLISH_GITHUB_REPO=ahmdsydysf/active-group-rebuild
   PUBLISH_WORKFLOW=publish.yml
   PUBLISH_BRANCH=main
   ```

6. On GitHub, Settings > Secrets and variables > Actions:
   - secrets `SSH_HOST` (the server name shown in cPanel, for example `server123.web-hosting.com`), `SSH_USER` (the cPanel user name) and `SSH_KEY` (the whole private key);
   - variables `API_URL` (`https://api.activegroup-eg.com`) and `SITE_URL` (`https://activegroup-eg.com`). Add `SSH_PORT`, `API_DIR` or `SITE_DIR` only if they differ from `21098`, `active-api` and `public_html`.
7. Run the Deploy workflow (Actions > Deploy > Run workflow), or merge into `main`. The first run uploads the code and creates the tables.
8. cPanel Terminal, once:

   ```bash
   cd ~/active-api
   php artisan key:generate --force
   php artisan db:seed --class=SettingsSeeder --force
   php artisan admin:create you@example.com
   php artisan optimize
   ```

   Run the Deploy workflow again so the site is built with the settings in place.
9. Cron Jobs: add `* * * * * cd ~/active-api && php artisan schedule:run >> /dev/null 2>&1`. It publishes scheduled posts and clears expired tokens. No queue worker is needed.
10. For the publish button: create a fine-grained GitHub token for this repository only, with "Actions: read and write", put it in `PUBLISH_GITHUB_TOKEN` in the server `.env`, and run `php artisan optimize` again.

## Skills

Restore project skills with:

```bash
npx skills experimental_install
```

## Roadmap

1. Foundation and skills (done)
2. Backend API with Swagger docs (done)
3. Public website, every page in Arabic and English (done)
4. Admin dashboard for all content, messages and users (done)
5. Move the data from the old site, then deploy to Namecheap
