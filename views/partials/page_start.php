<?php require __DIR__ . '/header.php'; ?>
<?php require __DIR__ . '/sidebar.php'; ?>
<main class="content">
<div class="page-heading">
    <div>
        <h1><?= e($pageTitle ?? APP_NAME) ?></h1>
    </div>
</div>
<?php require __DIR__ . '/alerts.php'; ?>
