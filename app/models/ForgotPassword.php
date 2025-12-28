<?php
namespace App\Models;

use App\Config\Database;
use PDO;

class ForgotPassword
{

    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    // ============================================================
    // SIMPAN OTP RESET PASSWORD
    // ============================================================
    public function createOtp($email, $code, $exp)
    {
        $stmt = $this->db->prepare("
            INSERT INTO forgot_password (user_email, otp_code, expires_at, used)
            VALUES (?, ?, ?, 0)
        ");
        $stmt->execute([$email, $code, $exp]);
    }

    // ============================================================
    // AMBIL OTP YANG BELUM DIGUNAKAN
    // ============================================================
    public function latestOtp($email)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM forgot_password 
            WHERE user_email = ? AND used = 0
            ORDER BY id DESC LIMIT 1
        ");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ============================================================
    // TANDAI OTP SUDAH DIPAKAI
    // ============================================================
    public function markUsed($id)
    {
        $stmt = $this->db->prepare("UPDATE forgot_password SET used = 1 WHERE id = ?");
        $stmt->execute([$id]);
    }
}
