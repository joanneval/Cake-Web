<?php
include "db.php";

if($_SERVER['REQUEST_METHOD'] == "POST") {
    try {
        // Handle DELETE first
        if(isset($_POST['delete_id'])) {
            $deleteId = $_POST['delete_id'];
            
            if (!preg_match('/^[0-9a-fA-F]{24}$/', $deleteId)) {
                echo "error: Invalid ID format";
                exit;
            }
            
            $id = new MongoDB\BSON\ObjectId($deleteId);
            $result = $inventory->deleteOne(['_id' => $id]);
            
            if($result->getDeletedCount() > 0) {
                echo "deleted";
            } else {
                echo "error: Item not found";
            }
            exit;
        }
        
        // Handle ADD/UPDATE/EDIT
        $name = trim($_POST['product_name']);
        $size = trim($_POST['size']);
        $flavor = trim($_POST['flavor']);
        $qty = (int)$_POST['quantity'];
        $price = (float)$_POST['price'];
        
        if(empty($name) || empty($size) || empty($flavor)) {
            echo "error: Missing required fields";
            exit;
        }
        
        // ✅ EDIT by ID (NEW)
        if(isset($_POST['edit_id']) && !empty($_POST['edit_id'])) {
            $editId = $_POST['edit_id'];
            if (preg_match('/^[0-9a-fA-F]{24}$/', $editId)) {
                $inventory->updateOne(
                    ['_id' => new MongoDB\BSON\ObjectId($editId)],
                    ['$set' => [
                        'product_name' => $name,
                        'size' => $size,
                        'flavor' => $flavor,
                        'quantity' => $qty,
                        'price' => $price,
                        'last_updated' => new MongoDB\BSON\UTCDateTime()
                    ]]
                );
                echo "success";
                exit;
            } else {
                echo "error: Invalid edit ID";
                exit;
            }
        }
        
        // Original ADD/UPDATE by name/size/flavor
        $existing = $inventory->findOne([
            'product_name' => $name,
            'size' => $size,
            'flavor' => $flavor
        ]);
        
        if($existing) {
            $inventory->updateOne(
                ['_id' => $existing['_id']],
                ['$set' => [
                    'quantity' => $qty,
                    'price' => $price,
                    'last_updated' => new MongoDB\BSON\UTCDateTime()
                ]]
            );
        } else {
            $inventory->insertOne([
                'product_name' => $name,
                'size' => $size,
                'flavor' => $flavor,
                'quantity' => $qty,
                'price' => $price,
                'last_updated' => new MongoDB\BSON\UTCDateTime()
            ]);
        }
        echo "success";
        
    } catch (Exception $e) {
        echo "error: " . $e->getMessage();
    }
    exit;
}

// Handle inventory check for specific product
if(isset($_GET['check'])) {
    $name = $_GET['check'];
    $cursor = $inventory->find(['product_name' => $name]);
    $items = [];
    foreach ($cursor as $item) {
        $item['_id'] = (string)$item['_id'];
        $item['last_updated'] = $item['last_updated']->toDateTime()->format('Y-m-d H:i:s');
        $items[] = $item;
    }
    header('Content-Type: application/json');
    echo json_encode($items);
    exit;
}

// Default: Return all inventory
$cursor = $inventory->find([], ['sort' => ['last_updated' => -1]]);
$items = [];
foreach ($cursor as $item) {
    $item['_id'] = (string)$item['_id'];
    $item['last_updated'] = $item['last_updated']->toDateTime()->format('Y-m-d H:i:s');
    $items[] = $item;
}
header('Content-Type: application/json');
echo json_encode($items);
?>