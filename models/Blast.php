<?php

namespace Models;

class Blast
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    // ------------------------------------------------------------------
    // Queue a new blast — returns blast_id
    // ------------------------------------------------------------------
    public function queue(
        int     $sentBy,
        string  $message,
        string  $recipientIds,   // JSON-encoded: ["all"]  or  [1,2,3,…]
        int     $total,
        string  $imagePath   = '',
        string  $blastLink   = '',
        ?string $scheduledAt = null,
        string  $provider    = 'fonnte',
        int     $delaySecs   = 12        // min seconds between sends
    ): int {
        $status = ($scheduledAt && strtotime($scheduledAt) > time())
            ? 'scheduled'
            : 'queued';

        $stmt = $this->db->prepare(
            'INSERT INTO blast_logs
               (status, provider, sent_by, template_name, custom_message, total_recipients,
                scheduled_at, recipient_ids, image_path, blast_link, delay_seconds, sent_count, failed_count)
             VALUES
               (:status, :provider, :by, :tpl, :msg, :total,
                :sched, :rids, :img, :link, :delay, 0, 0)'
        );
        $stmt->execute([
            ':status'   => $status,
            ':provider' => $provider,
            ':by'       => $sentBy,
            ':tpl'      => $provider,
            ':msg'      => $message,
            ':total'    => $total,
            ':sched'    => $scheduledAt ?: null,
            ':rids'     => $recipientIds,
            ':img'      => $imagePath,
            ':link'     => $blastLink,
            ':delay'    => $delaySecs,
        ]);
        return (int)$this->db->lastInsertId();
    }

    // ------------------------------------------------------------------
    // Cron: get the oldest blast that is due and not yet running
    // ------------------------------------------------------------------
    public function getNextDue(): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM blast_logs
              WHERE status IN ('queued','scheduled')
                AND (scheduled_at IS NULL OR scheduled_at <= NOW())
                AND NOT EXISTS (
                    SELECT 1 FROM blast_logs bl2 WHERE bl2.status = 'running'
                )
              ORDER BY created_at ASC
              LIMIT 1"
        );
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function markRunning(int $id): void
    {
        $this->db->prepare(
            "UPDATE blast_logs SET status='running', started_at=NOW() WHERE id=?"
        )->execute([$id]);
    }

    public function markDone(int $id, int $sent, int $failed): void
    {
        $this->db->prepare(
            "UPDATE blast_logs
                SET status='done', sent_count=:s, failed_count=:f, finished_at=NOW()
              WHERE id=:id"
        )->execute([':s' => $sent, ':f' => $failed, ':id' => $id]);
    }

    public function markStopped(int $id, int $sent, int $failed): void
    {
        $this->db->prepare(
            "UPDATE blast_logs
                SET status='stopped', sent_count=:s, failed_count=:f, finished_at=NOW()
              WHERE id=:id"
        )->execute([':s' => $sent, ':f' => $failed, ':id' => $id]);
    }

    public function getStatus(int $id): string
    {
        $stmt = $this->db->prepare('SELECT status FROM blast_logs WHERE id=? LIMIT 1');
        $stmt->execute([$id]);
        return (string)($stmt->fetchColumn() ?: '');
    }

    public function markFailed(int $id, int $sent, int $failed): void
    {
        $this->db->prepare(
            "UPDATE blast_logs
                SET status='failed', sent_count=:s, failed_count=:f, finished_at=NOW()
              WHERE id=:id"
        )->execute([':s' => $sent, ':f' => $failed, ':id' => $id]);
    }

    /** Live progress update — called after every send inside cron */
    public function updateProgress(int $id, int $sent, int $failed): void
    {
        $this->db->prepare(
            'UPDATE blast_logs SET sent_count=:s, failed_count=:f WHERE id=:id'
        )->execute([':s' => $sent, ':f' => $failed, ':id' => $id]);
    }

    // ------------------------------------------------------------------
    // Progress / status endpoint
    // ------------------------------------------------------------------
    public function getProgress(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT id, status, total_recipients, sent_count, failed_count,
                    scheduled_at, started_at, finished_at, custom_message, created_at
               FROM blast_logs WHERE id=?'
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    // ------------------------------------------------------------------
    // Legacy helpers (keep for history view + recipients modal)
    // ------------------------------------------------------------------
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
