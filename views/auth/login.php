<?php View::partial('layouts/header', ['title' => $title]); ?>

<div class="auth-wrap">
    <div class="auth-card">
        <h2>Welcome back</h2>
        <p class="sub">Login to manage your teacher portfolio.</p>
        <?php View::partial('layouts/alerts'); ?>

        <form method="post" action="<?= Helpers::url('/login') ?>">
            <input type="hidden" name="_csrf" value="<?= Helpers::csrfToken() ?>">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" class="form-control" required value="<?= Helpers::e(Helpers::old('email')) ?>">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Login</button>
        </form>
        <div class="auth-footer-link">New here? <a href="<?= Helpers::url('/register') ?>">Create a free portfolio</a></div>
    </div>
</div>

<?php View::partial('layouts/footer'); ?>
