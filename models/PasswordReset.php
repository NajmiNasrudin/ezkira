<?php

namespace Models;

class PasswordReset
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    public function create(string $email, string $token): void
    {
        $expiresAt = date('Y-m-d H:i:s', time() + 3600);
        $hash      = hash('sha256', $token);

        $stmt = $this->db->prepare(
            'INSERT INTO password_resets (email, token, expires_at)
             VALUES (:email, :token, :expires)
             ON DUPLICATE KEY UPDATE token = :token2, expires_at = :expires2, created_at = NOW()'
        );
        $stmt->execute([
            ':email'   => $email,
            ':token'   => $hash,
            ':expires' => $expiresAt,
            ':token2'  => $hash,
            ':expires2'=> $expiresAt,
        ]);
    }

    public function findByToken(string $token): ?array
    {
        $hash = hash('sha256', $token);
        $stmt = $this->db->prepare(
            'SELECT * FROM password_resets WHERE token = :token AND expires_at > NOW() LIMIT 1'
        );
        $stmt->execute([':token' => $hash]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function deleteByEmail(string $email): void
    {
        $stmt = $this->db->prepare('DELETE FROM password_resets WHERE email = :email');
        $stmt->execute([':email' => $email]);
    }
}
