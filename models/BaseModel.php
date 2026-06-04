<?php

declare(strict_types=1);

require_once __DIR__ . '/../core/Database.php';

abstract class BaseModel
{
    protected Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    protected function execute(string $sql, string $types = '', array $params = []): mysqli_result|bool
    {
        return $this->db->query($sql, $types, $params);
    }

    protected function rows(mysqli_result|bool $result): array
    {
        return $result instanceof mysqli_result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    protected function row(mysqli_result|bool $result): ?array
    {
        if (!$result instanceof mysqli_result) {
            return null;
        }

        $row = $result->fetch_assoc();
        return $row ?: null;
    }
}
