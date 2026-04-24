<?php
include "db.php";

$email = $_POST['email'];
$password = $_POST['password'];

$user = $users->findOne([
    'email' => $email,
    'password' => $password 
]);

if($user) {
    $user['_id'] = (string)$user['_id'];
    // Convert MongoDB DateTime objects
    if (isset($user['created_at']) && $user['created_at'] instanceof MongoDB\BSON\UTCDateTime) {
        $user['created_at'] = $user['created_at']->toDateTime()->format('Y-m-d H:i:s');
    }
    if (isset($user['updated_at']) && $user['updated_at'] instanceof MongoDB\BSON\UTCDateTime) {
        $user['updated_at'] = $user['updated_at']->toDateTime()->format('Y-m-d H:i:s');
    }
    echo json_encode($user);
} else {
    echo "invalid";
}
?>