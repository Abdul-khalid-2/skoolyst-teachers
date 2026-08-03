<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= Helpers::e($title ?? 'Skoolyst Teachers') ?></title>
    <meta name="description" content="Skoolyst Teachers — build and share your professional teaching portfolio in minutes.">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= Helpers::asset('css/app.css') ?>">
</head>
<body class="app-shell">

<nav class="app-navbar">
    <div class="container">
        <a href="<?= Helpers::url('/') ?>" class="app-brand">
            <img src="<?= Helpers::asset('image/Skoolyst.png') ?>" alt="Skoolyst" onerror="this.style.display='none'">
        </a>
        <button class="navbar-toggle" aria-label="Menu"><i class="fa fa-bars"></i></button>
        <ul class="app-nav-links">
            <li><a href="<?= Helpers::url('/') ?>">Directory</a></li>
            <?php if (Auth::check()): ?>
                <?php $u = Auth::user(); ?>
                <?php if (($u['role'] ?? '') === 'super-admin'): ?>
                    <li><a href="<?= Helpers::url('/admin') ?>">Admin Panel</a></li>
                <?php else: ?>
                    <li><a href="<?= Helpers::url('/dashboard') ?>">My Dashboard</a></li>
                    <li><a href="<?= Helpers::url('/p/' . $u['slug']) ?>" target="_blank">View Portfolio</a></li>
                <?php endif; ?>
                <li><a href="<?= Helpers::url('/logout') ?>" class="btn-outline">Logout</a></li>
            <?php else: ?>
                <li><a href="<?= Helpers::url('/login') ?>" class="btn-outline">Login</a></li>
                <li><a href="<?= Helpers::url('/register') ?>" class="btn-solid">Create Portfolio</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>
