<?php
/** /submit-opportunity — posts to /contact exactly like the old site. */
get_header();
the_post();
$email = fp_setting('contact_email');
?>
<div class="container py-4">
  <div style="width:48px;height:4px;background:var(--gold);border-radius:2px;" class="mb-3"></div>
  <h1 class="text-navy fw-bold"><?php the_title(); ?></h1>
  <p class="text-muted2">Tell us about a job, fellowship, scholarship, or grant and we'll review it for listing.</p>

  <div class="row g-4 bg-mist rounded-4 p-4 mt-3">
    <div class="col-md-6">
      <h2 class="text-navy fs-3">Submit Opportunity</h2>
      <?php if (trim(get_the_content())): the_content(); else: ?>
        <p class="text-muted2 mt-2">Include the position/scholarship title, host institution, application link, and deadline (or note if it's open until filled). Our team reviews every submission before it goes live.</p>
      <?php endif; ?>
      <?php if ($email): ?><p class="text-muted2 mt-2" style="font-size:14px;">Prefer email? Write to <a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a></p><?php endif; ?>
    </div>
    <div class="col-md-6">
      <div id="form-msg" aria-live="polite"></div>
      <form id="submit-form" action="<?php echo fr_url('/contact'); ?>" method="post">
        <div class="row g-3">
          <div class="col-6"><label class="form-label" for="f-name">Name</label><input id="f-name" type="text" class="form-control" name="name" required></div>
          <div class="col-6"><label class="form-label" for="f-email">Email</label><input id="f-email" type="email" class="form-control" name="email" required></div>
        </div>
        <div class="mt-3"><label class="form-label" for="f-subject">Subject</label><input id="f-subject" type="text" class="form-control" name="subject" value="Opportunity submission" required></div>
        <div class="mt-3">
          <label class="form-label" for="f-message">Message</label>
          <div class="text-muted2 mb-1" style="font-size:12px;">Include the title, institution, application link, and deadline (or "open until filled").</div>
          <textarea id="f-message" class="form-control" name="message" rows="5" required placeholder="e.g. Postdoctoral Research Fellow — Climate Policy, University of Cambridge, apply at https://…, deadline 30 Sept 2026"></textarea>
        </div>
        <input type="text" name="website" tabindex="-1" autocomplete="off" aria-hidden="true" style="position:absolute;left:-9999px;">
        <button type="submit" class="btn btn-gold rounded-pill mt-3">Send message</button>
      </form>
    </div>
  </div>
</div>
<script>
document.getElementById('submit-form').addEventListener('submit', async function (e) {
  e.preventDefault();
  var form = e.target, btn = form.querySelector('button'), msg = document.getElementById('form-msg');
  btn.disabled = true; btn.textContent = 'Sending…';
  try {
    var res = await fetch(form.action, { method: 'POST', body: new FormData(form) });
    var json = await res.json();
    if (!res.ok) throw new Error(json.error || 'Something went wrong.');
    msg.innerHTML = '<div class="alert alert-success">Thanks — your submission was sent. We will review it shortly.</div>';
    form.reset();
  } catch (err) {
    var d = document.createElement('div'); d.className = 'alert alert-danger'; d.textContent = err.message;
    msg.replaceChildren(d);
  } finally {
    btn.disabled = false; btn.textContent = 'Send message';
  }
});
</script>
<?php get_footer();
