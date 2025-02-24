<?php
use App\Controllers\AuthController;
use App\Controllers\TodoController;

header('Content-Type: application/json');

$requestMethod = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Remove 'public' from the path if it exists
$uriSegments = explode('/', trim($uri, '/'));
if ($uriSegments[1] === 'public' && isset($uriSegments[2])) {
    // Skip 'todo-api' and 'public' to get the actual endpoint
    $endpoint = $uriSegments[2];
} else {
    $endpoint = $uriSegments[0] ?? '';
}

$authController = new AuthController();
$todoController = new TodoController();

switch ($endpoint) {
    case 'register':
        if ($requestMethod === 'POST') $authController->register();
        break;
    case 'login':
        if ($requestMethod === 'POST') $authController->login();
        break;
    case 'todos':
        if ($requestMethod === 'POST') $todoController->create();
        elseif ($requestMethod === 'GET') $todoController->getAll();
        elseif ($requestMethod === 'PUT' && isset($uriSegments[3])) $todoController->update($uriSegments[3]);
        elseif ($requestMethod === 'DELETE' && isset($uriSegments[3])) $todoController->delete($uriSegments[3]);
        break;
    default:
        http_response_code(404);
        echo json_encode(['message' => 'Not Found']);
}