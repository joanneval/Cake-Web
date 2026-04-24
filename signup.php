<?php
include "db.php"; 

$adminEmail = 'admin@purpleshop.com';
if (!isset($_SESSION['admin_created'])) {  // Prevent duplicates
    $existing = $users->findOne(['email' => $adminEmail]);
    if (!$existing) {
        $users->insertOne([
            'email' => $adminEmail,
            'password' => 'admin123',
            'name' => 'Admin User',
            'role' => 'admin',
            'created_at' => new MongoDB\BSON\UTCDateTime()
        ]);
        echo "Admin created automatically!<br>";
    }
    session_start();
    $_SESSION['admin_created'] = true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['name'], $_POST['email'], $_POST['password'])) {
        echo "Missing form data";
        exit;
    }

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password']; 

    try {
        $existingUser = $users->findOne(['email' => $email]);
        if ($existingUser) {
            echo "Email already exists";
            exit;
        }

        $userData = [
            'name' => $name,
            'email' => $email,
            'password' => $password, 
            'phone' => '',
            'street' => '',
            'house_number' => '',
            'city' => '',
            'state_province' => '',
            'postal_code' => '',
            'created_at' => new MongoDB\BSON\UTCDateTime()
        ];

        $result = $users->insertOne($userData);

        if ($result->getInsertedCount() === 1) {
            echo "success";
        } else {
            echo "Database error";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Invalid request method";
}
?>
