<?php
namespace App\Models;
use App\Config\Database;
use PDO;

class Subject {
    private $db;
    public function __construct() { $this->db = Database::getConnection(); }

    public function create($name, $code = null, $desc = null, $created_by = null) {
        $stmt = $this->db->prepare("INSERT INTO subjects (code, name, description, created_by) VALUES (:code, :name, :desc, :cb)");
        $stmt->execute([':code'=>$code, ':name'=>$name, ':desc'=>$desc, ':cb'=>$created_by]);
        return $this->db->lastInsertId();
    }

    public function all() {
        $stmt = $this->db->query("SELECT * FROM subjects ORDER BY name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM subjects WHERE id=:id LIMIT 1");
        $stmt->execute([':id'=>$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $name, $code, $desc) {
        $stmt = $this->db->prepare("
            UPDATE subjects 
            SET name = :name, code = :code, description = :desc 
            WHERE id = :id
        ");
        return $stmt->execute([
            ':name' => $name,
            ':code' => $code,
            ':desc' => $desc,
            ':id'   => $id
        ]);
    }


    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM subjects WHERE id=:id");
        return $stmt->execute([':id'=>$id]);
    }
}
