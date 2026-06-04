<?php

declare(strict_types=1);

require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/DoctorModel.php';
require_once __DIR__ . '/../models/SpecializationModel.php';

final class UserController
{
    public function index(): void
    {
        Auth::requireRole('admin');
        $model = new UserModel();
        $page = currentPageNumber();
        $role = $_GET['role'] ?? '';
        $search = trim($_GET['search'] ?? '');
        $total = $model->countAll($role, $search);

        render('users/index', [
            'pageTitle' => 'Users',
            'users' => $model->getAllPaginated($page, $role, $search),
            'paginator' => new Paginator($total, ITEMS_PER_PAGE, $page),
            'role' => $role,
            'search' => $search,
        ]);
    }

    public function create(): void
    {
        Auth::requireRole('admin');
        $specializations = (new SpecializationModel())->getAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrfOrFail();
            $role = $_POST['role'] ?? 'patient';
            $userModel = new UserModel();
            $doctorModel = new DoctorModel();
            $password = $_POST['password'] ?? '';

            if ($password === '' || !in_array($role, ['admin', 'doctor', 'patient'], true)) {
                flash('danger', 'Please provide valid user details.');
                redirect(url('users', 'create'));
            }

            try {
                $userId = $userModel->create([
                    'name' => trim($_POST['name'] ?? ''),
                    'email' => filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL),
                    'password' => password_hash($password, PASSWORD_BCRYPT),
                    'role' => $role,
                    'phone' => trim($_POST['phone'] ?? ''),
                    'first_login' => $role === 'admin' ? 0 : 1,
                ]);

                if ($role === 'doctor') {
                    $doctorModel->create([
                        'user_id' => $userId,
                        'specialization_id' => (int) $_POST['specialization_id'],
                        'bio' => trim($_POST['bio'] ?? ''),
                        'consultation_fee' => (float) ($_POST['consultation_fee'] ?? 0),
                        'available_days' => implode(',', $_POST['available_days'] ?? ['Sun', 'Mon', 'Tue', 'Wed', 'Thu']),
                    ]);
                }

                flash('success', 'User created.');
                redirect(url('users'));
            } catch (Throwable $e) {
                flash('danger', 'Could not create user. Check that the email is unique.');
            }
        }

        render('users/form', ['pageTitle' => 'Create User', 'specializations' => $specializations, 'user' => null]);
    }

    public function edit(): void
    {
        Auth::requireRole('admin');
        $model = new UserModel();
        $id = (int) ($_GET['id'] ?? 0);
        $user = $model->findById($id);
        if (!$user) {
            render('errors/404', ['pageTitle' => 'Not Found']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrfOrFail();
            $avatar = null;
            try {
                $avatar = uploadImage($_FILES['avatar'] ?? [], 'avatars');
            } catch (Throwable $e) {
                flash('danger', $e->getMessage());
                redirect(url('users', 'edit', ['id' => $id]));
            }

            $model->update($id, [
                'name' => trim($_POST['name'] ?? ''),
                'phone' => trim($_POST['phone'] ?? ''),
                'avatar' => $avatar,
                'is_active' => isset($_POST['is_active']) ? 1 : 0,
            ]);

            if ($id === (int) Auth::currentUser()['id']) {
                $_SESSION['user']['name'] = trim($_POST['name'] ?? '');
                if ($avatar !== null) {
                    $_SESSION['user']['avatar'] = $avatar;
                }
            }

            flash('success', 'User updated.');
            redirect(url('users'));
        }

        render('users/form', ['pageTitle' => 'Edit User', 'user' => $user, 'specializations' => []]);
    }

    public function toggle(): void
    {
        Auth::requireRole('admin');
        csrfOrFail();
        $id = (int) ($_POST['id'] ?? 0);
        if ($id === (int) Auth::currentUser()['id']) {
            flash('danger', 'You cannot deactivate your own account.');
        } else {
            (new UserModel())->toggleActive($id);
            flash('success', 'User status updated.');
        }
        redirect(url('users'));
    }
}
