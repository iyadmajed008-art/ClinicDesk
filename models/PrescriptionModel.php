<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';

final class PrescriptionModel extends BaseModel
{
    public function findByAppointmentId(int $apptId): ?array
    {
        return $this->row($this->execute('SELECT * FROM prescriptions WHERE appointment_id = ?', 'i', [$apptId]));
    }

    public function create(array $data): int
    {
        $this->execute(
            'INSERT INTO prescriptions (appointment_id, diagnosis, medications, notes, file_path) VALUES (?, ?, ?, ?, ?)',
            'issss',
            [$data['appointment_id'], $data['diagnosis'], $data['medications'], $data['notes'] ?? null, $data['file_path'] ?? null]
        );

        return $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        return (bool) $this->execute(
            'UPDATE prescriptions SET diagnosis = ?, medications = ?, notes = ?, file_path = COALESCE(?, file_path) WHERE id = ?',
            'ssssi',
            [$data['diagnosis'], $data['medications'], $data['notes'] ?? null, $data['file_path'] ?? null, $id]
        );
    }

    public function getByPatient(int $patientId): array
    {
        return $this->rows($this->execute(
            'SELECT pr.*, a.appt_date, du.name AS doctor_name
             FROM prescriptions pr
             JOIN appointments a ON a.id = pr.appointment_id
             JOIN doctors d ON d.id = a.doctor_id
             JOIN users du ON du.id = d.user_id
             WHERE a.patient_id = ?
             ORDER BY pr.created_at DESC',
            'i',
            [$patientId]
        ));
    }

    public function countByPatient(int $patientId): int
    {
        $row = $this->row($this->execute(
            'SELECT COUNT(*) AS total FROM prescriptions pr JOIN appointments a ON a.id = pr.appointment_id WHERE a.patient_id = ?',
            'i',
            [$patientId]
        ));
        return (int) ($row['total'] ?? 0);
    }
}
