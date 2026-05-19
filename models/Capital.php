<?php

namespace Models;

class Capital
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    public function byMonth(int $year, int $month, int $userId): array
    {
        $stmt = $this->db->prepare(
            "SELECT c.*, u.name AS added_by
             FROM capitals c
             JOIN users u ON c.user_id = u.id
             WHERE YEAR(c.capital_date) = ? AND MONTH(c.capital_date) = ? AND c.user_id = ?
             ORDER BY c.capital_date DESC, c.created_at DESC"
        );
        $stmt->execute([$year, $month, $userId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function totalByMonth(int $year, int $month, int $userId): float
    {
        $stmt = $this->db->prepare(
            "SELECT COALESCE(SUM(amount), 0) FROM capitals
             WHERE YEAR(capital_date) = ? AND MONTH(capital_date) = ? AND user_id = ?"
        );
        $stmt->execute([$year, $month, $userId]);
        return (float) $stmt->fetchColumn();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO capitals (user_id, amount, description, capital_date)
             VALUES (:user_id, :amount, :description, :capital_date)'
        );
        $stmt->execute([
            ':user_id'      => $data['user_id'],
            ':amount'       => $data['amount'],
            ':description'  => $data['description'],
            ':capital_date' => $data['capital_date'],
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM capitals WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE capitals SET amount=:amount, description=:description, capital_date=:capital_date
             WHERE id=:id AND user_id=:user_id'
        );
        return $stmt->execute([
            ':amount'       => $data['amount'],
            ':description'  => $data['description'],
            ':capital_date' => $data['capital_date'],
            ':id'           => $id,
            ':user_id'      => $data['user_id'],
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM capitals WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
