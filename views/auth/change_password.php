<?php require __DIR__ . '/../partials/page_start.php'; ?>
<div class="card form-card">
    <div class="form-intro">
        <div>
            <h2>Update Your Password</h2>
            <p>Use at least 8 characters with uppercase, lowercase, and a number.</p>
        </div>
    </div>
    <form class="stacked-form" method="post">
        <input type="hidden" name="csrf_token" value="<?= e(CSRF::generateToken()) ?>">
        <section class="form-section">
            <div class="section-heading">
                <span>1</span>
                <div>
                    <h3>Password Details</h3>
                    <p>Enter your current password and confirm the new one.</p>
                </div>
            </div>
            <div class="form-grid">
                <div class="field">
                    <label>Current Password</label>
                    <div class="password-field">
                        <input type="password" name="current_password" required>
                        <button class="password-toggle" type="button" aria-label="Show password" data-password-toggle>
                            <span class="eye-icon" aria-hidden="true"></span>
                        </button>
                    </div>
                </div>
                <div class="field">
                    <label>New Password</label>
                    <div class="password-field">
                        <input type="password" name="new_password" required>
                        <button class="password-toggle" type="button" aria-label="Show password" data-password-toggle>
                            <span class="eye-icon" aria-hidden="true"></span>
                        </button>
                    </div>
                </div>
                <div class="field">
                    <label>Confirm New Password</label>
                    <div class="password-field">
                        <input type="password" name="confirm_password" required>
                        <button class="password-toggle" type="button" aria-label="Show password" data-password-toggle>
                            <span class="eye-icon" aria-hidden="true"></span>
                        </button>
                    </div>
                </div>
            </div>
        </section>
        <div class="form-actions">
            <button class="btn" type="submit">Update Password</button>
        </div>
    </form>
</div>
<?php require __DIR__ . '/../partials/page_end.php'; ?>
