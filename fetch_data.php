<?php
include 'db_connect.php';

// Set response header to JSON
header('Content-Type: application/json');

// Query to select every 100th row using ROW_NUMBER
$sql = "SELECT * FROM items";

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
