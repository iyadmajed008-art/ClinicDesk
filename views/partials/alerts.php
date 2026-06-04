<?php foreach ($_SESSION['flash'] ?? [] as $flash): ?>
    <div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
<?php endforeach; unset($_SESSION['flash']); ?>
