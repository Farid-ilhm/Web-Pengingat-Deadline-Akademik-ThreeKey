<?php
namespace App\Models;

use App\Config\Database;
use PDO;

class Notification {

    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /* ================== DEADLINE REMINDER ================== */

    public function getSchedulesByDayOffset(int $days): array {
        $stmt = $this->db->prepare("
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
        ");
        $stmt->bindValue(':days', $days, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ================== INSERT NOTIFICATION ================== */

    public function create(
        int $userId,
        int $scheduleId,
        int $reminderDay,
        string $title,
        string $message
    ) {
        // CEGAH DUPLIKASI
        $check = $this->db->prepare("
            SELECT id FROM notifications
            WHERE user_id = ?
            AND schedule_id = ?
            AND reminder_day = ?
            LIMIT 1
        ");
        $check->execute([$userId, $scheduleId, $reminderDay]);

        if ($check->fetch()) {
            return; // ❌ sudah pernah dibuat
        }

        $stmt = $this->db->prepare("
            INSERT INTO notifications 
            (user_id, schedule_id, reminder_day, title, message)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$userId, $scheduleId, $reminderDay, $title, $message]);
    }

    public function delete(int $id, int $userId): bool {
        $stmt = $this->db->prepare("
            DELETE FROM notifications
            WHERE id = ? AND user_id = ?
        ");
        return $stmt->execute([$id, $userId]);
    }


    /* ================== USER NOTIFICATION ================== */

    public function unreadByUser(int $userId): array {
        $stmt = $this->db->prepare("
            SELECT * FROM notifications
            WHERE user_id = ? AND is_read = 0
            ORDER BY created_at DESC
            LIMIT 10
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function allByUser(int $userId): array {
        $stmt = $this->db->prepare("
            SELECT * FROM notifications
            WHERE user_id = ?
            ORDER BY created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function markAsRead(int $id, int $userId) {
        $stmt = $this->db->prepare("
            UPDATE notifications SET is_read = 1
            WHERE id = ? AND user_id = ?
        ");
        $stmt->execute([$id, $userId]);
    }

    public function countUnread(int $userId): int {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM notifications
            WHERE user_id = ? AND is_read = 0
        ");
        $stmt->execute([$userId]);
        return (int)$stmt->fetchColumn();
    }
}
