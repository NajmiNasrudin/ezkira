<?php

namespace Models;

class User
{
    private \PDO $db;

    public const BUSINESS_TYPES = [
        'fnb'           => 'Makanan & Minuman (F&B)',
        'fashion'       => 'Fesyen & Pakaian',
        'beauty'        => 'Kecantikan & Kesihatan',
        'retail'        => 'Runcit / Kedai',
        'services'      => 'Perkhidmatan',
        'education'     => 'Pendidikan & Latihan',
        'technology'    => 'Teknologi & IT',
        'manufacturing' => 'Pembuatan',
        'agriculture'   => 'Pertanian',
        'other'         => 'Lain-lain',
    ];

    public function __construct()
    {
        $this->db = getDB();
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);
        return $stmt->fetch() ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function findByGoogleId(string $googleId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE google_id = :gid LIMIT 1');
        $stmt->execute([':gid' => $googleId]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO users (name, email, password, whatsapp_number, google_id, business_type, business_type_other, role, language, dark_mode, created_at, updated_at)
             VALUES (:name, :email, :password, :whatsapp, :google_id, :business_type, :business_type_other, :role, :lang, :dark, NOW(), NOW())'
        );
        $stmt->execute([
            ':name'                => $data['name'],
            ':email'               => $data['email'],
            ':password'            => $data['password'],
            ':whatsapp'            => $data['whatsapp_number'],
            ':google_id'           => $data['google_id']           ?? null,
            ':business_type'       => $data['business_type']       ?? null,
            ':business_type_other' => $data['business_type_other'] ?? null,
            ':role'                => $data['role']                ?? 'client',
            ':lang'                => $data['language']            ?? 'en',
            ':dark'                => $data['dark_mode']           ?? 0,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $allowed = [
            'name', 'pic_name', 'email', 'password', 'whatsapp_number', 'google_id',
            'business_type', 'business_type_other', 'role', 'language', 'dark_mode', 'profile_image',
        ];

        $sets   = [];
        $params = [':id' => $id];

        foreach ($data as $key => $value) {
            if (in_array($key, $allowed, true)) {
                $sets[]            = "`{$key}` = :{$key}";
                $params[":{$key}"] = $value;
            }
        }

        if (empty($sets)) {
            return false;
        }

        $sets[] = 'updated_at = NOW()';
        $sql    = 'UPDATE users SET ' . implode(', ', $sets) . ' WHERE id = :id';
        $stmt   = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /** All users that have a WhatsApp number — for blast */
    public function allWithPhone(): array
    {
        $stmt = $this->db->prepare(
            "SELECT id, name, email, whatsapp_number, role, business_type
             FROM users
             WHERE whatsapp_number IS NOT NULL AND whatsapp_number != ''
             ORDER BY name ASC"
        );
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /** Fetch multiple users by ID array — for blast */
    public function findManyByIds(array $ids): array
    {
        if (empty($ids)) return [];
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $this->db->prepare(
            "SELECT id, name, email, whatsapp_number, role
             FROM users WHERE id IN ({$placeholders})"
        );
        $stmt->execute($ids);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function emailExists(string $email, ?int $excludeId = null): bool
    {
        if ($excludeId) {
            $stmt = $this->db->prepare(
                'SELECT COUNT(*) FROM users WHERE email = :email AND id != :id'
            );
            $stmt->execute([':email' => $email, ':id' => $excludeId]);
        } else {
            $stmt = $this->db->prepare(
                'SELECT COUNT(*) FROM users WHERE email = :email'
            );
            $stmt->execute([':email' => $email]);
        }
        return (int)$stmt->fetchColumn() > 0;
    }
}
