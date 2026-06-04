<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';

final class AppointmentModel extends BaseModel
{
    public function book(array $data): bool
    {
        return (bool) $this->execute(
            'INSERT INTO appointments (patient_id, doctor_id, appt_date, appt_time, reason) VALUES (?, ?, ?, ?, ?)',
            'iisss',
            [$data['patient_id'], $data['doctor_id'], $data['appt_date'], $data['appt_time'], $data['reason'] ?? null]
        );
    }

    public function hasConflict(int $doctorId, string $date, string $time): bool
    {
        $row = $this->row($this->execute(
            "SELECT COUNT(*) AS total FROM appointments WHERE doctor_id = ? AND appt_date = ? AND appt_time = ? AND status <> 'cancelled'",
            'iss',
            [$doctorId, $date, $time]
        ));
        return (int) ($row['total'] ?? 0) > 0;
    }

    public function getByPatient(int $patientId, int $page, array $filters = []): array
    {
        return $this->list('patient', $patientId, $page, $filters);
    }

    public function getByDoctor(int $doctorId, int $page, array $filters = []): array
    {
        return $this->list('doctor', $doctorId, $page, $filters);
    }

    public function getAll(int $page, array $filters = []): array
    {
        return $this->list('admin', 0, $page, $filters);
    }

    public function countFiltered(string $scope, int $scopeId, array $filters = []): int
    {
        [$where, $types, $params] = $this->where($scope, $scopeId, $filters);
        $row = $this->row($this->execute(
            "SELECT COUNT(*) AS total
             FROM appointments a
             JOIN users p ON p.id = a.patient_id
             JOIN doctors d ON d.id = a.doctor_id
             JOIN users du ON du.id = d.user_id
             JOIN specializations s ON s.id = d.specialization_id
             {$where}",
            $types,
            $params
        ));
        return (int) ($row['total'] ?? 0);
    }

    public function updateStatus(int $id, string $status, string $notes = ''): bool
    {
        return (bool) $this->execute(
            'UPDATE appointments SET status = ?, doctor_notes = CASE WHEN ? = "" THEN doctor_notes ELSE ? END WHERE id = ?',
            'sssi',
            [$status, $notes, $notes, $id]
        );
    }

    public function findById(int $id): ?array
    {
        return $this->row($this->execute(
            'SELECT a.*, p.name AS patient_name, p.email AS patient_email, p.phone AS patient_phone,
                    d.user_id AS doctor_user_id, du.name AS doctor_name, s.name AS specialization,
                    pr.id AS prescription_id, pr.file_path
             FROM appointments a
             JOIN users p ON p.id = a.patient_id
             JOIN doctors d ON d.id = a.doctor_id
             JOIN users du ON du.id = d.user_id
             JOIN specializations s ON s.id = d.specialization_id
             LEFT JOIN prescriptions pr ON pr.appointment_id = a.id
             WHERE a.id = ?',
            'i',
            [$id]
        ));
    }

    public function todaysForDoctor(int $doctorId): array
    {
        return $this->rows($this->execute(
            'SELECT a.*, p.name AS patient_name
             FROM appointments a
             JOIN users p ON p.id = a.patient_id
             WHERE a.doctor_id = ? AND a.appt_date = CURDATE()
             ORDER BY a.appt_time',
            'i',
            [$doctorId]
        ));
    }

    public function recent(int $limit = 5): array
    {
        return $this->rows($this->execute(
            'SELECT a.*, p.name AS patient_name, du.name AS doctor_name
             FROM appointments a
             JOIN users p ON p.id = a.patient_id
             JOIN doctors d ON d.id = a.doctor_id
             JOIN users du ON du.id = d.user_id
             ORDER BY a.created_at DESC LIMIT ?',
            'i',
            [$limit]
        ));
    }

    public function adminStats(): array
    {
        $today = $this->row($this->execute('SELECT COUNT(*) AS total FROM appointments WHERE appt_date = CURDATE()'));
        $week = $this->rows($this->execute('SELECT status, COUNT(*) AS total FROM appointments WHERE YEARWEEK(appt_date, 1) = YEARWEEK(CURDATE(), 1) GROUP BY status'));
        return ['today' => (int) ($today['total'] ?? 0), 'week' => $week];
    }

    public function doctorStats(int $doctorId): array
    {
        $counts = $this->rows($this->execute(
            'SELECT status, COUNT(*) AS total FROM appointments
             WHERE doctor_id = ? AND MONTH(appt_date) = MONTH(CURDATE()) AND YEAR(appt_date) = YEAR(CURDATE())
             GROUP BY status',
            'i',
            [$doctorId]
        ));
        $upcoming = $this->rows($this->execute(
            "SELECT a.*, p.name AS patient_name FROM appointments a
             JOIN users p ON p.id = a.patient_id
             WHERE a.doctor_id = ? AND a.appt_date >= CURDATE() AND a.status IN ('pending','confirmed')
             ORDER BY a.appt_date, a.appt_time LIMIT 5",
            'i',
            [$doctorId]
        ));
        return ['counts' => $counts, 'upcoming' => $upcoming, 'today' => $this->todaysForDoctor($doctorId)];
    }

    public function patientStats(int $patientId): array
    {
        $active = $this->rows($this->execute(
            "SELECT a.*, du.name AS doctor_name FROM appointments a
             JOIN doctors d ON d.id = a.doctor_id
             JOIN users du ON du.id = d.user_id
             WHERE a.patient_id = ? AND a.status IN ('pending','confirmed')
             ORDER BY a.appt_date, a.appt_time",
            'i',
            [$patientId]
        ));
        $completed = $this->row($this->execute('SELECT COUNT(*) AS total FROM appointments WHERE patient_id = ? AND status = "completed"', 'i', [$patientId]));
        return ['active' => $active, 'completed' => (int) ($completed['total'] ?? 0), 'next' => $active[0] ?? null];
    }

    public function report(array $filters): array
    {
        [$where, $types, $params] = $this->where('admin', 0, $filters, true);
        return $this->rows($this->execute(
            "SELECT a.*, p.name AS patient_name, du.name AS doctor_name, s.name AS specialization
             FROM appointments a
             JOIN users p ON p.id = a.patient_id
             JOIN doctors d ON d.id = a.doctor_id
             JOIN users du ON du.id = d.user_id
             JOIN specializations s ON s.id = d.specialization_id
             {$where}
             ORDER BY a.appt_date, a.appt_time",
            $types,
            $params
        ));
    }

    private function list(string $scope, int $scopeId, int $page, array $filters): array
    {
        [$where, $types, $params] = $this->where($scope, $scopeId, $filters);
        $params[] = ITEMS_PER_PAGE;
        $params[] = ($page - 1) * ITEMS_PER_PAGE;

        return $this->rows($this->execute(
            "SELECT a.*, p.name AS patient_name, du.name AS doctor_name, s.name AS specialization,
                    pr.id AS prescription_id
             FROM appointments a
             JOIN users p ON p.id = a.patient_id
             JOIN doctors d ON d.id = a.doctor_id
             JOIN users du ON du.id = d.user_id
             JOIN specializations s ON s.id = d.specialization_id
             LEFT JOIN prescriptions pr ON pr.appointment_id = a.id
             {$where}
             ORDER BY a.appt_date DESC, a.appt_time DESC
             LIMIT ? OFFSET ?",
            $types . 'ii',
            $params
        ));
    }

    private function where(string $scope, int $scopeId, array $filters, bool $requireDates = false): array
    {
        $conditions = [];
        $types = '';
        $params = [];

        if ($scope === 'patient') {
            $conditions[] = 'a.patient_id = ?';
            $types .= 'i';
            $params[] = $scopeId;
        } elseif ($scope === 'doctor') {
            $conditions[] = 'a.doctor_id = ?';
            $types .= 'i';
            $params[] = $scopeId;
        }

        foreach (['status' => 'a.status', 'doctor_id' => 'a.doctor_id'] as $key => $column) {
            if (!empty($filters[$key])) {
                $conditions[] = "{$column} = ?";
                $types .= $key === 'doctor_id' ? 'i' : 's';
                $params[] = $filters[$key];
            }
        }

        if (!empty($filters['patient_search'])) {
            $conditions[] = 'p.name LIKE ?';
            $types .= 's';
            $params[] = '%' . $filters['patient_search'] . '%';
        }

        if (!empty($filters['start_date']) || $requireDates) {
            $conditions[] = 'a.appt_date >= ?';
            $types .= 's';
            $params[] = $filters['start_date'] ?? date('Y-m-d');
        }

        if (!empty($filters['end_date']) || $requireDates) {
            $conditions[] = 'a.appt_date <= ?';
            $types .= 's';
            $params[] = $filters['end_date'] ?? date('Y-m-d');
        }

        return [$conditions ? 'WHERE ' . implode(' AND ', $conditions) : '', $types, $params];
    }
}
