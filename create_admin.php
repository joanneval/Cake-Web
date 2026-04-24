<?php
include "db.php";

$adminData = [
    'email' => 'admin@purpleshop.com',      
    'password' => 'admin123',                 
    'name' => 'Admin User',
    'role' => 'admin',
    'phone' => '',
    'created_at' => new MongoDB\BSON\UTCDateTime()
];


$existing = $users->findOne(['email' => $adminData['email']]);
if($existing) {
    echo "Admin already exists!";
} else {
    $result = $users->insertOne($adminData);
    if($result->getInsertedCount() === 1) {
        echo "✅ ADMIN CREATED SUCCESSFULLY!";
        echo "<br>Email: " . $adminData['email'];
        echo "<br>Password: " . $adminData['password'];
    } else {
        echo "Failed to create admin";
    }
}
?>