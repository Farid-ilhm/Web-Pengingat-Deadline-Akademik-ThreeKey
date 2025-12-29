<?php
namespace App\Models;

use App\Config\Database;
use PDO;

class Notification
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /* ============================================================
     * DEADLINE REMINDER (H-7, H-3, H-1, H-0)
     * ============================================================ */
    public function getSchedulesByDayOffset(int $days): array
    {
        /**
         * CATATAN PENTING:
         * - H-0 TIDAK BOLEH pakai CURDATE()
         * - Harus pakai NOW() supaya jam ikut dihitung
         */

        if ($days === 0) {
            // ================= H-0 (hari ini, jam real) =================
            $sql = "
                SELECT 
                    sch.id AS schedule_id,
                    sch.title,
                    sch.end_datetime,
                    u.id AS user_id,
                    u.email,
                    u.name
                FROM schedules sch
                JOIN users u ON sch.user_id = u.id
                WHERE sch.end_datetime BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 1 DAY)
            ";
            $stmt = $this->db->prepare($sql);
        } else {
            // ================= H-7, H-3, H-1 =================
            $sql = "
                SELECT 
                    sch.id AS schedule_id,
                    sch.title,
                    sch.end_datetime,
                    u.id AS user_id,
                    u.email,
                    u.name
                FROM schedules sch
                JOIN users u ON sch.user_id = u.id
                WHERE DATE(sch.end_datetime) = DATE(DATE_ADD(CURDATE(), INTERVAL :days DAY))
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':days', $days, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ============================================================
     * INSERT NOTIFICATION (ANTI DUPLIKASI)
     * ============================================================ */
    public function create(
        int $userId,
        int $scheduleId,
        int $reminderDay,
        string $title,
        string $message
    ): void {
        // Cek duplikasi
        $check = $this->db->prepare("
            SELECT id FROM notifications
            WHERE user_id = ?
              AND schedule_id = ?
              AND reminder_day = ?
            LIMIT 1
        ");
        $check->execute([$userId, $scheduleId, $reminderDay]);

        if ($check->fetch()) {
            // Sudah ada → jangan insert ulang
            return;
        }

        $stmt = $this->db->prepare("
            INSERT INTO notifications
            (user_id, schedule_id, reminder_day, title, message, is_read, created_at)
            VALUES (?, ?, ?, ?, ?, 0, NOW())
        ");
        $stmt->execute([
            $userId,
            $scheduleId,
            $reminderDay,
            $title,
            $message
        ]);
    }

    /* ============================================================
     * USER NOTIFICATION
     * ============================================================ */

    public function unreadByUser(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM notifications
            WHERE user_id = ?
              AND is_read = 0
            ORDER BY created_at DESC
            LIMIT 10
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function allByUser(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM notifications
            WHERE user_id = ?
            ORDER BY created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function markAsRead(int $id, int $userId): void
    {
        $stmt = $this->db->prepare("
            UPDATE notifications
            SET is_read = 1
            WHERE id = ? AND user_id = ?
        ");
        $stmt->execute([$id, $userId]);
    }

    public function countUnread(int $userId): int
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) 
            FROM notifications
            WHERE user_id = ?
              AND is_read = 0
        ");
        $stmt->execute([$userId]);
        return (int) $stmt->fetchColumn();
    }
}
