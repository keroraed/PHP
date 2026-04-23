<?php

class Router {
    private $routes = [];
    private $notFoundCallback = null;

    
    public function get($path, $callback) {
        $this->routes['GET'][$path] = $callback;
        return $this;
    }

    
    public function post($path, $callback) {
        $this->routes['POST'][$path] = $callback;
        return $this;
    }

    
    public function put($path, $callback) {
        $this->routes['PUT'][$path] = $callback;
        return $this;
    }

    
    public function delete($path, $callback) {
        $this->routes['DELETE'][$path] = $callback;
        return $this;
    }

    
    public function notFound($callback) {
        $this->notFoundCallback = $callback;
        return $this;
    }

    
    public function dispatch($uri = null, $method = null) {
        if ($uri === null) {
            $uri = $_SERVER['REQUEST_URI'] ?? '/';
        }
        
        if ($method === null) {
            $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        }

        $uri = strtok($uri, '?');
        
        if ($uri !== '/' && substr($uri, -1) === '/') {
            $uri = rtrim($uri, '/');
        }

        if (isset($this->routes[$method][$uri])) {
            return $this->executeRoute($this->routes[$method][$uri]);
        }

        if (isset($this->routes[$method])) {
            foreach ($this->routes[$method] as $pattern => $callback) {
                if ($params = $this->matchRoute($pattern, $uri)) {
                    return $this->executeRoute($callback, $params);
                }
            }
        }

        if ($this->notFoundCallback) {
            return $this->executeRoute($this->notFoundCallback);
        }

        return $this->sendResponse(404, 'Route not found');
    }

    
    private function matchRoute($pattern, $uri) {
        $pattern = preg_replace('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', '([a-zA-Z0-9_-]+)', $pattern);
        $pattern = '#^' . $pattern . '$#';

        if (preg_match($pattern, $uri, $matches)) {
            array_shift($matches); // Remove full match
            return $matches;
        }

        return false;
    }

    
    private function executeRoute($callback, $params = []) {
        if (is_string($callback)) {
            list($controller, $method) = explode('@', $callback);
            
            $controllerClass = ucfirst($controller) . 'Controller';
            $controllerFile = __DIR__ . '/../controllers/' . $controllerClass . '.php';
            
            if (!file_exists($controllerFile)) {
                return $this->sendResponse(404, "Controller not found: $controllerClass");
            }

            require_once $controllerFile;
            
            if (!class_exists($controllerClass)) {
                return $this->sendResponse(500, "Class not found: $controllerClass");
            }

            $instance = new $controllerClass();
            
            if (!method_exists($instance, $method)) {
                return $this->sendResponse(500, "Method not found: $method");
            }

            return call_user_func_array([$instance, $method], $params);
        } elseif (is_callable($callback)) {
            return call_user_func_array($callback, $params);
        }

        return $this->sendResponse(500, 'Invalid route callback');
    }

    
    private function sendResponse($code, $message) {
        http_response_code($code);
        return [
            'status' => $code,
            'message' => $message
        ];
    }

    
    public function getRoutes() {
        return $this->routes;
    }
}
?>
