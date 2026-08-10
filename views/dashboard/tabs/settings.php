<div class="dash-card">
    <h3>Portfolio Visibility</h3>
    <p class="desc">Control whether your portfolio appears in the public directory and filters. Your share link always works for anyone who has it.</p>

    <form method="post" action="<?= Helpers::url('/dashboard/visibility') ?>">
        <input type="hidden" name="_csrf" value="<?= Helpers::csrfToken() ?>">
        <label style="display:flex;align-items:center;gap:10px;font-weight:600;cursor:pointer;">
            <input type="checkbox" name="is_public" value="1" <?= $user['is_public'] ? 'checked' : '' ?> style="width:18px;height:18px;">
            Show my portfolio in the public directory
        </label>
        <button type="submit" class="btn btn-primary" style="margin-top:18px;">Save Setting</button>
    </form>
</div>

<div class="dash-card">
    <h3>Resume / CV Download Access</h3>
    <p class="desc">Choose who can download your resume from your portfolio page. Your phone number is always only visible to logged-in teachers, and your email always stays visible to everyone, regardless of this setting.</p>

    <form method="post" action="<?= Helpers::url('/dashboard/resume-access') ?>">
        <input type="hidden" name="_csrf" value="<?= Helpers::csrfToken() ?>">
        <label style="display:flex;align-items:flex-start;gap:10px;font-weight:600;cursor:pointer;margin-bottom:12px;">
            <input type="radio" name="resume_access" value="everyone" <?= ($user['resume_access'] ?? 'everyone') === 'everyone' ? 'checked' : '' ?> style="width:18px;height:18px;margin-top:2px;">
            <span>Everyone<br><span class="desc" style="font-weight:400;">Anyone visiting your portfolio can download your resume, no account needed.</span></span>
        </label>
        <label style="display:flex;align-items:flex-start;gap:10px;font-weight:600;cursor:pointer;">
            <input type="radio" name="resume_access" value="login_required" <?= ($user['resume_access'] ?? 'everyone') === 'login_required' ? 'checked' : '' ?> style="width:18px;height:18px;margin-top:2px;">
            <span>Only logged-in teachers<br><span class="desc" style="font-weight:400;">Visitors without an account see a "please login" message with your email as an alternative.</span></span>
        </label>
        <button type="submit" class="btn btn-primary" style="margin-top:18px;">Save Setting</button>
    </form>
</div>

<div class="dash-card">
    <h3>Your Portfolio Link</h3>
    <p class="desc">Share this link on your CV, LinkedIn, or with recruiters.</p>
    <div class="share-box">
        <input type="text" id="shareLink2" readonly value="<?= Helpers::url('/p/' . $user['slug']) ?>">
        <button type="button" class="btn btn-primary btn-sm" data-copy="#shareLink2"><i class="fa fa-copy"></i> Copy Link</button>
    </div>
</div>

<div class="dash-card">
    <h3>Account</h3>
    <p class="desc">Logged in as <strong><?= Helpers::e($user['email']) ?></strong></p>
    <a href="<?= Helpers::url('/logout') ?>" class="btn btn-danger btn-sm">Logout</a>
</div>
