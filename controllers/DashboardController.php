<?php

declare(strict_types=1);

require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/DoctorModel.php';
require_once __DIR__ . '/../models/AppointmentModel.php';
require_once __DIR__ . '/../models/PrescriptionModel.php';

final class DashboardController
{
    public function index(): void
    {
        Auth::requireRole('admin', 'doctor', 'patient');
        $user = Auth::currentUser();
        if (($user['first_login'] ?? 0) === 1) {
            redirect(url('auth', 'change_password'));
        }

        $appointments = new AppointmentModel();

        if ($user['role'] === 'admin') {
            render('dashboard/admin', [
                'pageTitle' => 'Admin Dashboard',
                'userCounts' => (new UserModel())->countsByRole(),
                'appointmentStats' => $appointments->adminStats(),
                'recentAppointments' => $appointments->recent(),
            ]);
            return;
        }

        if ($user['role'] === 'doctor') {
            $doctor = (new DoctorModel())->findByUserId((int) $user['id']);
            render('dashboard/doctor', [
                'pageTitle' => 'Doctor Dashboard',
                'doctor' => $doctor,
                'stats' => $doctor ? $appointments->doctorStats((int) $doctor['id']) : ['counts' => [], 'upcoming' => [], 'today' => []],
            ]);
            return;
        }

        render('dashboard/patient', [
            'pageTitle' => 'Patient Dashboard',
            'stats' => $appointments->patientStats((int) $user['id']),
            'prescriptionCount' => (new PrescriptionModel())->countByPatient((int) $user['id']),
        ]);
    }
}
