<?php
namespace App\Services;

use App\Config\Database;
use PDO;

class AnalyticsService {

    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /* =================================================
       ============== USER ANALYTICS ===================
    ================================================= */

    private function semesterRange(int $year, string $semester): array {
        if ($semester === 'Genap') {
            return ["$year-01-01", "$year-06-30 23:59:59"];
        }
        return ["$year-07-01", "$year-12-31 23:59:59"];
    }

    public function workloadPerWeek(int $userId, int $year, string $semester): array {
        [$start, $end] = $this->semesterRange($year, $semester);

        $stmt = $this->db->prepare("
            SELECT WEEK(end_datetime, 1) AS week, COUNT(*) total
            FROM schedules
            WHERE user_id = :user
              AND end_datetime BETWEEN :start AND :end
            GROUP BY week
            ORDER BY week
        ");
        $stmt->execute([
            'user' => $userId,
            'start' => $start,
            'end' => $end
        ]);

        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    public function workloadPerSubject(int $userId, int $year, string $semester): array {
        [$start, $end] = $this->semesterRange($year, $semester);

        $stmt = $this->db->prepare("
            SELECT us.name, COUNT(s.id) total
            FROM schedules s
            JOIN user_subjects us ON us.id = s.user_subject_id
            WHERE s.user_id = :user
              AND s.end_datetime BETWEEN :start AND :end
            GROUP BY us.id
            ORDER BY total DESC
        ");
        $stmt->execute([
            'user' => $userId,
            'start' => $start,
            'end' => $end
        ]);

        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    public function semesterSummary(int $userId, int $year, string $semester): array {
        $weekly  = $this->workloadPerWeek($userId, $year, $semester);
        $subject = $this->workloadPerSubject($userId, $year, $semester);

        $total = array_sum($weekly);

        arsort($weekly);
        $busiestWeek  = key($weekly);
        $busiestCount = current($weekly);

        arsort($subject);
        $busiestSubject = key($subject);

        return [
            'semester'        => $semester,
            'total_deadline'  => $total,
            'busiest_week'    => $busiestWeek,
            'busiest_subject' => $busiestSubject,
            'recommendation'  =>
                ($busiestCount >= 3)
                ? "Minggu ke-$busiestWeek cukup padat, disarankan mulai lebih awal."
                : "Beban tugas relatif merata."
        ];
    }


    /* =================================================
       ============== ADMIN ANALYTICS ==================
    ================================================= */

    /** Total user terdaftar sampai akhir tahun */
    public function countTotalUsersByYear(int $year): int {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM users
            WHERE role = 'user'
              AND YEAR(created_at) <= :year
        ");
        $stmt->execute(['year' => $year]);
        return (int) $stmt->fetchColumn();
    }

    /** User baru dalam tahun terpilih */
    public function countNewUsersByYear(int $year): int {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM users
            WHERE role = 'user'
              AND YEAR(created_at) = :year
        ");
        $stmt->execute(['year' => $year]);
        return (int) $stmt->fetchColumn();
    }

    /** Total user per bulan (akumulatif) */
    public function totalUsersPerMonth(int $year): array {
        $stmt = $this->db->prepare("
            SELECT MONTH(created_at) AS month, COUNT(*) total
            FROM users
            WHERE role = 'user'
              AND YEAR(created_at) <= :year
            GROUP BY month
            ORDER BY month
        ");
        $stmt->execute(['year' => $year]);

        $raw = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        $data = array_fill(1, 12, 0);

        $running = 0;
        foreach ($raw as $m => $t) {
            $running += $t;
            $data[(int)$m] = $running;
        }

        return $data;
    }

    /** User baru per bulan */
    public function newUsersPerMonth(int $year): array {
        $stmt = $this->db->prepare("
            SELECT MONTH(created_at) AS month, COUNT(*) total
            FROM users
            WHERE role = 'user'
              AND YEAR(created_at) = :year
            GROUP BY month
            ORDER BY month
        ");
        $stmt->execute(['year' => $year]);

        $raw = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        $data = array_fill(1, 12, 0);

        foreach ($raw as $m => $t) {
            $data[(int)$m] = (int)$t;
        }

        return $data;
    }

    /** Tahun tersedia */
    public function getAvailableYears(): array {
        $stmt = $this->db->query("
            SELECT DISTINCT YEAR(created_at) AS year
            FROM users
            ORDER BY year DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
