<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Users;

class UserController extends Controller
{
    private $model;

    public function __construct()
    {
        $this->model = new Users();
    }

    public function index()
    {
        $this->requireRole('admin');
        
        $users = $this->model->getAll();
        return [
            'success' => true,
            'data' => $users,
            'message' => 'Users retrieved successfully',
            'code' => 200
        ];
    }

    public function show($id)
    {
        $user = $this->model->getById($id);
        
        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not found',
                'code' => 404
            ];
        }

        unset($user['password']);
        
        return [
            'success' => true,
            'data' => $user,
            'message' => 'User retrieved',
            'code' => 200
        ];
    }

    
    public function store()
    {
        $data = $_POST;

        if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
            return [
                'success' => false,
                'message' => 'Name, email, and password are required',
                'code' => 400
            ];
        }

        $existingUser = $this->model->findOneBy('email', $data['email']);
        if ($existingUser) {
            return [
                'success' => false,
                'message' => 'Email already registered',
                'code' => 409
            ];
        }

        $userData = [
            'name' => trim($data['name']),
            'email' => trim($data['email']),
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'room' => $data['room_no'] ?? $data['room'] ?? '',
            'ext' => $data['ext'] ?? '',
            'building' => $data['building'] ?? '',
            'role' => $data['role'] ?? 'user',
            'created_at' => date('Y-m-d H:i:s')
        ];

        $result = $this->model->insert($userData);

        if ($result) {
            return [
                'success' => true,
                'data' => ['id' => $this->model->lastInsertId()],
                'message' => 'User created successfully',
                'code' => 201
            ];
        }

        return [
            'success' => false,
            'message' => 'Failed to create user',
            'code' => 500
        ];
    }

    private function handlePhotoUpload($file)
    {
        $maxSize = 2 * 1024 * 1024; 
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $uploadDir = __DIR__ . '/../uploads/users/';

        if ($file['size'] > $maxSize) {
            return false;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedTypes)) {
            return false;
        }

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = time() . '_' . uniqid() . '.' . $ext;
        $filepath = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return '/uploads/users/' . $filename;
        }

        return false;
    }

    
    public function update($id)
    {
        $user = $this->model->getById($id);
        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not found',
                'code' => 404
            ];
        }

        if ($this->getUserId() != $id && !$this->hasRole('admin')) {
            return [
                'success' => false,
                'message' => 'Unauthorized',
                'code' => 403
            ];
        }

        $data = $_POST;

        $updateData = [];
        
        if (!empty($data['name'])) {
            $updateData['name'] = trim($data['name']);
        }
        
        if (!empty($data['room'])) {
            $updateData['room_no'] = $data['room'];
        }
        
        if (!empty($data['ext'])) {
            $updateData['ext'] = $data['ext'];
        }
        
        if (!empty($data['building'])) {
            $updateData['building'] = $data['building'];
        }

        if (!empty($data['password'])) {
            if (strlen($data['password']) < 6) {
                return [
                    'success' => false,
                    'message' => 'Password must be at least 6 characters',
                    'code' => 400
                ];
            }
            $updateData['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        if (!empty($data['role']) && $this->hasRole('admin')) {
            $updateData['role'] = $data['role'];
        }

        if (empty($updateData)) {
            return [
                'success' => false,
                'message' => 'No data to update',
                'code' => 400
            ];
        }

        $updateData['updated_at'] = date('Y-m-d H:i:s');

        $sql = "UPDATE users SET ";
        $params = [];
        $setParts = [];

        foreach ($updateData as $key => $value) {
            $setParts[] = "$key = ?";
            $params[] = $value;
        }

        $sql .= implode(", ", $setParts) . " WHERE id = ?";
        $params[] = $id;

        $result = $this->model->query($sql, $params);

        if ($result) {
            return [
                'success' => true,
                'message' => 'User updated successfully',
                'code' => 200
            ];
        }

        return [
            'success' => false,
            'message' => 'Failed to update user',
            'code' => 500
        ];
    }

    
    public function delete($id)
    {
        $this->requireRole('admin');

        $user = $this->model->getById($id);
        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not found',
                'code' => 404
            ];
        }

        $result = $this->model->delete($id);

        if ($result) {
            return [
                'success' => true,
                'message' => 'User deleted successfully',
                'code' => 200
            ];
        }

        return [
            'success' => false,
            'message' => 'Failed to delete user',
            'code' => 500
        ];
    }

    
    public function profile()
    {
        $this->requireAuth();

        $userId = $this->getUserId();
        $user = $this->model->getById($userId);

        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not found',
                'code' => 404
            ];
        }

        unset($user['password']);

        return [
            'success' => true,
            'data' => $user,
            'message' => 'Profile retrieved',
            'code' => 200
        ];
    }

    
    public function search()
    {
        $this->requireRole('admin');

        $query = $_GET['q'] ?? '';
        
        if (strlen($query) < 2) {
            return [
                'success' => false,
                'message' => 'Search query must be at least 2 characters',
                'code' => 400
            ];
        }

        $sql = "SELECT id, name, email, room, ext, building, role, created_at FROM users 
                WHERE name LIKE ? OR email LIKE ? 
                ORDER BY name ASC";
        
        $searchTerm = "%$query%";
        $results = $this->model->query($sql, [$searchTerm, $searchTerm]);
        $results = $this->model->fetchAll($results);

        return [
            'success' => true,
            'data' => $results,
            'message' => 'Search completed',
            'code' => 200
        ];
    }
}