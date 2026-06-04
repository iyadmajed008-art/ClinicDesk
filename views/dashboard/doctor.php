<?php require __DIR__ . '/../partials/page_start.php'; ?>
<div class="card">
    <h2>Today's Appointments</h2>
    <table class="table">
        <tr><th>Time</th><th>Patient</th><th>Status</th></tr>
        <?php foreach ($stats['today'] as $appt): ?>
            <tr><td><?= e(formatTime($appt['appt_time'])) ?></td><td><?= e($appt['patient_name']) ?></td><td><span class="badge <?= e($appt['status']) ?>"><?= e($appt['status']) ?></span></td></tr>
        <?php endforeach; ?>
    </table>
</div>
<div class="grid">
    <?php foreach ($stats['counts'] as $count): ?>
        <div class="card"><h3><?= e(ucfirst($count['status'])) ?></h3><strong><?= e($count['total']) ?></strong></div>
    <?php endforeach; ?>
</div>
<div class="card">
    <h2>Upcoming</h2>
    <table class="table">
        <tr><th>Date</th><th>Time</th><th>Patient</th><th>Status</th></tr>
        <?php foreach ($stats['upcoming'] as $appt): ?>
            <tr><td><?= e(formatDate($appt['appt_date'])) ?></td><td><?= e(formatTime($appt['appt_time'])) ?></td><td><?= e($appt['patient_name']) ?></td><td><?= e($appt['status']) ?></td></tr>
        <?php endforeach; ?>
    </table>
</div>
<?php require __DIR__ . '/../partials/page_end.php'; ?>
