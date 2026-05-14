<?php
require_once __DIR__ . '/vendor/autoload.php';
try {
    $client = new MongoDB\Client("mongodb+srv://admin:admin123@cluster0.mwtea4i.mongodb.net/?appName=Cluster0");
    $db = $client->cake_shop;
    $users = $db->users;
    $orders = $db->orders;
    $inventory = $db->inventory;
    $products = $db->products;
} catch (Exception $e) {
    die("MongoDB connection failed: " . $e->getMessage());
}
?>