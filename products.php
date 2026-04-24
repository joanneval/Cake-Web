<?php
include "db.php";

if($_SERVER['REQUEST_METHOD'] == "POST") {
    if(isset($_POST['delete_id'])) {
        $id = new MongoDB\BSON\ObjectId($_POST['delete_id']);
        $products->deleteOne(['_id' => $id]);
        echo "deleted";
        exit;
    }
    
    $data = [
        'name' => $_POST['name'],
        'size' => $_POST['size'],
        'flavor' => $_POST['flavor'],
        'price' => (float)$_POST['price'],
        'description' => $_POST['description'] ?? '',
        'available' => true,
        'created_at' => new MongoDB\BSON\UTCDateTime()
    ];
    
    $existing = $products->findOne([
        'name' => $data['name'],
        'size' => $data['size'],
        'flavor' => $data['flavor']
    ]);
    
    if($existing) {
        $products->updateOne(
            ['_id' => $existing['_id']],
            ['$set' => $data]
        );
    } else {
        $products->insertOne($data);
    }
    echo "success";
    exit;
}

if(isset($_GET['id'])) {
    $id = new MongoDB\BSON\ObjectId($_GET['id']);
    $product = $products->findOne(['_id' => $id]);
    if($product) {
        $product['_id'] = (string)$product['_id'];
    }
    header('Content-Type: application/json');
    echo json_encode($product);
    exit;
}


$cursor = $products->find(['available' => true], ['sort' => ['name' => 1]]);
$productsArray = [];
foreach ($cursor as $product) {
    $product['_id'] = (string)$product['_id'];
    $productsArray[] = $product;
}
header('Content-Type: application/json');
echo json_encode($productsArray);
?>