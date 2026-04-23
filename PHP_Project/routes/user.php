<?php


$router->get('/order/create', function() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login');
        exit;
    }
    include 'views/order/create.php';
});

$router->get('/orders', function() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login');
        exit;
    }
    include 'views/orders.php';
});

$router->get('/my-orders', function() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login');
        exit;
    }
    include 'views/user/my_order.php';
});

$router->post('/orders/store', function() {
    if (!isset($_SESSION['user_id'])) {
        return ['success' => false, 'message' => 'Unauthorized: Please login', 'code' => 401];
    }

    require_once 'controllers/orderController.php';
    $controller = new OrderController();
    
    return $controller->store();
});
