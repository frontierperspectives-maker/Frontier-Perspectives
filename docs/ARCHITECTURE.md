# Frontier Perspectives — How the website is built

> **Who this is for:** an AI assistant or a web developer who has been asked to explain,
> change or fix something on the Frontier Perspectives website. It describes exactly how
> the site works, where every piece of code lives, and how to change things safely.
>
> **If you are the site owner:** you don't need to read this. Give this file (together with
> `ADMIN-GUIDE.md`) to an AI assistant such as ChatGPT, Claude or Gemini, and ask your
> question in plain English. See `AI-HELP-PROMPT.md` for a ready-made prompt.

---

## 1. Summary

| | |
|---|---|
| Website | https://frontierperspectives.com (WordPress in `public_html`; went live 24 Nov 2026) |
| What it is | A research and opportunities platform: research articles, publications, and listings of academic jobs, fellowships, scholarships and grants. |
| Platform | WordPress (self-hosted), PHP 8.3, MySQL/MariaDB |
| Hosting | Hostinger shared web hosting, account `u359144621`, LiteSpeed web server |
| Custom code | **One plugin** (`fp-core`) + **one theme** (`frontier`). No page builder, no ACF, no other content plugins. |
| Front-end | Bootstrap 5.3.3 (bundled locally in the theme) + a small brand stylesheet (`assets/app.css`) |
| Admin editor | **Classic editor for all content types and pages** (block editor disabled in `post-types.php`, `use_block_editor_for_post*` filters) so every field is visible on one scrolling page. Unused boxes (Custom Fields, Excerpt, Slug, and Featured image on listings) are hidden. Posts and Comments menus are hidden (the site uses Articles and has no comments). |
| Source code | GitHub: `frontierperspectives-maker/Frontier-Perspectives` (this repository) |
| History | Rebuilt in November 2026 from a hand-built CodeIgniter 3 site. Every old URL was kept identical. |

**Design principle:** all content behaviour (content types, fields, deadline logic, URLs,
SEO, security, contact form) lives in the **plugin**. The **theme** only decides how things
look. Switching or redesigning the theme never loses data or behaviour.

---

## 2. Repository layout

```
wp-content/
  plugins/fp-core/                 ← all behaviour (the "brain")
    fp-core.php                    bootstrap, version, settings migrations, /admin → /wp-admin redirect
    inc/fields.php                 ★ THE FIELD SCHEMA — every custom field and dropdown option
    inc/post-types.php             content types, taxonomy, REST meta, deadline sort key
    inc/meta-boxes.php             edit-screen boxes generated from the schema
    inc/query.php                  deadline auto-hide, filters, /fellowships, /grants, country pages, /search, /p/<slug>
    inc/settings.php               "Site Settings" admin page (logo, contact email, footer text, social links)
    inc/contact.php                contact form endpoint (POST /contact) + "Messages" inbox
    inc/admin.php                  list columns, Expired badge, Duplicate, bulk Publish/Unpublish
    inc/helpers.php                template helpers + Open Graph / description meta tags
    inc/seo.php                    titles, descriptions, canonicals, noindex rules, structured data, sitemap
    inc/security.php               hardening (XML-RPC off, login throttling, headers, no user enumeration…)
    inc/legal.php                  creates draft Privacy Policy and Terms of Use pages once
    inc/import.php                 one-time importer from the old CodeIgniter database (Tools → Import Old Site)
  themes/frontier/                 ← how it looks
    style.css                      theme header (name, version)
    functions.php                  asset loading, ★ MAIN MENU ITEMS (fr_nav_items), small template helpers
    header.php / footer.php        site header with menu + search; footer with columns and links
    front-page.php                 homepage
    archive-fp_job.php             /jobs, /fellowships, /jobs/<country>
    archive-fp_scholarship.php     /scholarships, /grants, /scholarships/<country>
    archive-fp_article.php         /articles (+ ?category= filter)
    archive-fp_publication.php     /publications
    single-fp_*.php                detail pages for each content type
    page.php                       normal pages (Privacy, Terms, any page you create)
    page-about.php                 /about (mission, section blurbs, team from Authors)
    page-submit-opportunity.php    /submit-opportunity (form)
    search.php / 404.php / index.php
    partials/opp-grid.php          the job/scholarship card grid
    partials/filter-bar.php        filter pills
    partials/article-card.php      article card
    assets/app.css                 ★ brand colours, fonts and component styles
    assets/vendor/                 Bootstrap 5.3.3 CSS + JS (MIT licence)
docs/                              this documentation
```

---

## 3. Content model

All custom fields are stored as post meta with the key `_fp_<fieldname>` and are defined
in **one place**: `fp_fields()` in `inc/fields.php`. Meta boxes, saving, sanitising, REST
exposure and the importer all read from that array.

### 3.1 Content types (custom post types)

| Admin menu label | Post type | Public URL | Notes |
|---|---|---|---|
| Articles | `fp_article` | `/articles`, `/articles/<slug>` | Uses taxonomy `fp_category` ("Categories") and WordPress tags. Body = main editor. |
| Publications | `fp_publication` | `/publications`, `/publications/<slug>` | Linked to Authors (contributors) and Articles. |
| Jobs & Fellowships | `fp_job` | `/jobs`, `/jobs/<slug>`, `/fellowships`, `/jobs/<country>` | Deadline auto-hide. |
| Scholarships & Grants | `fp_scholarship` | `/scholarships`, `/scholarships/<slug>`, `/grants`, `/scholarships/<country>` | Deadline auto-hide. |
| Authors | `fp_author` | *(no public page)* | Shown on /about ("Our Team"), as article bylines and publication contributors. Bio = main editor. |
| Hero Slides | `fp_hero` | *(not public)* | Homepage feature box. Order = "Order" field (Page Attributes box). |
| Messages | `fp_message` | *(private)* | Contact-form submissions. Cannot be created by hand. |
| Pages | `page` (WordPress core) | `/about`, `/submit-opportunity`, `/p/<slug>` | `/p/<slug>` is an alias that serves any page by its slug (kept from the old site). |

### 3.2 Fields per type (labels as shown in the admin)

**Articles** — Standfirst (summary) · Author (one Author) · Main image (or Featured Image) ·
Image description (alt) · Caption · Photo credit · Key findings (one per line) · Keywords
(one per line) · SEO box: SEO title, SEO description, Social share image. Plus WordPress
Categories and Tags.

**Publications** — Edition · Type (Journal / Report / Special Edition) · Cover image · PDF ·
ISSN · Table of contents (one per line) · Contributors (many Authors) · Articles in this
publication (many Articles) · Feature on homepage (checkbox) · SEO box.

**Jobs & Fellowships** — Institution (required) · Institution logo · Location · Country /
region (dropdown) · Remote OK · Position type (dropdown) · Field / discipline · Salary /
stipend · Application deadline (date, optional) · Deadline note · Application link
(required) · Keywords. Description = main editor. "Posted" date = WordPress publish date.

**Scholarships & Grants** — Funder (required) · Funder logo · Level / type (dropdown) ·
Country / region (dropdown) · Eligible regions (one per line) · Application deadline ·
Deadline note · Application link (required) · Keywords. Description = main editor.

**Authors** — Role · Photo · Links (one URL per line). Name = title, bio = main editor.

**Hero Slides** — Standfirst · Image · Eyebrow label (default "Research Article") · Link
target (e.g. `/articles/my-article` or a full URL) · Date shown. Title = slide headline.

### 3.3 Dropdown options (edit in `inc/fields.php`)

- **Country / region:** United States, United Kingdom, Canada, Australia, New Zealand,
  Europe, Asia, Middle East, Africa. Each has a URL slug in `fp_country_slugs()`
  (`usa`, `uk`, `canada`, `australia`, `new-zealand`, `europe`, `asia`, `middle-east`, `africa`).
- **Job position type:** Job, Faculty, Postdoc, PhD, Research Staff, Research Fellowship,
  Graduate Fellowship, Postgraduate Fellowship, Postdoctoral Fellowship, Visiting
  Fellowship, Professional Fellowship.
- **Scholarship level:** BS/MS, PhD, Post Doc, Research Grant, Student Grant, Faculty Grant,
  Early Career Researcher Grant.
- **Publication type:** Journal, Report, Special Edition.

> ⚠️ Renaming an existing option does **not** rename it on existing items — items keep the
> old value and the dropdown will show "— Select —" for them until re-saved. Add new
> options freely; rename/remove only after updating the items that use them.

### 3.4 Site Settings (one-off values)

- **Settings → General** (WordPress core): Site Title ("Frontier Perspectives"), Tagline
  ("Discover. Apply. Succeed.").
- **Site Settings** (custom page, option `fp_settings`): Logo · Contact email · Footer
  mission statement · Social links (one per line, `Label | https://url`).

The footer mission statement is reused as the homepage intro paragraph, the About-page
quote and the homepage meta description.

---

## 4. Behaviour (the important logic)

### 4.1 Deadline auto-hide (jobs and scholarships)
- Each job/scholarship has an optional deadline date. A hidden sort key
  `_fp_deadline_sort` is kept in sync automatically (`fp_sync_deadline()` in
  `post-types.php`): `YYYY-MM-DD 23:59:59`, or `2099-12-31 23:59:59` when blank.
- Every public **list** (archives, fellowships, grants, country pages, search, homepage
  counts, "More opportunities", REST lists, sitemap) only shows items whose sort key is
  ≥ now (`fp_open_clause()` in `query.php`). Lists are sorted soonest-deadline first.
- An item stays visible **through the whole of its deadline day** and disappears after
  midnight (site timezone UTC).
- Nothing is deleted or unpublished. **Detail pages of expired items still load** (so old
  links/bookmarks work) and show a "This opportunity has closed" notice. The admin list
  shows a red **Expired** badge.
- If *Deadline note* is filled, the note is displayed instead of the date, but the date
  (if any) still controls hiding.

### 4.2 Derived pages
- `/fellowships` = open jobs whose Position type contains "fellow".
- `/grants` = open scholarships whose Level contains "grant".
- `/jobs/<country>` and `/scholarships/<country>` = open items with that Country/region.
  Only the 9 slugs in `fp_country_slugs()` exist; anything else under `/jobs/` is treated as
  a job slug.
- Filter pills (`?type=`, `?discipline=`, `?location=Remote`, `?country=`, `?level=`,
  `?region=`, articles `?category=`) are built automatically from the values present on
  currently open items, so new values appear without any configuration.

### 4.3 Homepage (`front-page.php`)
- Left: site name badge, Tagline as H1, footer mission text, two buttons, live counts.
- Right feature box: the **first Hero Slide** (lowest Order). If there are no published
  slides, the **featured Publication** (or newest publication) is shown instead. *Only one
  slide is displayed — it is not a rotating carousel.*
- "Latest Articles" = 3 newest articles; "Recent Research Articles" = the next 3 that are
  not already in the hero or sidebar.
- Stats band and four quick-link cards (texts are written in the template).

### 4.4 Contact form
- `/submit-opportunity` posts via JavaScript to `POST /contact` (fields: name, email,
  subject, message; hidden honeypot `website`). Responses are JSON.
- Validation: all fields required, valid email, max lengths (name 120, subject 200,
  message 5,000), max 5 links, max 5 submissions per IP per 10 minutes.
- Saved as private **Messages** (unread count bubble in the admin menu) and emailed to the
  Site Settings contact email via `wp_mail()`. **Email delivery requires SMTP to be
  configured** (see §8) — messages are always saved even if email fails.
- `GET /contact` redirects to `/submit-opportunity`.

### 4.5 URLs and redirects
- Permalinks: `/%postname%` with **no trailing slash**, matching the old site exactly.
- `/p/<slug>` serves any Page. `/search?q=` is the site search. `/sitemap.xml` →
  `/wp-sitemap.xml`. `/admin` and `/admin/*` → `/wp-admin/`.
- Author archives and `?author=` redirect to the homepage (security).

### 4.6 SEO (`inc/seo.php`)
- Titles and meta descriptions per page type are set in `fp_seo_meta()` (wording copied
  from the old site). Each item's **SEO box** overrides title/description.
- Canonical tags; filtered lists canonicalise to the unfiltered list and are
  `noindex,follow`; search results are `noindex,follow`.
- Structured data (JSON-LD): Organization + WebSite/SearchAction (home), BreadcrumbList,
  **JobPosting** for every job (eligible for Google for Jobs), Article for articles.
- `<h1>` tags inside imported content are demoted to `<h2>` so each page has one H1.
- WordPress core sitemap at `/wp-sitemap.xml` (users and tags excluded); RSS at `/feed`
  lists articles.
- Open Graph / Twitter card tags in `helpers.php`.
- **Search-engine visibility:** Settings → Reading → "Discourage search engines" must be
  **unticked** on the live site (tick it only on staging copies).

### 4.7 Security (`inc/security.php`)
XML-RPC refused (403) · login throttling (6 failures per IP or 10 per username → 20-minute
lock) · generic login error messages · user enumeration blocked (REST users endpoint
hidden from visitors, author archives redirected) · security headers (HSTS,
X-Frame-Options, X-Content-Type-Options, Referrer-Policy, Permissions-Policy) · WordPress
version hidden · dashboard theme/plugin file editor disabled · emoji CDN removed ·
`readme.html`, `license.txt`, `wp-config-sample.php` deleted from the server.

---

## 5. Things that are written in code (not editable in the admin)

| What | File | Where |
|---|---|---|
| Main menu items and dropdowns | `themes/frontier/functions.php` | `fr_nav_items()` |
| Footer columns ("Explore", "Opportunities") and the "Let's explore ideas that matter." line | `themes/frontier/footer.php` | top of file |
| Homepage badge text, buttons, stats band ("Evidence. Insight. Impact."), four quick-link cards | `themes/frontier/front-page.php` | whole file |
| List-page headings and intro sentences | `archive-fp_*.php` | `fr_page_head(...)` calls |
| About page: three section blurbs; contact box text | `themes/frontier/page-about.php` | middle of file |
| Submit page headings, default help text, form labels | `themes/frontier/page-submit-opportunity.php` | whole file |
| Page titles/descriptions for list pages | `plugins/fp-core/inc/seo.php` | `fp_seo_meta()` |
| Dropdown options, field labels, help texts | `plugins/fp-core/inc/fields.php` | `fp_fields()` |
| Colours, fonts, spacing | `themes/frontier/assets/app.css` | `:root` variables at top |
| Items per page on job/scholarship lists (24) | `plugins/fp-core/inc/query.php` | `posts_per_page` |
| Contact form limits | `plugins/fp-core/inc/contact.php` | validation block |
| Login lockout thresholds | `plugins/fp-core/inc/security.php` | `FP_LOGIN_*` constants |

Brand colours (`assets/app.css`): `--navy #0F5C5C` (teal), `--navy-light #0E7C7B`,
`--gold #F26B3A` (orange), `--gold-light #F5895F`, `--cream #F4FAFA` (background),
`--mist #E3F2F0`, `--ink #14343A` (text), `--slate2 #5C7784` (muted text),
`--link #0D9488`. Headings use Georgia (serif); body uses the system sans-serif font.

---

## 6. How to make code changes safely

1. **Never edit code on the live site first.** Create a staging copy with Hostinger's
   *WordPress → Staging* tool (hPanel), test there, then apply the same change to live.
2. Edit the file in this GitHub repository (GitHub's web editor works fine for small
   text changes).
3. Upload the changed file(s) to the same path under `wp-content/` using **Hostinger
   hPanel → Websites → File Manager** (or Hostinger's Git deployment).
4. If you changed `fp-core`, bump `FP_CORE_VERSION` in `fp-core.php` (this re-runs the
   settings migration and refreshes URL rules). If you changed the theme's CSS, bump
   `Version:` in `themes/frontier/style.css` (forces browsers to load the new CSS).
5. Check the affected pages, then repeat on the live site.
6. If something breaks: re-upload the previous version of the file from GitHub. A PHP
   error in the plugin can show "There has been a critical error" — WordPress then emails
   the admin a recovery link, or rename the `fp-core` folder via File Manager to disable it
   temporarily (data is not lost).

**Common code changes, with exact recipe:**

- *Add a dropdown option* (e.g. a new position type "Lecturer"): add the string to the
  `options` array of `position_type` in `inc/fields.php`. If the option should also appear
  in the main menu, add a line in `fr_nav_items()`.
- *Add a new country/region with its own page*: add the name to `fp_country_options()`
  **and** name ⇒ slug to `fp_country_slugs()` in `inc/fields.php`; bump `FP_CORE_VERSION`
  (rebuilds URL rules). It then appears in the Jobs menu automatically.
- *Add a new field* (e.g. "Contract length" on jobs): add one line to the `fp_job` array in
  `fp_fields()`, e.g. `'contract_length' => ['label' => 'Contract length', 'type' => 'text'],`.
  It appears in the edit screen automatically. To display it, add
  `fp_get('contract_length')` in `single-fp_job.php` (e.g. in the `$meta` array).
- *Change a colour*: edit the variable at the top of `assets/app.css`, bump theme version.
- *Change the menu*: edit `fr_nav_items()` in `themes/frontier/functions.php`.
- *Show more/fewer items per page*: change `posts_per_page` (24) in `inc/query.php`.

---

## 7. Operations

- **Backups:** Hostinger hPanel → Websites → Backups (automatic). Before any big change,
  create a manual backup there. The original CodeIgniter site's backups from 2026-11-09 are
  in the private folder `/domains/frontierperspectives.com/fp-backups-private/` (not
  web-accessible).
- **Old site (rollback):** the CodeIgniter files were moved (not deleted) to
  `/domains/frontierperspectives.com/old-codeigniter-site/` and its database
  `u359144621_fp_research` was left untouched. To roll back, move the WordPress files out of
  `public_html` and the old files back in. Once the new site has run well for a month or two,
  both can be deleted (keep a downloaded copy).
- **Root `.htaccess`:** a copy is in `server/htaccess-public_html`. The top *Frontier Perspectives hardening* block blocks archives/dumps/config files, redirects old `/uploads/` image links, and makes HTML pages and the sitemap **uncacheable** (otherwise Hostinger's default WordPress block caches pages for a week, so new/expired jobs would show late). Keep that block when editing the file; WordPress only rewrites its own `# BEGIN/END WordPress` section.
- **Google Search Console:** verified with the file `public_html/google12fd5cc4ad7ab8b0.html` (owner: frontierperspectives@gmail.com). **Never delete it** — Google re-checks it; deleting it loses Search Console access. Sitemap submitted: `wp-sitemap.xml`.
- **Old image links:** `/uploads/<file>` (old site) redirects to
  `/wp-content/uploads/2026/11/<file>` via the root `.htaccess`, which also blocks archives,
  dumps, `wp-config.php`, `readme.html`, etc.
- **Updates:** WordPress core minor/security updates install automatically. Check
  Dashboard → Updates monthly; update plugins one at a time and check the site after each.
- **Plugins installed:** `fp-core` (custom, required), `hostinger` (Hostinger Tools) and `wp-mail-smtp` (sends the site's emails through Gmail).
  Recommended additions at launch: *LiteSpeed Cache* (speed) and an SMTP plugin such as
  *WP Mail SMTP* (email delivery). Avoid page builders — they are not needed.
- **Theme:** `frontier` (custom, required by the design).
- **Users:** create one account per person (Users → Add New). Use the **Editor** role for
  day-to-day content work; keep **Administrator** for the owner only.
- **Importer:** *Tools → Import Old Site* was only for the migration from the old site. It
  is safe to ignore; it does nothing unless the old site's files are present.

## 8. Known limitations / open items (as of handover)

1. **Email delivery** — ✅ configured 24 Nov 2026: WP Mail SMTP → Other SMTP → `smtp.gmail.com:587` TLS, user `frontierperspectives@gmail.com`, password = a Google *App Password* named "Frontier Website". If emails stop (e.g. the Google password was changed or 2-Step Verification turned off), create a new App Password and paste it into WP Mail SMTP → Settings.
   (contact-form notifications, password-reset emails). Messages are always saved in the
   admin regardless.
2. **Privacy Policy and Terms of Use** — created as *drafts* from a template; review and
   publish them (footer links appear automatically once published). They are not legal
   advice.
3. **Main menu and homepage texts** are in code (see §5), not in *Appearance → Menus*.
4. **Hero Slides** show one slide only (no carousel).
5. **Page caching** is not yet enabled (install LiteSpeed Cache at launch).
6. **Analytics / Google Search Console** are not yet connected.
