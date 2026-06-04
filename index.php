<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/core/helpers.php';
require_once __DIR__ . '/core/Auth.php';
require_once __DIR__ . '/core/CSRF.php';
require_once __DIR__ . '/core/Paginator.php';

function render(string $view, array $data = []): void
{
    extract($data, EXTR_SKIP);
    require __DIR__ . '/views/' . $view . '.php';
}

function csrfOrFail(): void
{
    if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
        flash('danger', 'Invalid form token. Please try again.');
        redirect($_SERVER['HTTP_REFERER'] ?? url('dashboard'));
    }
}

$page = preg_replace('/[^a-z_]/', '', $_GET['page'] ?? 'dashboard');
$action = preg_replace('/[^a-z_]/', '', $_GET['action'] ?? 'index');

$controllers = [
    'auth' => 'AuthController',
    'dashboard' => 'DashboardController',
    'users' => 'UserController',
    'doctors' => 'DoctorController',
    'appointments' => 'AppointmentController',
    'prescriptions' => 'PrescriptionController',
    'reports' => 'ReportController',
    'error' => 'ErrorController',
];

if (!isset($controllers[$page])) {
    render('errors/404', ['pageTitle' => 'Not Found']);
    exit;
}

require_once __DIR__ . '/controllers/' . $controllers[$page] . '.php';
$controller = new $controllers[$page]();

if (!method_exists($controller, $action)) {
    render('errors/404', ['pageTitle' => 'Not Found']);
    exit;
}

$controller->$action();
