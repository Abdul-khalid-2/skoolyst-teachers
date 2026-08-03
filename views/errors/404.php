<?php View::partial('layouts/header', ['title' => 'Page Not Found']); ?>

<div class="auth-wrap" style="min-height:60vh;">
    <div style="text-align:center;">
        <div style="font-size:80px;font-weight:800;color:var(--primary);line-height:1;">404</div>
        <h2 style="margin:14px 0 8px;color:var(--text-dark);">Page not found</h2>
        <p style="color:var(--text-muted);margin-bottom:24px;">The page or portfolio you're looking for doesn't exist or isn't public.</p>
        <a href="<?= Helpers::url('/') ?>" class="btn btn-primary">Back to Home</a>
    </div>
</div>

<?php View::partial('layouts/footer'); ?>
