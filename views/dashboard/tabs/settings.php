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
