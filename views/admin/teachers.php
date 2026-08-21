<?php View::partial('layouts/header', ['title' => $title]); ?>

<div class="container" style="padding:30px 20px;">
    <h2 style="color:var(--primary);margin-bottom:4px;">Manage Teachers</h2>
    <p style="color:var(--text-muted);margin-bottom:22px;"><?= $result['total'] ?> teacher account(s) registered.</p>

    <?php View::partial('layouts/alerts'); ?>

    <form method="get" action="<?= Helpers::url('/admin/teachers') ?>" style="margin-bottom:20px;max-width:340px;">
        <input type="text" name="search" class="form-control" placeholder="Search by name or email..." value="<?= Helpers::e($search) ?>">
    </form>

    <div class="table-wrap">
        <table class="app-table">
            <thead><tr><th>Name</th><th>Email</th><th>City</th><th>Status</th><th>Joined</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($result['data'] as $t): ?>
                <tr>
                    <td><a href="<?= Helpers::url('/p/' . $t['slug']) ?>" target="_blank" style="color:var(--primary);font-weight:600;"><?= Helpers::e($t['full_name']) ?></a></td>
                    <td><?= Helpers::e($t['email']) ?></td>
                    <td><?= Helpers::e($t['city']) ?></td>
                    <td><span class="badge badge-<?= $t['status'] ?>"><?= ucfirst($t['status']) ?></span></td>
                    <td><?= date('M j, Y', strtotime($t['created_at'])) ?></td>
                    <td style="white-space:nowrap;">
                        <?php foreach (['active' => 'Activate', 'inactive' => 'Disable', 'pending' => 'Mark Pending'] as $st => $label): ?>
                            <?php if ($t['status'] !== $st): ?>
                            <form method="post" action="<?= Helpers::url('/admin/teachers/' . $t['id'] . '/status') ?>" style="display:inline-block;margin-right:4px;">
                                <input type="hidden" name="_csrf" value="<?= Helpers::csrfToken() ?>">
                                <input type="hidden" name="status" value="<?= $st ?>">
                                <button type="submit" class="btn btn-sm" style="background:var(--primary-soft);color:var(--primary);"><?= $label ?></button>
                            </form>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <form method="post" action="<?= Helpers::url('/admin/teachers/' . $t['id'] . '/send-welcome') ?>" style="display:inline-block;margin-right:4px;" onsubmit="return confirm('Send the welcome email to ' + <?= json_encode($t['email']) ?> + '?');">
                            <input type="hidden" name="_csrf" value="<?= Helpers::csrfToken() ?>">
                            <button type="submit" class="btn btn-sm" style="background:#e6f4ea;color:#1e7e34;">Send Welcome Email</button>
                        </form>
                        <form method="post" action="<?= Helpers::url('/admin/teachers/' . $t['id'] . '/send-reminder') ?>" style="display:inline-block;margin-right:4px;" onsubmit="return confirm('Send a profile completion reminder to ' + <?= json_encode($t['email']) ?> + '?');">
                            <input type="hidden" name="_csrf" value="<?= Helpers::csrfToken() ?>">
                            <button type="submit" class="btn btn-sm" style="background:#fff7e6;color:#8a6d1a;">Add Missing Details</button>
                        </form>
                        <form method="post" action="<?= Helpers::url('/admin/teachers/' . $t['id'] . '/delete') ?>" style="display:inline-block;" onsubmit="return confirm('Delete this teacher account permanently?');">
                            <input type="hidden" name="_csrf" value="<?= Helpers::csrfToken() ?>">
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$result['data']): ?><tr><td colspan="6" style="text-align:center;color:var(--text-muted);">No teachers found.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($result['last_page'] > 1): ?>
        <div class="pagination">
            <?php for ($p = 1; $p <= $result['last_page']; $p++): ?>
                <a href="<?= Helpers::url('/admin/teachers') ?>?page=<?= $p ?>&search=<?= urlencode($search) ?>" class="<?= $p == ($result['page'] ?? 1) ? 'active' : '' ?>"><?= $p ?></a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>

<?php View::partial('layouts/footer'); ?>
