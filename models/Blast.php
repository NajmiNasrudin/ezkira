<?php

namespace Models;

class Blast
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    // Create a blast log entry, return its ID
    public function createLog(int $sentBy, string $template, ?string $customMsg, int $total): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO blast_logs (sent_by, template_name, custom_message, total_recipients)
             VALUES (:by, :tpl, :msg, :total)'
        );
        $stmt->execute([':by' => $sentBy, ':tpl' => $template, ':msg' => $customMsg, ':total' => $total]);
        return (int)$this->db->lastInsertId();
    }

    public function updateLog(int $blastId, int $sent, int $failed): void
    {
        $this->db->prepare(
            'UPDATE blast_logs SET sent_count=:s, failed_count=:f WHERE id=:id'
        )->execute([':s' => $sent, ':f' => $failed, ':id' => $blastId]);
    }

    public function logRecipient(int $blastId, ?int $userId, string $name, string $phone, string $status, ?string $error): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO blast_recipients (blast_id, user_id, name, phone, status, error_msg, sent_at)
             VALUES (:bid, :uid, :name, :phone, :status, :err, :ts)'
        );
        $stmt->execute([
            ':bid'    => $blastId,
            ':uid'    => $userId,
            ':name'   => $name,
            ':phone'  => $phone,
            ':status' => $status,
            ':err'    => $error,
            ':ts'     => $status === 'sent' ? date('Y-m-d H:i:s') : null,
        ]);
    }

    public function history(int $limit = 20): array
    {
        $stmt = $this->db->prepare(
            'SELECT b.*, u.name AS sender_name
             FROM blast_logs b
             LEFT JOIN users u ON b.sent_by = u.id
             ORDER BY b.created_at DESC LIMIT ' . (int)$limit
        );
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function recipients(int $blastId): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM blast_recipients WHERE blast_id = ? ORDER BY id ASC'
        );
        $stmt->execute([$blastId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findLog(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM blast_logs WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }
}
