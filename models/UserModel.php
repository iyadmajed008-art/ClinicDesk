<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';

final class UserModel extends BaseModel
{
    public function findById(int $id): ?array
    {
        return $this->row($this->execute('SELECT * FROM users WHERE id = ?', 'i', [$id]));
    }

    public function findByEmail(string $email): ?array
    {
        return $this->row($this->execute('SELECT * FROM users WHERE email = ?', 's', [$email]));
    }

    public function create(array $data): int
    {
        $this->execute(
            'INSERT INTO users (name, email, password, role, phone, first_login) VALUES (?, ?, ?, ?, ?, ?)',
            'sssssi',
            [$data['name'], $data['email'], $data['password'], $data['role'], $data['phone'] ?? null, $data['first_login'] ?? 1]
        );

        return $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        return (bool) $this->execute(
            'UPDATE users SET name = ?, phone = ?, avatar = COALESCE(?, avatar), is_active = ? WHERE id = ?',
            'sssii',
            [$data['name'], $data['phone'] ?? null, $data['avatar'] ?? null, (int) $data['is_active'], $id]
        );
    }

    public function updatePassword(int $id, string $newHash, int $firstLogin = 0): bool
    {
        return (bool) $this->execute(
            'UPDATE users SET password = ?, first_login = ? WHERE id = ?',
            'sii',
            [$newHash, $firstLogin, $id]
        );
    }

    public function getAllPaginated(int $page, string $role = '', string $search = ''): array
    {
        $limit = ITEMS_PER_PAGE;
        $offset = ($page - 1) * ITEMS_PER_PAGE;
        [$where, $types, $params] = $this->filters($role, $search);
        $params[] = $limit;
        $params[] = $offset;

        return $this->rows($this->execute(
            "SELECT * FROM users {$where} ORDER BY created_at DESC LIMIT ? OFFSET ?",
            $types . 'ii',
            $params
        ));
    }

    public function countAll(string $role = '', string $search = ''): int
    {
        [$where, $types, $params] = $this->filters($role, $search);
        $row = $this->row($this->execute("SELECT COUNT(*) AS total FROM users {$where}", $types, $params));
        return (int) ($row['total'] ?? 0);
    }

    public function toggleActive(int $id): bool
    {
        return (bool) $this->execute('UPDATE users SET is_active = 1 - is_active WHERE id = ?', 'i', [$id]);
    }

    public function countsByRole(): array
    {
        return $this->rows($this->execute('SELECT role, COUNT(*) AS total FROM users GROUP BY role'));
    }

    private function filters(string $role, string $search): array
    {
        $conditions = [];
        $types = '';
        $params = [];

        if ($role !== '') {
            $conditions[] = 'role = ?';
            $types .= 's';
            $params[] = $role;
        }

        if ($search !== '') {
            $conditions[] = '(name LIKE ? OR email LIKE ?)';
            $types .= 'ss';
            $term = '%' . $search . '%';
            $params[] = $term;
            $params[] = $term;
        }

        return [$conditions ? 'WHERE ' . implode(' AND ', $conditions) : '', $types, $params];
    }
}
