<?php require __DIR__ . '/../partials/header.php'; ?>
<main class="auth-shell">
    <section class="auth-panel">
        <div class="auth-brand">
            <div class="auth-mark">C</div>
            <div>
                <h1>ClinicDesk</h1>
                <p>Clinic Management Dashboard</p>
            </div>
        </div>
        <?php require __DIR__ . '/../partials/alerts.php'; ?>
        <form method="post" action="<?= e(url('auth', 'login')) ?>">
            <input type="hidden" name="csrf_token" value="<?= e(CSRF::generateToken()) ?>">
            <label>Email</label>
            <input type="email" name="email" required>
            <label>Password</label>
            <div class="password-field">
                <input type="password" name="password" required>
                <button class="password-toggle" type="button" aria-label="Show password" data-password-toggle>
                    <span class="eye-icon" aria-hidden="true"></span>
                </button>
            </div>
            <button class="btn" type="submit">Login</button>
        </form>
    </section>
</main>
<?php require __DIR__ . '/../partials/footer.php'; ?>
