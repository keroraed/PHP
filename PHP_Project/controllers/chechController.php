<?php
require_once __DIR__ . '/../config/dp.php';
require_once __DIR__ . '/../models/order.php';
require_once __DIR__ . '/../models/orderItem.php';
require_once __DIR__ . '/../models/users.php';
require_once __DIR__ . '/../models/products.php';

class CheckController {
    
    private $order;
    private $orderItem;
    private $user;
    private $product;

    public function __construct() {
        $this->order = new Order();
        $this->orderItem = new OrderItem();
        $this->user = new User();
        $this->product = new Product();
    }

    
    public function showCheckout() {
        $userId = $_SESSION['user_id'] ?? $_GET['user_id'] ?? null;
        
        if (!$userId) {
            return [
                'success' => false,
                'message' => 'User ID is required',
                'code' => 400
            ];
        }

        $user = $this->user->getUserById($userId);
        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not found',
                'code' => 404
            ];
        }

        $products = $this->product->getAllProducts();
        $productsList = $products->fetchAll(PDO::FETCH_ASSOC);

        return [
            'success' => true,
            'data' => [
                'user' => $user,
                'products' => $productsList
            ],
            'message' => 'Checkout form loaded'
        ];
    }

    
    public function processCheckout() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return [
                'success' => false,
                'message' => 'Invalid request method',
                'code' => 405
            ];
        }

        $userId = $_POST['user_id'] ?? $_SESSION['user_id'] ?? null;
        $items = $_POST['items'] ?? null; // Array of items: [{product_id, quantity, price}]

        if (!$userId || !$items) {
            return [
                'success' => false,
                'message' => 'User ID and items are required',
                'code' => 400
            ];
        }

        try {
            $user = $this->user->getUserById($userId);
            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'User not found',
                    'code' => 404
                ];
            }

            $totalPrice = 0;
            $itemsArray = is_array($items) ? $items : json_decode($items, true);
            
            foreach ($itemsArray as $item) {
                if (!isset($item['product_id']) || !isset($item['quantity']) || !isset($item['price'])) {
                    return [
                        'success' => false,
                        'message' => 'Invalid item data',
                        'code' => 400
                    ];
                }

                $product = $this->product->getProductById($item['product_id']);
                if (!$product) {
                    return [
                        'success' => false,
                        'message' => 'Product not found: ' . $item['product_id'],
                        'code' => 404
                    ];
                }

                $totalPrice += $item['price'] * $item['quantity'];
            }

            $orderId = $this->createOrder($userId, $itemsArray, $totalPrice);
            
            if ($orderId) {
                return [
                    'success' => true,
                    'data' => [
                        'order_id' => $orderId,
                        'total_price' => $totalPrice
                    ],
                    'message' => 'Order created successfully',
                    'code' => 201
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to create order',
                    'code' => 500
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'code' => 500
            ];
        }
    }

    
    private function createOrder($userId, $items, $totalPrice) {
        try {
            $firstProduct = $items[0];
            $result = $this->order->insertOrder($userId, $firstProduct['product_id'], $firstProduct['quantity'], $totalPrice);
            
            if (!$result) {
                return false;
            }

            $orders = $this->order->getAllOrders();
            $ordersList = $orders->fetchAll(PDO::FETCH_ASSOC);
            $orderId = end($ordersList)['id'];

            foreach ($items as $item) {
                $this->orderItem->insertOrderItem(
                    $orderId,
                    $item['product_id'],
                    $item['quantity'],
                    $item['price']
                );
            }

            return $orderId;
        } catch (Exception $e) {
            throw $e;
        }
    }

    
    public function getSummary($orderId) {
        $order = $this->order->getOrderById($orderId);
        if (!$order) {
            return [
                'success' => false,
                'message' => 'Order not found',
                'code' => 404
            ];
        }

        $items = $this->orderItem->getAllOrderItems();
        $itemsArray = $items->fetchAll(PDO::FETCH_ASSOC);
        $orderItems = array_filter($itemsArray, function($item) use ($orderId) {
            return $item['order_id'] == $orderId;
        });

        $user = $this->user->getUserById($order['user_id']);

        return [
            'success' => true,
            'data' => [
                'order' => $order,
                'user' => $user,
                'items' => array_values($orderItems)
            ],
            'message' => 'Order summary retrieved'
        ];
    }

    
    public function confirmCheckout($orderId) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return [
                'success' => false,
                'message' => 'Invalid request method',
                'code' => 405
            ];
        }

        $order = $this->order->getOrderById($orderId);
        if (!$order) {
            return [
                'success' => false,
                'message' => 'Order not found',
                'code' => 404
            ];
        }

        try {
            
            return [
                'success' => true,
                'data' => [
                    'order_id' => $orderId,
                    'status' => 'completed'
                ],
                'message' => 'Checkout completed successfully',
                'code' => 200
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'code' => 500
            ];
        }
    }

    
    public function cancelCheckout($orderId) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return [
                'success' => false,
                'message' => 'Invalid request method',
                'code' => 405
            ];
        }

        $order = $this->order->getOrderById($orderId);
        if (!$order) {
            return [
                'success' => false,
                'message' => 'Order not found',
                'code' => 404
            ];
        }

        try {
            $items = $this->orderItem->getAllOrderItems();
            $itemsArray = $items->fetchAll(PDO::FETCH_ASSOC);
            foreach ($itemsArray as $item) {
                if ($item['order_id'] == $orderId) {
                    $this->orderItem->deleteOrderItem($item['id']);
                }
            }

            $this->order->deleteOrder($orderId);

            return [
                'success' => true,
                'message' => 'Checkout cancelled and order deleted',
                'code' => 200
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'code' => 500
            ];
        }
    }
}
?>
