# EZKIRA — Project Context for Claude Code

SME finance-monitoring web app (revenue, expenses, P&L, budget tracking) for Malaysian SME business owners. Live at **https://ezkira.com**.

## Stack

- **Backend:** Pure PHP 8.1, hand-rolled MVC (no framework)
- **DB:** MySQL via PDO, prepared statements only
- **Frontend:** TailwindCSS via **CDN** (`cdn.tailwindcss.com`, not a compiled build — do not introduce a build step without discussing it first), vanilla JS (no bundler)
- **Hosting:** Shared cPanel hosting
- **Deploy:** GitHub Actions → FTP → cPanel, triggered on every push to `master` (`.github/workflows/deploy.yml`)
- **Auth:** Session-based + Google OAuth2 (`GOOGLE_CLIENT_ID`/`GOOGLE_CLIENT_SECRET` in `config/config.php`)
- **WhatsApp Blast feature:** uses Fonnte API (`FONNTE_TOKEN` constant), not a WA_* constant naming

## Architecture

- `app/Core/Router.php` + `routes/web.php` → route table (manual, not auto-discovered)
- `controllers/` → one class per resource, extends `App\Core\Controller`
- `models/` → PDO queries, one class per table-ish concept
- `views/` → plain PHP templates, `views/layouts/main.php` is the shared shell, `views/layouts/partials/` has nav/footer/help-drawer
- `config/config.php` → **NOT in git** (`.gitignore`'d). Must be created manually on each environment (local XAMPP vs production). `config/database.php` reads from it and is in git.
- `assets/js/app.js` → single global JS file loaded on every page (dark mode toggle, mobile nav, user dropdown, flash auto-dismiss)
- BASE_PATH must be defined before requiring `config/config.php` — production's config uses it for error log paths. Any new bootstrap/CLI script needs `define('BASE_PATH', ...)` before the require.

## Deploy details

- FTP deploy excludes: `.git`, `.github`, `.claude`, `uploads/profiles/**`, `uploads/receipts/**`, `uploads/blast/**`, `storage/logs/**`, `storage/sessions/**`, `config/config.php`, `.env*`, `node_modules`
- Protocol is plain `ftp` (not `ftps`) — known weakness, not yet fixed, needs cPanel host to confirm FTPS support first
- **Every code change must be committed AND pushed to `master` to actually reach production** — there is no separate staging. A fix sitting in the working tree does nothing; this has caused real confusion before (user reported a bug "still not fixed" when the fix was simply never pushed).
- `config/config.php` must be manually created and uploaded to the server once (see `SETUP.md` / `DEPLOYMENT.md`), then typically `chmod 444`'d so FTP overwrites don't clobber it, and so it never gets wiped by a deploy.

## Database

- `database/schema.sql` is the base schema but has historically drifted behind the migration files — when setting up a fresh DB, apply `schema.sql` **then every** `database/migration_*.sql` in numeric order. Migrations as of now: 002 (business type), 003 (Google auth / `google_id`), 004 (refunds/`entry_type`), 005 (capitals table), 006 (`payment_method`), 007 (blast tables), 008 (rename `revenue_targets.amount` → `target_amount`).
- **Action needed on existing production DB:** migration 008 has been committed but has NOT been confirmed run on the live `ezkira.com` database yet. Until it is, `Revenue::getTarget()`/`setTarget()` will throw a SQL error (column `target_amount` doesn't exist on old installs). Run this once via phpMyAdmin:
  ```sql
  ALTER TABLE `revenue_targets`
      CHANGE COLUMN `amount` `target_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00;
  ```

## Known issues / tech debt (not yet fixed)

- **Budget % settings are global, not per-user** (`models/Setting.php` + `ExpenseController::index()`). If one user changes their opex/marketing/cogs budget %, it overwrites it for every other user. A `budget_pct` table with `user_id` exists in schema but is unused — this is the intended fix path, needs a real feature pass.
- **FTP deploy uses plain `ftp`**, not `ftps`/`sftp` — ask host if FTPS is supported before switching (don't just flip the workflow blind, confirm cPanel supports it first).
- **CSRF token is not rotated per-request** (`app/Core/CSRF.php`) — only rotated on login. Low priority, needs care before changing (easy to break forms if done wrong).
- `tools/setup_google_auth.php` is a one-time DB migration runner guarded by a hardcoded token (`?token=fikira-google-2025`). It's still tracked in the repo. **Verify it has actually been deleted from the live production server** — it should never stay live after use.

## Recently fixed (context for "why does the code look like this")

- IDOR bugs in `ExpenseController::delete()` / `receipt()` / `receiptFile()` — added `user_id` ownership checks so one user can't delete/view another user's expenses or receipts.
- `Revenue::periodWhere()` used `WEEK(sale_date, 1)` while `recentTransactions()` used `WEEK(sale_date, 3)` (ISO week, matching PHP's `date('W')`) — standardized on mode 3.
- Open redirect in `AuthController::switchLang()` via unvalidated `HTTP_REFERER` — now validated against `APP_URL`'s host before redirecting.
- `Session::destroy()` cookie clear was missing `samesite=Lax`, inconsistent with how the cookie was originally set.
- Mobile hamburger nav (`views/layouts/partials/nav.php`) — went through several iterations (onclick JS → inline JS → CSS `peer-checked`) before landing on a **pure CSS checkbox+label toggle with a literal `<style>` block**, specifically to avoid any dependency on Tailwind CDN's JIT class generation being reliable for variant classes, and to avoid any JS execution/event-propagation issues on mobile browsers. Don't revert this to a JS-driven toggle without a strong reason.

## Working conventions

- User communicates in **Bahasa Malaysia** mixed with English (rojak) — respond in kind unless asked otherwise.
- User is non-technical-leaning product owner, not a PHP developer — explain fixes in plain terms, don't assume they'll read a diff unprompted.
- **Always commit AND push to `master` after a fix** — a local-only edit is invisible to the user testing on `ezkira.com`. Confirm the push succeeded before telling the user to test.
- No automated test suite exists. Verification = manual testing on the live site or local XAMPP (`http://localhost:8001` via the PHP built-in server configured in `.claude/launch.json`), since MySQL often isn't running locally either — check before assuming you can browser-test.
