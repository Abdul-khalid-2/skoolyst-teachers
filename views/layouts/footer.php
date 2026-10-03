<?php require ROOT_PATH . '/views/components/skoolyst-apps.php'; ?>

<footer class="app-footer">
    <div class="container">
        &copy; <?= date('Y') ?> Skoolyst Teachers. All rights reserved. &nbsp;|&nbsp;
        <a href="<?= Helpers::url('/') ?>">Directory</a> &nbsp;|&nbsp;
        <a href="<?= Helpers::url('/privacy-policy') ?>">Privacy Policy</a> &nbsp;|&nbsp;
        <a href="<?= Helpers::url('/terms-of-service') ?>">Terms of Service</a> &nbsp;|&nbsp;
        <a href="https://skoolyst.com" target="_blank">Skoolyst.com</a>
    </div>
</footer>

<script src="<?= Helpers::asset('js/app.js') ?>"></script>
</body>
</html>
