<?php
include "db.php";

if($_SERVER['REQUEST_METHOD'] == "POST") {
    
    if(isset($_POST['update_status_id']) && isset($_POST['status'])) {
        $id = new MongoDB\BSON\ObjectId($_POST['update_status_id']);
        $orders->updateOne(
            ['_id' => $id],
            ['$set' => ['status' => $_POST['status']]]
        );
        echo "status_updated";
        exit;
    }
    
    if(isset($_POST['delete_id'])) {
        $id = new MongoDB\BSON\ObjectId($_POST['delete_id']);
        $orders->deleteOne(['_id' => $id]);
        echo "deleted";
        exit;
    }

    $cakeName = $_POST['cake'];
    $size = $_POST['size'];
    $flavor = $_POST['flavor'];

    $inventoryItem = $inventory->findOne([
        'product_name' => $cakeName,
        'size' => $size,
        'flavor' => $flavor
    ]);

    if (!$inventoryItem || $inventoryItem['quantity'] <= 0) {
        echo "error: Item out of stock or not found";
        exit;
    }

    $inventory->updateOne(
         ['_id'=> $inventoryItem['_id']],
         [
        '$inc' => ['quantity' => -1],          
        '$set' => [                             
            'last_updated' => new MongoDB\BSON\UTCDateTime(),
            'price' => $inventoryItem['price']  
        ]
    ]
    );

    $orderData = [
        'customer' => $_POST['customer'],
        'email' => $_POST['email'],
        'phone' => $_POST['phone'],
        'address' => $_POST['address'] ?? '',
        'street' => $_POST['street'] ?? '',
        'house_number' => $_POST['house_number'] ?? '',
        'city' => $_POST['city'] ?? '',
        'state_province' => $_POST['state_province'] ?? '',
        'postal_code' => $_POST['postal_code'] ?? '',
        'cake' => $_POST['cake'],
        'price' => (float)$_POST['price'],
        'size' => $_POST['size'],
        'flavor' => $_POST['flavor'],
        'delivery_date' => $_POST['date'],
        'payment' => $_POST['payment'],
        'status' => 'Pending',
        'created_at' => new MongoDB\BSON\UTCDateTime()
    ];

    $result = $orders->insertOne($orderData);
    if($result->getInsertedCount() === 1) {
        echo "success";
    } else {
        echo "Database error";
    }
} else {
    $cursor = $orders->find([], ['sort' => ['delivery_date' => -1]]);
    $ordersArray = [];
    
    foreach ($cursor as $order) {
        $order['_id'] = (string)$order['_id'];
        if(isset($order['delivery_date']) && $order['delivery_date'] instanceof MongoDB\BSON\UTCDateTime) {
            $order['delivery_date'] = $order['delivery_date']->toDateTime()->format('Y-m-d');
        }
        $ordersArray[] = $order;
    }
    
    header('Content-Type: application/json');
    echo json_encode($ordersArray);
}
?>
