<?php require __DIR__ . '/../partials/page_start.php'; ?>
<div class="card form-card">
    <div class="form-intro">
        <div>
            <h2>Prescription For <?= e($appointment['patient_name']) ?></h2>
            <p><?= e(formatDate($appointment['appt_date'])) ?> at <?= e(formatTime($appointment['appt_time'])) ?>. PDF upload is optional and must be under 3 MB.</p>
        </div>
    </div>
    <form class="stacked-form" method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= e(CSRF::generateToken()) ?>">
        <input type="hidden" name="appointment_id" value="<?= e($appointment['id']) ?>">
        <section class="form-section">
            <div class="section-heading">
                <span>1</span>
                <div>
                    <h3>Clinical Notes</h3>
                    <p>Diagnosis and medications are required before saving the prescription.</p>
                </div>
            </div>
            <div class="form-grid">
                <div class="field field-wide">
                    <label>Diagnosis</label>
                    <textarea name="diagnosis" required></textarea>
                </div>
                <div class="field field-wide">
                    <label>Medications</label>
                    <textarea name="medications" required></textarea>
                </div>
                <div class="field field-wide">
                    <label>Notes</label>
                    <textarea name="notes"></textarea>
                </div>
                <div class="field">
                    <label>Prescription PDF</label>
                    <input type="file" name="prescription_file" accept="application/pdf">
                </div>
            </div>
        </section>
        <div class="form-actions">
            <a class="btn btn-secondary" href="<?= e(url('appointments', 'detail', ['id' => $appointment['id']])) ?>">Cancel</a>
            <button class="btn" type="submit">Save Prescription</button>
        </div>
    </form>
</div>
<?php require __DIR__ . '/../partials/page_end.php'; ?>
