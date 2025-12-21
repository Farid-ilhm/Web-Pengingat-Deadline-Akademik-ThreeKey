<?php
namespace App\Controllers;

use App\Models\User;
use App\Models\OTP;
use App\Models\ForgotPassword;
use App\Helpers\Session;
use App\Helpers\Email;

class AuthController {

    private $user;
    private $otp;
    private $fp;

    public function __construct() {
        $this->user = new User();
        $this->otp  = new OTP();
        $this->fp   = new ForgotPassword();
    }

    // ============================================================
    //  REGISTER + OTP VERIFIKASI
    // ============================================================

    public function register($name, $email, $password) {
        if ($this->user->findByEmail($email))
            return ['success' => false, 'message' => 'Email sudah terdaftar'];

        $this->user->createManual($name, $email, $password);

        $code = rand(100000, 999999);
        $exp  = date("Y-m-d H:i:s", strtotime("+{$_ENV['OTP_EXPIRY_MINUTES']} minutes"));

        $this->otp->create($email, $code, $exp);

        Email::send($email, "OTP Verifikasi", "<b>Kode OTP Anda: $code</b>");

        return ['success' => true, 'message' => 'OTP telah dikirim ke email'];
    }

    public function verifyOtp($email, $code) {
        $otp = $this->otp->latestActive($email);

        if (!$otp) return ['success' => false, 'message' => 'Tidak ada OTP aktif'];
        if ($otp['code'] != $code) return ['success' => false, 'message' => 'OTP salah'];
        if (strtotime($otp['expires_at']) < time()) return ['success' => false, 'message' => 'OTP kadaluarsa'];

        $this->otp->markUsed($otp['id']);
        $this->user->setVerified($email);

        return ['success' => true, 'message' => 'Akun terverifikasi'];
    }

    // ============================================================
    //  LOGIN MANUAL
    // ============================================================

    public function loginManual($email, $password) {
        $u = $this->user->findByEmail($email);

        if (!$u) {
            return ['success' => false, 'message' => 'Email tidak ditemukan'];
        }

        // ⛔ BLOK akun Google
        if ($u['provider'] === 'google') {
            return [
                'success' => false,
                'message' => 'Akun ini terdaftar melalui Google. Silakan login dengan Google.'
            ];
        }

        // ⛔ CEK password NULL (aman PHP 8+)
        if (empty($u['password'])) {
            return [
                'success' => false,
                'message' => 'Akun ini tidak memiliki password.'
            ];
        }

        if (!$u['is_verified']) {
            return [
                'success' => false,
                'message' => 'Akun belum diverifikasi'
            ];
        }

        if (!password_verify($password, $u['password'])) {
            return [
                'success' => false,
                'message' => 'Password salah'
            ];
        }

        // ✅ Login sukses
        Session::set('user', [
            'id'       => $u['id'],
            'name'     => $u['name'],
            'email'    => $u['email'],
            'role'     => $u['role'],
            'provider' => $u['provider'],
            'profile_pic' => $u['profile_pic'] ?? null
        ]);

        return [
            'success' => true,
            'role'    => $u['role']
        ];
    }

    // ============================================================
    //  RESEND OTP
    // ============================================================

    public function resendOtp($email) {
        if (!$this->user->findByEmail($email))
            return ['success'=>false, 'message'=>'Email tidak ditemukan'];

        $code = rand(100000, 999999);
        $exp  = date("Y-m-d H:i:s", strtotime("+10 minutes"));

        $this->otp->create($email, $code, $exp);

        Email::send($email, "OTP Baru", "<b>Kode OTP Anda: $code</b>");

        return ['success' => true];
    }

    // ============================================================
    //  RESET PASSWORD FULL OTP (BARU)
    // ============================================================

    // 1. Request OTP untuk reset password
    public function requestResetOtp($email) {
        $u = $this->user->findByEmail($email);
        if (!$u) return ['success'=>false,'message'=>'Email tidak ditemukan'];

        $code = rand(100000, 999999);
        $exp  = date("Y-m-d H:i:s", strtotime("+10 minutes"));

        $this->fp->createOtp($email, $code, $exp);

        Email::send($email, "OTP Reset Password", "<b>Kode OTP reset password Anda: $code</b>");

        return ['success'=>true,'message'=>'OTP reset password telah dikirim ke email'];
    }

    // 2. Verifikasi OTP reset password
    public function verifyResetOtp($email, $code) {
        $otp = $this->fp->latestOtp($email);

        if (!$otp) return ['success'=>false,'message'=>'Tidak ada OTP aktif'];
        if ($otp['otp_code'] != $code) return ['success'=>false,'message'=>'OTP salah'];
        if (strtotime($otp['expires_at']) < time()) return ['success'=>false,'message'=>'OTP kadaluarsa'];

        $this->fp->markUsed($otp['id']);
        return ['success'=>true, 'message'=>'OTP benar'];
    }

    // 3. Simpan password baru
    public function setNewPassword($email, $newPass) {
        $user = $this->user->findByEmail($email);
        if (!$user) {
            return ['success'=>false, 'message'=>'User tidak ditemukan'];
        }

        $hash = password_hash($newPass, PASSWORD_BCRYPT);

        $this->user->updatePassword($user['id'], $hash);

        return ['success'=>true, 'message'=>'Password berhasil diubah'];
    }

}
