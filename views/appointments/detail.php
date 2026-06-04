<?php require __DIR__ . '/../partials/page_start.php'; ?>
<div class="card appointment-summary-card">
    <div class="detail-grid">
        <p><strong>Patient:</strong> <?= e($appointment['patient_name']) ?> (<?= e($appointment['patient_email']) ?>)</p>
        <p><strong>Doctor:</strong> <?= e($appointment['doctor_name']) ?> - <?= e($appointment['specialization']) ?></p>
        <p><strong>Date:</strong> <?= e(formatDate($appointment['appt_date'])) ?> at <?= e(formatTime($appointment['appt_time'])) ?></p>
        <p><strong>Status:</strong> <span class="badge <?= e($appointment['status']) ?>"><?= e($appointment['status']) ?></span></p>
    </div>
    <div class="detail-text-grid">
        <div>
            <span>Reason</span>
            <p><?= e($appointment['reason'] ?: 'No reason provided.') ?></p>
        </div>
        <div>
            <span>Doctor Notes</span>
            <p><?= e($appointment['doctor_notes'] ?: 'No notes yet.') ?></p>
        </div>
    </div>
    <?php if ($appointment['prescription_id']): ?>
        <a class="btn" href="<?= e(url('prescriptions', 'download', ['id' => $appointment['id']])) ?>">Download Prescription</a>
    <?php endif; ?>
</div>
<?php if (in_array(Auth::role(), ['admin', 'doctor'], true)): ?>
<div class="card form-card">
    <div class="form-intro compact">
        <div>
            <h2>Update Appointment</h2>
            <p>Change the appointment status and keep clinical notes attached to this visit.</p>
        </div>
    </div>
    <form class="stacked-form" method="post" action="<?= e(url('appointments', 'update_status')) ?>">
        <input type="hidden" name="csrf_token" value="<?= e(CSRF::generateToken()) ?>">
        <input type="hidden" name="id" value="<?= e($appointment['id']) ?>">
        <section class="form-section">
            <div class="section-heading">
                <span>1</span>
                <div>
                    <h3>Status And Notes</h3>
                    <p>Use notes for doctor-facing context; patients can still see final appointment details.</p>
                </div>
            </div>
            <div class="form-grid appointment-update-grid">
                <div class="field">
                    <label>Status</label>
                    <select name="status">
                        <?php foreach (['pending','confirmed','completed','cancelled'] as $s): ?>
                            <option value="<?= e($s) ?>" <?= $appointment['status'] === $s ? 'selected' : '' ?>><?= e(ucfirst($s)) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="field field-wide">
                    <label>Notes</label>
                    <textarea name="doctor_notes"><?= e($appointment['doctor_notes']) ?></textarea>
                </div>
            </div>
        </section>
        <div class="form-actions">
            <?php if (Auth::role() === 'doctor' && $appointment['status'] === 'completed' && !$appointment['prescription_id']): ?>
                <a class="btn btn-success" href="<?= e(url('prescriptions', 'create', ['appointment_id' => $appointment['id']])) ?>">Add Prescription</a>
            <?php endif; ?>
            <button class="btn" type="submit">Update Appointment</button>
        </div>
    </form>
</div>
<?php endif; ?>
<?php require __DIR__ . '/../partials/page_end.php'; ?>
