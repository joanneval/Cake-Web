<?php
include "db.php";

if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="sales_report_' . date('Y-m-d') . '.csv"');
    
    $cursor = $orders->find([], ['sort' => ['delivery_date' => -1]]);
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Customer', 'Cake', 'Price', 'Date', 'Status']);
    
    foreach ($cursor as $order) {
        $dateStr = isset($order['delivery_date']) ? 
            (is_string($order['delivery_date']) ? $order['delivery_date'] : 
            $order['delivery_date']->toDateTime()->format('Y-m-d')) : 'N/A';
        fputcsv($output, [
            $order['customer'] ?? 'N/A',
            $order['cake'] ?? 'N/A',
            '₱' . number_format($order['price'] ?? 0, 2),
            $dateStr,
            $order['status'] ?? 'Pending'
        ]);
    }
    fclose($output);
    exit;
}

// Build flexible filter
$filter = []; 
if (isset($_GET['date']) && !empty($_GET['date'])) {
    $date = $_GET['date'];  
    $filter['delivery_date'] = $date;  
}

$cursor = $orders->find($filter, ['sort' => ['delivery_date' => -1]]);
$sales = [];
foreach ($cursor as $sale) {
    $sale['_id'] = (string)$sale['_id'];
    
    
    if (isset($sale['delivery_date'])) {
        if ($sale['delivery_date'] instanceof MongoDB\BSON\UTCDateTime) {
            $sale['delivery_date'] = $sale['delivery_date']->toDateTime()->format('Y-m-d');
        } 
    } else {
        $sale['delivery_date'] = 'N/A';
    }
    
    $sales[] = $sale;
}

header('Content-Type: application/json');
echo json_encode($sales);
?>