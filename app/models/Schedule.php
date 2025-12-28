<?php
namespace App\Models;
use App\Config\Database;
use PDO;

class Schedule
{

    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    // ============================================================
    // CREATE SCHEDULE
    // ============================================================
    public function create($user_id, $user_subject_id, $template_id, $title, $desc, $start_dt, $end_dt, $google_event_id = null)
    {
        $stmt = $this->db->prepare("
            INSERT INTO schedules
            (user_id, user_subject_id, template_id, title, description, start_datetime, end_datetime, google_event_id)
            VALUES (:uid, :usid, :tid, :title, :desc, :start, :end, :gid)
        ");
        $stmt->execute([
            ':uid' => $user_id,
            ':usid' => $user_subject_id,
            ':tid' => $template_id,
            ':title' => $title,
            ':desc' => $desc,
            ':start' => $start_dt,
            ':end' => $end_dt,
            ':gid' => $google_event_id
        ]);
        return $this->db->lastInsertId();
    }

    // ============================================================
    // READ / LIST
    // ============================================================
    public function allByUser($user_id)
    {
        $stmt = $this->db->prepare("
            SELECT 
                sch.*,
                COALESCE(us.name, '(Tanpa Mata Pelajaran)') AS subj_name,
                t.name AS template_name
            FROM schedules sch
            LEFT JOIN user_subjects us ON sch.user_subject_id = us.id
            JOIN templates t ON sch.template_id = t.id
            WHERE sch.user_id = :uid
            ORDER BY sch.start_datetime DESC
        ");
        $stmt->execute([':uid' => $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ============================================================
    // DELETE
    // ============================================================
    public function delete($id, $user_id)
    {
        $stmt = $this->db->prepare("DELETE FROM schedules WHERE id=:id AND user_id=:uid");
        return $stmt->execute([':id' => $id, ':uid' => $user_id]);
    }

    // ============================================================
    // FIND ONE
    // ============================================================
    public function find($id, $user_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM schedules WHERE id=:id AND user_id=:uid LIMIT 1");
        $stmt->execute([':id' => $id, ':uid' => $user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ============================================================
    // UPDATE
    // ============================================================
    public function updateSchedule($id, $user_id, $title, $desc, $start_dt, $end_dt)
    {
        $stmt = $this->db->prepare("
            UPDATE schedules
            SET title=:title, description=:desc, start_datetime=:start_dt, end_datetime=:end_dt
            WHERE id=:id AND user_id=:uid
        ");
        return $stmt->execute([
            ':title' => $title,
            ':desc' => $desc,
            ':start_dt' => $start_dt,
            ':end_dt' => $end_dt,
            ':id' => $id,
            ':uid' => $user_id
        ]);
    }

    // ============================================================
    // GANTT DATA
    // ============================================================
    public function ganttByUser($user_id)
    {
        $stmt = $this->db->prepare("
            SELECT title, start_datetime, end_datetime
            FROM schedules
            WHERE user_id = :uid
            ORDER BY start_datetime
        ");
        $stmt->execute([':uid' => $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
