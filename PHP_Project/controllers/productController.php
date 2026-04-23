<?php
require_once __DIR__ . '/../config/dp.php';
require_once __DIR__ . '/../models/products.php';

class ProductController {
    
    private $product;

    public function __construct() {
        $this->product = new Product();
    }

    
    public function index() {
        $products = $this->product->getAllProducts();
        return [
            'success' => true,
            'data' => $products->fetchAll(PDO::FETCH_ASSOC),
            'message' => 'Products retrieved successfully'
        ];
    }

    
    public function show($id) {
        $product = $this->product->getProductById($id);
        if (!$product) {
            return [
                'success' => false,
                'message' => 'Product not found',
                'code' => 404
            ];
        }
        return [
            'success' => true,
            'data' => $product,
            'message' => 'Product retrieved successfully'
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

        $name = $_POST['name'] ?? null;
        $price = $_POST['price'] ?? null;
        $category_id = $_POST['category_id'] ?? null;
        $description = $_POST['description'] ?? null;
        $image = $this->handleImageUpload();

        if (!$name || !$price || !$category_id) {
            return [
                'success' => false,
                'message' => 'Name, price, and category are required',
                'code' => 400
            ];
        }

        try {
            $result = $this->product->insertProduct($name, $price, $category_id, $description, $image);
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Product created successfully',
                    'code' => 201
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to create product',
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

        $product = $this->product->getProductById($id);
        if (!$product) {
            return [
                'success' => false,
                'message' => 'Product not found',
                'code' => 404
            ];
        }

        $name = $_POST['name'] ?? $product['name'];
        $price = $_POST['price'] ?? $product['price'];
        $category_id = $_POST['category_id'] ?? $product['category_id'];
        $description = $_POST['description'] ?? $product['description'];
        $image = isset($_FILES['image']) ? $this->handleImageUpload() : $product['image'];

        try {
            $result = $this->product->updateProduct($id, $name, $price, $category_id, $description, $image);
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Product updated successfully',
                    'code' => 200
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to update product',
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

        $product = $this->product->getProductById($id);
        if (!$product) {
            return [
                'success' => false,
                'message' => 'Product not found',
                'code' => 404
            ];
        }

        try {
            $result = $this->product->deleteProduct($id);
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Product deleted successfully',
                    'code' => 200
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to delete product',
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

    
    private function handleImageUpload() {
        if (!isset($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('File upload error: ' . $_FILES['image']['error']);
        }

        $uploadDir = __DIR__ . '/../uploads/products/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileName = time() . '_' . basename($_FILES['image']['name']);
        $filePath = $uploadDir . $fileName;

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $filePath)) {
            throw new Exception('Failed to move uploaded file');
        }

        return $fileName;
    }
}
?>
