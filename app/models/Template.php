<?php
namespace App\Models;

use App\Config\Database;
use PDO;

class Template {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function create($name, $desc = null, $created_by = null) {
        $stmt = $this->db->prepare("
            INSERT INTO templates (name, description, created_by)
            VALUES (:name, :desc, :cb)
        ");
        $stmt->execute([
            ':name' => $name,
            ':desc' => $desc,
            ':cb'   => $created_by
        ]);

        return $this->db->lastInsertId();
    }

    public function all() {
        $stmt = $this->db->query("SELECT * FROM templates ORDER BY name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM templates WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $name, $desc) {
        $stmt = $this->db->prepare("
            UPDATE templates
            SET name = :name, description = :desc
            WHERE id = :id
        ");
        return $stmt->execute([
            ':name' => $name,
            ':desc' => $desc,
            ':id'   => $id
        ]);
    }

    public function delete($id) {
        // Cegah delete jika sedang dipakai schedules
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM schedules WHERE template_id = :id");
        $stmt->execute([':id' => $id]);

        if ($stmt->fetchColumn() > 0) {
            return [
                'success' => false,
                'message' => 'Template ini sedang digunakan user, tidak dapat dihapus.'
            ];
        }

        // Jika aman → hapus
        $stmt = $this->db->prepare("DELETE FROM templates WHERE id = :id");
        $stmt->execute([':id' => $id]);

        return [
            'success' => true,
            'message' => 'Template berhasil dihapus.'
        ];
    }
}
