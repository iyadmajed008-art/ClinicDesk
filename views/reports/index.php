<?php require __DIR__ . '/../partials/page_start.php'; ?>
<div class="card">
    <form method="get">
        <input type="hidden" name="page" value="reports">
        <label>Start Date</label>
        <input type="date" name="start_date" value="<?= e($filters['start_date']) ?>" required>
        <label>End Date</label>
        <input type="date" name="end_date" value="<?= e($filters['end_date']) ?>" required>
        <label>Doctor</label>
        <select name="doctor_id">
            <option value="">All</option>
            <?php foreach ($doctors as $doctor): ?>
                <option value="<?= e($doctor['id']) ?>" <?= (string) $filters['doctor_id'] === (string) $doctor['id'] ? 'selected' : '' ?>><?= e($doctor['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <label>Status</label>
        <select name="status">
            <option value="">All</option>
            <?php foreach (['pending','confirmed','completed','cancelled'] as $s): ?>
                <option value="<?= e($s) ?>" <?= $filters['status'] === $s ? 'selected' : '' ?>><?= e(ucfirst($s)) ?></option>
            <?php endforeach; ?>
        </select>
        <button class="btn" type="submit">Generate</button>
        <?php if ($rows): ?>
            <button class="btn btn-success" name="export" value="csv" type="submit">Export CSV</button>
        <?php endif; ?>
    </form>
</div>
<div class="card">
    <h2>Total: <?= e(count($rows)) ?></h2>
    <?php $statusCounts = array_count_values(array_column($rows, 'status')); ?>
    <?php foreach ($statusCounts as $status => $count): ?>
        <span class="badge <?= e($status) ?>"><?= e($status) ?>: <?= e($count) ?></span>
    <?php endforeach; ?>
    <table class="table">
        <tr><th>Patient</th><th>Doctor</th><th>Specialization</th><th>Date</th><th>Time</th><th>Status</th><th>Reason</th></tr>
        <?php foreach ($rows as $row): ?>
            <tr>
                <td><?= e($row['patient_name']) ?></td>
                <td><?= e($row['doctor_name']) ?></td>
                <td><?= e($row['specialization']) ?></td>
                <td><?= e(formatDate($row['appt_date'])) ?></td>
                <td><?= e(formatTime($row['appt_time'])) ?></td>
                <td><span class="badge <?= e($row['status']) ?>"><?= e($row['status']) ?></span></td>
                <td><?= e($row['reason']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
<?php require __DIR__ . '/../partials/page_end.php'; ?>
