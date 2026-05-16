<?php

namespace Models;

class SessionToken
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    public function create(int $userId, string $tokenHash, string $expiresAt): void
    {
        // Remove any existing tokens for this user first
        $this->deleteByUserId($userId);

        $stmt = $this->db->prepare(
            'INSERT INTO sessions (user_id, token_hash, expires_at)
             VALUES (:uid, :hash, :exp)'
        );
        $stmt->execute([
            ':uid'  => $userId,
            ':hash' => $tokenHash,
            ':exp'  => $expiresAt,
        ]);
    }

    public function findByTokenHash(string $hash): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT s.*, u.* FROM sessions s
             JOIN users u ON u.id = s.user_id
             WHERE s.token_hash = :hash AND s.expires_at > NOW()
             LIMIT 1'
        );
        $stmt->execute([':hash' => $hash]);
        return $stmt->fetch() ?: null;
    }

    public function deleteByUserId(int $userId): void
    {
        $stmt = $this->db->prepare('DELETE FROM sessions WHERE user_id = :uid');
        $stmt->execute([':uid' => $userId]);
    }

    public function deleteByTokenHash(string $hash): void
    {
        $stmt = $this->db->prepare('DELETE FROM sessions WHERE token_hash = :hash');
        $stmt->execute([':hash' => $hash]);
    }

    public function deleteExpired(): void
    {
        $this->db->exec('DELETE FROM sessions WHERE expires_at <= NOW()');
    }
}
