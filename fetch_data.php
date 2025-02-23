<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Check if 'name' is set in the POST request
    if (isset($_POST['name'])) {
        $medicineName = $_POST['name'];

        // Prevent SQL Injection
        $stmt = $conn->prepare("SELECT * FROM items WHERE Name = ?");
        $stmt->bind_param("s", $medicineName);
        $stmt->execute();
        $result = $stmt->get_result();

        $medicineData = [];
        while ($row = $result->fetch_assoc()) {
            $medicineData[] = $row;
        }

        // Send JSON response
        echo json_encode($medicineData);
        $stmt->close();
        $conn->close();
    } else {
        echo json_encode(["error" => "No medicine name provided"]);
    }
} else {
    echo json_encode(["error" => "Invalid request method"]);
}
/*
// Set response header to JSON
header('Content-Type: application/json');

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
$conn->close();*/
?>
