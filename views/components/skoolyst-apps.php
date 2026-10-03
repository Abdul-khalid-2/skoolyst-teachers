<?php
/**
 * "Other Skoolyst Apps" — a card per active Skoolyst app except this one,
 * shown just above the footer. The list lives in config/skoolyst_apps.php.
 */
$skoolystRegistry = require ROOT_PATH . '/config/skoolyst_apps.php';
$skoolystCurrent = $skoolystRegistry['current'] ?? '';
?>
<nav class="skoolyst-apps" aria-labelledby="skoolyst-apps-title">
    <div class="container">
        <h2 id="skoolyst-apps-title" class="skoolyst-apps-title">Other Skoolyst Apps</h2>
        <ul class="skoolyst-apps-grid">
            <?php foreach ($skoolystRegistry['apps'] ?? [] as $app): ?>
                <?php if (empty($app['active']) || ($app['key'] ?? '') === $skoolystCurrent) continue; ?>
                <li>
                    <a href="<?= Helpers::e($app['url']) ?>" class="skoolyst-app-card">
                        <span class="skoolyst-app-icon"><i class="<?= Helpers::e($app['icon'] ?? 'fa-solid fa-link') ?>" aria-hidden="true"></i></span>
                        <span class="skoolyst-app-name"><?= Helpers::e($app['name']) ?></span>
                        <span class="skoolyst-app-desc"><?= Helpers::e($app['description'] ?? '') ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</nav>
