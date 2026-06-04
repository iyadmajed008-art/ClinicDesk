<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';

final class SpecializationModel extends BaseModel
{
    public function getAll(): array
    {
        return $this->rows($this->execute('SELECT * FROM specializations ORDER BY name'));
    }

    public function create(string $name): bool
    {
        return (bool) $this->execute('INSERT INTO specializations (name) VALUES (?)', 's', [$name]);
    }

    public function delete(int $id): bool
    {
        return (bool) $this->execute('DELETE FROM specializations WHERE id = ?', 'i', [$id]);
    }

    public function isSafeToDelete(int $id): bool
    {
        $row = $this->row($this->execute('SELECT COUNT(*) AS total FROM doctors WHERE specialization_id = ?', 'i', [$id]));
        return (int) ($row['total'] ?? 0) === 0;
    }
}
