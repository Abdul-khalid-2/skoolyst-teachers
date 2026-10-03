<?php View::partial('layouts/header', ['title' => $title, 'robots' => 'noindex, follow']); ?>

<div class="auth-wrap">
    <div class="auth-card">
        <h2>Create your portfolio</h2>
        <p class="sub">It's free — set up your profile, then share your unique link.</p>
        <?php View::partial('layouts/alerts'); ?>

        <form method="post" action="<?= Helpers::url('/register') ?>">
            <input type="hidden" name="_csrf" value="<?= Helpers::csrfToken() ?>">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="full_name" class="form-control" required value="<?= Helpers::e(Helpers::old('full_name')) ?>" placeholder="e.g. Ayesha Khan">
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" class="form-control" required value="<?= Helpers::e(Helpers::old('email')) ?>" placeholder="you@example.com">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Password</label>
                    <div class="password-field">
                        <input type="password" name="password" class="form-control" required minlength="8">
                        <button type="button" class="password-toggle" aria-label="Show password" aria-pressed="false"><i class="fa fa-eye" aria-hidden="true"></i></button>
                    </div>
                </div>
                <div class="form-group">
                    <label>Confirm Password</label>
                    <div class="password-field">
                        <input type="password" name="password_confirmation" class="form-control" required minlength="8">
                        <button type="button" class="password-toggle" aria-label="Show password" aria-pressed="false"><i class="fa fa-eye" aria-hidden="true"></i></button>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Create Free Portfolio</button>
        </form>
        <div class="auth-divider"><span>or</span></div>
        <a href="<?= Helpers::url('/auth/skoolyst') ?>" class="btn btn-skoolyst btn-block">
            <img src="<?= Helpers::asset('image/favicon/favicon-32.png') ?>" alt="" width="20" height="20">
            Continue with Skoolyst
        </a>
        <a href="<?= Helpers::url('/auth/google') ?>" class="btn btn-skoolyst btn-google btn-block">
            <svg width="20" height="20" viewBox="0 0 48 48" aria-hidden="true"><path fill="#FFC107" d="M43.6 20.5H42V20H24v8h11.3C33.7 32.7 29.2 36 24 36c-6.6 0-12-5.4-12-12s5.4-12 12-12c3.1 0 5.8 1.2 7.9 3.1l5.7-5.7C34 6.1 29.3 4 24 4 12.9 4 4 12.9 4 24s8.9 20 20 20 20-8.9 20-20c0-1.3-.1-2.4-.4-3.5z"/><path fill="#FF3D00" d="m6.3 14.7 6.6 4.8C14.7 15.1 19 12 24 12c3.1 0 5.8 1.2 7.9 3.1l5.7-5.7C34 6.1 29.3 4 24 4 16.3 4 9.7 8.3 6.3 14.7z"/><path fill="#4CAF50" d="M24 44c5.2 0 9.9-2 13.4-5.2l-6.2-5.2C29.2 35.1 26.7 36 24 36c-5.2 0-9.6-3.3-11.3-7.9l-6.5 5C9.5 39.6 16.2 44 24 44z"/><path fill="#1976D2" d="M43.6 20.5H42V20H24v8h11.3c-.8 2.2-2.2 4.2-4.1 5.6l6.2 5.2C37 39.2 44 34 44 24c0-1.3-.1-2.4-.4-3.5z"/></svg>
            Continue with Google
        </a>
        <p class="auth-legal">
            By continuing, you agree to our <a href="<?= Helpers::url('/terms-of-service') ?>">Terms of Service</a>
            and <a href="<?= Helpers::url('/privacy-policy') ?>">Privacy Policy</a>.
        </p>
        <div class="auth-footer-link">Already have an account? <a href="<?= Helpers::url('/login') ?>">Login</a></div>
    </div>
</div>

<?php View::partial('layouts/footer'); ?>
