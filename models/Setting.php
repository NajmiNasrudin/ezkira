<?php

namespace Models;

class Setting
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $stmt = $this->db->prepare(
            'SELECT value FROM settings WHERE `key` = :key LIMIT 1'
        );
        $stmt->execute([':key' => $key]);
        $row = $stmt->fetch();
        return $row ? $row['value'] : $default;
    }

    public function set(string $key, ?string $value): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO settings (`key`, value) VALUES (:key, :val)
             ON DUPLICATE KEY UPDATE value = :val2, updated_at = NOW()'
        );
        $stmt->execute([':key' => $key, ':val' => $value, ':val2' => $value]);
    }

    public function all(): array
    {
        return $this->db->query('SELECT * FROM settings ORDER BY `key`')->fetchAll();
    }
}
