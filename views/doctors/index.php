<?php require __DIR__ . '/../partials/page_start.php'; ?>
<div class="card">
    <table class="table">
        <tr><th>Name</th><th>Email</th><th>Specialization</th><th>Fee</th><th>Available</th><th>Actions</th></tr>
        <?php foreach ($doctors as $doctor): ?>
            <tr>
                <td><?= e($doctor['name']) ?></td>
                <td><?= e($doctor['email']) ?></td>
                <td><?= e($doctor['specialization']) ?></td>
                <td><?= e($doctor['consultation_fee']) ?></td>
                <td><?= e($doctor['available_days']) ?></td>
                <td><a class="btn btn-secondary" href="<?= e(url('doctors', 'edit', ['id' => $doctor['id']])) ?>">Edit</a></td>
            </tr>
        <?php endforeach; ?>
    </table>
    <?php require __DIR__ . '/../partials/pagination.php'; ?>
</div>
<?php require __DIR__ . '/../partials/page_end.php'; ?>
