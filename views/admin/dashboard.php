<?php View::partial('layouts/header', ['title' => $title]); ?>

<div class="container" style="padding:30px 20px;">
    <h2 style="color:var(--primary);margin-bottom:4px;">Admin Dashboard</h2>
    <p style="color:var(--text-muted);margin-bottom:26px;">Overview of your Skoolyst Teachers platform.</p>

    <?php View::partial('layouts/alerts'); ?>

    <div class="stat-cards">
        <div class="stat-card"><strong><?= $stats['total'] ?></strong><span>Total Teachers</span></div>
        <div class="stat-card"><strong><?= $stats['active'] ?></strong><span>Active</span></div>
        <div class="stat-card"><strong><?= $stats['pending'] ?></strong><span>Pending</span></div>
        <div class="stat-card"><strong><?= $stats['inactive'] ?></strong><span>Inactive</span></div>
    </div>

    <div class="table-wrap" style="margin-bottom:14px;">
        <table class="app-table">
            <thead><tr><th>Name</th><th>Email</th><th>City</th><th>Status</th><th>Joined</th></tr></thead>
            <tbody>
            <?php foreach ($result['data'] as $t): ?>
                <tr>
                    <td data-label="Name"><a href="<?= Helpers::url('/p/' . $t['slug']) ?>" target="_blank" style="color:var(--primary);font-weight:600;"><?= Helpers::e($t['full_name']) ?></a></td>
                    <td data-label="Email"><?= Helpers::e($t['email']) ?></td>
                    <td data-label="City"><?= Helpers::e($t['city']) ?></td>
                    <td data-label="Status"><span class="badge badge-<?= $t['status'] ?>"><?= ucfirst($t['status']) ?></span></td>
                    <td data-label="Joined"><?= date('M j, Y', strtotime($t['created_at'])) ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$result['data']): ?><tr><td colspan="5" style="text-align:center;color:var(--text-muted);">No teachers registered yet.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($result['last_page'] > 1): ?>
        <div class="pagination" style="margin-bottom:22px;">
            <?php for ($p = 1; $p <= $result['last_page']; $p++): ?>
                <a href="<?= Helpers::url('/admin') ?>?page=<?= $p ?>" class="<?= $p == $result['page'] ? 'active' : '' ?>"><?= $p ?></a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>

    <a href="<?= Helpers::url('/admin/teachers') ?>" class="btn btn-primary">Manage All Teachers</a>
</div>

<?php View::partial('layouts/footer'); ?>
