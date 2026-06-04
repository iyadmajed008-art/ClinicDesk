<?php require __DIR__ . '/../partials/page_start.php'; ?>
<div class="card">
    <form method="post">
        <input type="hidden" name="csrf_token" value="<?= e(CSRF::generateToken()) ?>">
        <input name="name" required placeholder="Specialization name">
        <button class="btn" type="submit">Add</button>
    </form>
</div>
<div class="card">
    <table class="table">
        <tr><th>Name</th><th>Action</th></tr>
        <?php foreach ($specializations as $spec): ?>
            <tr>
                <td><?= e($spec['name']) ?></td>
                <td>
                    <form class="inline" method="post">
                        <input type="hidden" name="csrf_token" value="<?= e(CSRF::generateToken()) ?>">
                        <input type="hidden" name="form_action" value="delete">
                        <input type="hidden" name="id" value="<?= e($spec['id']) ?>">
                        <button class="btn btn-danger" type="submit">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
<?php require __DIR__ . '/../partials/page_end.php'; ?>
