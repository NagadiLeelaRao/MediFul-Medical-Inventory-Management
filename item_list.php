<?php
include 'db_connect.php';

// Set response header to JSON
header('Content-Type: application/json');

// Query to fetch only the medicine names
$sql = "SELECT name FROM items";
$result = $conn->query($sql);

if ($result === false) {
    echo json_encode(["error" => "Query failed: " . $conn->error]);
    exit;
}

$medicineNames = array();
while ($row = $result->fetch_assoc()) {
    $medicineNames[] = $row['name'];
}

// Output JSON response
echo json_encode($medicineNames, JSON_PRETTY_PRINT);

// Close connection
$conn->close();
?>
