# Frontier Perspectives — Walkthrough Video Scripts

A complete production kit for a library of short "how-to" videos that let someone with no
technical background run the website. Each video has: the goal, what to prepare, a
**click-by-click shot list** (what to do on screen) and a **word-for-word voiceover**.

---

## 0. How to produce the videos

### 0.1 Recommended tools
| Need | Easiest option | Alternatives |
|---|---|---|
| Record screen + auto step-by-step video with **AI voiceover & captions** | **Guidde** (guidde.com, browser extension — records your clicks, writes the narration and generates an AI voice; you can paste the voiceover text below to replace its draft) | **Scribe** (step-by-step guides with screenshots), **Tango** |
| Record screen yourself | **Loom** (free, browser) or **OBS Studio** (free, desktop) | Windows: *Clipchamp* / Xbox Game Bar (Win+G); Mac: ⌘+Shift+5 |
| Edit + AI voice from a script | **Descript** (paste the voiceover; "AI voices"; edit video by editing text; removes "um"s) | **Clipchamp** (text-to-speech built in), **CapCut** |
| AI voice only | **ElevenLabs** (paste script → download MP3) | OpenAI TTS, Microsoft Clipchamp TTS |

**Simplest workflow:** install the Guidde extension → for each video below, click *Capture*,
perform the **Shot list** slowly → stop → in the editor replace the generated narration with
the **Voiceover** text → choose a voice → export / share link.

**Script-first workflow (best quality):** generate each voiceover as audio (ElevenLabs /
Descript), then record the screen while listening, or line up the clicks to the audio in
Descript/CapCut.

### 0.2 Recording checklist (do once)
- Record on the **staging site** (`staging.frontierperspectives.com/wp-admin`) or use clearly
  fake test content on the live site — and delete it afterwards.
- Browser window **1920×1080**, zoom **110–125%** so text is readable on phones.
- Close other tabs, hide bookmarks bar, turn off notifications.
- Use a test job/scholarship named "**DEMO – …**" so nothing real is affected.
- Keep each video **2–5 minutes**, one task per video. Speak (or generate) slowly.
- Add the video title as the first frame and "Next: …" as the last.
- After recording, delete any DEMO content (or move to Trash).

### 0.3 AI prompt to turn any section of the guide into a new script
If you need a video that isn't listed below, paste this into an AI together with
`ADMIN-GUIDE.md`:

> You are writing a screen-recording script for a WordPress admin walkthrough video for a
> non-technical university professor. Using ONLY the steps and labels in the attached
> ADMIN-GUIDE.md, write a script for: **[TOPIC]**. Output: (1) title, (2) goal in one
> sentence, (3) preparation, (4) a numbered shot list with the exact buttons/menus to click
> as they are named in the guide, (5) a friendly word-for-word voiceover (max 450 words,
> short sentences, no jargon, explain *why* as well as *how*), (6) 3–5 on-screen caption
> callouts. Do not invent menu items that aren't in the guide.

---

## Video list

| # | Title | Length |
|---|---|---|
| 01 | Welcome: how the website works | 4 min |
| 02 | Logging in, your password and staying safe | 3 min |
| 03 | A tour of the admin | 4 min |
| 04 | Adding a job | 5 min |
| 05 | Adding a fellowship | 2 min |
| 06 | Adding a scholarship or grant | 4 min |
| 07 | Deadlines: how listings expire automatically | 3 min |
| 08 | Editing, extending, duplicating and removing listings | 4 min |
| 09 | Writing and publishing an article | 6 min |
| 10 | Images, links, videos and tables inside articles | 4 min |
| 11 | Scheduling an article | 2 min |
| 12 | Categories and tags | 2 min |
| 13 | Publications (journals & reports with PDF) | 4 min |
| 14 | Authors and the "Our Team" section | 3 min |
| 15 | Changing the homepage feature box | 3 min |
| 16 | Logo, tagline, contact email, footer, social links | 3 min |
| 17 | Editing pages: About, Submit, Privacy, Terms, new pages | 4 min |
| 18 | Reading and handling messages | 3 min |
| 19 | Adding users and roles | 3 min |
| 20 | Google: SEO box, sharing images, Search Console | 4 min |
| 21 | Monthly maintenance: updates and backups | 4 min |
| 22 | Undo, restore and the Trash | 3 min |
| 23 | When something goes wrong (troubleshooting) | 4 min |
| 24 | Getting help from AI with the handover files | 3 min |

---

## 01 · Welcome: how the website works
**Goal:** understand the public site and how content flows from the admin to the website.
**Prepare:** homepage open on the public site.

**Shot list**
1. Homepage — slowly scroll from top to bottom.
2. Hover the menu: **Research Articles**, **Jobs** (open dropdown), **Fellowships**,
   **Scholarships**, **Grants**, **About**.
3. Click **Jobs** → **All Jobs**. Click a filter pill (e.g. a country), then **All**.
4. Open one job, scroll, hover **Apply Now**.
5. Go back; click **Submit Opportunity** in the header.
6. Show the footer.

**Voiceover**
"Welcome to the Frontier Perspectives website. In this short series you'll learn how to run
everything on the site yourself — no technical knowledge needed.
Let's start with what visitors see. At the top is the menu: research articles, jobs,
fellowships, scholarships, grants and the About page. The homepage shows our headline, the
latest articles and live counts of open opportunities.
Here's the Jobs page. Every job you add in the admin appears here automatically, sorted by
deadline — the soonest first. Visitors can filter by type, field, location and country with
these buttons. And here's the best part: when a deadline passes, the listing disappears from
these lists by itself. You never have to remember to take things down.
Each listing has its own page with the details and an Apply button that goes to the
official website.
Fellowships, scholarships and grants work the same way. Visitors can also send us
opportunities with the Submit Opportunity form — those arrive in your admin as messages.
In the next videos we'll go behind the scenes, starting with how to log in."

---

## 02 · Logging in, your password and staying safe
**Prepare:** logged out; a browser private window.

**Shot list**
1. Type `frontierperspectives.com/wp-admin` → login screen.
2. Show **Lost your password?** link (don't click).
3. Log in → Dashboard.
4. Top-right name → **Edit Profile** → scroll to **Account Management** → **Set New
   Password** (show, then **Cancel**).
5. Top-right → **Log Out**.

**Voiceover**
"To manage the website, go to frontierperspectives dot com slash wp-admin. Bookmark this
page. Enter your username or email and your password, and click Log In.
If you ever forget your password, click 'Lost your password?' and you'll get a reset link
by email.
For security, the site locks a login for twenty minutes after several wrong passwords — so
if you see that message, simply wait and try again.
To change your password, click your name at the top right, then Edit Profile. Scroll down to
Account Management and click Set New Password. WordPress suggests a strong password — you can
keep it or type your own, then click Update Profile.
A few safety tips: never share your password by email, give each helper their own login
instead of sharing yours, and always log out on shared computers — top right, Log Out."

---

## 03 · A tour of the admin
**Shot list**
1. Dashboard. Hover each left-menu item in order: Articles, Publications, Jobs &
   Fellowships, Scholarships & Grants, Authors, Hero Slides, Messages, Media, Pages, Users,
   Site Settings, Settings.
2. Click **Jobs & Fellowships** — show the table, **search box**, **Published/Drafts/Trash**
   links, the **Deadline** column and a red **Expired** badge.
3. Hover a row to show **Edit · Quick Edit · Trash · View · Duplicate**.
4. Click the site name in the top bar → public site → back to Dashboard.

**Voiceover**
"This is the admin dashboard. Everything is in the menu on the left.
Articles is for research articles, Publications for journals and reports, Jobs and
Fellowships and Scholarships and Grants for listings. Authors are the people on the About
page and article bylines. Hero Slides control the big box on the homepage. Messages is your
inbox from the website form. Media holds all uploaded images and PDFs, Pages has the simple
pages like About, and Site Settings holds your logo, contact email and social links.
Let's open Jobs and Fellowships. Every job is a row in this table. Use the search box to find
one, and these links to see only published items, drafts or the trash. The Deadline column
shows when each closes — a red 'Expired' label means the deadline has passed and the job is
already hidden from the public site.
Hover over a row to see the actions: edit, quick edit, trash, view the live page, and
duplicate — very handy for similar listings.
To jump to the website at any time, click the site name at the top left."

---

## 04 · Adding a job
**Prepare:** a real or demo job advert open in another tab; institution logo saved on your
computer.

**Shot list**
1. **Jobs & Fellowships → Add New**.
2. Type title "DEMO – Postdoctoral Researcher in Public Health".
3. Paste the description into the main text area; add one heading with **+ → Heading**.
4. Scroll to **Details**; fill **Institution**, upload **Institution logo** (Choose / upload
   → Upload files → Select Files → Choose file), **Location**, **Country / region**,
   **Remote OK** (leave unticked), **Position type** = Postdoc, **Field / discipline** =
   Health, **Salary / stipend**, **Application deadline** (pick a date 3 weeks ahead),
   **Application link**.
5. **Publish → Publish** → **View Job**.
6. Open `/jobs` — show the new job in the list and as a filter value.

**Voiceover**
"Let's add a job. Go to Jobs and Fellowships, then Add New.
First the title — the position name exactly as you'd like visitors to see it.
Below it is the main text area. Paste the full job description here. Formatting from Word or
the original website is kept. To add a heading, click the plus button and choose Heading.
Now scroll down to the Details box. Institution is required — that's the employer. Upload the
institution's logo: click Choose or upload, Upload files, select the image, and Choose file.
Location is free text, as you want it shown. Country or region decides which country page it
appears on. Tick Remote OK only if the job can be done remotely.
Position type matters: anything ending in 'Fellowship' also appears on the Fellowships page.
Field or discipline becomes a filter on the Jobs page automatically.
Salary is optional. Then the application deadline — pick the date. If there's no fixed
deadline, leave it empty and type something like 'Open until filled' in the Deadline note.
Finally, the application link — the official page where people apply.
Click Publish, and Publish again to confirm. That's it — the job is live. Here it is on the
Jobs page, sorted by deadline, with its field already available as a filter."

---

## 05 · Adding a fellowship
**Shot list:** Duplicate the DEMO job → change title to "DEMO – Visiting Research
Fellowship" → **Position type** = Visiting Fellowship → Publish → open `/fellowships` and the
**Fellowships → Visiting Fellowships** menu item.

**Voiceover**
"Fellowships are added exactly like jobs — the only difference is the Position type.
I'll save time by duplicating an existing listing: hover it and click Duplicate. A draft copy
opens. I change the title, and set Position type to one of the Fellowship types — here,
Visiting Fellowship. Publish.
Now it appears on the Fellowships page, and under Fellowships, Visiting Fellowships in the
menu. It also stays on the main Jobs page, because fellowships are a kind of academic
position."

---

## 06 · Adding a scholarship or grant
**Shot list**
1. **Scholarships & Grants → Add New**; title "DEMO – Global Masters Scholarship 2027".
2. Paste description.
3. Details: **Funder**, **Funder logo**, **Level / type** = BS/MS, **Country / region**,
   **Eligible regions** (two lines: "All nationalities", "Africa"), **Application deadline**,
   **Application link**.
4. Publish → `/scholarships` → click the **Region** filter "Africa".
5. Duplicate → change level to **Research Grant** → Publish → open `/grants`.

**Voiceover**
"Scholarships and grants live under Scholarships and Grants. Click Add New, give it a title
and paste the description.
In Details, the Funder is required — the organisation offering it — and you can upload their
logo.
Level or type decides where it appears: BS/MS, PhD and Post Doc are scholarships. Any level
with 'Grant' in its name also appears on the Grants page.
Eligible regions are who can apply — one per line. These become the Region filter for
visitors.
Deadline, deadline note and application link work just like jobs. Publish.
Here it is on the Scholarships page, and the Region filter already includes 'Africa'.
Now a grant: I'll duplicate it and change the level to Research Grant. After publishing, it
appears on the Grants page too."

---

## 07 · Deadlines: how listings expire automatically
**Shot list**
1. Edit a DEMO job → set deadline to **today** → Update → `/jobs` shows "Closes today".
2. Set deadline to **yesterday** → Update → `/jobs`: gone. Open its page URL directly →
   "This opportunity has closed" notice.
3. Admin list → red **Expired** badge.
4. Set deadline to next month → Update → back on `/jobs`.

**Voiceover**
"Here's how deadlines work, because it saves you a lot of work.
A listing stays on the website through the whole of its deadline day — visitors see 'Closes
today' in red. Listings closing within two weeks are highlighted too.
After midnight, the listing disappears from every public list automatically: jobs,
fellowships, country pages, search and the homepage counts.
But nothing is deleted. If someone has an old link, the page still opens and clearly says
the opportunity has closed. In the admin you'll see a red Expired label.
If a deadline is extended, just change the date and click Update — it's back immediately.
And for listings with no fixed date, leave the deadline empty and use the Deadline note
instead; they stay listed until you unpublish them."

---

## 08 · Editing, extending, duplicating and removing listings
**Shot list:** Edit → change salary → Update · Quick Edit (title/status) · Duplicate ·
Switch to draft · tick 3 rows → **Bulk actions → Unpublish (move to draft)** → Apply ·
Trash → **Trash** link → **Restore**.

**Voiceover**
"To change a listing, hover it and click Edit, make the change, and click Update.
For quick fixes to the title or status, use Quick Edit right in the list.
Duplicate is perfect when an institution posts several similar positions.
To take something offline without deleting it, switch it to draft. To do many at once, tick
them, choose Unpublish in Bulk actions, and click Apply.
Deleting moves an item to the Trash. You can restore it from the Trash for thirty days.
A tip: for filled positions, it's better to set the deadline to yesterday than to delete —
old links keep working and Google is told the job has closed."

---

## 09 · Writing and publishing an article
**Shot list**
1. **Articles → Add New**; title; type 2 paragraphs; **+ → Heading**; **+ → Quote**;
   **+ → List**.
2. Details: **Standfirst**, **Author**, **Main image** + **Image description (alt)**,
   **Caption**, **Photo credit**, **Key findings** (3 lines).
3. Sidebar: tick a **Category**, add 2 **Tags**.
4. **Preview** → **Publish** → view article.

**Voiceover**
"Let's publish a research article. Articles, Add New.
Type the headline as the title, then write or paste the article. Use the plus button to add
headings for sections, quotes, and bullet lists — this makes long articles easy to read and
helps Google understand them.
Below, in Details: the standfirst is a one-to-three sentence summary shown under the headline
and on article cards. Choose the author. Add a main image — then describe it in the 'alt' box
for blind readers and search engines — plus a caption and photo credit if needed.
Key findings appear as a highlighted box at the top. One finding per line. Leave it empty if
you don't want the box.
On the right, tick a category, and add a few tags.
Click Preview to check it, then Publish. Here it is, live, with the reading time calculated
automatically."

---

## 10 · Images, links, videos and tables inside articles
**Shot list:** + → Image → Upload → add Alt text · select text → link icon → paste URL ·
paste a YouTube URL on its own line · + → Table (3×3).

**Voiceover**
"Inside an article you can add more than text. Click plus, Image, and upload a picture — add
its alt text in the right sidebar.
To make a link, select the words, click the link icon, and paste the address.
To embed a video, just paste the YouTube link on its own line; it becomes a player.
And for data, plus, Table, choose the columns and rows, and type in the cells."

---

## 11 · Scheduling an article
**Shot list:** Edit a draft → sidebar **Publish: Immediately** → pick next Monday 9:00 →
**Schedule** → Articles list shows **Scheduled**.

**Voiceover**
"You can prepare articles in advance. In the sidebar, click 'Publish: Immediately', choose
the date and time, and the button changes to Schedule. Click it, and the article goes live
by itself at that moment. In the list it shows as Scheduled."

---

## 12 · Categories and tags
**Shot list:** Articles → **Categories** → add "Climate" → Articles list → `/articles`
filter pills → `?category=` filter.

**Voiceover**
"Categories group your articles — like Climate, Economy or Education — and appear as filter
buttons on the Research Articles page once an article uses them. Add or rename them under
Articles, Categories. Tags are smaller labels shown at the end of an article. Use a handful
of categories, and tags freely."

---

## 13 · Publications
**Shot list:** **Publications → Add New** → title, description → Details: **Edition**,
**Type**, **Cover image**, **PDF** (upload), **ISSN**, **Table of contents** (lines),
**Contributors** (Ctrl/⌘-click two authors), **Articles in this publication**, **Feature on
homepage** → Publish → view page → **Download PDF**.

**Voiceover**
"Publications are your journals, reports and special editions. Add New, give it a title and
a description.
In Details, set the edition — for example Volume 2, Issue 1 — and the type. Upload a portrait
cover image and the PDF itself. Add the ISSN if it has one, and the table of contents, one
entry per line.
Contributors and articles are chosen from lists: hold Control, or Command on a Mac, to select
several.
Tick 'Feature on homepage' to make it the homepage highlight when there's no hero slide.
Publish — visitors can now read the description and download the PDF."

---

## 14 · Authors and the "Our Team" section
**Shot list:** Authors → Add New → name, bio, Role, Photo → Publish → `/about` Our Team →
move to Draft → gone from About.

**Voiceover**
"Authors are the people behind the site. Each author has a name, a short bio, a role and a
photo. Published authors appear in the Our Team section of the About page, and can be picked
as an article's author or a publication's contributor. To remove someone from the About page,
switch them to draft — their past articles keep their name."

---

## 15 · Changing the homepage feature box
**Shot list:** Hero Slides → edit → Title, **Image**, **Eyebrow label**, **Link target**
(`/articles/…`), **Order** = 0 → Update → homepage.

**Voiceover**
"The big box on the right of the homepage is controlled by Hero Slides. It shows one slide —
the one with the lowest Order number.
Edit it: the title is the headline, the eyebrow is the small orange label above it, and the
link target is where it goes when clicked — for example slash articles slash the article's
address. Upload a wide image and click Update. To feature a publication instead, switch all
hero slides to draft and tick 'Feature on homepage' on the publication."

---

## 16 · Logo, tagline, contact email, footer, social links
**Shot list:** Settings → General → Tagline → Save · Site Settings → Logo, Contact email,
Footer mission statement, Social links (`LinkedIn | https://…`) → Save → homepage + footer.

**Voiceover**
"Site-wide details are in two places. Under Settings, General you'll find the Site Title and
the Tagline — the tagline is the big headline on the homepage.
Everything else is under Site Settings: the logo, the contact email shown on the site, the
mission statement used in the footer, homepage and About page, and your social media links —
one per line, the name, a vertical bar, then the address. Save, and the whole site updates."

---

## 17 · Editing pages: About, Submit, Privacy, Terms, new pages
**Shot list:** Pages → About Us → add a paragraph → Update → `/about` · Pages → Privacy
Policy (draft) → read → Publish → footer shows **Privacy** · Pages → Add New "FAQ" → Publish
→ view.

**Voiceover**
"Simple pages are under Pages. On the About page, any text you add appears under the mission
quote.
The Privacy Policy and Terms of Use were prepared as drafts. Read them, adjust anything that
doesn't fit — ideally have them checked — and publish. The links appear in the footer
automatically.
You can also create new pages, like an FAQ, with Add New. Note: adding a page to the main menu
is a small design change — see the help video on getting help from AI."

---

## 18 · Reading and handling messages
**Shot list:** public `/submit-opportunity` → send a DEMO message → admin **Messages**
bubble → open → From box → click email → Trash.

**Voiceover**
"When someone uses the Submit Opportunity form, the message lands in Messages — the number
shows how many are unread. Click a message to read it; the sender's email is on the right —
click it to reply from your email. When it's an opportunity worth listing, add it under Jobs or
Scholarships, then trash the message.
The form has built-in spam protection, so you'll rarely see junk."

---

## 19 · Adding users and roles
**Shot list:** Users → Add New → fill → Role **Editor** → Add New User · hover → Delete.

**Voiceover**
"Never share your own password. Instead, give each helper their own account: Users, Add New,
fill in their username and email, and choose a role. Editor can manage all content but not the
site settings — ideal for a research assistant. Author can only write their own articles. When
someone leaves, delete their account and assign their content to you."

---

## 20 · Google: SEO box, sharing images, Search Console
**Shot list:** article → **SEO** box (title, description, social share image) · Settings →
Reading → "Discourage search engines" unticked · Search Console → Sitemaps →
`wp-sitemap.xml`.

**Voiceover**
"The site is already set up for Google: page titles, descriptions, a sitemap, and special job
information so listings can appear in Google's job search.
For important articles you can fine-tune how they appear: in the SEO box, write a title of
about sixty characters and a description of about one hundred and fifty. The social share
image is what LinkedIn or WhatsApp shows when someone shares the link.
One setting to know: under Settings, Reading, 'Discourage search engines' must stay unticked,
or Google will hide the site.
Finally, connect Google Search Console and submit the sitemap — the address is your domain
slash wp-sitemap dot xml."

---

## 21 · Monthly maintenance: updates and backups
**Shot list:** Dashboard → Updates → update one plugin → check homepage/job/form · Hostinger
hPanel → Websites → Backups → create backup.

**Voiceover**
"Once a month, spend ten minutes on maintenance. Security updates for WordPress install
themselves, but check Dashboard, Updates for plugin updates. Update them one at a time, and
after each, check the homepage, a job page and the form.
Every six months, make a backup in Hostinger: in hPanel, Websites, Backups, create a backup,
and download a copy to your computer."

---

## 22 · Undo, restore and the Trash
**Shot list:** edit article → change text → **Undo** · sidebar **Revisions** → slider →
**Restore** · list → Trash → Restore.

**Voiceover**
"Mistakes are easy to fix. While editing, the undo arrow reverses recent changes. For older
changes, open Revisions in the sidebar, slide back to an earlier version, and restore it. And
anything deleted sits in the Trash for thirty days — open Trash and click Restore."

---

## 23 · When something goes wrong
**Shot list:** show Ctrl+F5 · Messages vs email (C-62) · Hostinger one-click WordPress login.

**Voiceover**
"If a change doesn't appear, refresh with Control plus F5 — browsers sometimes show an old
copy.
If website emails don't arrive, the messages are still safe in the Messages section; email
sending needs a one-time setup described in the guide.
If you're locked out, wait twenty minutes, or log in through Hostinger: in hPanel, open
WordPress and click Login. If you ever see 'critical error', it's usually a plugin update —
deactivate the latest one in Hostinger, and contact a developer with the architecture file."

---

## 24 · Getting help from AI with the handover files
**Shot list:** open the `docs` folder (GitHub) → `AI-HELP-PROMPT.md` → copy prompt → open
ChatGPT/Claude → attach `ADMIN-GUIDE.md` + `ARCHITECTURE.md` → paste prompt + question
("How do I add 'Lecturer' as a position type?") → show answer.

**Voiceover**
"For anything not covered in these videos, you have a technical handbook written for AI
assistants. Open the docs folder, copy the prompt from the AI help file, then open ChatGPT,
Claude or Gemini, attach the admin guide and the architecture file, paste the prompt and ask
your question in plain English. The assistant will tell you exactly which buttons to click —
or, if it needs a small code change, exactly what a developer should do."
