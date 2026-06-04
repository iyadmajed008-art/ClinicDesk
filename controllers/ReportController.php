<?php

declare(strict_types=1);

require_once __DIR__ . '/../models/AppointmentModel.php';
require_once __DIR__ . '/../models/DoctorModel.php';

final class ReportController
{
    public function index(): void
    {
        Auth::requireRole('admin');
        $filters = [
            'start_date' => $_GET['start_date'] ?? '',
            'end_date' => $_GET['end_date'] ?? '',
            'doctor_id' => $_GET['doctor_id'] ?? '',
            'status' => $_GET['status'] ?? '',
        ];
        $rows = [];

        if ($filters['start_date'] !== '' && $filters['end_date'] !== '') {
            if ($filters['start_date'] > $filters['end_date']) {
                flash('danger', 'Start date must be before end date.');
            } else {
                $rows = (new AppointmentModel())->report($filters);
            }
        }

        if (($_GET['export'] ?? '') === 'csv') {
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="appointments_report.csv"');
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Patient', 'Doctor', 'Specialization', 'Date', 'Time', 'Status', 'Reason']);
            foreach ($rows as $row) {
                fputcsv($out, [$row['patient_name'], $row['doctor_name'], $row['specialization'], $row['appt_date'], $row['appt_time'], $row['status'], $row['reason']]);
            }
            exit;
        }

        render('reports/index', [
            'pageTitle' => 'Reports',
            'rows' => $rows,
            'filters' => $filters,
            'doctors' => (new DoctorModel())->getAll(),
        ]);
    }
}
