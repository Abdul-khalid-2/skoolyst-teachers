<?php View::partial('layouts/header', ['title' => $title]); ?>

<div class="auth-wrap" style="min-height:60vh;">
    <div style="text-align:center; max-width:480px; margin:0 auto;">
        <div style="font-size:48px; color:var(--primary); margin-bottom:10px;"><i class="fa fa-lock"></i></div>
        <h2 style="margin:0 0 8px; color:var(--text-dark);">Please login first to download</h2>
        <p style="color:var(--text-muted); margin-bottom:8px;">
            <?= Helpers::e($teacher['full_name']) ?> has made their resume available to logged-in teachers only.
        </p>
        <p style="color:var(--text-muted); margin-bottom:24px;">
            You can log in to download it, or reach out directly by email instead.
        </p>
        <div style="display:flex; gap:12px; justify-content:center; flex-wrap:wrap;">
            <a href="<?= Helpers::url('/login') ?>" class="btn btn-primary"><i class="fa fa-sign-in-alt"></i> Login to download</a>
            <a href="mailto:<?= Helpers::e($teacher['email']) ?>" class="btn btn-outline"><i class="fa fa-envelope"></i> Email <?= Helpers::e($teacher['full_name']) ?> directly</a>
        </div>
        <p style="margin-top:26px;">
            <a href="<?= Helpers::url('/p/' . $teacher['slug']) ?>" style="color:var(--text-muted);"><i class="fa fa-arrow-left"></i> Back to portfolio</a>
        </p>
    </div>
</div>

<?php View::partial('layouts/footer'); ?>
