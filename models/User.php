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

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO users (name, email, password, whatsapp_number, business_type, business_type_other, role, language, dark_mode, created_at, updated_at)
             VALUES (:name, :email, :password, :whatsapp, :business_type, :business_type_other, :role, :lang, :dark, NOW(), NOW())'
        );
        $stmt->execute([
            ':name'                => $data['name'],
            ':email'               => $data['email'],
            ':password'            => $data['password'],
            ':whatsapp'            => $data['whatsapp_number'],
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
            'name', 'pic_name', 'email', 'password', 'whatsapp_number',
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
