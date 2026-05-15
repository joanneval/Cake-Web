<?php
require_once __DIR__ . '/vendor/autoload.php';
try {
    $client = new MongoDB\Client("mongodb://localhost:27017");
    $db = $client->cake_shop;
    $users = $db->users;
    $orders = $db->orders;
    $inventory = $db->inventory;
    $products = $db->products;
} catch (Exception $e) {
    die("MongoDB connection failed: " . $e->getMessage());
}
?>