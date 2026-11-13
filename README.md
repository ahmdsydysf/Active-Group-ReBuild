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

## Publishing

The site is static HTML, so a change made in the dashboard shows on the site only after a rebuild. When there are unpublished changes, the dashboard shows a "نشر التعديلات" button in the top bar. Pressing it starts `.github/workflows/publish.yml`, which builds the site from the live API and uploads `frontend/out/` to the hosting over FTPS. It takes a few minutes.

Setup, once:

1. On GitHub, under Settings > Secrets and variables > Actions, add:
   - variables `API_URL` (the backend URL, for example `https://api.activegroup-eg.com`) and `SITE_URL` (`https://activegroup-eg.com`);
   - secrets `FTP_SERVER`, `FTP_USERNAME`, `FTP_PASSWORD`, and `FTP_DIR` if the site folder isn't `public_html/`.
2. Create a fine-grained GitHub token for this repository only, with "Actions: read and write".
3. In the backend `.env`, set `PUBLISH_GITHUB_TOKEN` to that token and `PUBLISH_GITHUB_REPO` to `owner/repo`.

The workflow also runs every night, so posts scheduled for a future date appear without anyone pressing the button.

## Hosting on Namecheap (cPanel)

- Backend: upload `backend/` outside `public_html`, point a subdomain such as `api.` at `backend/public`, create the MySQL database in cPanel, fill `.env` (`APP_ENV=production`, `APP_DEBUG=false`, database, mail, `FRONTEND_URL`, `SANCTUM_STATEFUL_DOMAINS`), then run `php artisan migrate --force` and `php artisan storage:link`.
- Add one cron job: `* * * * * php /home/USER/backend/artisan schedule:run >> /dev/null 2>&1`. It publishes scheduled posts and cleans old tokens. No queue worker is needed.
- Frontend: the publish workflow uploads the static files into `public_html/`.

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
