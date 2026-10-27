# Thrive & Flow — Studio Platform

Laravel rebuild of the Pre & Postnatal Studio (thriveandflow.co.za): public site, member studio with a week-aware video player, and a Studio Manager admin. Styled with the customer's supplied brand palette (blush `#c89f9c`, plum `#603946`, sage `#98b9a5`, warm white `#faf8f6` and charcoal `#4a4a4a`), Cormorant Garamond + Poppins.

## Run it

```bash
composer install
cp .env.example .env && php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
php artisan serve
```

No npm build needed — assets are plain files in `public/css` and `public/js`.

**Demo logins** (password: `password`)

| Who | Email | Sees |
|---|---|---|
| Studio owner | admin@thriveandflow.co.za | `/admin` — Studio Manager |
| Naledi (T2, week 26, active) | naledi@example.com | `/studio` — member dashboard |
| Ayesha (postpartum wk 9, on trial) | ayesha@example.com | `/studio` |

Set `SEED_DEMO=false` in `.env` before seeding a real environment.

## Switch to MySQL

In `.env`, swap the DB block:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=thriveandflow
DB_USERNAME=...
DB_PASSWORD=...
```

then `php artisan migrate --seed`. No code changes — nothing here is SQLite-specific.

## How it hangs together

- **Roles** — `users.role` (`admin` | `member`), `Role` enum, `admin` middleware alias (bootstrap/app.php), `User::isAdmin()`.
- **Content** — `Program` → `ProgramWeek` → `Lesson`. Programs are `program` (weekly, e.g. Bump Strength T2, weeks 14–27) or `collection` (flat library, e.g. Stretch Library). Seeders carry the full real course structure (160+ classes).
- **Access** — `Lesson::accessFor($user)` returns `ok | login | subscribe | drip | unpublished`; player + views route on it. Drip: pregnancy programs with `drip_by_week` unlock up to the member's `pregnancyWeek() + 1` (derived from due date). Free-preview classes are public.
- **Billing** — `Plan` (R249/m, R2 490/y, R349 guide) + `Subscription` (trialing/active/past_due/…). Self-serve free trial (once per member, no card); after that a subscription sits `past_due` until the owner records payment in Admin → member → "Record a payment" (EFT reality until PayFast is wired in). `Subscription::extendOnePeriod()` is the single activation path.
- **Progress** — `lesson_progress` (seconds + completed_at) and `favorites`; streak and per-program % computed on the dashboard.
- **Admin** — custom-built Studio Manager (`/admin`): overview KPIs + signups chart, program builder (weeks, classes, reorder, publish/preview toggles, video refs), members (search, grant/end, role), plans.
- **Video** — `lessons.video_provider` (`bunny` | `youtube` | `vimeo` | `file`) + `video_ref`; the player embeds accordingly and shows a branded placeholder when unset. Bunny Stream signed URLs are the plan for production.

## Deliberately stubbed (phase 2)

- PayFast subscriptions (webhooks → `extendOnePeriod()` slots straight in; `PAYFAST_*` keys already in `.env.example`)
- Password reset + email verification (mailer currently `log`)
- Email nudges (trial ending, new week)
- Bunny signed-URL generation (`BUNNY_*` keys in `.env.example`)

## Tests

`php artisan test` — feature coverage over roles, preview/subscription/drip access, registration, trial start, and admin CRUD.

<!-- pm-llm-usage:start -->
## LLM task usage (all agents)

Claude Code, Codex and other LLM runtimes must follow the shared
[measured token reporting workflow](<../LLM-TOKEN-USAGE.md>). Start a task window with the
PM timer, report measured provider/model usage with stable event IDs, and sync
before switching tasks or completing work. Missing telemetry is unknown; failed
reporting stays **PM sync pending**. Use your own surface token and PM session.
<!-- pm-llm-usage:end -->

## Shared agent collaboration

For occasional PM handoffs between any LLMs, read `/home/ryan/Development/LLM-COLLABORATION.md`. Use your own identity, preserve a concise context note, and keep decisions/approvals with Ryan. The briefing records availability and the API contract.

## October 2026 public-site refresh

The public home, catalogue and course pages retain the original blush/slate identity, with a three-scene illustrated introduction and richer course companion guides. `public/css/refresh.css` scopes the new site styles to `.public-site`; member access, subscriptions, progress and the Studio Manager retain their existing behavior. No new migration or npm build is needed.

- `config/course_guides.php`: expectations, practice goals, session suggestions and adaptations for nine existing course slugs, plus a fallback for new courses. Database records remain the authority for published lessons, equipment and access.
- `config/movements.php`: three sourced educational movement examples. The homepage viewer supports selection, articulated illustrated characters inside an accessible SVG stage, pause/replay, a scrubber, step-by-step poses, labelled body-focus maps and a full guide dialog. `/?movement=cat-neutral#movement` links to a specific example; unknown values fall back to the first example.
- `public/images/`: original custom PNG illustrations and optimized WebP delivery copies. Exact built-in ImageGen prompts and asset checks are in [docs/illustration-prompts.md](docs/illustration-prompts.md).
- Motion respects the system reduced-motion preference. Exercise demos start paused and play a single cycle; the homepage scene has a persistent pause control for the current visit. Written movement guides remain available without JavaScript.

Start the review server with `php artisan serve --host=127.0.0.1 --port=8037`. Browse `/`, `/programs` and `/programs/bump-strength-t2`. Clinical sources are linked in the movement/course guidance; these illustrative additions have not received an independent clinical review. Existing lesson videos still need their actual video references in Studio Manager where unset.

Targeted verification for this refresh: `CourseGuidesTest` (5 tests, 92 assertions), `MovementStudioTest` (1 test, 24 assertions), and four selected public/access/drip cases from `StudioTest` (13 assertions) passed. Browser QA covered desktop and 390px mobile, scene controls, movement controls/dialog focus, stage filters, deep links, reduced motion and no-JS fallback. Full suite and whole-tree analysis were not run. Preview captures are in `docs/screenshots/`. PM sync remains pending because Prenatal was absent from the accessible project list; the session audit is retained locally outside the project.

The October 26 animation refinement uses the original detailed cat/calf artwork and a purpose-made seated-character arm sheet. See [illustration provenance and rig maintenance](docs/illustration-prompts.md#articulated-demonstrations--26-october-2026). Review the player at `/?movement=side-reach#movement`; desktop/mobile pose captures are in `docs/screenshots/animation-20261026/`.

## Customer brand update — October 2026

The customer-supplied palette updates the shared site colours, with prenatal blush, postnatal plum and nutrition sage accents. The homepage uses the supplied Roche portrait above its existing copy; the illustrated studio scene remains interactive. The grey specified for body text is reserved for lighter accents; body copy uses a darker grey for readability. No asset build is required.

The homepage portrait is intentionally kept outside version control. To display it, place the approved `roche-portrait.webp` in `public/images/`; the page works without it. Customer transfer files, local databases, generated screenshots and runtime files are also excluded from the public repository.
