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
                    <td data-label="Name"><a href="<?= Helpers::url('/p/' . $t['slug']) ?>" target="_blank" style="color:var(--primary);font-weight:600;"><?= Helpers::e($t['full_name']) ?></a></td>
                    <td data-label="Email"><?= Helpers::e($t['email']) ?></td>
                    <td data-label="City"><?= Helpers::e($t['city']) ?></td>
                    <td data-label="Status"><span class="badge badge-<?= $t['status'] ?>"><?= ucfirst($t['status']) ?></span></td>
                    <td data-label="Joined"><?= date('M j, Y', strtotime($t['created_at'])) ?></td>
                    <td class="actions-cell">
                        <div class="actions-dropdown">
                            <button type="button" class="actions-dropdown-toggle">Actions <span class="caret">&#9662;</span></button>
                            <div class="actions-dropdown-menu">
                                <?php foreach (['active' => 'Activate', 'inactive' => 'Disable', 'pending' => 'Mark Pending'] as $st => $label): ?>
                                    <?php if ($t['status'] !== $st): ?>
                                    <form method="post" action="<?= Helpers::url('/admin/teachers/' . $t['id'] . '/status') ?>">
                                        <input type="hidden" name="_csrf" value="<?= Helpers::csrfToken() ?>">
                                        <input type="hidden" name="status" value="<?= $st ?>">
                                        <button type="submit"><?= $label ?></button>
                                    </form>
                                    <?php endif; ?>
                                <?php endforeach; ?>

                                <div class="dropdown-divider"></div>

                                <form method="post" action="<?= Helpers::url('/admin/teachers/' . $t['id'] . '/send-welcome') ?>" onsubmit="return confirm('Send the welcome email to ' + <?= json_encode($t['email']) ?> + '?');">
                                    <input type="hidden" name="_csrf" value="<?= Helpers::csrfToken() ?>">
                                    <button type="submit">Send Welcome Email</button>
                                </form>
                                <form method="post" action="<?= Helpers::url('/admin/teachers/' . $t['id'] . '/send-reminder') ?>" onsubmit="return confirm('Send a profile completion reminder to ' + <?= json_encode($t['email']) ?> + '?');">
                                    <input type="hidden" name="_csrf" value="<?= Helpers::csrfToken() ?>">
                                    <button type="submit">Add Missing Details</button>
                                </form>

                                <div class="dropdown-divider"></div>

                                <form method="post" action="<?= Helpers::url('/admin/teachers/' . $t['id'] . '/delete') ?>" onsubmit="return confirm('Delete this teacher account permanently?');">
                                    <input type="hidden" name="_csrf" value="<?= Helpers::csrfToken() ?>">
                                    <button type="submit" class="danger">Delete</button>
                                </form>
                            </div>
                        </div>
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
