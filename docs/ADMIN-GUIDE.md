# Frontier Perspectives — Website Owner's Guide

**A plain-English, step-by-step manual for running the website. No technical knowledge needed.**

How to use this guide:
- **Part A** teaches the basics (logging in, finding your way around, the Publish button).
- **Part B** walks through each kind of content (jobs, scholarships, articles, …) step by step.
- **Part C** is a big **question-and-answer** list: "How do I…?" for everything you might
  want to do in the next year. Use your browser's search (Ctrl+F / ⌘+F) to find a topic.
- **Part D** covers safety, backups and what to do if something looks wrong.

If you ever get stuck, give this file and `ARCHITECTURE.md` to any AI assistant (ChatGPT,
Claude, Gemini…) and ask your question — see `AI-HELP-PROMPT.md`.

> 🔒 **Golden rules**
> 1. Nothing you do in the admin can break the *design* of the site. The worst that can
>    happen is a typo on a page — and every change can be undone.
> 2. Deleted items go to the **Trash** first and can be restored for 30 days.
> 3. If a change is not showing on the website, refresh the page with **Ctrl+F5**
>    (Windows) or **⌘+Shift+R** (Mac).

---

# PART A — The basics

## A1. Logging in
1. Go to **https://frontierperspectives.com/wp-admin** (the old address
   `/admin/login` also takes you there).
2. Type your **username or email** and **password**, then click **Log In**.
3. You arrive at the **Dashboard**.

- **Forgot your password?** Click **Lost your password?** under the login box and enter
  your email. A reset link is emailed to you. (If no email arrives within 10 minutes,
  check your Spam folder; if it is not there either, see C-62.)
- **"Too many failed login attempts"?** For security the site locks a login after several
  wrong passwords. Wait 20 minutes and try again carefully.

## A2. Finding your way around
The black menu on the left of the admin is where everything lives:

| Menu item | What it's for |
|---|---|
| **Dashboard** | Home screen of the admin. |
| **Articles** | Research articles. Also contains **Categories** and **Tags**. |
| **Publications** | Journals, reports and special editions (with PDF and cover). |
| **Jobs & Fellowships** | Job and fellowship listings. |
| **Scholarships & Grants** | Scholarship and grant listings. |
| **Authors** | People: bylines on articles, contributors on publications, and the "Our Team" section on the About page. |
| **Hero Slides** | The large feature box on the right of the homepage. |
| **Messages** | Messages sent through the website form. A number bubble shows how many are unread. |
| **Media** | All uploaded images and PDFs. |
| **Pages** | Simple pages: About, Submit an Opportunity, Privacy Policy, Terms of Use. |
| **Users** | People who can log in to the admin. |
| **Site Settings** | Logo, contact email, footer text, social media links. |
| **Settings** | WordPress settings (site name, tagline, etc.). |

At the very top there is a thin black bar. Click the **site name** (top left) to jump to
the public website; hover over it and click **Dashboard** to come back.

## A3. The list screens
Clicking e.g. **Jobs & Fellowships** shows a table of all jobs.
- **Search box** (top right) — find an item by its title.
- **All / Published / Drafts / Trash** links above the table — filter by status.
- Hover over a row to see the actions: **Edit**, **Quick Edit**, **Trash**, **View**,
  **Duplicate**.
- Tick several rows, choose an action in the **Bulk actions** dropdown (**Publish**,
  **Unpublish (move to draft)**, **Move to Trash**, **Edit**), then click **Apply**.
- Jobs and scholarships show the **Deadline** column. Items whose deadline has passed show
  a red **Expired** badge — they are automatically hidden from the public lists.
- Click a column heading such as **Deadline** or **Date** to sort by it.

## A4. The edit screen (how every item is edited)
Every item — job, scholarship, article, publication, author, page — opens in the **same
simple editor**, one scrolling page:
1. **Title** — the box at the top ("Add title").
2. **Text box** — under the title, with a Word-like toolbar. This is the description/body.
   Click into it and type, or paste from Word. Toolbar: the **Paragraph** menu (choose
   *Heading 2* for a section heading), **B** bold, *I* italic, bullet list, numbered list,
   quote, alignment, and the **link** (chain) button. The last button (**Toolbar Toggle**)
   shows a second row with more tools (underline, text colour, undo/redo, …). **Add Media**
   (above the toolbar) inserts an image or file.
3. **Details box** — scroll **below** the text box. All the specific fields (institution,
   deadline, application link, …) are here. Fields marked as required must be filled.
4. **Right-hand column** — the **Publish** box (status, visibility, publish date) and, for
   articles, **Categories**, **Tags**, **Featured image** and the **SEO** box.
5. **Web address** — shown under the title as *Permalink* with an **Edit** button.

**Saving:**
- **Save Draft** — saves privately; nothing appears on the website.
- **Publish** — one click; the item is live immediately. A message appears at the top, e.g.
  *"Job published. View job"*.
- After publishing, the button says **Update**. Click it after every change.
- **Preview** — see what it will look like before publishing.

**Unpublishing:** in the Publish box, next to **Status: Published** click **Edit** → choose
**Draft** → **OK** → click **Update**.

**Undo:** press **Ctrl+Z** (⌘+Z on Mac) while typing, or show the second toolbar row
(*Toolbar Toggle*) and use the undo arrow. For older changes, use **Revisions → Browse**
in the Publish box to restore an earlier version.

## A5. Copy-pasting text from Word, Google Docs or a website
Paste directly into the main text area — headings, bold, lists and links are kept. If
formatting looks messy, paste with **Ctrl+Shift+V** (⌘+Shift+V on Mac) to paste as plain
text, then re-apply headings with the **+** button.

## A6. Uploading images and PDFs
- In any image or PDF field, click **Choose / upload** → **Upload files** tab → **Select
  Files** → pick the file → click **Choose file**. The address of the file is filled in for
  you.
- Good image sizes: logos square, at least 200×200 px; article/hero images landscape, about
  1600×900 px; publication covers portrait (3:4), about 900×1200 px. Prefer JPG or WebP
  under 500 KB. (Use https://squoosh.app to shrink big photos.)
- All uploads are in **Media** in the left menu.

---

# PART B — Step-by-step for each type of content

## B1. Add a job
1. **Jobs & Fellowships → Add New**.
2. **Title**: the position name, e.g. *Postdoctoral Research Fellow in Climate Policy*.
3. **Main text area**: paste the full job description (duties, requirements, benefits).
4. Scroll down to **Details** and fill in:
   - **Institution** *(required)* — e.g. *University of Cambridge*.
   - **Institution logo** — *Choose / upload* the institution's logo (square works best).
   - **Location** — city and country as you want it shown, e.g. *Cambridge, UK*.
   - **Country / region** — choose from the list. This decides which country page
     (e.g. Jobs → United Kingdom) it appears on.
   - **Remote OK** — tick if the job can be done remotely.
   - **Position type** — e.g. *Job*, *Faculty*, *Postdoc*, *PhD*, or one of the
     *… Fellowship* types. **Any "Fellowship" type also appears on the Fellowships page.**
   - **Field / discipline** — free text, e.g. *Health*, *Economics*. Becomes a filter on the
     Jobs page automatically.
   - **Salary / stipend** — optional, free text, e.g. *£38,000 per year*.
   - **Application deadline** — pick the date. Leave **blank** if there is no fixed date.
   - **Deadline note** — optional text shown *instead of* the date, e.g. *Open until
     filled* or *Rolling admissions*.
   - **Application link** *(required)* — the official page where people apply
     (starts with https://).
   - **Keywords** — optional, one per line.
5. Click **Publish** (top right).
6. Click **View Job** to check it. It now appears on /jobs (and on /fellowships and the
   country page if applicable), sorted by deadline.

**What happens when the deadline passes?** Nothing to do! After midnight on the deadline
day the job disappears from all lists automatically. It is *not* deleted: the page still
opens from old links (with a "This opportunity has closed" notice) and stays in the admin
with a red **Expired** badge. If the deadline is extended, just change the date and click
**Update** — it comes back.

## B2. Add a fellowship
Exactly like a job (B1). Choose a **Position type** ending in **Fellowship** (Research,
Graduate, Postgraduate, Postdoctoral, Visiting or Professional). It will appear on
**/fellowships**, in the **Fellowships** menu under its type, and also on **/jobs**.

## B3. Add a scholarship or grant
1. **Scholarships & Grants → Add New**.
2. **Title**: e.g. *Chevening Scholarship 2027 – Fully Funded Master's in the UK*.
3. **Main text area**: full description, benefits, eligibility, how to apply.
4. **Details**:
   - **Funder** *(required)*, **Funder logo**.
   - **Level / type** — *BS/MS*, *PhD*, *Post Doc* for scholarships; *Research Grant*,
     *Student Grant*, *Faculty Grant*, *Early Career Researcher Grant* for grants.
     **Any "Grant" level also appears on the Grants page** (everything appears on /scholarships).
   - **Country / region** — where the study/funding takes place.
   - **Eligible regions** — who can apply, one per line, e.g. *All nationalities*,
     *Africa*, *Commonwealth countries*. Becomes the Region filter.
   - **Application deadline**, **Deadline note**, **Application link** *(required)*,
     **Keywords** — as for jobs.
5. **Publish**. Same automatic expiry as jobs.

## B4. Write a research article
1. **Articles → Add New**.
2. **Title** and the **article body** in the text box. For a section title, put the cursor
   on the line and choose **Heading 2** in the **Paragraph** menu. Use the **quote** button
   for quotations, the **bullet list** button for lists, and **Add Media** for pictures inside
   the text.
3. **Details** box:
   - **Standfirst (summary)** — 1–3 sentences shown under the headline and on cards.
   - **Author** — choose from your Authors (create them first under Authors, see B6).
   - **Main image** — the large picture at the top. (Alternatively set a *Featured image*
     in the right-hand column — either works.)
   - **Image description (alt)** — describe the image for blind readers and Google.
   - **Caption**, **Photo credit** — shown under the image.
   - **Key findings** — one per line; shown as a highlighted teal box. Leave empty to hide it.
   - **Keywords** — for SEO, one per line.
4. Right-hand column: tick a **Category** (create new ones with *+ Add New Category*); add
   **Tags** (shown as small labels at the bottom of the article).
5. **SEO** box (optional): custom Google title and description, and a **Social share image**
   used when the link is shared on LinkedIn/WhatsApp/Facebook.
6. **Publish** — or **schedule** it: in the Publish box, next to **Publish immediately**
   click **Edit**, set a future date and time, click **OK**, then click **Schedule**. It goes
   live automatically at that time.

## B5. Add a publication (journal, report, special edition)
1. **Publications → Add New**.
2. **Title**, and the **description** in the main text area.
3. **Details**: **Edition** (e.g. *Volume 2, Issue 1 — Spring 2027*), **Type**,
   **Cover image** (portrait), **PDF** (*Choose / upload* the PDF), **ISSN**,
   **Table of contents** (one entry per line), **Contributors** (hold **Ctrl**/**⌘** and
   click to select several Authors), **Articles in this publication** (same way),
   **Feature on homepage** (tick to make it the homepage highlight when there are no Hero
   Slides).
4. **Publish**. It appears on /publications with a **Download PDF** button.

## B6. Add a team member / author
1. **Authors → Add New**.
2. **Title** = the person's full name. **Main text area** = short bio (2–4 sentences).
3. **Details**: **Role** (e.g. *Editor-in-Chief*), **Photo** (square), **Links** (one URL
   per line).
4. **Publish**. They appear in **Our Team** on the About page (alphabetical), and can be
   chosen as an article Author or publication Contributor.
- To remove someone from the About page: move them to **Draft** (articles they wrote keep
  their byline) or **Trash**.

## B7. Change the homepage feature box (Hero Slide)
The large box on the right of the homepage shows the **first** Hero Slide.
1. **Hero Slides → Add New** (or edit the existing one).
2. **Title** = the headline in the box.
3. **Details**: **Image** (landscape), **Eyebrow label** (small orange text above the
   headline, e.g. *New Report*), **Link target** (where clicking goes, e.g.
   `/articles/my-new-article` or `/publications/annual-report-2027`), **Standfirst**,
   **Date shown**.
4. In the **Attributes** box on the right set **Order** — the slide with the **lowest number** is shown
   (e.g. 0 beats 1).
5. **Publish**.
- To show the featured publication instead, move all Hero Slides to Draft.

## B8. Edit the About page
**Pages → About Us → Edit.** Text you type in the main text area appears under the mission
quote. The mission quote itself comes from **Site Settings → Footer mission statement**; the
team list comes from **Authors**. The three short section texts (Research Articles /
Publications / Opportunities) are part of the design — see C-70 to change them.

## B9. Edit the Submit an Opportunity page
**Pages → Submit an Opportunity → Edit.** Text in the main area replaces the default
instructions next to the form. The form itself is built in.

## B10. Privacy Policy and Terms of Use
Both were created as **drafts** from a template. **Pages → Privacy Policy → Edit**: read it,
adjust anything that doesn't fit (ideally have it checked), then **Publish**. Do the same for
**Terms of Use**. Once published, the **Privacy** and **Terms** links appear in the footer
automatically (addresses /p/privacy and /p/terms).

## B11. Read and answer messages
1. **Messages** (with the number bubble). Click a subject to read it — it is then marked
   **Read**.
2. The sender's name and email are shown in the **From** box on the right. Click the email
   address to reply from your own email program.
3. Delete handled messages with **Trash** to keep the list tidy.
- Submissions through "Submit an Opportunity" arrive here too. To publish one, open
  **Jobs & Fellowships → Add New** (or Scholarships) and copy the details across.

## B12. Site-wide settings
- **Settings → General**: **Site Title** and **Tagline** (the tagline is the big homepage
  headline and appears under the logo). Click **Save Changes**.
- **Site Settings** (left menu):
  - **Logo** — square image shown in the header.
  - **Contact email** — shown on the site and where form notifications are sent.
  - **Footer mission statement** — used in the footer, homepage intro, About quote and as
    Google's description of the homepage.
  - **Social links** — one per line, written as `LinkedIn | https://linkedin.com/company/…`.
    They appear in the footer's **Connect** column.
  - Click **Save Changes**.

---

# PART C — Questions & answers (everything you might want to do)

### Jobs, fellowships, scholarships, grants
**C-1. How do I edit a job that's already live?** Jobs & Fellowships → hover the job → **Edit**
→ change → **Update**.

**C-2. A deadline was extended. What do I do?** Edit the item, change **Application
deadline**, click **Update**. If it had expired, it reappears in the lists immediately.

**C-3. The job has no deadline ("open until filled").** Leave **Application deadline**
empty and write *Open until filled* in **Deadline note**. It will stay listed until you
unpublish it.

**C-4. How do I remove a job early (position filled)?** Either set the deadline to
yesterday (it then shows as *Expired* and the page says closed — best for Google), or move it
to **Draft** (page disappears completely).

**C-5. Should I delete expired listings?** Not necessary. Keeping them keeps old links
working. If the list gets long, sort by **Deadline** and bulk-trash very old ones (e.g.
older than a year).

**C-6. How do I add a job that's similar to an existing one?** Hover the existing one →
**Duplicate**. A draft copy "(copy)" opens; change what's different, fix the title, then
**Publish**.

**C-7. How do I publish/unpublish many at once?** Tick them in the list → **Bulk actions**
→ **Publish** or **Unpublish (move to draft)** → **Apply**.

**C-8. A job is in the wrong country page.** Edit it → **Country / region** → choose the
right one → **Update**.

**C-9. I need a country/region that's not in the list (e.g. "South America").** This needs a
small code change (2 lines). Ask an AI with `ARCHITECTURE.md` (section 6, "Add a new
country/region") or a developer. Meanwhile choose the closest region.

**C-10. I need a new position type or level (e.g. "Lecturer", "Masters Grant").** Small code
change — see ARCHITECTURE.md §6 "Add a dropdown option".

**C-11. How do the filters on the Jobs page get their options?** Automatically from the
open listings. A new *Field / discipline* value appears as a filter as soon as a job using
it is published.

**C-12. Why does a job show "Closes today" / "3 days left" in red?** Deadlines within 14 days
are highlighted automatically.

**C-13. Why is a job visible on /jobs but not on /fellowships?** Only *Position types*
containing "Fellowship" appear on /fellowships.

**C-14. Why isn't a scholarship showing under Grants?** Only *Levels* containing "Grant"
appear on /grants.

**C-15. The job page shows the title twice.** The description you pasted starts with the
title. Delete that first line in the main text area.

**C-16. How do I see how many listings are open?** The homepage shows the live counts; the
Jobs page says "N open opportunities".

### Articles & publications
**C-20. How do I schedule an article for next Monday?** Edit → Publish box → **Publish
immediately: Edit** → set the date/time → **OK** → **Schedule**.

**C-21. How do I change an article's web address (URL)?** Under the title → *Permalink* → **Edit** → change → **OK** → **Update**. ⚠️
Changing the address of an article that's already been shared breaks old links; WordPress
usually redirects the old address, but avoid it when possible.

**C-22. How do I add a new category?** Articles → **Categories** → fill **Name** →
**Add New Category**. Categories appear as filter pills on /articles once an article uses them.

**C-23. How do I rename or delete a category?** Articles → Categories → hover → **Edit** or
**Delete** (articles are not deleted).

**C-24. How do I put an image inside an article?** Click in the text where it should go →
**Add Media** → **Upload files** (or pick from the Media Library) → fill **Alternative Text**
→ **Insert into article**.

**C-25. How do I embed a YouTube video?** Paste the YouTube link on its own line in the
article — it turns into a video player.

**C-26. How do I add a table?** Make the table in Word or Google Docs, copy it, and paste it
into the text box — the table is kept.

**C-27. How do I make text a link?** Select the text → click the **link (chain) button** (or
Ctrl/⌘+K) → paste the address → Enter.

**C-28. How do I replace a publication's PDF?** Edit the publication → **PDF** →
**Choose / upload** the new file → **Update**.

**C-29. How do I make a publication the homepage highlight?** Tick **Feature on homepage**.
(It shows only when there are no published Hero Slides.)

**C-30. How do I add an article to a publication's contents?** Edit the publication →
**Articles in this publication** → Ctrl/⌘-click to add it → **Update**.

**C-31. How do I show an author's photo?** Authors → edit → **Photo**.

### Homepage, pages, menus, design
**C-40. How do I change the big homepage headline?** It's the **Tagline**: Settings →
General → **Tagline** → **Save Changes**.

**C-41. How do I change the homepage paragraph under the headline?** Site Settings →
**Footer mission statement** (also changes the footer and About quote).

**C-42. How do I change the homepage feature box?** See B7.

**C-43. How do I change the logo?** Site Settings → **Logo** → Choose / upload → Save.

**C-44. How do I change the browser-tab icon (favicon)?** Appearance → Customize → **Site
Identity** → **Site Icon** → upload a square 512×512 image → Publish.

**C-45. How do I add a new simple page (e.g. "Partners", "FAQ")?** Pages → **Add New** →
title + content → **Publish**. Its address is shown under the title (e.g.
`/partners`, also available as `/p/partners`). To link it in the main menu or footer, see
C-46.

**C-46. How do I add, rename or remove a main-menu or footer item?** The menu is part of the
site's design code (it isn't in *Appearance → Menus*). Ask an AI using
`AI-HELP-PROMPT.md` + `ARCHITECTURE.md` (§5, `fr_nav_items()`), or a developer; it's a
5-minute change.

**C-47. How do I change colours or fonts?** Design change in `assets/app.css` — ask a
developer/AI (ARCHITECTURE.md §5 lists the colour codes).

**C-48. How do I change the social media links?** Site Settings → **Social links**.

**C-49. How do I change the contact email shown on the site?** Site Settings → **Contact
email**.

**C-50. How do I hide the "Our Team" section?** Move all Authors to **Draft**. The section
disappears from the About page; articles that already name them as Author keep showing the
name. (To choose a drafted person as a *new* article's Author, publish them again.)

**C-51. How do I change the site name?** Settings → General → **Site Title**.

### Messages & email
**C-60. Where do website messages go?** **Messages** in the admin (always), plus an email to
the Site Settings contact email (only if email sending is set up — see C-62).

**C-61. Someone says they sent a message, but I can't find it.** Check **Messages** →
**All** and **Trash**. The form refuses messages with more than 5 links or over 5,000
characters, and limits each person to 5 messages per 10 minutes (anti-spam).

**C-62. Emails from the website (notifications, password resets) don't arrive.** WordPress
needs an email service ("SMTP") to send reliably. One-time setup (≈10 minutes):
1. Create a mailbox for the domain — e.g. `noreply@frontierperspectives.com` — in
   **Hostinger hPanel → Emails** (most Hostinger plans include free email). Or use a
   Gmail account with an *App Password* (Google Account → Security → 2-Step Verification →
   App passwords).
2. In WordPress: Plugins → **Add New** → search **WP Mail SMTP** → **Install** →
   **Activate**.
3. Follow its setup wizard: choose **Other SMTP** (Hostinger: host `smtp.hostinger.com`,
   port `465`, encryption **SSL**, username = the full mailbox address, password = the
   mailbox password) or **Gmail**.
4. Use its **Email Test** tab to send yourself a test email.

**C-63. I'm getting spam through the form.** Delete it from Messages. If it becomes a lot,
install a CAPTCHA plugin (e.g. *Cloudflare Turnstile* via the "Simple Cloudflare Turnstile"
plugin) — ask a developer to connect it to the form.

### People & access
**C-70. How do I give a colleague/student access to add listings?** Users → **Add New** →
username, email, choose **Role: Editor** (can edit all content but not settings) or
**Author** (only their own articles) → **Add New User**. They receive a login link by email
(needs C-62), or set a password for them and share it privately.

**C-71. How do I remove someone's access?** Users → hover → **Delete** → choose to attribute
their content to another user → **Confirm Deletion**.

**C-72. How do I change my password?** Click your name (top right) → **Edit Profile** →
**Set New Password** → **Update Profile**.

**C-73. How do I change my email address?** Users → Profile → **Email** → Update. WordPress
emails a confirmation link to the new address.

**C-74. I'm locked out.** Wait 20 minutes (login lockout). If you've forgotten the password
and email doesn't work, log in to **Hostinger hPanel → WordPress → Overview → Login to
WordPress** (one-click login), then change your password (C-72).

### Google, SEO & sharing
**C-80. How do I control how an article looks in Google?** Edit → **SEO** box → **SEO
title** (≈60 characters) and **SEO description** (≈155 characters).

**C-81. How do I control the image when a link is shared on LinkedIn/WhatsApp?** SEO box →
**Social share image** (1200×630 px ideal).

**C-82. Are jobs shown in Google's job search?** Yes — every job page includes the
information Google for Jobs needs. Expired jobs are automatically marked as expired.

**C-83. How do I submit the site to Google?** Go to **Google Search Console**
(search.google.com/search-console) → **Add property** → *Domain* →
`frontierperspectives.com` → verify (Hostinger DNS) → **Sitemaps** → submit
`https://frontierperspectives.com/wp-sitemap.xml`.

**C-84. How do I see visitor numbers?** Install **Site Kit by Google** (Plugins → Add New),
connect Google Analytics, and visitor charts appear in the Dashboard.

**C-85. Google doesn't show the site at all.** Settings → **Reading** → make sure
**"Discourage search engines from indexing this site" is NOT ticked** → Save.

### Maintenance & safety
**C-90. Do I need to update anything?** WordPress security updates install themselves.
Once a month: **Dashboard → Updates** → update plugins → check the homepage, a job page and
the form still work.

**C-91. How do I make a backup?** Hostinger hPanel → **Websites → Backups** → create a
backup. Do this before big changes.

**C-92. How do I restore something I deleted?** Within 30 days: open the list (e.g. Jobs) →
**Trash** → hover → **Restore**. Older: restore a backup in Hostinger (C-91).

**C-93. How do I restore an older version of an article's text?** Edit → Publish box →
**Revisions: Browse** → drag the slider to the version → **Restore This Revision**.

**C-94. Should I install other plugins?** Only when needed and from reputable authors. Never
install page builders (Elementor etc.) — they're not needed and can conflict with the design.
Recommended: WP Mail SMTP (email), LiteSpeed Cache (speed), Site Kit (analytics).

**C-95. Can I change the theme?** Don't. The **Frontier** theme *is* the design. Switching
themes changes the whole look (content is not lost, and switching back restores it).

**C-96. Can I delete the "Frontier Perspectives Core" plugin?** **Never.** It contains all
the jobs, scholarships, articles etc. behaviour. Deactivating it makes those sections
disappear (data is kept and comes back when re-activated).

**C-97. The site shows "There has been a critical error".** Usually caused by a plugin update.
Check your email for WordPress's *recovery mode* link, or in Hostinger hPanel open the
**WordPress** section → Plugins and deactivate the most recently updated plugin. Then
contact a developer with `ARCHITECTURE.md`.

**C-98. The site looks unstyled / broken layout.** Refresh with Ctrl+F5. If it persists, a
caching plugin may be serving old files — LiteSpeed Cache → **Purge All**.

**C-99. Who has the technical details?** Everything is in the GitHub repository
`frontierperspectives-maker/Frontier-Perspectives` (folder `docs/`).

---

# PART D — Weekly / monthly routine (suggested)

**Weekly (15–30 min)**
- Check **Messages**; reply and trash handled ones.
- Add new jobs/scholarships (Duplicate saves time).
- Glance at the homepage.

**Monthly (15 min)**
- Dashboard → **Updates** (plugins).
- Sort Jobs and Scholarships by Deadline; bulk-trash listings expired > 6 months ago
  (optional).
- Check the Hero Slide is still current.

**Every 6 months**
- Make a Hostinger backup and download it to your computer.
- Review Users: remove people who no longer need access.
- Review the Privacy Policy and Terms dates.
