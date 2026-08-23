<?php View::partial('layouts/header', ['title' => $title, 'robots' => 'noindex, follow']); ?>

<?php
$tabs = [
    'basic'          => ['icon' => 'fa-id-card', 'label' => 'Basic Info'],
    'education'      => ['icon' => 'fa-graduation-cap', 'label' => 'Education'],
    'experience'     => ['icon' => 'fa-briefcase', 'label' => 'Experience'],
    'skills'         => ['icon' => 'fa-star', 'label' => 'Skills'],
    'certifications' => ['icon' => 'fa-certificate', 'label' => 'Certifications'],
    'projects'       => ['icon' => 'fa-diagram-project', 'label' => 'Projects'],
    'languages'      => ['icon' => 'fa-language', 'label' => 'Languages'],
    'awards'         => ['icon' => 'fa-trophy', 'label' => 'Awards'],
    'social'         => ['icon' => 'fa-share-nodes', 'label' => 'Social Links'],
    'resume'         => ['icon' => 'fa-file-pdf', 'label' => 'Resume'],
    'template'       => ['icon' => 'fa-palette', 'label' => 'Template'],
    'settings'       => ['icon' => 'fa-gear', 'label' => 'Visibility & Settings'],
];
$activeTab = array_key_exists($tab, $tabs) ? $tab : 'basic';
$portfolioUrl = Helpers::url('/p/' . $user['slug']);
?>

<div class="container" style="padding-top:20px;">
    <div class="dash-card" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:14px;">
        <div>
            <h3 style="margin-bottom:2px;">Hi, <?= Helpers::e($user['full_name']) ?> 👋</h3>
            <p class="desc" style="margin:0;">Complete every section below to make your portfolio stand out.</p>
        </div>
        <div class="share-box" style="margin:0;">
            <input type="text" id="shareLink" readonly value="<?= $portfolioUrl ?>">
            <button type="button" class="btn btn-primary btn-sm" data-copy="#shareLink"><i class="fa fa-copy"></i> Copy Link</button>
            <a href="<?= $portfolioUrl ?>" target="_blank" class="btn btn-light btn-sm" style="border:1px solid var(--border);">View</a>
        </div>
    </div>
</div>

<button class="mobile-dash-toggle"><i class="fa fa-bars"></i> Menu: <?= $tabs[$activeTab]['label'] ?></button>

<div class="dash-wrap">
    <aside class="dash-sidebar">
        <h5>Portfolio Sections</h5>
        <?php foreach ($tabs as $key => $meta): ?>
            <a href="<?= Helpers::url('/dashboard') ?>?tab=<?= $key ?>" class="<?= $activeTab === $key ? 'active' : '' ?>">
                <i class="fa <?= $meta['icon'] ?>"></i> <?= $meta['label'] ?>
            </a>
        <?php endforeach; ?>
    </aside>

    <main class="dash-main">
        <?php View::partial('layouts/alerts'); ?>
        <?php View::partial('dashboard/tabs/' . $activeTab, ['user' => $user]); ?>
    </main>
</div>

<?php View::partial('layouts/footer'); ?>
