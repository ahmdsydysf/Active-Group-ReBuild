# Active Group — Rebuild

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
php artisan migrate
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
npm run dev                  # http://localhost:3000
```

## Skills

Restore project skills with:

```bash
npx skills experimental_install
```

## Roadmap

1. Foundation and skills ← current
2. Backend: improved schema from the old SQL dump, data migration script, REST API (auth, roles, uploads), Swagger docs
3. Public website: all old pages redesigned, AR/EN + RTL, SEO, optimized images
4. Admin dashboard: full CRUD for all content, messages, stats
5. Deploy to Namecheap hosting
