<?php
$success = Helpers::flash('success');
$errors  = Helpers::flash('errors');
?>
<?php if ($success): ?>
    <div class="alert alert-success"><i class="fa fa-circle-check"></i> <?= Helpers::e($success) ?></div>
<?php endif; ?>
<?php if ($errors): ?>
    <div class="alert alert-error">
        <?php foreach (explode('|', $errors) as $err): ?>
            <div><i class="fa fa-circle-exclamation"></i> <?= Helpers::e($err) ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
