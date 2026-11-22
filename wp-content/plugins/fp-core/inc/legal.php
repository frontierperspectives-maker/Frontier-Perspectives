<?php
/**
 * Creates DRAFT "Privacy Policy" and "Terms of Use" pages (slugs privacy, terms) if
 * they don't exist yet. They are templates to review — not legal advice. Once
 * published, the footer links to /p/privacy and /p/terms appear automatically.
 */
defined('ABSPATH') || exit;

function fp_create_legal_drafts(): void {
    $email = fp_setting('contact_email') ?: get_option('admin_email');
    $site  = get_bloginfo('name') ?: 'Frontier Perspectives';
    $date  = wp_date('F j, Y');

    $pages = [
        'privacy' => ['Privacy Policy', <<<HTML
<p><em>Last updated: {$date}</em></p>
<p>This policy explains what information {$site} ("we", "us") collects when you use this website, and how we use it.</p>
<h2>Information we collect</h2>
<ul>
<li><strong>Information you send us.</strong> When you use our contact or "Submit an Opportunity" form, we receive your name, email address, subject and message.</li>
<li><strong>Technical information.</strong> Like most websites, our hosting provider automatically records basic technical data such as your IP address, browser type and the pages you visit, for security and to keep the site running.</li>
<li><strong>Cookies.</strong> Visitors who only read the site do not receive tracking cookies from us. Site administrators receive login cookies when they sign in.</li>
</ul>
<h2>How we use it</h2>
<ul>
<li>To reply to your message and review opportunities you submit.</li>
<li>To protect the website against spam and abuse (for example, limiting repeated form submissions or login attempts).</li>
</ul>
<p>We do not sell your personal information or use it for advertising.</p>
<h2>How long we keep it</h2>
<p>Messages are kept only as long as needed to handle your enquiry, and are deleted when no longer needed.</p>
<h2>Links to other websites</h2>
<p>Job, fellowship, scholarship and grant listings link to the websites of the institutions and funders that offer them. Those websites have their own privacy policies, which we do not control.</p>
<h2>Your rights</h2>
<p>You can ask us to tell you what information we hold about you, to correct it, or to delete it, by emailing <a href="mailto:{$email}">{$email}</a>.</p>
<h2>Changes to this policy</h2>
<p>We may update this policy from time to time. The date at the top shows when it was last changed.</p>
<h2>Contact</h2>
<p>Questions about this policy: <a href="mailto:{$email}">{$email}</a>.</p>
HTML],
        'terms' => ['Terms of Use', <<<HTML
<p><em>Last updated: {$date}</em></p>
<p>By using the {$site} website you agree to these terms.</p>
<h2>About the listings</h2>
<p>We publish information about jobs, fellowships, scholarships and grants offered by other organisations. We try to keep it accurate and up to date, but details such as eligibility, funding and deadlines can change without notice. <strong>Always check the official website of the institution or funder before applying.</strong> We are not involved in any application or selection process, and we never charge applicants a fee.</p>
<h2>Research articles and publications</h2>
<p>Articles and publications are provided for information only and do not constitute professional, legal or financial advice. Unless stated otherwise, the content on this website is the property of {$site}. You may quote short extracts with a clear credit and a link back to the original page.</p>
<h2>Submitting an opportunity</h2>
<p>When you submit an opportunity, you confirm that the information is accurate to the best of your knowledge. We may edit, decline or remove any submission at our discretion.</p>
<h2>External links</h2>
<p>We link to third-party websites for your convenience. We are not responsible for their content, availability or privacy practices.</p>
<h2>Limitation of liability</h2>
<p>The website is provided "as is". To the extent permitted by law, we are not liable for any loss arising from your use of the website or reliance on its content.</p>
<h2>Changes</h2>
<p>We may update these terms from time to time. Continued use of the website means you accept the updated terms.</p>
<h2>Contact</h2>
<p>Questions about these terms: <a href="mailto:{$email}">{$email}</a>.</p>
HTML],
    ];

    foreach ($pages as $slug => [$title, $html]) {
        if (get_page_by_path($slug, OBJECT, 'page')) continue;
        wp_insert_post([
            'post_type'    => 'page',
            'post_status'  => 'draft',
            'post_title'   => $title,
            'post_name'    => $slug,
            'post_content' => $html,
        ]);
    }
}

add_action('init', function () {
    if (get_option('fp_legal_drafts_created')) return;
    fp_create_legal_drafts();
    update_option('fp_legal_drafts_created', 1, false);
}, 100);
