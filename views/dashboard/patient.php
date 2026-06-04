<?php require __DIR__ . '/../partials/page_start.php'; ?>
<div class="grid">
    <div class="card"><h3>Active Appointments</h3><strong><?= e(count($stats['active'])) ?></strong></div>
    <div class="card"><h3>Completed Appointments</h3><strong><?= e($stats['completed']) ?></strong></div>
    <div class="card"><h3>Prescriptions</h3><strong><?= e($prescriptionCount) ?></strong></div>
</div>
<?php if ($stats['next']): ?>
<div class="card">
    <h2>Next Appointment</h2>
    <p><?= e($stats['next']['doctor_name']) ?> on <?= e(formatDate($stats['next']['appt_date'])) ?> at <?= e(formatTime($stats['next']['appt_time'])) ?></p>
</div>
<?php endif; ?>
<div class="card">
    <h2>Active Appointments</h2>
    <table class="table">
        <tr><th>Doctor</th><th>Date</th><th>Time</th><th>Status</th></tr>
        <?php foreach ($stats['active'] as $appt): ?>
            <tr><td><?= e($appt['doctor_name']) ?></td><td><?= e(formatDate($appt['appt_date'])) ?></td><td><?= e(formatTime($appt['appt_time'])) ?></td><td><span class="badge <?= e($appt['status']) ?>"><?= e($appt['status']) ?></span></td></tr>
        <?php endforeach; ?>
    </table>
</div>
<?php require __DIR__ . '/../partials/page_end.php'; ?>
