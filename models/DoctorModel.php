<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';

final class DoctorModel extends BaseModel
{
    public function findById(int $id): ?array
    {
        return $this->row($this->execute(
            'SELECT d.*, u.name, u.email, u.phone, s.name AS specialization
             FROM doctors d
             JOIN users u ON u.id = d.user_id
             JOIN specializations s ON s.id = d.specialization_id
             WHERE d.id = ?',
            'i',
            [$id]
        ));
    }

    public function findByUserId(int $userId): ?array
    {
        return $this->row($this->execute(
            'SELECT d.*, u.name, u.email, u.phone, s.name AS specialization
             FROM doctors d
             JOIN users u ON u.id = d.user_id
             JOIN specializations s ON s.id = d.specialization_id
             WHERE d.user_id = ?',
            'i',
            [$userId]
        ));
    }

    public function getAll(): array
    {
        return $this->rows($this->execute(
            'SELECT d.id, d.available_days, d.consultation_fee, u.name, s.name AS specialization
             FROM doctors d
             JOIN users u ON u.id = d.user_id
             JOIN specializations s ON s.id = d.specialization_id
             WHERE u.is_active = 1
             ORDER BY u.name'
        ));
    }

    public function getAllPaginated(int $page): array
    {
        $limit = ITEMS_PER_PAGE;
        $offset = ($page - 1) * ITEMS_PER_PAGE;
        return $this->rows($this->execute(
            'SELECT d.*, u.name, u.email, u.phone, s.name AS specialization
             FROM doctors d
             JOIN users u ON u.id = d.user_id
             JOIN specializations s ON s.id = d.specialization_id
             ORDER BY u.name LIMIT ? OFFSET ?',
            'ii',
            [$limit, $offset]
        ));
    }

    public function countAll(): int
    {
        $row = $this->row($this->execute('SELECT COUNT(*) AS total FROM doctors'));
        return (int) ($row['total'] ?? 0);
    }

    public function create(array $data): int
    {
        $this->execute(
            'INSERT INTO doctors (user_id, specialization_id, bio, consultation_fee, available_days, photo) VALUES (?, ?, ?, ?, ?, ?)',
            'iisdss',
            [
                $data['user_id'],
                $data['specialization_id'],
                $data['bio'] ?? null,
                (float) ($data['consultation_fee'] ?? 0),
                $data['available_days'],
                $data['photo'] ?? null,
            ]
        );

        return $this->db->lastInsertId();
    }

    public function update(int $doctorId, array $data): bool
    {
        return (bool) $this->execute(
            'UPDATE doctors SET specialization_id = ?, bio = ?, consultation_fee = ?, available_days = ?, photo = COALESCE(?, photo) WHERE id = ?',
            'isdssi',
            [
                $data['specialization_id'],
                $data['bio'] ?? null,
                (float) ($data['consultation_fee'] ?? 0),
                $data['available_days'],
                $data['photo'] ?? null,
                $doctorId,
            ]
        );
    }

    public function getAvailableDays(int $doctorId): array
    {
        $row = $this->row($this->execute('SELECT available_days FROM doctors WHERE id = ?', 'i', [$doctorId]));
        return $row ? array_filter(array_map('trim', explode(',', $row['available_days']))) : [];
    }
}
