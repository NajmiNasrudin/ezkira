<?php

namespace Models;

use App\Core\ReceiptImage;
use PDO;

/**
 * One-off clean-up for receipt photos uploaded before ReceiptImage existed.
 * Re-encodes large photos in place (PNG/WebP become .jpg) and keeps the DB rows in step.
 * Runs in short time-boxed batches so it fits shared-hosting request limits; the admin
 * page calls it repeatedly with the returned cursor until `done` is true.
 */
class ReceiptShrinker
{
    private const MIN_BYTES  = 400 * 1024; // smaller files are left alone
    private const MIN_SAVING = 0.10;       // keep the original unless we save at least 10%
    private const IMAGE_EXT  = ['jpg', 'jpeg', 'png', 'webp'];

    private PDO $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    /**
     * @param string $cursor "r:<id>" (expense_receipts) then "e:<id>" (legacy expenses.receipt_path)
     */
    public function run(string $cursor, float $seconds = 20.0): array
    {
        $stop = microtime(true) + $seconds;
        [$table, $after] = self::parseCursor($cursor);
        $stats = ['checked' => 0, 'shrunk' => 0, 'before' => 0, 'after' => 0];

        while (microtime(true) < $stop) {
            $row = $this->next($table, $after);
            if ($row === null) {
                if ($table === 'r') {
                    [$table, $after] = ['e', 0];
                    continue;
                }
                return $stats + ['cursor' => 'e:' . $after, 'done' => true];
            }
            $after = (int) $row['id'];
            $stats['checked']++;
            if ($saved = $this->shrink($table, $row)) {
                $stats['shrunk']++;
                $stats['before'] += $saved[0];
                $stats['after']  += $saved[1];
            }
        }

        return $stats + ['cursor' => $table . ':' . $after, 'done' => false];
    }

    private static function parseCursor(string $cursor): array
    {
        return preg_match('/^([re]):(\d+)$/', $cursor, $m) ? [$m[1], (int) $m[2]] : ['r', 0];
    }

    private function next(string $table, int $after): ?array
    {
        $sql = $table === 'r'
            ? 'SELECT id, path, name FROM expense_receipts WHERE id > ? ORDER BY id LIMIT 1'
            : "SELECT id, receipt_path AS path, receipt_name AS name FROM expenses
                WHERE id > ? AND receipt_path IS NOT NULL AND receipt_path <> '' ORDER BY id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$after]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /** @return array{int,int}|null [bytes before, bytes after] when the file was replaced */
    private function shrink(string $table, array $row): ?array
    {
        $path = (string) $row['path'];
        $full = BASE_PATH . '/' . $path;
        if (!str_starts_with($path, 'uploads/receipts/') || str_contains($path, '..')
            || !in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), self::IMAGE_EXT, true)
            || !is_file($full)) {
            return null;
        }
        $before = (int) filesize($full);
        if ($before < self::MIN_BYTES) {
            return null;
        }

        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($full) ?: '';
        $tmp  = ReceiptImage::compress($full, $mime);
        if ($tmp === null) {
            return null;
        }
        $after = (int) filesize($tmp);
        if ($after < 1 || $after > $before * (1 - self::MIN_SAVING)) {
            @unlink($tmp);
            return null;
        }

        $newPath = preg_replace('/\.[^.\/]+$/', '.jpg', $path);
        $newFull = BASE_PATH . '/' . $newPath;
        if ($newPath !== $path && file_exists($newFull)) {
            @unlink($tmp);
            return null;
        }
        if (!@rename($tmp, $newFull)) {
            @unlink($tmp);
            return null;
        }
        @chmod($newFull, 0644);

        $newName = $row['name'] !== null ? preg_replace('/\.(png|webp|jpe?g)$/i', '.jpg', (string) $row['name']) : null;
        // Update by path in both tables, in case a legacy row and a multi-file row share the file
        $nameSql = $table === 'r'
            ? 'UPDATE expense_receipts SET name = ? WHERE id = ?'
            : 'UPDATE expenses SET receipt_name = ? WHERE id = ?';
        try {
            $this->db->beginTransaction();
            $this->db->prepare('UPDATE expense_receipts SET path = ?, size_bytes = ? WHERE path = ?')
                ->execute([$newPath, $after, $path]);
            $this->db->prepare('UPDATE expenses SET receipt_path = ? WHERE receipt_path = ?')
                ->execute([$newPath, $path]);
            if ($newName !== null) {
                $this->db->prepare($nameSql)->execute([$newName, $row['id']]);
            }
            $this->db->commit();
        } catch (\Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log('Receipt shrink DB update failed: ' . $e->getMessage());
            if ($newPath !== $path) {
                @unlink($newFull); // original is untouched, keep using it
            }
            return null;
        }

        if ($newPath !== $path) {
            @unlink($full);
        }
        return [$before, $after];
    }
}
