# Active Group Rebuild — notes for Claude

- Monorepo: `backend/` (Laravel 13 API only) and `frontend/` (Next.js 16 + Tailwind 4, TypeScript).
- The backend serves JSON only under `/api/v1`. Every endpoint must carry OpenAPI attributes (`OpenApi\Attributes`) so Swagger (`/api/documentation`) stays complete. Run `php artisan l5-swagger:generate` after changing endpoints.
- Auth: Laravel Sanctum bearer tokens for the admin dashboard.
- The admin dashboard is custom-built in Next.js; do not add Filament, Nova or other Laravel admin panels.
- Content is bilingual (Arabic and English). The UI must support RTL.
- Target hosting is Namecheap, so keep the backend compatible with shared cPanel hosting (MySQL/MariaDB, no long-running workers required).
- Before finishing backend work run `php artisan test`; for frontend run `npm run lint` and `npm run build`.
- Use the project skills in `.claude/skills/` (laravel-specialist, frontend-design, ui-ux-pro-max, web-design-guidelines, find-animation-opportunities, codebase-design).
- Run every piece of user-facing copy (site text, English translations, README and docs) through the `unslop` skill before finishing.
