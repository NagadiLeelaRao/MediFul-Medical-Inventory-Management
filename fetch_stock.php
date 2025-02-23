<?php
include 'db_connect.php';

// Set response header to JSON
header('Content-Type: application/json');

// Query to select every 100th row using ROW_NUMBER
$sql = "SELECT stock.id, Name, Manufacturer, Pack_Size, Price, qty, batchno, mfd_date, exp_date, (Price*Qty) as Total FROM stock, items where stock.id=items.id";

$result = $conn->query($sql);

//Check if query was successful
if ($result === false) {
    echo json_encode(["error" => "Query failed: " . $conn->error]);
    exit;
}

// Fetch data
$data = array();
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// Output JSON response
echo json_encode($data, JSON_PRETTY_PRINT);

// Close connection
$conn->close();
?>
