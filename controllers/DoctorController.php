<?php

declare(strict_types=1);

require_once __DIR__ . '/../models/DoctorModel.php';
require_once __DIR__ . '/../models/SpecializationModel.php';

final class DoctorController
{
    public function index(): void
    {
        Auth::requireRole('admin', 'doctor');
        $model = new DoctorModel();
        $page = currentPageNumber();
        render('doctors/index', [
            'pageTitle' => 'Doctors',
            'doctors' => $model->getAllPaginated($page),
            'paginator' => new Paginator($model->countAll(), ITEMS_PER_PAGE, $page),
        ]);
    }

    public function edit(): void
    {
        Auth::requireRole('admin', 'doctor');
        $model = new DoctorModel();
        $id = (int) ($_GET['id'] ?? 0);
        $doctor = $model->findById($id);

        if (!$doctor) {
            render('errors/404', ['pageTitle' => 'Not Found']);
            return;
        }

        if (Auth::role() === 'doctor' && (int) $doctor['user_id'] !== (int) Auth::currentUser()['id']) {
            redirect(url('error', 'forbidden'));
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrfOrFail();
            try {
                $photo = uploadImage($_FILES['photo'] ?? [], 'doctor_photos');
                $model->update($id, [
                    'specialization_id' => (int) $_POST['specialization_id'],
                    'bio' => trim($_POST['bio'] ?? ''),
                    'consultation_fee' => (float) ($_POST['consultation_fee'] ?? 0),
                    'available_days' => implode(',', $_POST['available_days'] ?? []),
                    'photo' => $photo,
                ]);
                flash('success', 'Doctor profile updated.');
                redirect(url('doctors'));
            } catch (Throwable $e) {
                flash('danger', $e->getMessage());
            }
        }

        render('doctors/form', [
            'pageTitle' => 'Edit Doctor',
            'doctor' => $doctor,
            'specializations' => (new SpecializationModel())->getAll(),
        ]);
    }

    public function specializations(): void
    {
        Auth::requireRole('admin');
        $model = new SpecializationModel();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrfOrFail();
            $action = $_POST['form_action'] ?? 'create';
            if ($action === 'delete') {
                $id = (int) $_POST['id'];
                if ($model->isSafeToDelete($id)) {
                    $model->delete($id);
                    flash('success', 'Specialization deleted.');
                } else {
                    flash('danger', 'This specialization is assigned to doctors.');
                }
            } else {
                $model->create(trim($_POST['name'] ?? ''));
                flash('success', 'Specialization added.');
            }
            redirect(url('doctors', 'specializations'));
        }

        render('doctors/specializations', ['pageTitle' => 'Specializations', 'specializations' => $model->getAll()]);
    }
}
