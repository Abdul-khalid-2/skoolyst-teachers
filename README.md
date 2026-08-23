# Skoolyst Teachers — Teacher Portfolio SaaS Platform

A production-ready PHP (OOP, MVC) platform where teachers of every discipline
(School, College, University, Technical, Medical, Science, Mathematics, Arts,
Computer Science, General, etc.) register, build a portfolio from reusable
JSON-driven sections, and share one clean link (`/p/your-name`) with a UUID
behind the scenes. Built on top of your existing personal portfolio theme.

---

## 1. Requirements

- PHP 8.0+ with `pdo_mysql`, `mbstring`, `fileinfo` extensions
- MySQL 5.7+ / MariaDB 10.3+ (JSON column support)
- Apache with `mod_rewrite` (for clean URLs via `.htaccess`)

## 2. Folder Structure

```
teacher-portfolio/
├── index.php              # single front controller (entry point)
├── .htaccess               # clean URL rewriting
├── config/
│   ├── config.php           # <-- EDIT: DB credentials, BASE_URL
│   └── database.php         # (reserved)
├── core/                    # Router, Controller, Model, View, Auth, Helpers, Database
├── controllers/             # HomeController, AuthController, DashboardController,
│                             # PortfolioController, AdminController
├── models/
│   └── Teacher.php           # single scalable table model (teacher + super-admin)
├── views/
│   ├── layouts/               # shared header/footer/alerts
│   ├── home/                   # public landing + directory + filters
│   ├── auth/                    # login / register
│   ├── dashboard/                # teacher dashboard (tabs/ = each portfolio section)
│   ├── portfolio/                 # public portfolio (show.php) + resume (resume.php)
│   ├── admin/                      # super-admin panel
│   └── errors/404.php
├── assets/
│   ├── css/ (app.css = SaaS shell, style.css + skin/ = original portfolio theme)
│   ├── js/  (app.js = SaaS shell, Script.js + style-switcher.js = theme)
│   ├── image/
│   └── uploads/profile, uploads/resume   # must be writable (755/775)
└── database/schema.sql       # run this first
```

## 3. Installation

1. **Create the database**
   Import `database/schema.sql` into MySQL. This creates the `skoolyst_teachers`
   database, the single `teachers` table (with indexes on `subject`, `city`,
   `qualification`, `teacher_type`, `status` for fast filtering at scale), and
   seeds a default **super-admin**:
   - Email: `admin@skoolyst.com`
   - Password: `Admin@12345`
   - **Change this password immediately after first login** (there's no
     in-app "change password" screen yet — update the `password` column with
     a new `password_hash()` value, or add one; see Roadmap below).

2. **Configure the app**
   Edit `config/config.php` for your **database** credentials — that part is
   always required:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'skoolyst_teachers');
   define('DB_USER', 'your_db_user');
   define('DB_PASS', 'your_db_password');
   ```
   `BASE_URL` is **auto-detected** from the request (protocol + host + the
   folder the app is installed in), so it works out of the box whether you
   run it at your domain root in production, or inside a subfolder on
   localhost — e.g. `http://localhost/projects/teacher-portfolio/`. You do
   **not** need to edit `BASE_URL` for normal use.
   Only set `$FORCE_BASE_URL` near the top of `config.php` if you're behind a
   reverse proxy/CDN where auto-detection guesses wrong (e.g. it keeps
   showing an internal hostname instead of your public domain).

3. **Deploy**
   Point your domain/subdomain's document root at the `teacher-portfolio/`
   folder (the one containing `index.php` and `.htaccess`). Typical cPanel
   setup: upload the whole folder into `public_html` (or a subdomain's root).

4. **Permissions**
   Make sure these are writable by the web server:
   ```
   assets/uploads/profile
   assets/uploads/resume
   ```

5. **Visit the site**
   - `/` — public landing page + directory + filters
   - `/register` — teacher signup
   - `/login` — teacher & admin login (admin is auto-redirected to `/admin`)
   - `/dashboard` — teacher's portfolio editor
   - `/p/{slug}` — public portfolio (shareable link)
   - `/p/{slug}/resume` — download/print resume (auto-generated, or the
     teacher's uploaded PDF if they added one)
   - `/admin` — super-admin panel

## 4. How data is stored (scalable-by-design)

Everything lives in **one table**, `teachers`, as requested:

- Fast-filtered columns are plain indexed `VARCHAR`/`ENUM` fields: `subject`,
  `city`, `qualification`, `teacher_type`, `status`, `role`.
- Flexible, repeatable sections are `JSON` columns, each an array of objects:
  `educations`, `experiences`, `skills`, `certifications`, `projects`,
  `languages`, `awards`, `social_links`, `services`.
- Every teacher gets a `uuid` (globally unique, used internally / for future
  API-style lookups) and a human-readable `slug` (used in the public URL,
  e.g. `/p/ayesha-khan`).
- `role` distinguishes `teacher` vs `super-admin` in the same table — no
  separate admin table needed, exactly as scoped.

This keeps the schema simple to reason about while remaining fully
extensible: adding a new field to, say, "experience" entries requires zero
migrations — it's just a new key in the JSON payload the dashboard sends.

## 5. Templates

`teachers.template` (default: `'default'`) selects which portfolio view
renders. Only `default` (your original theme, wired to dynamic data) ships
today. To add a new theme later:

1. Duplicate `views/portfolio/show.php` → `views/portfolio/show-<name>.php`.
2. In `PortfolioController::show()`, extend the `in_array(... ['default'])`
   whitelist and branch `View::render()` to the right file based on
   `$teacher['template']`.
3. Add the new option to `views/dashboard/tabs/template.php`.

## 6. Security notes already in place

- Passwords hashed with `password_hash()` / verified with `password_verify()`.
- CSRF tokens on every state-changing form (`Helpers::csrfToken()` /
  `Controller::verifyCsrf()`).
- PDO prepared statements everywhere (no raw string concatenation into SQL).
- Session cookies set `HttpOnly`, `SameSite=Lax`.
- Uploaded files are type/size validated and renamed (no user-controlled
  filenames reach disk).
- `config/`, `core/`, `controllers/`, `models/`, `views/`, `database/` each
  have a `.htaccess` denying direct web access.

## 7. Roadmap / suggested next steps

These weren't in the original scope but are natural next steps for a
production SaaS:

- Email verification + password reset flow
- "Change password" screen in the dashboard/admin settings
- Rate limiting on login/register
- Image resizing/cropping on profile photo upload
- Additional paid templates (the architecture already supports this)
- Admin ability to feature/verify teachers, and basic analytics (the
  `views_count` column is already tracked per portfolio)

## 8. SEO implementation notes

**Phase 1 — Directory visibility audit.** Reviewed `Teacher::filter()`,
`HomeController`, and the directory card template. No "profile completeness"
condition gates directory visibility — filtering already relies only on
`status = 'active'` and `is_public = 1`, and the card template already
degrades gracefully when optional fields (phone, email, bio, education,
skills, city, photo) are missing. No code changes were needed for this phase.

**Phase 2 — SEO metadata.**
- `Teacher::seoTitle()` / `Teacher::seoDescription()` (in `models/Teacher.php`)
  generate a unique `<title>` and meta description per public profile, with
  safe fallbacks when subject, city, category, or bio are missing — a
  profile's title/description is never empty or broken.
- Homepage (`HomeController` → `views/home/index.php` →
  `views/layouts/header.php`) now ships a fixed, targeted title/meta
  description and a self-referencing canonical URL.
- Every public profile page (`views/portfolio/show.php`) now has a unique
  title, a ~150-160 character meta description (from bio, or a generated
  fallback), and a self-referencing canonical URL pointing at `/p/{slug}`.
- `views/layouts/header.php` accepts optional `description`/`canonical`
  variables; other pages (login, register, dashboard, admin, 404) are
  unaffected since they don't pass them.

**Phase 3 — robots.txt and sitemap.xml.**
- `robots.txt` is a static plain-text file at the project root (served
  directly by Apache since it's a real file — `.htaccess` never routes it
  through `index.php`, so it can't render as an HTML 404 page). Allows
  everything except `/login`, `/register`, `/dashboard`, `/admin`, and
  references the sitemap.
- `/sitemap.xml` is generated dynamically by `SitemapController` (registered
  in `index.php`), with `Content-Type: application/xml`. It lists the
  homepage plus every teacher from `Teacher::allPublicForSitemap()` — the
  same `status = 'active'` / `is_public = 1` rule used by the public
  directory, so private/inactive/deleted profiles never appear and no
  optional-field completeness check removes anyone.

**Phase 4 — Structured data (JSON-LD).**
- `Teacher::jsonLd()` (in `models/Teacher.php`) builds a `Person` +
  `ProfilePage` schema (as a single `@graph`) for each public profile page,
  rendered in `views/portfolio/show.php`. A field (description, image,
  jobTitle, address/locality) is only included when real data exists —
  nothing is invented, and missing phone/email/education/skills never break
  or hide the schema. The `@id`/`url` values match the page's own canonical
  URL.
- The homepage (`views/home/index.php`) now includes a minimal `WebSite`
  schema.
- All JSON-LD is encoded with `JSON_HEX_TAG | JSON_HEX_AMP` (in addition to
  `JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE`) so a bio or name
  containing `</script>`, `&`, or other special characters can't break out
  of the script tag or produce invalid JSON.

**Phase 5 — On-page content and headings.**
- Homepage `<h1>` is now "Find Qualified Teachers in Pakistan" (was
  marketing copy aimed at teacher sign-ups); a short intro paragraph and
  crawlable "Popular subjects / cities / qualifications / categories" link
  lists (built from the same `$subjects`/`$cities`/`$qualifications`/
  `Teacher::TEACHER_TYPE_LABELS` data already used by the filter dropdowns)
  now sit above the filter bar, plus a genuine FAQ section
  (`<details>/<summary>`) grounded in real platform behavior (free
  portfolios, filtering, phone-reveal login requirement, the `is_public`
  privacy toggle) - no invented claims.
- Profile page: the "Hello, I'm {name}" heading is now an `<h1>` (was
  `<h3>` — CSS selectors updated in `assets/css/style.css` to keep the
  exact same visual styling under the new tag). Category
  (`Teacher::TEACHER_TYPE_LABELS`) is now shown alongside Subject/City when
  present. A small breadcrumb-style nav links back to the directory and to
  the subject-/city-filtered listing when that data exists. Avatar `alt`
  text (`Teacher::altText()`) is now descriptive (name + subject/category +
  city, with safe fallbacks) instead of just the bare name, on both the
  profile hero photo and directory card avatars.
- Every addition is conditional on real data - no blank headings, no
  keyword stuffing, existing filter functionality and design untouched.

**Phase 6 — Indexing, privacy and status handling.**
- Confirmed `Teacher::allPublicForSitemap()` and `Teacher::filter()` already
  share the exact same eligibility rule (`status = 'active' AND
  is_public = 1`), so active+public profiles are the only ones ever in the
  directory/sitemap, and no optional-field completeness check affects
  eligibility.
- `PortfolioController::show()` now computes that same eligibility rule
  and sets `<meta name="robots">` accordingly on the profile page: `index,
  follow` when eligible, `noindex, follow` otherwise (private and/or
  inactive) — so a profile that's reachable by direct link (e.g. an
  owner's preview) is never indexable when it shouldn't be.
- A deleted teacher's row no longer exists, so `Teacher::findBySlug()`
  returns null and `/p/{slug}` already 404s correctly — verified, no
  change needed.
- Added `<meta name="robots" content="noindex, follow">` to
  login, register, dashboard, admin, 404, and the resume-locked gate page
  (all via `views/layouts/header.php`, which now accepts an optional
  `robots` variable, defaulting to `index, follow` everywhere else).
- `robots.txt` was deliberately left untouched — it already only lists the
  utility route prefixes (not used as an access-control or "hide private
  profile" mechanism, since Disallow entries are public and not
  enforceable).
- Confirmed no private data (phone, email is only shown because a teacher
  chooses to put it on their own public bio, never system fields like
  `id`/`uuid`/`resume_access`) appears in `Teacher::jsonLd()`, the meta
  description, or the page title — phone numbers specifically are never
  in the page HTML at all, only delivered via an authenticated AJAX call.

**Phase 7 — Performance and image SEO.**
- Removed a duplicate Font Awesome load on the profile page
  (`views/portfolio/show.php` was loading both the self-hosted
  `css/all.css` + `assets/webfonts/*` and the same library again from a
  CDN — confirmed every icon class in use exists in the local copy, then
  dropped the CDN `<link>`).
- Added explicit `width`/`height` HTML attributes only where they can't
  conflict with anything: the directory-card avatars, where CSS already
  fixes both `width: 84px; height: 84px;` to the exact same values. For
  the logo, hero thumbnail, and profile photo — each governed by CSS via
  a fixed dimension on one axis (`height: 40px`, `aspect-ratio: 16/9`, or
  `height: 400px`) with the other axis left to scale — adding HTML
  `width`/`height` attributes caused the browser to render at the
  image's natural size instead of respecting that CSS, breaking the
  layout (reported and fixed twice during testing: hero thumbnail
  cropping via `object-fit: cover`, then the logo rendering full-size).
  Removed the attributes from all three; the existing CSS rules already
  fully constrain their box with no layout-shift risk, since CSS applies
  before the image's own dimensions are relevant.
- Added `loading="lazy"` to the below-the-fold directory-card avatars;
  removed it from the hero video thumbnail (above the fold, so it now
  loads eagerly instead of competing for load priority against nothing).
- Added an `onerror` fallback on directory-card and profile-photo `<img>`
  tags so a broken/deleted image URL swaps to the generated ui-avatars
  placeholder instead of a broken-image icon — a bad image never hides or
  breaks a profile.
- Identified and fixed an oversized asset on the homepage: the hero video
  thumbnail (`assets/image/thumnal.png`) was a 920KB PNG displayed at a
  fraction of its native 1672x941 size. Replaced it with a resized,
  re-encoded `assets/image/thumnal.jpg` (899x506, ~68KB — a 93% reduction)
  with no visible quality loss at its actual display size.
- Also identified two large, entirely unreferenced images in the repo
  (`assets/image/Skoolyst1.png` ~172KB, `assets/image/Skoolyst2.png`
  ~1.1MB) - not loaded by any page, so no runtime performance impact, but
  flagging them here since they're dead weight in the repo. Left them in
  place rather than deleting without confirmation.
- `.htaccess`: added `mod_expires`/`mod_headers` cache-control rules
  scoped strictly to static file extensions (images, fonts, CSS, JS), so
  browsers/CDNs cache them for 1 week-1 month. This only touches files
  Apache serves directly - dynamic routes like `/sitemap.xml` (no matching
  physical file) are untouched and still go through `index.php` every
  request.


