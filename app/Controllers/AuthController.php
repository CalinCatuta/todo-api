<?php
namespace App\Controllers;

use App\Models\User;
use App\Services\AuthService;

/**
 * Controller for authentication-related endpoints
 */
class AuthController {
    private $user;
    private $authService;

    public function __construct() {
        $this->user = new User();
        $this->authService = new AuthService();
    }

    /**
     * Handle user registration
     */
   /**
 * Handle user registration
 */
public function register() {
    // Try to parse JSON input
    $data = json_decode(file_get_contents("php://input"), true);
    
    // If JSON is empty or not provided, try $_POST (for form data)
    if (!$data || empty($data)) {
        $data = $_POST;
    }

    if (!isset($data['username']) || !isset($data['email']) || !isset($data['password'])) {
        http_response_code(400);
        echo json_encode(['message' => 'Missing required fields']);
        return;
    }

    if ($this->user->register($data['username'], $data['email'], $data['password'])) {
        http_response_code(201);
        echo json_encode(['message' => 'User registered successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['message' => 'Registration failed']);
    }
}

    /**
     * Handle user login
     */
    public function login() {
        // Try to parse JSON input
        $data = json_decode(file_get_contents("php://input"), true);
        
        // If JSON is empty or not provided, try $_POST (for form data)
        if (!$data || empty($data)) {
            $data = $_POST;
        }

        if (!isset($data['email']) || !isset($data['password'])) {
            http_response_code(400);
            echo json_encode(['message' => 'Missing required fields']);
            return;
        }

        $user = $this->user->findByEmail($data['email']);
        if ($user && password_verify($data['password'], $user['password'])) {
            $token = $this->authService->generateToken($user['id']);
            echo json_encode(['token' => $token]);
        } else {
            http_response_code(401);
            echo json_encode(['message' => 'Invalid credentials']);
        }
    }
}