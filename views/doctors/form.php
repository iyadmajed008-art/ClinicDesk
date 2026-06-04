<?php require __DIR__ . '/../partials/page_start.php'; ?>
<div class="card form-card">
    <div class="form-intro">
        <div>
            <h2><?= e($doctor['name']) ?></h2>
            <p><?= e($doctor['email']) ?> · Manage specialization, fee, availability, and profile photo.</p>
        </div>
    </div>
    <form class="stacked-form" method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= e(CSRF::generateToken()) ?>">
        <section class="form-section">
            <div class="section-heading">
                <span>1</span>
                <div>
                    <h3>Professional Details</h3>
                    <p>These details appear wherever patients choose a doctor.</p>
                </div>
            </div>
            <div class="form-grid">
                <div class="field">
                    <label>Specialization</label>
                    <select name="specialization_id">
                        <?php foreach ($specializations as $spec): ?>
                            <option value="<?= e($spec['id']) ?>" <?= (int) $doctor['specialization_id'] === (int) $spec['id'] ? 'selected' : '' ?>><?= e($spec['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="field">
                    <label>Consultation Fee</label>
                    <input type="number" step="0.01" name="consultation_fee" value="<?= e($doctor['consultation_fee']) ?>">
                </div>
                <div class="field field-wide">
                    <label>Bio</label>
                    <textarea name="bio"><?= e($doctor['bio'] ?? '') ?></textarea>
                </div>
            </div>
        </section>
        <section class="form-section">
            <div class="section-heading">
                <span>2</span>
                <div>
                    <h3>Availability And Photo</h3>
                    <p>Select working days and optionally upload a JPG or PNG profile photo.</p>
                </div>
            </div>
            <?php $selected = explode(',', $doctor['available_days']); ?>
            <div class="field field-wide">
                <label>Available Days</label>
                <div class="checkbox-grid">
                    <?php foreach (WEEK_DAYS as $day): ?>
                        <label><input type="checkbox" name="available_days[]" value="<?= e($day) ?>" <?= in_array($day, $selected, true) ? 'checked' : '' ?>> <?= e($day) ?></label>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="field">
                <label>Photo</label>
                <input type="file" name="photo" accept="image/png,image/jpeg">
            </div>
        </section>
        <div class="form-actions">
            <a class="btn btn-secondary" href="<?= e(url('doctors')) ?>">Cancel</a>
            <button class="btn" type="submit">Save Doctor</button>
        </div>
    </form>
</div>
<?php require __DIR__ . '/../partials/page_end.php'; ?>
