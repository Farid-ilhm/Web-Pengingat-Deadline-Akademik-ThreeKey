<?php
namespace App\Models;

use App\Config\Database;
use PDO;

class User
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /* =========================
       CREATE USER
    ========================= */

    public function createManual(string $name, string $email, string $password): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO users (name, email, password, provider, is_verified)
            VALUES (?, ?, ?, 'manual', 0)
        ");

        return $stmt->execute([
            $name,
            $email,
            password_hash($password, PASSWORD_DEFAULT)
        ]);
    }

    public function createGoogleUser(string $name, string $email, string $googleId): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO users (name, email, provider, provider_id, is_verified)
            VALUES (?, ?, 'google', ?, 1)
        ");

        return $stmt->execute([
            $name,
            $email,
            $googleId
        ]);
    }

    /* =========================
       READ USER
    ========================= */

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM users WHERE email = ? LIMIT 1
        ");
        $stmt->execute([$email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM users WHERE id = ? LIMIT 1
        ");
        $stmt->execute([$id]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    /* =========================
       UPDATE USER
    ========================= */

    public function setVerified(string $email): bool
    {
        $stmt = $this->db->prepare("
            UPDATE users SET is_verified = 1 WHERE email = ?
        ");
        return $stmt->execute([$email]);
    }

    public function updatePassword(int $id, string $hash): bool
    {
        $stmt = $this->db->prepare("
            UPDATE users SET password = ? WHERE id = ?
        ");
        return $stmt->execute([$hash, $id]);
    }

    public function setRefreshToken(string $email, string $refreshToken): bool
    {
        $stmt = $this->db->prepare("
            UPDATE users SET provider_refresh_token = ? WHERE email = ?
        ");
        return $stmt->execute([$refreshToken, $email]);
    }

    /**
     * UPDATE PROFIL (AMAN)
     * - Jika $photo NULL → foto lama TIDAK diubah
     * - Jika $photo ADA → foto diganti
     */
    public function updateProfile(
        int $id,
        string $name,
        string $email,
        ?string $photo = null
    ): bool {
        if ($photo) {
            $stmt = $this->db->prepare("
                UPDATE users
                SET name = ?, email = ?, profile_pic = ?
                WHERE id = ?
            ");
            return $stmt->execute([$name, $email, $photo, $id]);
        }

        $stmt = $this->db->prepare("
            UPDATE users
            SET name = ?, email = ?
            WHERE id = ?
        ");
        return $stmt->execute([$name, $email, $id]);
    }

    public function changePassword(int $id, string $newPassword): bool
    {
        $stmt = $this->db->prepare("
            UPDATE users SET password = ? WHERE id = ?
        ");

        return $stmt->execute([
            password_hash($newPassword, PASSWORD_DEFAULT),
            $id
        ]);
    }

    /* =========================
       DELETE USER
    ========================= */

    public function deleteUser(int $id): bool
    {
        $stmt = $this->db->prepare("
            DELETE FROM users WHERE id = ?
        ");
        return $stmt->execute([$id]);
    }

    /* =========================
       ADMIN / LISTING
    ========================= */

    public function allUsers(): array
    {
        $stmt = $this->db->query("
            SELECT id, name, email, role, created_at
            FROM users
            WHERE role = 'user'
            ORDER BY created_at DESC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
