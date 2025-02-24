<?php
namespace App\Middleware;

use App\Services\AuthService;

/**
 * Middleware to protect routes with JWT authentication
 */
class AuthMiddleware {
    private $authService;

    public function __construct() {
        $this->authService = new AuthService();
    }

    /**
     * Check if request has a valid JWT token
     * @return int|null User ID if authenticated
     */
    public function handle() {
        $headers = apache_request_headers();
        if (!isset($headers['Authorization'])) {
            http_response_code(401);
            echo json_encode(['message' => 'No token provided']);
            exit;
        }

        $token = str_replace('Bearer ', '', $headers['Authorization']);
        $userId = $this->authService->verifyToken($token);
        if (!$userId) {
            http_response_code(401);
            echo json_encode(['message' => 'Invalid token']);
            exit;
        }
        return $userId;
    }
}