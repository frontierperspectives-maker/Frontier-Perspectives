# Getting help from an AI assistant

Use this whenever you want to change something on the website and aren't sure how.

## Steps
1. Open ChatGPT (chatgpt.com), Claude (claude.ai) or Gemini (gemini.google.com).
2. **Attach** these two files from the `docs` folder: `ADMIN-GUIDE.md` and `ARCHITECTURE.md`.
   (Download them from GitHub: open the file → *Download raw file* button.)
3. **Copy everything in the box below**, paste it into the chat, and replace the last line
   with your question.

```
You are helping the owner of the Frontier Perspectives website (a WordPress site). The owner
is a university professor with no technical background. I have attached two files:
ADMIN-GUIDE.md (how to use the admin, written for the owner) and ARCHITECTURE.md (exactly how
the site is built, for developers and AI).

Rules for your answer:
1. First decide: can this be done in the WordPress admin, or does it need a code change?
   Say which, in one sentence.
2. If it can be done in the admin: give numbered, click-by-click steps using the exact menu
   names, button labels and field names from the attached files. Do not invent menus or
   plugins that are not mentioned there. Mention anything that could go wrong.
3. If it needs a code change: explain in plain English why, then give step-by-step
   instructions a developer (or I) can follow: which file in the GitHub repository to edit
   (exact path), the exact code to add or change, how to upload it via Hostinger File Manager,
   which version number to bump (see ARCHITECTURE.md section 6), and how to check it worked.
   Remind me to make a backup first.
4. Never suggest installing a page builder, changing the theme, or deactivating the
   "Frontier Perspectives Core" plugin.
5. If my question is unclear, ask me one short clarifying question first.

My question: [WRITE YOUR QUESTION HERE]
```

## Example questions
- "How do I add a scholarship that has no deadline?"
- "A university emailed that their job closes a week later — what do I change?"
- "I want 'Lecturer' as a new position type."
- "How do I add a 'Partners' page to the main menu?"
- "Can visitors subscribe to an email alert for new jobs?"
- "Change the orange colour to a darker shade."
- "The website emails aren't arriving. What do I do?"
- "Give a research assistant access to add jobs but not change settings."
