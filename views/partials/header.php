<?php $pageTitle = $pageTitle ?? APP_NAME; ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?> | <?= e(APP_NAME) ?></title>
    <link rel="stylesheet" href="public/assets/adminlte/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="public/assets/app.css">
</head>
<body>
<?php if (Auth::check()): ?>
<div class="topbar">
    <div>
        <strong><?= e(APP_NAME) ?></strong>
        <span>Clinic Management Dashboard</span>
    </div>
    <form class="inline" method="post" action="<?= e(url('auth', 'logout')) ?>">
        <input type="hidden" name="csrf_token" value="<?= e(CSRF::generateToken()) ?>">
        <button class="btn btn-secondary" type="submit">Logout</button>
    </form>
</div>
<div class="layout">
<?php endif; ?>
