<?php

declare(strict_types=1);

require_once __DIR__ . '/../models/UserModel.php';

final class AuthController
{
    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->authenticate();
            return;
        }

        if (Auth::check()) {
            redirect(url('dashboard'));
        }

        render('auth/login', ['pageTitle' => 'Login']);
    }

    public function logout(): void
    {
        Auth::requireRole('admin', 'doctor', 'patient');
        csrfOrFail();
        Auth::logout();
    }

    public function change_password(): void
    {
        Auth::requireRole('admin', 'doctor', 'patient');
        $userModel = new UserModel();
        $user = $userModel->findById((int) Auth::currentUser()['id']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrfOrFail();
            $current = $_POST['current_password'] ?? '';
            $new = $_POST['new_password'] ?? '';
            $confirm = $_POST['confirm_password'] ?? '';

            if (!$user || !password_verify($current, $user['password'])) {
                flash('danger', 'Current password is incorrect.');
            } elseif ($new !== $confirm || strlen($new) < 8 || !preg_match('/[A-Z]/', $new) || !preg_match('/[a-z]/', $new) || !preg_match('/\d/', $new)) {
                flash('danger', 'New password must be at least 8 characters and include uppercase, lowercase, and a number.');
            } else {
                $userModel->updatePassword((int) $user['id'], password_hash($new, PASSWORD_BCRYPT), 0);
                $_SESSION['user']['first_login'] = 0;
                flash('success', 'Password updated.');
                redirect(url('dashboard'));
            }
        }

        render('auth/change_password', ['pageTitle' => 'Change Password']);
    }

    private function authenticate(): void
    {
        csrfOrFail();
        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';
        $user = (new UserModel())->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            flash('danger', 'Invalid credentials.');
            redirect(url('auth', 'login'));
        }

        if ((int) $user['is_active'] !== 1) {
            flash('danger', 'Account suspended. Contact admin.');
            redirect(url('auth', 'login'));
        }

        Auth::login($user);
        redirect(((int) $user['first_login'] === 1) ? url('auth', 'change_password') : url('dashboard'));
    }
}
