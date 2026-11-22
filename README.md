# Frontier Perspectives

Source code and handover documentation for **https://frontierperspectives.com** — a research
and opportunities platform (research articles, publications, academic jobs, fellowships,
scholarships and grants), built on WordPress.

## Documentation
| File | For whom | What it is |
|---|---|---|
| [`docs/ADMIN-GUIDE.md`](docs/ADMIN-GUIDE.md) | Site owner / editors | Click-by-click guide to running the site + a big Q&A ("How do I…?"). |
| [`docs/VIDEO-SCRIPTS.md`](docs/VIDEO-SCRIPTS.md) | Whoever makes the training videos | 24 walkthrough-video scripts (shot list + word-for-word voiceover) and how to record them. |
| [`docs/AI-HELP-PROMPT.md`](docs/AI-HELP-PROMPT.md) | Site owner | Copy-paste prompt to get accurate help from ChatGPT / Claude / Gemini. |
| [`docs/ARCHITECTURE.md`](docs/ARCHITECTURE.md) | Developers and AI assistants | Exactly how the site is built and how to change it safely. |

## What's in this repository
```
wp-content/plugins/fp-core/   custom plugin: content types, fields, deadline logic, URLs, SEO, security, contact form
wp-content/themes/frontier/   custom theme: the site's design (Bootstrap 5 + brand styles)
docs/                         handover documentation
```
WordPress core and the database are **not** in this repository; they live on Hostinger.
No passwords or keys are stored here — keep it that way.

## Deploying a change (short version)
1. Make the change here.
2. Upload the changed file(s) to the same path under `wp-content/` with Hostinger hPanel →
   File Manager — on the staging site first if possible.
3. Plugin change → bump `FP_CORE_VERSION` in `fp-core.php`. CSS change → bump `Version:` in
   `themes/frontier/style.css`.
4. Check the site. Full details: `docs/ARCHITECTURE.md` §6.
