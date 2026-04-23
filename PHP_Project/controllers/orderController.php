<?php
require_once __DIR__ . '/../config/dp.php';
require_once __DIR__ . '/../models/order.php';
require_once __DIR__ . '/../models/orderItem.php';

class OrderController {
    
    private $order;
    private $orderItem;

    public function __construct() {
        $this->order = new Order();
        $this->orderItem = new OrderItem();
    }

    
    public function index() {
        $orders = $this->order->getAllOrders();
        return [
            'success' => true,
            'data' => $orders->fetchAll(PDO::FETCH_ASSOC),
            'message' => 'Orders retrieved successfully'
        ];
    }

    
    public function show($id) {
        $order = $this->order->getOrderById($id);
        if (!$order) {
            return [
                'success' => false,
                'message' => 'Order not found',
                'code' => 404
            ];
        }

        $items = $this->orderItem->getAllOrderItems();
        $itemsArray = $items->fetchAll(PDO::FETCH_ASSOC);
        $orderItems = array_filter($itemsArray, function($item) use ($id) {
            return $item['order_id'] == $id;
        });

        return [
            'success' => true,
            'data' => [
                'order' => $order,
                'items' => array_values($orderItems)
            ],
            'message' => 'Order retrieved successfully'
        ];
    }

    
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return [
                'success' => false,
                'message' => 'Invalid request method',
                'code' => 405
            ];
        }

        $userId = $_POST['user_id'] ?? null;
        $productId = $_POST['product_id'] ?? null;
        $quantity = $_POST['quantity'] ?? null;
        $totalPrice = $_POST['total_price'] ?? null;

        if (!$userId || !$productId || !$quantity || !$totalPrice) {
            return [
                'success' => false,
                'message' => 'All fields are required',
                'code' => 400
            ];
        }

        try {
            $result = $this->order->insertOrder($userId, $productId, $quantity, $totalPrice);
            if ($result) {
                return [
                    'success' => true,
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

    
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'PUT') {
            return [
                'success' => false,
                'message' => 'Invalid request method',
                'code' => 405
            ];
        }

        $order = $this->order->getOrderById($id);
        if (!$order) {
            return [
                'success' => false,
                'message' => 'Order not found',
                'code' => 404
            ];
        }

        $userId = $_POST['user_id'] ?? $order['user_id'];
        $productId = $_POST['product_id'] ?? $order['product_id'];
        $quantity = $_POST['quantity'] ?? $order['quantity'];
        $totalPrice = $_POST['total_price'] ?? $order['total_price'];

        try {
            $result = $this->order->updateOrder($id, $userId, $productId, $quantity, $totalPrice);
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Order updated successfully',
                    'code' => 200
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to update order',
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

    
    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            return [
                'success' => false,
                'message' => 'Invalid request method',
                'code' => 405
            ];
        }

        $order = $this->order->getOrderById($id);
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
                if ($item['order_id'] == $id) {
                    $this->orderItem->deleteOrderItem($item['id']);
                }
            }

            $result = $this->order->deleteOrder($id);
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Order deleted successfully',
                    'code' => 200
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to delete order',
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

    
    public function addItem($orderId) {
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

        $productId = $_POST['product_id'] ?? null;
        $quantity = $_POST['quantity'] ?? null;
        $price = $_POST['price'] ?? null;

        if (!$productId || !$quantity || !$price) {
            return [
                'success' => false,
                'message' => 'All fields are required',
                'code' => 400
            ];
        }

        try {
            $result = $this->orderItem->insertOrderItem($orderId, $productId, $quantity, $price);
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Order item added successfully',
                    'code' => 201
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to add order item',
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

    
    public function removeItem($itemId) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            return [
                'success' => false,
                'message' => 'Invalid request method',
                'code' => 405
            ];
        }

        $item = $this->orderItem->getOrderItemById($itemId);
        if (!$item) {
            return [
                'success' => false,
                'message' => 'Order item not found',
                'code' => 404
            ];
        }

        try {
            $result = $this->orderItem->deleteOrderItem($itemId);
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Order item removed successfully',
                    'code' => 200
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to remove order item',
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
}
?>
