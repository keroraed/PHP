<?php

abstract class Controller {
    
    
    protected function loadView($viewName, $data = []) {
        $viewPath = __DIR__ . '/../views/' . $viewName . '.php';
        
        if (!file_exists($viewPath)) {
            die("View not found: $viewName");
        }

        if (!empty($data)) {
            extract($data, EXTR_PREFIX_ALL, 'data');
        }

        include $viewPath;
    }

    
    protected function json($data, $statusCode = 200) {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
        exit;
    }

    
    protected function redirect($url) {
        header("Location: $url");
        exit;
    }

    
    protected function isAuthenticated() {
        return isset($_SESSION['user_id']);
    }

    
    protected function hasRole($role) {
        return isset($_SESSION['role']) && $_SESSION['role'] === $role;
    }

    
    protected function getUserId() {
        return $_SESSION['user_id'] ?? null;
    }

    
    protected function getUserRole() {
        return $_SESSION['role'] ?? null;
    }

    
    protected function requireAuth() {
        if (!$this->isAuthenticated()) {
            $this->redirect('/login');
        }
    }

    
    protected function requireRole($role) {
        $this->requireAuth();
        if (!$this->hasRole($role)) {
            http_response_code(403);
            die('Access denied');
        }
    }
}
?>
