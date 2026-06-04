<?php if (Auth::check()): ?>
<aside class="sidebar">
    <?php $currentPage = $_GET['page'] ?? 'dashboard'; $currentAction = $_GET['action'] ?? 'index'; ?>
    <div class="sidebar-profile">
        <?php $currentUser = Auth::currentUser(); ?>
        <?php if (!empty($currentUser['avatar'])): ?>
            <img class="avatar-image" src="<?= e($currentUser['avatar']) ?>" alt="<?= e($currentUser['name']) ?>">
        <?php else: ?>
            <div class="avatar-initial"><?= e(strtoupper(substr($currentUser['name'], 0, 1))) ?></div>
        <?php endif; ?>
        <div>
            <h3><?= e($currentUser['name']) ?></h3>
            <p><?= e(ucfirst(Auth::role())) ?></p>
        </div>
    </div>
    <nav class="side-nav">
    <a class="<?= $currentPage === 'dashboard' ? 'active' : '' ?>" href="<?= e(url('dashboard')) ?>">Dashboard</a>
    <a class="<?= $currentPage === 'appointments' && $currentAction !== 'book' ? 'active' : '' ?>" href="<?= e(url('appointments')) ?>">Appointments</a>
    <?php if (Auth::role() === 'patient'): ?>
        <a class="<?= $currentPage === 'appointments' && $currentAction === 'book' ? 'active' : '' ?>" href="<?= e(url('appointments', 'book')) ?>">Book Appointment</a>
        <a class="<?= $currentPage === 'prescriptions' ? 'active' : '' ?>" href="<?= e(url('prescriptions')) ?>">My Prescriptions</a>
    <?php endif; ?>
    <?php if (Auth::role() === 'admin'): ?>
        <a class="<?= $currentPage === 'users' && $currentAction !== 'create' ? 'active' : '' ?>" href="<?= e(url('users')) ?>">Users</a>
        <a class="<?= $currentPage === 'users' && $currentAction === 'create' ? 'active' : '' ?>" href="<?= e(url('users', 'create')) ?>">Create User</a>
        <a class="<?= $currentPage === 'doctors' && $currentAction !== 'specializations' ? 'active' : '' ?>" href="<?= e(url('doctors')) ?>">Doctors</a>
        <a class="<?= $currentPage === 'doctors' && $currentAction === 'specializations' ? 'active' : '' ?>" href="<?= e(url('doctors', 'specializations')) ?>">Specializations</a>
        <a class="<?= $currentPage === 'reports' ? 'active' : '' ?>" href="<?= e(url('reports')) ?>">Reports</a>
    <?php endif; ?>
    <?php if (Auth::role() === 'doctor'): ?>
        <a class="<?= $currentPage === 'doctors' ? 'active' : '' ?>" href="<?= e(url('doctors')) ?>">Doctor Profiles</a>
    <?php endif; ?>
    <a class="<?= $currentPage === 'auth' ? 'active' : '' ?>" href="<?= e(url('auth', 'change_password')) ?>">Change Password</a>
    </nav>
</aside>
<?php endif; ?>
