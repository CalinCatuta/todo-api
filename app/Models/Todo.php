<?php
namespace App\Models;

use PDO;

/**
 * Todo model for CRUD operations
 */
class Todo {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($userId, $title, $description) {
        $stmt = $this->db->prepare(
            "INSERT INTO todos (user_id, title, description) VALUES (:user_id, :title, :description)"
        );
        return $stmt->execute([
            'user_id' => $userId,
            'title' => $title,
            'description' => $description
        ]);
    }

    public function getAll($userId) {
        $stmt = $this->db->prepare("SELECT * FROM todos WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public function update($id, $userId, $title, $description, $isCompleted) {
        $stmt = $this->db->prepare(
            "UPDATE todos SET title = :title, description = :description, is_completed = :is_completed 
             WHERE id = :id AND user_id = :user_id"
        );
        return $stmt->execute([
            'id' => $id,
            'user_id' => $userId,
            'title' => $title,
            'description' => $description,
            'is_completed' => $isCompleted
        ]);
    }

    public function delete($id, $userId) {
        $stmt = $this->db->prepare("DELETE FROM todos WHERE id = :id AND user_id = :user_id");
        return $stmt->execute(['id' => $id, 'user_id' => $userId]);
    }
}