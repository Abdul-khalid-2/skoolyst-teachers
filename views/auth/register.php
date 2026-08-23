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
                    <input type="password" name="password" class="form-control" required minlength="8">
                </div>
                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" name="password_confirmation" class="form-control" required minlength="8">
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Create Free Portfolio</button>
        </form>
        <div class="auth-footer-link">Already have an account? <a href="<?= Helpers::url('/login') ?>">Login</a></div>
    </div>
</div>

<?php View::partial('layouts/footer'); ?>
