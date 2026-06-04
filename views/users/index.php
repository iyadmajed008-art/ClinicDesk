<?php require __DIR__ . '/../partials/page_start.php'; ?>
<div class="card">
    <form method="get">
        <input type="hidden" name="page" value="users">
        <select name="role">
            <option value="">All roles</option>
            <?php foreach (['admin', 'doctor', 'patient'] as $r): ?>
                <option value="<?= e($r) ?>" <?= $role === $r ? 'selected' : '' ?>><?= e(ucfirst($r)) ?></option>
            <?php endforeach; ?>
        </select>
        <input name="search" value="<?= e($search) ?>" placeholder="Search name or email">
        <button class="btn" type="submit">Filter</button>
        <a class="btn btn-success" href="<?= e(url('users', 'create')) ?>">Create User</a>
    </form>
</div>
<div class="card">
    <table class="table">
        <tr><th>Name</th><th>Email</th><th>Role</th><th>Active</th><th>Actions</th></tr>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= e($user['name']) ?></td>
                <td><?= e($user['email']) ?></td>
                <td><?= e($user['role']) ?></td>
                <td><?= ((int) $user['is_active'] === 1) ? 'Yes' : 'No' ?></td>
                <td>
                    <a class="btn btn-secondary" href="<?= e(url('users', 'edit', ['id' => $user['id']])) ?>">Edit</a>
                    <form class="inline" method="post" action="<?= e(url('users', 'toggle')) ?>">
                        <input type="hidden" name="csrf_token" value="<?= e(CSRF::generateToken()) ?>">
                        <input type="hidden" name="id" value="<?= e($user['id']) ?>">
                        <button class="btn btn-danger" type="submit">Toggle</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <?php require __DIR__ . '/../partials/pagination.php'; ?>
</div>
<?php require __DIR__ . '/../partials/page_end.php'; ?>
