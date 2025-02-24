<?php
namespace App\Services;

use Firebase\JWT\JWT;

/**
 * Service for handling JWT authentication
 */
class AuthService {
    private $secret;

    public function __construct() {
        $this->secret = $_ENV['JWT_SECRET'];
    }

    /**
     * Generate a JWT token for a user
     * @param int $userId
     * @return string
     */
    public function generateToken($userId) {
        $payload = [
            'iss' => 'todo-api',
            'iat' => time(),
            'exp' => time() + (60 * 60), // 1 hour expiration
            'sub' => $userId
        ];
        return JWT::encode($payload, $this->secret, 'HS256');
    }

    /**
     * Verify a JWT token
     * @param string $token
     * @return int|null User ID if valid, null if invalid
     */
    public function verifyToken($token) {
        try {
            $decoded = JWT::decode($token, new \Firebase\JWT\Key($this->secret, 'HS256'));
            return $decoded->sub;
        } catch (\Exception $e) {
            return null;
        }
    }
}