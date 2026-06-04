<?php if (($paginator ?? null) && $paginator->totalPages() > 1): ?>
    <p>
        <?php if ($paginator->hasPrev()): ?>
            <a class="btn btn-secondary" href="<?= e(url($_GET['page'] ?? 'dashboard', $_GET['action'] ?? 'index', array_merge($_GET, ['p' => $paginator->currentPage - 1]))) ?>">Previous</a>
        <?php endif; ?>
        Page <?= e($paginator->currentPage) ?> of <?= e($paginator->totalPages()) ?>
        <?php if ($paginator->hasNext()): ?>
            <a class="btn btn-secondary" href="<?= e(url($_GET['page'] ?? 'dashboard', $_GET['action'] ?? 'index', array_merge($_GET, ['p' => $paginator->currentPage + 1]))) ?>">Next</a>
        <?php endif; ?>
    </p>
<?php endif; ?>
