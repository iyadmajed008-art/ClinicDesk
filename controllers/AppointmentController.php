<?php

declare(strict_types=1);

require_once __DIR__ . '/../models/AppointmentModel.php';
require_once __DIR__ . '/../models/DoctorModel.php';

final class AppointmentController
{
    public function index(): void
    {
        Auth::requireRole('admin', 'doctor', 'patient');
        $model = new AppointmentModel();
        $page = currentPageNumber();
        $filters = [
            'status' => $_GET['status'] ?? '',
            'doctor_id' => $_GET['doctor_id'] ?? '',
            'patient_search' => trim($_GET['patient_search'] ?? ''),
            'start_date' => $_GET['start_date'] ?? '',
            'end_date' => $_GET['end_date'] ?? '',
        ];
        $role = Auth::role();
        $scopeId = 0;

        if ($role === 'patient') {
            $scope = 'patient';
            $scopeId = (int) Auth::currentUser()['id'];
            $appointments = $model->getByPatient($scopeId, $page, $filters);
        } elseif ($role === 'doctor') {
            $scope = 'doctor';
            $doctor = (new DoctorModel())->findByUserId((int) Auth::currentUser()['id']);
            $scopeId = (int) ($doctor['id'] ?? 0);
            $appointments = $model->getByDoctor($scopeId, $page, $filters);
        } else {
            $scope = 'admin';
            $appointments = $model->getAll($page, $filters);
        }

        render('appointments/index', [
            'pageTitle' => 'Appointments',
            'appointments' => $appointments,
            'paginator' => new Paginator($model->countFiltered($scope, $scopeId, $filters), ITEMS_PER_PAGE, $page),
            'filters' => $filters,
            'doctors' => (new DoctorModel())->getAll(),
        ]);
    }

    public function book(): void
    {
        Auth::requireRole('patient');
        $doctorModel = new DoctorModel();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrfOrFail();
            $doctorId = (int) $_POST['doctor_id'];
            $date = $_POST['appt_date'] ?? '';
            $time = $_POST['appt_time'] ?? '';
            $days = $doctorModel->getAvailableDays($doctorId);
            $dayName = date('D', strtotime($date));

            if ($date < date('Y-m-d')) {
                flash('danger', 'Appointment date cannot be in the past.');
            } elseif (!in_array($dayName, $days, true)) {
                flash('danger', 'The selected doctor is not available on that day.');
            } elseif ((new AppointmentModel())->hasConflict($doctorId, $date, $time)) {
                flash('danger', 'This slot is already booked, please choose another time.');
            } else {
                (new AppointmentModel())->book([
                    'patient_id' => (int) Auth::currentUser()['id'],
                    'doctor_id' => $doctorId,
                    'appt_date' => $date,
                    'appt_time' => $time,
                    'reason' => trim($_POST['reason'] ?? ''),
                ]);
                flash('success', 'Appointment requested.');
                redirect(url('appointments'));
            }
        }

        render('appointments/book', ['pageTitle' => 'Book Appointment', 'doctors' => $doctorModel->getAll()]);
    }

    public function detail(): void
    {
        Auth::requireRole('admin', 'doctor', 'patient');
        $appointment = (new AppointmentModel())->findById((int) ($_GET['id'] ?? 0));
        if (!$appointment || !$this->canAccess($appointment)) {
            redirect(url('error', 'forbidden'));
        }

        render('appointments/detail', ['pageTitle' => 'Appointment Detail', 'appointment' => $appointment]);
    }

    public function update_status(): void
    {
        Auth::requireRole('admin', 'doctor');
        csrfOrFail();
        $model = new AppointmentModel();
        $appointment = $model->findById((int) ($_POST['id'] ?? 0));
        if (!$appointment || !$this->canAccess($appointment)) {
            redirect(url('error', 'forbidden'));
        }

        $status = $_POST['status'] ?? '';
        if (in_array($status, ['pending', 'confirmed', 'completed', 'cancelled'], true)) {
            $model->updateStatus((int) $appointment['id'], $status, trim($_POST['doctor_notes'] ?? ''));
            flash('success', 'Appointment updated.');
        }
        redirect(url('appointments', 'detail', ['id' => $appointment['id']]));
    }

    public function cancel(): void
    {
        Auth::requireRole('patient');
        csrfOrFail();
        $model = new AppointmentModel();
        $appointment = $model->findById((int) ($_POST['id'] ?? 0));
        if (!$appointment || (int) $appointment['patient_id'] !== (int) Auth::currentUser()['id'] || $appointment['status'] !== 'pending') {
            redirect(url('error', 'forbidden'));
        }

        $model->updateStatus((int) $appointment['id'], 'cancelled');
        flash('success', 'Appointment cancelled.');
        redirect(url('appointments'));
    }

    private function canAccess(array $appointment): bool
    {
        $user = Auth::currentUser();
        if ($user['role'] === 'admin') {
            return true;
        }
        if ($user['role'] === 'patient') {
            return (int) $appointment['patient_id'] === (int) $user['id'];
        }
        return (int) $appointment['doctor_user_id'] === (int) $user['id'];
    }
}
