<?php require __DIR__ . '/../partials/page_start.php'; ?>
<div class="card user-form-card">
    <?php if (!$user): ?>
        <div class="form-intro">
            <div>
                <h2>Create a Staff or Patient Account</h2>
                <p>Set the login details first, then choose the role. Doctor profile fields appear only for doctor accounts.</p>
            </div>
        </div>
    <?php endif; ?>
    <form class="user-form" method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= e(CSRF::generateToken()) ?>">
        <?php if (!$user): ?>
            <section class="form-section">
                <div class="section-heading">
                    <span>1</span>
                    <div>
                        <h3>Account Details</h3>
                        <p>These fields create the user's login profile.</p>
                    </div>
                </div>
                <div class="form-grid">
                    <div class="field">
                        <label>Name</label>
                        <input name="name" value="<?= e($user['name'] ?? '') ?>" required>
                    </div>
                    <div class="field">
                        <label>Email</label>
                        <input type="email" name="email" required>
                    </div>
                    <div class="field">
                        <label>Phone</label>
                        <input name="phone">
                    </div>
                    <div class="field">
                        <label>Temporary Password</label>
                        <div class="password-field">
                            <input type="password" name="password" required>
                            <button class="password-toggle" type="button" aria-label="Show password" data-password-toggle>
                                <span class="eye-icon" aria-hidden="true"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <section class="form-section">
                <div class="section-heading">
                    <span>2</span>
                    <div>
                        <h3>Role</h3>
                        <p>The selected role controls dashboard access and permissions.</p>
                    </div>
                </div>
                <div class="role-choice">
                    <select name="role" data-role-select>
                        <option value="patient">Patient</option>
                        <option value="doctor">Doctor</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
            </section>

            <section class="form-section doctor-panel" data-doctor-fields hidden>
                <div class="section-heading">
                    <span>3</span>
                    <div>
                        <h3>Doctor Profile</h3>
                        <p>Specialization, fee, and availability are stored in the doctor record.</p>
                    </div>
                </div>
                <div class="form-grid">
                    <div class="field">
                        <label>Specialization</label>
                        <select name="specialization_id">
                            <?php foreach ($specializations as $spec): ?>
                                <option value="<?= e($spec['id']) ?>"><?= e($spec['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="field">
                        <label>Consultation Fee</label>
                        <input type="number" step="0.01" name="consultation_fee" value="0">
                    </div>
                    <div class="field field-wide">
                        <label>Available Days</label>
                        <div class="checkbox-grid">
                            <?php foreach (WEEK_DAYS as $day): ?>
                                <label><input type="checkbox" name="available_days[]" value="<?= e($day) ?>" <?= in_array($day, ['Sun','Mon','Tue','Wed','Thu'], true) ? 'checked' : '' ?>> <?= e($day) ?></label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="field field-wide">
                        <label>Bio</label>
                        <textarea name="bio"></textarea>
                    </div>
                </div>
            </section>
        <?php else: ?>
            <section class="form-section">
                <div class="section-heading">
                    <span>1</span>
                    <div>
                        <h3>User Profile</h3>
                        <p>Update profile details and account status.</p>
                    </div>
                </div>
                <div class="form-grid">
                    <div class="field">
                        <label>Name</label>
                        <input name="name" value="<?= e($user['name'] ?? '') ?>" required>
                    </div>
                    <div class="field">
                        <label>Phone</label>
                        <input name="phone" value="<?= e($user['phone'] ?? '') ?>">
                    </div>
                    <div class="field">
                        <label>Avatar</label>
                        <?php if (!empty($user['avatar'])): ?>
                            <div class="avatar-preview">
                                <img src="<?= e($user['avatar']) ?>" alt="<?= e($user['name']) ?>">
                                <span>Current avatar</span>
                            </div>
                        <?php endif; ?>
                        <input type="file" name="avatar" accept="image/png,image/jpeg">
                    </div>
                    <div class="field status-field">
                        <label><input type="checkbox" name="is_active" value="1" <?= (int) $user['is_active'] === 1 ? 'checked' : '' ?>> Active account</label>
                    </div>
                </div>
            </section>
        <?php endif; ?>
        <div class="form-actions">
            <a class="btn btn-secondary" href="<?= e(url('users')) ?>">Cancel</a>
            <button class="btn" type="submit">Save User</button>
        </div>
    </form>
</div>
<?php require __DIR__ . '/../partials/page_end.php'; ?>
