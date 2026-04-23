<?php


$router->get('/', function() {
    include 'views/user/home.php';
});

$router->get('/login', function() {
    if (isset($_SESSION['user_id'])) {
        header('Location: /');
        exit;
    }
    include 'views/auth/login.php';
});

$router->post('/login', function() {
    require_once 'models/users.php';
    $user = new User();
    
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (!$email || !$password) {
        header('Location: /login?error=Invalid request');
        exit;
    }
    
    $userRecord = $user->authenticate($email, $password);
    
    if ($userRecord) {
        $_SESSION['user_id'] = $userRecord['id'];
        $_SESSION['role'] = $userRecord['role'] ?? 'user';
        header('Location: /');
    } else {
        header('Location: /login?error=Invalid credentials');
    }
    exit;
});

$router->get('/register', function() {
    if (isset($_SESSION['user_id'])) {
        header('Location: /');
        exit;
    }
    include 'views/auth/register.php';
});

$router->post('/register', function() {
    http_response_code(403);
    header('Location: /register?error=Self-registration is disabled. Please contact admin.');
    exit;
});

$router->get('/logout', function() {
    session_destroy();
    header('Location: /');
    exit;
});

$router->get('/products', function() {
    include 'views/products.php';
});

$router->get('/about', function() {
    include 'views/about.php';
});

$router->get('/contact', function() {
    include 'views/contact.php';
});

$router->post('/contact/send', function() {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $adminEmail = 'admin@example.com';
    
    if (!$name || !$email || !$subject || !$message) {
        header('Location: /contact?error=All fields are required');
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: /contact?error=Please enter a valid email address');
        exit;
    }

    require_once 'models/contactMessage.php';
    $contactMessage = new ContactMessage();
    $saved = $contactMessage->insert([
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'subject' => $subject,
        'message' => $message
    ]);

    if (!$saved) {
        header('Location: /contact?error=Unable to save your message right now. Please try again.');
        exit;
    }

    $safeName = str_replace(["\r", "\n"], '', $name);
    $safeEmail = str_replace(["\r", "\n"], '', $email);
    $safeSubject = str_replace(["\r", "\n"], '', $subject);

    $emailSubject = '[Contact Form] ' . $safeSubject;
    $emailBody = "New message from contact form\n\n"
        . "Name: {$safeName}\n"
        . "Email: {$safeEmail}\n"
        . "Phone: " . ($phone ?: 'N/A') . "\n"
        . "Subject: {$safeSubject}\n\n"
        . "Message:\n{$message}\n";

    $headers = [
        'MIME-Version: 1.0',
        'Content-Type: text/plain; charset=UTF-8',
        'From: Cafeteria Contact Form <no-reply@cafeteria.local>',
        'Reply-To: ' . $safeEmail,
        'X-Mailer: PHP/' . phpversion()
    ];

    $sent = @mail($adminEmail, $emailSubject, $emailBody, implode("\r\n", $headers));

    if (!$sent) {
        header('Location: /contact?success=Your message was saved to admin dashboard. Email delivery is currently unavailable.');
        exit;
    }
    
    header('Location: /contact?success=Your message has been sent to admin. You will get a reply soon.');
    exit;
});

$router->get('/product/{id}', function($id) {
    require_once 'models/products.php';
    $product = new Product();
    $productData = $product->getProductById($id);
    
    if (!$productData) {
        http_response_code(404);
        include '404.php';
        return;
    }
    
    include 'views/product/detail.php';
});
