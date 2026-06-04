<?php

declare(strict_types=1);

require_once __DIR__ . '/../models/PrescriptionModel.php';
require_once __DIR__ . '/../models/AppointmentModel.php';
require_once __DIR__ . '/../models/DoctorModel.php';

final class PrescriptionController
{
    public function index(): void
    {
        Auth::requireRole('patient');
        render('prescriptions/index', [
            'pageTitle' => 'My Prescriptions',
            'prescriptions' => (new PrescriptionModel())->getByPatient((int) Auth::currentUser()['id']),
        ]);
    }

    public function create(): void
    {
        Auth::requireRole('doctor');
        $appointmentModel = new AppointmentModel();
        $prescriptionModel = new PrescriptionModel();
        $appointment = $appointmentModel->findById((int) ($_GET['appointment_id'] ?? $_POST['appointment_id'] ?? 0));

        if (!$appointment || !$this->doctorOwns($appointment) || $appointment['status'] !== 'completed' || $prescriptionModel->findByAppointmentId((int) $appointment['id'])) {
            redirect(url('error', 'forbidden'));
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrfOrFail();
            try {
                $filePath = uploadPrescriptionPdf($_FILES['prescription_file'] ?? [], (int) $appointment['id']);
                $prescriptionModel->create([
                    'appointment_id' => (int) $appointment['id'],
                    'diagnosis' => trim($_POST['diagnosis'] ?? ''),
                    'medications' => trim($_POST['medications'] ?? ''),
                    'notes' => trim($_POST['notes'] ?? ''),
                    'file_path' => $filePath,
                ]);
                flash('success', 'Prescription added.');
                redirect(url('appointments', 'detail', ['id' => $appointment['id']]));
            } catch (Throwable $e) {
                flash('danger', $e->getMessage());
            }
        }

        render('prescriptions/form', ['pageTitle' => 'Add Prescription', 'appointment' => $appointment]);
    }

    public function download(): void
    {
        Auth::requireRole('admin', 'doctor', 'patient');
        $appointment = (new AppointmentModel())->findById((int) ($_GET['id'] ?? 0));
        $prescription = $appointment ? (new PrescriptionModel())->findByAppointmentId((int) $appointment['id']) : null;

        if (!$appointment || !$prescription || !$this->canAccess($appointment)) {
            redirect(url('error', 'forbidden'));
        }

        $path = __DIR__ . '/../' . $prescription['file_path'];
        if (!$prescription['file_path'] || !is_file($path)) {
            flash('danger', 'Prescription file is missing.');
            redirect(url('appointments', 'detail', ['id' => $appointment['id']]));
        }

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="prescription.pdf"');
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;
    }

    private function doctorOwns(array $appointment): bool
    {
        return (int) $appointment['doctor_user_id'] === (int) Auth::currentUser()['id'];
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
