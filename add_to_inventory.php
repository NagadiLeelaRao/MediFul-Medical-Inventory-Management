<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db_connect.php'; // Ensure this connects correctly

$response = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Debugging: Check received POST data
    file_put_contents('debug_log.txt', print_r($_POST, true));

    if (isset($_POST['id'], $_POST['Name'], $_POST['qty'], $_POST['batchno'], $_POST['mfd_date'], $_POST['exp_date'])) {
        $medicineId = intval($_POST['id']);
        $medicineName = trim($_POST['Name']);
        $quantity = intval($_POST['qty']);
        $batchNo = trim($_POST['batchno']);
        $mfdDate = trim($_POST['mfd_date']);
        $expDate = trim($_POST['exp_date']);

        // Check if the medicine exists in the 'items' table
        $stmtCheck = $conn->prepare("SELECT Id FROM items WHERE Id = ?");
        if (!$stmtCheck) {
            echo json_encode(["success" => false, "error" => "Database error: " . $conn->error]);
            exit();
        }
        $stmtCheck->bind_param("i", $medicineId);
        $stmtCheck->execute();
        $result = $stmtCheck->get_result();

        if ($result->num_rows === 0) {
            echo json_encode(["success" => false, "error" => "Medicine not found in items table"]);
            exit();
        }
        $stmtCheck->close();

        // Insert into the 'stock' table
        $stmt = $conn->prepare("INSERT INTO stock (id, qty, batchno, mfd_date, exp_date) VALUES (?, ?, ?, ?, ?)");
        if (!$stmt) {
            echo json_encode(["success" => false, "error" => "Database prepare error: " . $conn->error]);
            exit();
        }

        $stmt->bind_param("iisss", $medicineId, $quantity, $batchNo, $mfdDate, $expDate);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Medicine added to stock successfully"]);
        } else {
            echo json_encode(["success" => false, "error" => "Database insert error: " . $stmt->error]);
        }

        $stmt->close();
    } else {
        echo json_encode(["success" => false, "error" => "Missing required fields"]);
    }
} else {
    echo json_encode(["success" => false, "error" => "Invalid request method"]);
}

$conn->close();
?>
