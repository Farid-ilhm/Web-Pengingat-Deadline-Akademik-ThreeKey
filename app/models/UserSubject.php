<?php
namespace App\Models;

use App\Config\Database;
use PDO;

class UserSubject
{

    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    // ============================================================
    // CREATE
    // ============================================================
    public function create($user_id, $subject_id, $name, $note = null)
    {
        $stmt = $this->db->prepare("
            INSERT INTO user_subjects (user_id, subject_id, name, note)
            VALUES (:uid, :sid, :name, :note)
        ");
        $stmt->execute([
            ':uid' => $user_id,
            ':sid' => $subject_id,
            ':name' => $name,
            ':note' => $note
        ]);

        return $this->db->lastInsertId();
    }

    // ============================================================
    // FIND BY USER & SUBJECT (CEGAH DUPLIKAT)
    // ============================================================
    public function findByUserAndSubject($userId, $subjectId)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM user_subjects
            WHERE user_id = :uid AND subject_id = :sid
            LIMIT 1
        ");
        $stmt->execute([
            ':uid' => $userId,
            ':sid' => $subjectId
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ============================================================
    // FIND BY USER & NAME
    // ============================================================
    public function findByUserAndName($userId, $name)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM user_subjects
            WHERE user_id = :uid AND name = :name
            LIMIT 1
        ");
        $stmt->execute([
            ':uid' => $userId,
            ':name' => $name
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ============================================================
    // READ / LIST
    // ============================================================
    public function allByUser($user_id)
    {
        $stmt = $this->db->prepare("
            SELECT us.*, s.name AS global_name, s.code AS global_code
            FROM user_subjects us
            LEFT JOIN subjects s ON us.subject_id = s.id
            WHERE us.user_id = :uid
            ORDER BY us.created_at DESC
        ");
        $stmt->execute([':uid' => $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ============================================================
    // FIND ONE
    // ============================================================
    public function find($id)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM user_subjects WHERE id = :id LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ============================================================
    // UPDATE
    // ============================================================
    public function update($id, $user_id, $name, $note)
    {
        $stmt = $this->db->prepare("
            UPDATE user_subjects
            SET name = :name, note = :note
            WHERE id = :id AND user_id = :uid
        ");
        return $stmt->execute([
            ':name' => $name,
            ':note' => $note,
            ':id' => $id,
            ':uid' => $user_id
        ]);
    }

    // ============================================================
    // DELETE
    // ============================================================
    public function delete($id, $user_id)
    {
        $stmt = $this->db->prepare("
            DELETE FROM user_subjects
            WHERE id = :id AND user_id = :uid
        ");
        return $stmt->execute([
            ':id' => $id,
            ':uid' => $user_id
        ]);
    }
}
