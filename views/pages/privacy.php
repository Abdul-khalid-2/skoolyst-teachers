<?php View::partial('layouts/header', compact('title', 'description', 'canonical')); ?>
<?php $supportEmail = Helpers::e(SUPPORT_EMAIL); ?>

<div class="legal-wrap">
    <article class="legal-card">
        <h1>Privacy Policy</h1>
        <p class="legal-updated">Last updated: 1 October 2026</p>

        <p>
            This Privacy Policy explains how Skoolyst Teachers (“we”, “us”), available at
            <a href="<?= Helpers::url('/') ?>"><?= Helpers::e(parse_url(BASE_URL, PHP_URL_HOST)) ?></a> and part of the
            Skoolyst family of services, collects, uses, shares and protects information when you create a teacher
            portfolio or browse the teacher directory.
        </p>

        <h2>1. Information we collect</h2>
        <h3>Account information</h3>
        <ul>
            <li><strong>Email sign-up:</strong> your full name, email address and password. Passwords are stored only as a secure one-way hash; we never see or store your actual password.</li>
            <li><strong>Continue with Google:</strong> with your permission, Google shares your name, email address, whether your email is verified, and your Google account ID. We request only the basic <code>openid</code>, <code>email</code> and <code>profile</code> scopes.</li>
            <li><strong>Login with Skoolyst:</strong> skoolyst.com shares your name, email address, whether your email is verified, and your Skoolyst account ID.</li>
        </ul>
        <p>
            We do <strong>not</strong> access your Gmail messages, Google Contacts, Google Drive, calendar or any other
            Google data beyond the basic profile described above, and we do not receive your Google or Skoolyst password.
        </p>

        <h3>Portfolio information you choose to add</h3>
        <p>
            Profile photo, profession, subject, qualification, city, country, phone number, gender, date of birth, bio,
            website, years of experience, education, work experience, skills, certifications, projects, languages,
            awards, services, social media links, and an optional résumé (PDF). All of these are optional except your name.
        </p>

        <h3>Activity information</h3>
        <ul>
            <li><strong>Profile views:</strong> a simple count of how many times your portfolio has been viewed.</li>
            <li><strong>Call requests:</strong> when a logged-in user taps “Call Me” on a portfolio, we record which user requested the phone number of which teacher, and when, so teachers can see their contact history.</li>
            <li><strong>Technical data:</strong> like any website, our servers keep standard logs (such as IP address, browser type and the pages requested) for security and troubleshooting.</li>
        </ul>

        <h2>2. How we use your information</h2>
        <ul>
            <li>To create and run your account and sign you in, including through Google or Skoolyst.</li>
            <li>To publish your portfolio and list it in the teacher directory, according to your visibility settings.</li>
            <li>To send service emails, such as a welcome email or a reminder to complete your profile. We do not send marketing emails.</li>
            <li>To keep the service secure, prevent abuse, and fix problems.</li>
        </ul>
        <p>We do not sell your personal information, and we do not use it for advertising profiles.</p>

        <h2>3. What is public</h2>
        <ul>
            <li>Your portfolio page and the details you add to it are <strong>public</strong> while your profile is set to visible. You can hide your portfolio from the public directory at any time in your dashboard settings.</li>
            <li>Your <strong>phone number</strong> is shown only to visitors who are logged in.</li>
            <li>Your <strong>résumé</strong> is available to everyone or only to logged-in users, depending on the setting you choose in your dashboard.</li>
            <li>Your email address and password are never shown on your public portfolio.</li>
        </ul>

        <h2>4. How we share information</h2>
        <p>We share information only in these cases:</p>
        <ul>
            <li><strong>Skoolyst email service</strong> (ads.skoolyst.com): your name and email address are used to deliver service emails to you.</li>
            <li><strong>Skoolyst Ads</strong> (ads.skoolyst.com): the directory home page may show an advertisement. We report only which ad was seen or clicked, not who you are. The ad image is loaded from ads.skoolyst.com, which can see your IP address in the normal way any website can.</li>
            <li><strong>Sign-in providers:</strong> Google and skoolyst.com process your sign-in when you choose to use them, under their own privacy policies.</li>
            <li><strong>Legal reasons:</strong> when required by law, or to protect the rights, safety and security of our users or the service.</li>
        </ul>

        <h2>5. Google user data</h2>
        <p>
            Skoolyst Teachers’ use and transfer to any other app of information received from Google APIs will adhere to the
            <a href="https://developers.google.com/terms/api-services-user-data-policy" target="_blank" rel="noopener">Google API Services User Data Policy</a>,
            including the Limited Use requirements. We use the information received from Google only to sign you in and to
            create or link your Skoolyst Teachers account. We do not use it for advertising, we do not sell it, and we do not
            transfer it to anyone except as needed to provide the service or as required by law.
        </p>

        <h2>6. Cookies</h2>
        <p>
            We use a single essential session cookie to keep you logged in and protect forms from forgery. We do not use
            analytics, advertising or tracking cookies.
        </p>

        <h2>7. Data retention and deletion</h2>
        <p>
            We keep your information for as long as your account exists. You can edit or remove most portfolio details at
            any time from your dashboard. To delete your account entirely, email us at
            <a href="mailto:<?= $supportEmail ?>"><?= $supportEmail ?></a> from the email address on your account. We will
            delete your account, portfolio, uploaded photo and résumé, and your link to Google or Skoolyst sign-in.
        </p>
        <p>
            You can also remove Skoolyst Teachers’ access to your Google account at any time from your
            <a href="https://myaccount.google.com/permissions" target="_blank" rel="noopener">Google Account permissions</a> page.
        </p>

        <h2>8. Security</h2>
        <p>
            We use HTTPS encryption, hashed passwords, and protection against cross-site request forgery. Sign-in secrets
            are kept only on our servers. No method of transmission or storage is completely secure, but we work to
            protect your information.
        </p>

        <h2>9. Children</h2>
        <p>
            Skoolyst Teachers is intended for teachers and other adults. You must be at least 18 years old to create an
            account. We do not knowingly collect personal information from children.
        </p>

        <h2>10. Your choices and rights</h2>
        <ul>
            <li>View and correct your information from your dashboard.</li>
            <li>Hide your portfolio from the public directory.</li>
            <li>Choose who can download your résumé.</li>
            <li>Request a copy or deletion of your data by contacting us.</li>
        </ul>

        <h2>11. Changes to this policy</h2>
        <p>
            We may update this policy from time to time. When we do, we will change the “Last updated” date above, and for
            significant changes we will let account holders know by email.
        </p>

        <h2>12. Contact us</h2>
        <p>
            Questions about this policy or your data? Email <a href="mailto:<?= $supportEmail ?>"><?= $supportEmail ?></a>.
        </p>

        <p class="legal-related">See also our <a href="<?= Helpers::url('/terms-of-service') ?>">Terms of Service</a>.</p>
    </article>
</div>

<?php View::partial('layouts/footer'); ?>
