<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

final class Database
{
    private static ?self $instance = null;
    private mysqli $conn;

    private function __construct()
    {
        mysqli_report(MYSQLI_REPORT_OFF);
        $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        if ($this->conn->connect_errno) {
            throw new RuntimeException('Database connection failed.');
        }

        $this->conn->set_charset('utf8mb4');
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function query(string $sql, string $types = '', array $params = []): mysqli_result|bool
    {
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new RuntimeException('Database query failed.');
        }

        if ($types !== '' && $params !== []) {
            $stmt->bind_param($types, ...$params);
        }

        if (!$stmt->execute()) {
            return false;
        }

        $result = $stmt->get_result();
        return $result instanceof mysqli_result ? $result : true;
    }

    public function lastInsertId(): int
    {
        return $this->conn->insert_id;
    }
}
