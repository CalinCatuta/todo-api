<?php
namespace App\Controllers;

use App\Models\Todo;
use App\Middleware\AuthMiddleware;

/**
 * Controller for Todo CRUD operations
 */
class TodoController {
    private $todo;
    private $authMiddleware;

    public function __construct() {
        $this->todo = new Todo();
        $this->authMiddleware = new AuthMiddleware();
    }

    /**
     * Create a new todo
     */
    public function create() {
        $userId = $this->authMiddleware->handle();
        
        // Parse raw input from php://input
        $rawInput = file_get_contents("php://input");
        $data = null;

        // Try to parse as JSON
        $data = json_decode($rawInput, true);
        
        // If not JSON, try to parse as x-www-form-urlencoded
        if (!$data || empty($data)) {
            parse_str($rawInput, $data);
        }

        if (!isset($data['title'])) {
            http_response_code(400);
            echo json_encode(['message' => 'Title is required']);
            return;
        }

        if ($this->todo->create($userId, $data['title'], $data['description'] ?? '')) {
            http_response_code(201);
            echo json_encode(['message' => 'Todo created']);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Failed to create todo']);
        }
    }

    /**
     * Get all todos for the authenticated user
     */
    public function getAll() {
        $userId = $this->authMiddleware->handle();
        $todos = $this->todo->getAll($userId);
        echo json_encode($todos);
    }

    /**
     * Update a todo
     */
    public function update($id) {
        $userId = $this->authMiddleware->handle();
        
        // Parse raw input from php://input
        $rawInput = file_get_contents("php://input");
        $data = null;

        // Try to parse as JSON
        $data = json_decode($rawInput, true);
        
        // If not JSON, try to parse as x-www-form-urlencoded
        if (!$data || empty($data)) {
            parse_str($rawInput, $data);
        }

        if (!isset($data['title'])) {
            http_response_code(400);
            echo json_encode(['message' => 'Title is required']);
            return;
        }

        if ($this->todo->update($id, $userId, $data['title'], $data['description'] ?? '', $data['is_completed'] ?? 0)) {
            echo json_encode(['message' => 'Todo updated']);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Todo not found']);
        }
    }

    /**
     * Delete a todo
     */
    public function delete($id) {
        $userId = $this->authMiddleware->handle();
        if ($this->todo->delete($id, $userId)) {
            echo json_encode(['message' => 'Todo deleted']);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Todo not found']);
        }
    }
}