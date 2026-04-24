<?php
include "db.php";

header('Content-Type: text/plain'); // Clean output

if($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['email'])) {
    http_response_code(400);
    echo "Missing email or invalid method";
    exit;
}

$email = trim($_POST['email']);
if(empty($email)) {
    echo "Empty email";
    exit;
}

$name = trim($_POST['name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$street = trim($_POST['street'] ?? '');
$house_number = trim($_POST['house_number'] ?? '');
$city = trim($_POST['city'] ?? '');
$state_province = trim($_POST['state_province'] ?? '');
$postal_code = trim($_POST['postal_code'] ?? '');

$updateData = [
    '$set' => [
        'name' => $name,
        'phone' => $phone,
        'street' => $street,
        'house_number' => $house_number,
        'city' => $city,
        'state_province' => $state_province,
        'postal_code' => $postal_code,
        'updated_at' => new MongoDB\BSON\UTCDateTime()
    ]
];

try {
    $result = $users->updateOne(
        ['email' => $email],
        $updateData
    );
    
    if($result->getMatchedCount() > 0) {
        echo "success";
    } else {
        echo "User not found for email: " . $email;
    }
} catch (Exception $e) {
    error_log("Update profile error: " . $e->getMessage());
    echo "Database error";
}
?>