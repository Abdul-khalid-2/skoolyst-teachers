<?php View::partial('layouts/header', compact('title', 'description', 'canonical')); ?>
<?php $supportEmail = Helpers::e(SUPPORT_EMAIL); ?>

<div class="legal-wrap">
    <article class="legal-card">
        <h1>Terms of Service</h1>
        <p class="legal-updated">Last updated: 1 October 2026</p>

        <p>
            These Terms of Service (“Terms”) govern your use of Skoolyst Teachers (“we”, “us”), available at
            <a href="<?= Helpers::url('/') ?>"><?= Helpers::e(parse_url(BASE_URL, PHP_URL_HOST)) ?></a>. By creating an account
            or using the service, including by signing in with Google or Skoolyst, you agree to these Terms and to our
            <a href="<?= Helpers::url('/privacy-policy') ?>">Privacy Policy</a>. If you do not agree, please do not use the service.
        </p>

        <h2>1. The service</h2>
        <p>
            Skoolyst Teachers lets teachers build a professional online portfolio and résumé, and lets schools, parents and
            students browse a public directory of teachers and contact them. The service is currently free to use.
        </p>

        <h2>2. Your account</h2>
        <ul>
            <li>You must be at least 18 years old to create an account.</li>
            <li>The information you give us must be accurate, and you may create an account only for yourself.</li>
            <li>You are responsible for keeping your password and your Google or Skoolyst sign-in secure, and for all activity under your account.</li>
            <li>Tell us promptly at <a href="mailto:<?= $supportEmail ?>"><?= $supportEmail ?></a> if you believe your account has been accessed without your permission.</li>
        </ul>

        <h2>3. Your content</h2>
        <ul>
            <li>You keep ownership of everything you add to your portfolio: text, photos, résumé and links.</li>
            <li>You give us a non-exclusive, worldwide, royalty-free permission to host, display and share that content as part of the service: on your public portfolio, in the directory, and in search engine results. This permission ends when you remove the content or delete your account.</li>
            <li>You confirm that your content is truthful and that you have the right to share it, including any photos you upload.</li>
        </ul>

        <h2>4. Acceptable use</h2>
        <p>You agree not to:</p>
        <ul>
            <li>List false or misleading qualifications, experience, certificates or awards.</li>
            <li>Impersonate another person or create an account on someone else’s behalf without their permission.</li>
            <li>Use contact details found on the service to harass, spam or market to teachers.</li>
            <li>Upload anything unlawful, offensive, or that infringes someone else’s rights.</li>
            <li>Scrape, copy or harvest data from the directory in bulk, or interfere with the service’s security or operation.</li>
        </ul>

        <h2>5. Directory and contacting teachers</h2>
        <p>
            Teachers write their own portfolios. We do not verify qualifications, identity or background, and we do not
            employ, recommend or endorse any teacher. Anyone hiring or engaging a teacher is responsible for their own
            checks. Any arrangement between a teacher and a school, parent or student is solely between them; we are
            not a party to it.
        </p>

        <h2>6. Advertising</h2>
        <p>
            Parts of the service may show advertisements provided through Skoolyst Ads. We are not responsible for
            the products, services or websites that advertisers offer.
        </p>

        <h2>7. Suspension and termination</h2>
        <p>
            You may stop using the service at any time and ask us to delete your account. We may hide, suspend or remove
            a portfolio or account that breaks these Terms, is reported as inaccurate or harmful, or puts the service or
            other users at risk.
        </p>

        <h2>8. Disclaimers</h2>
        <p>
            The service is provided “as is” and “as available”. We do our best to keep it running and accurate, but we
            do not guarantee that it will be uninterrupted, error-free, or that information in portfolios is correct.
        </p>

        <h2>9. Limitation of liability</h2>
        <p>
            To the extent permitted by law, Skoolyst Teachers is not liable for any indirect or consequential loss, or for
            any loss arising from content written by teachers or from dealings between users of the service.
        </p>

        <h2>10. Changes to these Terms</h2>
        <p>
            We may update these Terms from time to time. When we do, we will change the “Last updated” date above, and for
            significant changes we will let account holders know by email. Continuing to use the service after a change
            means you accept the updated Terms.
        </p>

        <h2>11. Governing law</h2>
        <p>These Terms are governed by the laws of Pakistan.</p>

        <h2>12. Contact us</h2>
        <p>Questions about these Terms? Email <a href="mailto:<?= $supportEmail ?>"><?= $supportEmail ?></a>.</p>

        <p class="legal-related">See also our <a href="<?= Helpers::url('/privacy-policy') ?>">Privacy Policy</a>.</p>
    </article>
</div>

<?php View::partial('layouts/footer'); ?>
