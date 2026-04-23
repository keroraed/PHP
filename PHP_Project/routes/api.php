<?php



$router->get('/api/categories', function() {
    require_once 'models/category.php';
    $category = new Category();
    $categories = $category->getAll();
    
    return [
        'success' => true,
        'data' => $categories,
        'code' => 200
    ];
});

$router->post('/api/categories', function() {
    if (($_SESSION['role'] ?? null) !== 'admin') {
        return ['success' => false, 'message' => 'Unauthorized', 'code' => 403];
    }
    
    require_once 'models/category.php';
    $name = $_POST['name'] ?? '';
    
    if (!$name) {
        return ['success' => false, 'message' => 'Category name is required', 'code' => 400];
    }
    
    $category = new Category();
    $result = $category->insert(['name' => $name]);
    
    return [
        'success' => $result,
        'message' => $result ? 'Category created' : 'Failed to create category',
        'code' => $result ? 201 : 500
    ];
});


$router->get('/api/products', function() {
    require_once 'models/products.php';
    $product = new Product();
    $products = $product->getAll();
    
    return [
        'success' => true,
        'data' => $products,
        'code' => 200
    ];
});

$router->get('/api/products/{id}', function($id) {
    require_once 'models/products.php';
    $product = new Product();
    $productData = $product->getProductById($id);
    
    if (!$productData) {
        return ['success' => false, 'message' => 'Product not found', 'code' => 404];
    }
    
    return ['success' => true, 'data' => $productData, 'code' => 200];
});

$router->post('/api/products', function() {
    if (($_SESSION['role'] ?? null) !== 'admin') {
        return ['success' => false, 'message' => 'Unauthorized', 'code' => 403];
    }
    
    require_once 'controllers/productController.php';
    $controller = new productController();
    
    return $controller->store();
});

$router->put('/api/products/{id}', function($id) {
    if (($_SESSION['role'] ?? null) !== 'admin') {
        return ['success' => false, 'message' => 'Unauthorized', 'code' => 403];
    }
    
    require_once 'controllers/productController.php';
    $controller = new productController();
    
    return $controller->update($id);
});

$router->delete('/api/products/{id}', function($id) {
    if (($_SESSION['role'] ?? null) !== 'admin') {
        return ['success' => false, 'message' => 'Unauthorized', 'code' => 403];
    }
    
    require_once 'controllers/productController.php';
    $controller = new productController();
    
    return $controller->delete($id);
});


$router->get('/api/users', function() {
    require_once 'models/users.php';
    $user = new User();
    $users = $user->getAll();
    
    return [
        'success' => true,
        'data' => array_map(function($u) {
            unset($u['password']);
            return $u;
        }, $users),
        'code' => 200
    ];
});

$router->get('/api/users/{id}', function($id) {
    require_once 'models/users.php';
    $user = new User();
    $userData = $user->getUserById($id);
    
    if (!$userData) {
        return ['success' => false, 'message' => 'User not found', 'code' => 404];
    }
    
    unset($userData['password']);
    return ['success' => true, 'data' => $userData, 'code' => 200];
});

$router->get('/api/me', function() {
    if (!isset($_SESSION['user_id'])) {
        return ['success' => false, 'message' => 'Unauthorized', 'code' => 401];
    }
    
    require_once 'models/users.php';
    $user = new User();
    $userData = $user->getUserById($_SESSION['user_id']);
    
    if (!$userData) {
        return ['success' => false, 'message' => 'User not found', 'code' => 404];
    }
    
    unset($userData['password']);
    return ['success' => true, 'data' => $userData, 'code' => 200];
});

$router->post('/api/users', function() {
    if (($_SESSION['role'] ?? null) !== 'admin') {
        return ['success' => false, 'message' => 'Unauthorized', 'code' => 403];
    }
    
    require_once 'models/users.php';
    $input = json_decode(file_get_contents('php://input'), true);

    if (!is_array($input)) {
        return ['success' => false, 'message' => 'Invalid JSON payload', 'code' => 400];
    }
    
    $user = new User();
    $result = $user->insertUser(
        $input['name'] ?? '',
        $input['email'] ?? '',
        password_hash($input['password'] ?? '', PASSWORD_DEFAULT),
        $input['room_no'] ?? '',
        $input['ext'] ?? '',
        $input['profile_picture'] ?? null
    );
    
    return [
        'success' => $result,
        'message' => $result ? 'User created' : 'Failed to create user',
        'code' => $result ? 201 : 500
    ];
});

$router->put('/api/users/{id}', function($id) {
    if (($_SESSION['role'] ?? null) !== 'admin') {
        return ['success' => false, 'message' => 'Unauthorized', 'code' => 403];
    }
    
    require_once 'models/users.php';
    $input = json_decode(file_get_contents('php://input'), true);

    if (!is_array($input)) {
        return ['success' => false, 'message' => 'Invalid JSON payload', 'code' => 400];
    }

    $allowed = ['name', 'email', 'room_no', 'ext', 'building', 'role', 'profile_picture'];
    $data = array_intersect_key($input, array_flip($allowed));

    if (!empty($input['password'])) {
        $data['password'] = password_hash($input['password'], PASSWORD_DEFAULT);
    }

    if (empty($data)) {
        return ['success' => false, 'message' => 'No data to update', 'code' => 400];
    }
    
    $user = new User();
    $result = $user->update($id, $data);
    
    return [
        'success' => $result,
        'message' => $result ? 'User updated' : 'Failed to update user',
        'code' => $result ? 200 : 500
    ];
});

$router->delete('/api/users/{id}', function($id) {
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

$router->get('/api/users/search/{q}', function($q) {
    require_once 'models/users.php';
    $user = new User();
    
    $users = $user->connection->query("SELECT id, name, email FROM users WHERE name LIKE '%$q%' LIMIT 10")->fetchAll();
    
    return ['success' => true, 'data' => $users, 'code' => 200];
});

$router->get('/api/admin/messages', function() {
    if (($_SESSION['role'] ?? null) !== 'admin') {
        return ['success' => false, 'message' => 'Unauthorized', 'code' => 403];
    }

    require_once 'models/contactMessage.php';
    $messageModel = new ContactMessage();
    $messages = $messageModel->getAll();

    return ['success' => true, 'data' => $messages, 'code' => 200];
});

$router->post('/api/admin/messages/{id}/read', function($id) {
    if (($_SESSION['role'] ?? null) !== 'admin') {
        return ['success' => false, 'message' => 'Unauthorized', 'code' => 403];
    }

    if ((int)$id <= 0) {
        return ['success' => false, 'message' => 'Invalid message ID', 'code' => 400];
    }

    require_once 'models/contactMessage.php';
    $messageModel = new ContactMessage();
    $result = $messageModel->markAsRead($id);

    return [
        'success' => $result,
        'message' => $result ? 'Message marked as read' : 'Message not found',
        'code' => $result ? 200 : 404
    ];
});


$router->get('/api/orders', function() {
    if (!isset($_SESSION['user_id'])) {
        return ['success' => false, 'message' => 'Unauthorized', 'code' => 401];
    }
    
    require_once 'models/order.php';
    $order = new Order();
    
    $userId = ($_SESSION['role'] ?? null) === 'admin' ? null : $_SESSION['user_id'];
    $orders = $userId ? $order->getOrdersByUser($userId) : $order->getAll();
    
    return ['success' => true, 'data' => $orders, 'code' => 200];
});

$router->get('/api/orders/{id}', function($id) {
    if (!isset($_SESSION['user_id'])) {
        return ['success' => false, 'message' => 'Unauthorized', 'code' => 401];
    }
    
    require_once 'models/order.php';
    $order = new Order();
    $orderData = $order->getOrderById($id);
    
    if (!$orderData) {
        return ['success' => false, 'message' => 'Order not found', 'code' => 404];
    }
    
    return ['success' => true, 'data' => $orderData, 'code' => 200];
});

$router->post('/api/orders/create', function() {
    if (!isset($_SESSION['user_id'])) {
        return ['success' => false, 'message' => 'Unauthorized', 'code' => 403];
    }
    
    require_once 'models/order.php';
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['items']) || empty($input['items'])) {
        return ['success' => false, 'message' => 'Invalid order data', 'code' => 400];
    }
    
    $orderModel = new Order();
    $userId = ($_SESSION['role'] ?? null) === 'admin' ? $input['userId'] : $_SESSION['user_id'];
    
    $stmt = $orderModel->connection->prepare(
        "INSERT INTO orders (user_id, subtotal, tax, total, notes, status, created_at) 
         VALUES (?, ?, ?, ?, ?, 'pending', NOW())"
    );
    
    $result = $stmt->execute([
        $userId,
        $input['subtotal'] ?? 0,
        $input['tax'] ?? 0,
        $input['total'] ?? 0,
        $input['notes'] ?? ''
    ]);
    
    if (!$result) {
        return ['success' => false, 'message' => 'Failed to create order', 'code' => 500];
    }
    
    $orderId = $orderModel->connection->lastInsertId();
    
    foreach ($input['items'] as $item) {
        $orderModel->addItem($orderId, $item['id'], $item['quantity'], $item['price']);
    }
    
    return [
        'success' => true,
        'data' => ['orderId' => $orderId],
        'message' => 'Order created successfully',
        'code' => 201
    ];
});

$router->put('/api/orders/{id}', function($id) {
    if (!isset($_SESSION['user_id'])) {
        return ['success' => false, 'message' => 'Unauthorized', 'code' => 401];
    }
    
    require_once 'models/order.php';
    $input = json_decode(file_get_contents('php://input'), true);
    
    $order = new Order();
    $result = $order->update($id, $input);
    
    return [
        'success' => $result,
        'message' => $result ? 'Order updated' : 'Failed to update order',
        'code' => $result ? 200 : 500
    ];
});

$router->delete('/api/orders/{id}', function($id) {
    if (!isset($_SESSION['user_id'])) {
        return ['success' => false, 'message' => 'Unauthorized', 'code' => 401];
    }
    
    require_once 'models/order.php';
    $order = new Order();
    $result = $order->delete($id);
    
    return [
        'success' => $result,
        'message' => $result ? 'Order deleted' : 'Failed to delete order',
        'code' => $result ? 200 : 500
    ];
});

$router->get('/api/orders/all', function() {
    if (($_SESSION['role'] ?? null) !== 'admin') {
        return ['success' => false, 'message' => 'Unauthorized', 'code' => 403];
    }
    
    require_once 'models/order.php';
    $order = new Order();
    $orders = $order->getAll();
    
    return ['success' => true, 'data' => $orders, 'code' => 200];
});

$router->post('/api/orders/{id}/status', function($id) {
    if (($_SESSION['role'] ?? null) !== 'admin') {
        return ['success' => false, 'message' => 'Unauthorized', 'code' => 403];
    }
    
    require_once 'models/order.php';
    $input = json_decode(file_get_contents('php://input'), true);
    
    $order = new Order();
    $result = $order->updateStatus($id, $input['status'] ?? '');
    
    return [
        'success' => $result,
        'message' => $result ? 'Status updated' : 'Failed to update status',
        'code' => $result ? 200 : 500
    ];
});

$router->post('/api/orders/{id}/cancel', function($id) {
    if (!isset($_SESSION['user_id'])) {
        return ['success' => false, 'message' => 'Unauthorized', 'code' => 401];
    }
    
    require_once 'models/order.php';
    $order = new Order();

    $orderData = $order->getOrderById($id);
    if (!$orderData) {
        return ['success' => false, 'message' => 'Order not found', 'code' => 404];
    }

    $isAdmin = (($_SESSION['role'] ?? null) === 'admin');
    $isOwner = ((int)$orderData['user_id'] === (int)$_SESSION['user_id']);

    if (!$isAdmin && !$isOwner) {
        return ['success' => false, 'message' => 'Forbidden', 'code' => 403];
    }

    if (in_array($orderData['status'], ['done', 'cancelled'], true)) {
        return ['success' => false, 'message' => 'Order cannot be cancelled', 'code' => 400];
    }

    $result = $order->updateStatus($id, 'cancelled');
    
    return [
        'success' => $result,
        'message' => $result ? 'Order cancelled' : 'Failed to cancel order',
        'code' => $result ? 200 : 500
    ];
});

$router->post('/api/wishlist/{id}', function($id) {
    return [
        'success' => true,
        'message' => 'Wishlist updated',
        'code' => 200
    ];
});

$router->get('/api/wishlist', function() {
    if (!isset($_SESSION['user_id'])) {
        return [
            'success' => false,
            'message' => 'Unauthorized',
            'code' => 401
        ];
    }
    
    return [
        'success' => true,
        'data' => [],
        'message' => 'Wishlist retrieved',
        'code' => 200
    ];
});


$router->post('/api/cart/add', function() {
    $productId = $_POST['product_id'] ?? null;
    $quantity = $_POST['quantity'] ?? 1;
    
    if (!$productId) {
        return [
            'success' => false,
            'message' => 'Product ID is required',
            'code' => 400
        ];
    }
    
    return [
        'success' => true,
        'message' => 'Product added to cart',
        'code' => 200
    ];
});

$router->get('/api/cart', function() {
    return [
        'success' => true,
        'data' => [],
        'message' => 'Cart retrieved',
        'code' => 200
    ];
});
