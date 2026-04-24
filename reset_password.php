<?php
include "db.php";

$email = $_POST['email'];
$password = $_POST['password'];

$update = $users->updateOne(
    ['email' => $email],
    ['$set' => ['password' => $password]]
);

if($update->getMatchedCount() > 0){
    echo "success";
}else{
    echo "Email not found";
}
?>