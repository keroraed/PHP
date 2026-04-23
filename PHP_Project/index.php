<?php


session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);


require_once 'config/dp.php';
require_once 'core/Router.php';
require_once 'core/controller.php';
require_once 'core/model.php';

$router = new Router();


require_once 'routes/public.php';    // Public routes (home, auth, static pages)
require_once 'routes/api.php';       // API routes (RESTful endpoints)
require_once 'routes/admin.php';     // Admin routes (admin-only pages)
require_once 'routes/user.php';      // User routes (authenticated user pages)


$router->notFound(function() {
    http_response_code(404);
    include '404.php';
});


$isApiRequest = str_starts_with(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/', '/api/')
    || (($_SERVER['HTTP_ACCEPT'] ?? '') && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json'));

try {
    $response = $router->dispatch();

    if (is_array($response)) {
        $statusCode = (int)($response['code'] ?? $response['status'] ?? 200);
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');

        if (!isset($response['success'])) {
            $response['success'] = $statusCode >= 200 && $statusCode < 300;
        }

        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    if ($isApiRequest && $response === null) {
        http_response_code(200);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => true,
            'message' => 'Request completed',
            'code' => 200
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
} catch (Throwable $e) {
    $statusCode = 500;
    http_response_code($statusCode);

    if ($isApiRequest) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => false,
            'message' => 'Internal server error',
            'error' => $e->getMessage(),
            'code' => $statusCode
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    echo '<h1>500 - Internal Server Error</h1>';
    echo '<p>' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</p>';
    exit;
}

if (is_array($response)) {
    header('Content-Type: application/json');
    echo json_encode($response);
}
