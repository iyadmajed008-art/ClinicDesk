<?php require __DIR__ . '/../partials/page_start.php'; ?>
<div class="card form-card">
    <div class="form-intro">
        <div>
            <h2>Request an Appointment</h2>
            <p>Choose a doctor, future date, and available time slot. Availability is shown beside each doctor.</p>
        </div>
    </div>
    <form class="stacked-form" method="post">
        <input type="hidden" name="csrf_token" value="<?= e(CSRF::generateToken()) ?>">
        <section class="form-section">
            <div class="section-heading">
                <span>1</span>
                <div>
                    <h3>Appointment Details</h3>
                    <p>Pick a valid date that matches the selected doctor's working days.</p>
                </div>
            </div>
            <div class="form-grid">
                <div class="field field-wide">
                    <label>Doctor</label>
                    <select name="doctor_id" required>
                        <?php foreach ($doctors as $doctor): ?>
                            <option value="<?= e($doctor['id']) ?>"><?= e($doctor['name']) ?> - <?= e($doctor['specialization']) ?> (<?= e($doctor['available_days']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="field">
                    <label>Date</label>
                    <input type="date" name="appt_date" min="<?= e(date('Y-m-d')) ?>" required>
                </div>
                <div class="field">
                    <label>Time</label>
                    <select name="appt_time" required>
                        <?php foreach (TIME_SLOTS as $slot): ?>
                            <option value="<?= e($slot) ?>"><?= e($slot) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="field field-wide">
                    <label>Reason</label>
                    <input name="reason" maxlength="255" placeholder="Short reason for the visit">
                </div>
            </div>
        </section>
        <div class="form-actions">
            <a class="btn btn-secondary" href="<?= e(url('appointments')) ?>">Cancel</a>
            <button class="btn" type="submit">Request Appointment</button>
        </div>
    </form>
</div>
<?php require __DIR__ . '/../partials/page_end.php'; ?>
