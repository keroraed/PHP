<?php



$router->get('/admin/products', function() {
    if (($_SESSION['role'] ?? null) !== 'admin') {
        header('Location: /');
        exit;
    }
    include 'views/admin/products.php';
});

$router->get('/admin/users', function() {
    if (($_SESSION['role'] ?? null) !== 'admin') {
        header('Location: /');
        exit;
    }
    include 'views/admin/users.php';
});

$router->get('/admin/orders', function() {
    if (($_SESSION['role'] ?? null) !== 'admin') {
        header('Location: /');
        exit;
    }
    include 'views/admin/orders.php';
});

$router->get('/admin/messages', function() {
    if (($_SESSION['role'] ?? null) !== 'admin') {
        header('Location: /');
        exit;
    }
    include 'views/admin/messages.php';
});

$router->get('/admin/products/add', function() {
    if (($_SESSION['role'] ?? null) !== 'admin') {
        header('Location: /');
        exit;
    }
    include 'views/admin/add-product.php';
});

$router->get('/admin/users/add', function() {
    if (($_SESSION['role'] ?? null) !== 'admin') {
        header('Location: /');
        exit;
    }
    include 'views/admin/add-user.php';
});

$router->get('/admin/products/{id}/edit', function($id) {
    if (($_SESSION['role'] ?? null) !== 'admin') {
        header('Location: /');
        exit;
    }
    
    require_once 'models/products.php';
    $product = new Product();
    $productData = $product->getProductById($id);
    
    if (!$productData) {
        header('Location: /admin/products');
        exit;
    }
    
    echo json_encode($productData);
    exit;
});


$router->post('/admin/users', function() {
    if (!isset($_SESSION['user_id'])) {
        return ['success' => false, 'message' => 'Unauthorized: Please login', 'code' => 401];
    }
    
    if (($_SESSION['role'] ?? null) !== 'admin') {
        return ['success' => false, 'message' => 'Unauthorized: Admin access required', 'code' => 403];
    }
    
    require_once 'controllers/adminUserController.php';
    $controller = new adminUserController();
    
    return $controller->store();
});

$router->post('/admin/products', function() {
    if (($_SESSION['role'] ?? null) !== 'admin') {
        return ['success' => false, 'message' => 'Unauthorized', 'code' => 403];
    }
    
    require_once 'controllers/productController.php';
    $controller = new productController();
    
    return $controller->store();
});

$router->post('/admin/products/store', function() {
    if (!isset($_SESSION['user_id'])) {
        return ['success' => false, 'message' => 'Unauthorized: Please login', 'code' => 401];
    }
    
    if (($_SESSION['role'] ?? null) !== 'admin') {
        return ['success' => false, 'message' => 'Unauthorized: Admin access required', 'code' => 403];
    }
    
    require_once 'controllers/productController.php';
    $controller = new productController();
    return $controller->store();
});

$router->post('/admin/products/{id}', function($id) {
    if (($_SESSION['role'] ?? null) !== 'admin') {
        return ['success' => false, 'message' => 'Unauthorized', 'code' => 403];
    }
    
    require_once 'controllers/productController.php';
    $controller = new productController();
    
    return $controller->update($id);
});

$router->delete('/admin/products/{id}', function($id) {
    if (($_SESSION['role'] ?? null) !== 'admin') {
        return ['success' => false, 'message' => 'Unauthorized', 'code' => 403];
    }
    
    require_once 'controllers/productController.php';
    $controller = new productController();
    
    return $controller->delete($id);
});

$router->delete('/admin/users/{id}', function($id) {
    if (($_SESSION['role'] ?? null) !== 'admin') {
        return ['success' => false, 'message' => 'Unauthorized', 'code' => 403];
    }
    
    require_once 'models/users.php';
    $user = new User();
    $result = $user->delete($id);
    
    return [
        'success' => $result,
        'message' => $result ? 'User deleted' : 'Failed to delete user',
        'code' => $result ? 200 : 500
    ];
});
