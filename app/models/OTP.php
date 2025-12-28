<?php
namespace App\Models;

use App\Config\Database;
use PDO;

class OTP
{

    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    // ============================================================
    // CREATE OTP
    // ============================================================
    public function create($email, $code, $exp)
    {
        $stmt = $this->db->prepare("
            INSERT INTO otp (user_email, code, expires_at)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$email, $code, $exp]);
    }

    // ============================================================
    // LATEST ACTIVE OTP
    // ============================================================
    public function latestActive($email)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM otp 
            WHERE user_email = ? AND used = 0
            ORDER BY created_at DESC LIMIT 1
        ");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ============================================================
    // MARK USED
    // ============================================================
    public function markUsed($id)
    {
        $stmt = $this->db->prepare("UPDATE otp SET used = 1 WHERE id = ?");
        $stmt->execute([$id]);
    }
}
