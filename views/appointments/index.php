<?php require __DIR__ . '/../partials/page_start.php'; ?>
<div class="card">
    <form method="get">
        <input type="hidden" name="page" value="appointments">
        <select name="status">
            <option value="">All statuses</option>
            <?php foreach (['pending','confirmed','completed','cancelled'] as $s): ?>
                <option value="<?= e($s) ?>" <?= ($filters['status'] ?? '') === $s ? 'selected' : '' ?>><?= e(ucfirst($s)) ?></option>
            <?php endforeach; ?>
        </select>
        <?php if (Auth::role() === 'admin'): ?>
            <select name="doctor_id">
                <option value="">All doctors</option>
                <?php foreach ($doctors as $doctor): ?>
                    <option value="<?= e($doctor['id']) ?>" <?= (string) ($filters['doctor_id'] ?? '') === (string) $doctor['id'] ? 'selected' : '' ?>><?= e($doctor['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <input name="patient_search" value="<?= e($filters['patient_search'] ?? '') ?>" placeholder="Patient name">
        <?php endif; ?>
        <input type="date" name="start_date" value="<?= e($filters['start_date'] ?? '') ?>">
        <input type="date" name="end_date" value="<?= e($filters['end_date'] ?? '') ?>">
        <button class="btn" type="submit">Filter</button>
        <?php if (Auth::role() === 'patient'): ?><a class="btn btn-success" href="<?= e(url('appointments', 'book')) ?>">Book</a><?php endif; ?>
    </form>
</div>
<div class="card">
    <table class="table">
        <tr><th>Patient</th><th>Doctor</th><th>Specialization</th><th>Date</th><th>Time</th><th>Status</th><th>Action</th></tr>
        <?php foreach ($appointments as $appt): ?>
            <tr>
                <td><?= e($appt['patient_name']) ?></td>
                <td><?= e($appt['doctor_name']) ?></td>
                <td><?= e($appt['specialization']) ?></td>
                <td><?= e(formatDate($appt['appt_date'])) ?></td>
                <td><?= e(formatTime($appt['appt_time'])) ?></td>
                <td><span class="badge <?= e($appt['status']) ?>"><?= e($appt['status']) ?></span></td>
                <td>
                    <a class="btn btn-secondary" href="<?= e(url('appointments', 'detail', ['id' => $appt['id']])) ?>">View</a>
                    <?php if (Auth::role() === 'patient' && $appt['status'] === 'pending'): ?>
                        <form class="inline" method="post" action="<?= e(url('appointments', 'cancel')) ?>">
                            <input type="hidden" name="csrf_token" value="<?= e(CSRF::generateToken()) ?>">
                            <input type="hidden" name="id" value="<?= e($appt['id']) ?>">
                            <button class="btn btn-danger" type="submit">Cancel</button>
                        </form>
                    <?php endif; ?>
                    <?php if ($appt['prescription_id']): ?>
                        <a class="btn" href="<?= e(url('prescriptions', 'download', ['id' => $appt['id']])) ?>">PDF</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <?php require __DIR__ . '/../partials/pagination.php'; ?>
</div>
<?php require __DIR__ . '/../partials/page_end.php'; ?>
