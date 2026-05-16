<?php

namespace Models;

class ActivityLog
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    public function getByUserId(int $userId, int $limit = 10): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM activity_logs
             WHERE user_id = :uid
             ORDER BY created_at DESC
             LIMIT :lim'
        );
        $stmt->bindValue(':uid', $userId, \PDO::PARAM_INT);
        $stmt->bindValue(':lim', $limit,  \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getAll(int $limit = 50): array
    {
        $stmt = $this->db->prepare(
            'SELECT al.*, u.name as user_name FROM activity_logs al
             LEFT JOIN users u ON u.id = al.user_id
             ORDER BY al.created_at DESC
             LIMIT :lim'
        );
        $stmt->bindValue(':lim', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
