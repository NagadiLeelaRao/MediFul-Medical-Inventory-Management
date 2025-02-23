<?php
include 'db_connect.php';
header('Content-Type: application/json');

// Check if a specific medicine name is provided
$data = json_decode(file_get_contents("php://input"), true);
$medicineName = isset($data['name']) ? trim($data['name']) : null;

if ($medicineName) {
    // Fetch stock details for the given medicine name
    $stmt = $conn->prepare("SELECT items.id, items.name, items.price, stock.qty 
                            FROM stock 
                            JOIN items ON stock.id = items.id 
                            WHERE items.name = ?");
    $stmt->bind_param("s", $medicineName);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $stockData = $result->fetch_assoc();
        echo json_encode(["success" => true, "data" => $stockData]);
    } else {
        echo json_encode(["success" => false, "error" => "Medicine not available in stock"]);
    }

    $stmt->close();
} else {
    // Fetch all medicines and stock data if no name is provided
    $stmt = $conn->prepare("SELECT items.id, items.name, items.price, stock.qty 
                            FROM stock 
                            JOIN items ON stock.id = items.id");
    $stmt->execute();
    $result = $stmt->get_result();

    $stockData = [];
    while ($row = $result->fetch_assoc()) {
        $stockData[] = $row;
    }

    echo json_encode(["success" => true, "data" => $stockData]);
    $stmt->close();
}

$conn->close();
?>
