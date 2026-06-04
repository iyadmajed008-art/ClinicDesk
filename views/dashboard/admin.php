<?php require __DIR__ . '/../partials/page_start.php'; ?>
<div class="grid">
    <?php foreach ($userCounts as $count): ?>
        <div class="card"><h3><?= e(ucfirst($count['role'])) ?> Users</h3><strong><?= e($count['total']) ?></strong></div>
    <?php endforeach; ?>
    <div class="card"><h3>Appointments Today</h3><strong><?= e($appointmentStats['today']) ?></strong></div>
</div>
<div class="card status-summary-card">
    <div class="card-title-row">
        <div>
            <h2>This Week By Status</h2>
            <p>Appointment activity grouped by current status.</p>
        </div>
    </div>
    <div class="status-summary-grid">
        <?php foreach ($appointmentStats['week'] as $row): ?>
            <div class="status-summary-item <?= e($row['status']) ?>">
                <span><?= e(ucfirst($row['status'])) ?></span>
                <strong><?= e($row['total']) ?></strong>
            </div>
        <?php endforeach; ?>
        <?php if (!$appointmentStats['week']): ?>
            <div class="empty-state">No appointments recorded this week.</div>
        <?php endif; ?>
    </div>
</div>
<div class="card">
    <h2>Recent Appointments</h2>
    <table class="table">
        <tr><th>Patient</th><th>Doctor</th><th>Date</th><th>Status</th></tr>
        <?php foreach ($recentAppointments as $appt): ?>
            <tr><td><?= e($appt['patient_name']) ?></td><td><?= e($appt['doctor_name']) ?></td><td><?= e(formatDate($appt['appt_date'])) ?></td><td><span class="badge <?= e($appt['status']) ?>"><?= e($appt['status']) ?></span></td></tr>
        <?php endforeach; ?>
    </table>
</div>
<?php require __DIR__ . '/../partials/page_end.php'; ?>
