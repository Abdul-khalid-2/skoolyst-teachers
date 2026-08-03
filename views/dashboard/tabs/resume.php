<div class="dash-card">
    <h3>Resume / CV</h3>
    <p class="desc">
        By default, visitors can download an auto-generated resume built from the sections you filled in
        (Education, Experience, Skills, etc). You can optionally upload your own PDF instead — if uploaded,
        your PDF will be served when someone clicks "Download Resume" on your portfolio.
    </p>

    <?php if (!empty($user['resume_file'])): ?>
        <div class="alert alert-success">
            <i class="fa fa-file-pdf"></i> A custom resume PDF is currently active.
            <a href="<?= Helpers::asset($user['resume_file']) ?>" target="_blank" style="font-weight:600;">View file</a>
        </div>
    <?php else: ?>
        <div class="alert alert-error" style="background:#eef3f8;color:var(--primary);border-color:var(--primary-soft);">
            <i class="fa fa-circle-info"></i> No custom PDF uploaded — the auto-generated resume is currently active.
        </div>
    <?php endif; ?>

    <form method="post" action="<?= Helpers::url('/dashboard/resume-upload') ?>" enctype="multipart/form-data" style="display:flex;gap:14px;align-items:center;flex-wrap:wrap;">
        <input type="hidden" name="_csrf" value="<?= Helpers::csrfToken() ?>">
        <input type="file" name="resume_file" accept="application/pdf" required>
        <button type="submit" class="btn btn-primary btn-sm">Upload PDF Resume</button>
    </form>

    <div style="margin-top:22px;">
        <a href="<?= Helpers::url('/p/' . $user['slug'] . '/resume') ?>" target="_blank" class="btn btn-light" style="border:1px solid var(--border);">
            <i class="fa fa-eye"></i> Preview Resume
        </a>
    </div>
</div>
