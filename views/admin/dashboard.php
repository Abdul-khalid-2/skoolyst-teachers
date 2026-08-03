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

    <div class="table-wrap" style="margin-bottom:26px;">
        <table class="app-table">
            <thead><tr><th>Name</th><th>Email</th><th>City</th><th>Status</th><th>Joined</th></tr></thead>
            <tbody>
            <?php foreach ($recent as $t): ?>
                <tr>
                    <td><a href="<?= Helpers::url('/p/' . $t['slug']) ?>" target="_blank" style="color:var(--primary);font-weight:600;"><?= Helpers::e($t['full_name']) ?></a></td>
                    <td><?= Helpers::e($t['email']) ?></td>
                    <td><?= Helpers::e($t['city']) ?></td>
                    <td><span class="badge badge-<?= $t['status'] ?>"><?= ucfirst($t['status']) ?></span></td>
                    <td><?= date('M j, Y', strtotime($t['created_at'])) ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$recent): ?><tr><td colspan="5" style="text-align:center;color:var(--text-muted);">No teachers registered yet.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>

    <a href="<?= Helpers::url('/admin/teachers') ?>" class="btn btn-primary">Manage All Teachers</a>
</div>

<?php View::partial('layouts/footer'); ?>
