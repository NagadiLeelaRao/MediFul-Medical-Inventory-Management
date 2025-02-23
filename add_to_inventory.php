<?php
// Disable error display in production
ini_set('display_errors', 0);
error_reporting(0);

// Set JSON content type
header('Content-Type: application/json');

// Function to send JSON response
function sendJsonResponse($success, $message, $errorDetails = null) {
    $response = [
        'success' => $success,
        'message' => $message
    ];
    if ($errorDetails) {
        $response['error_details'] = $errorDetails;
    }
    echo json_encode($response);
    exit;
}

try {
    // Include database connection
    require_once 'db_connect.php';
    
    // Check request method
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        sendJsonResponse(false, "Invalid request method");
    }

    // Validate all required fields
    $requiredFields = ['id', 'Name', 'qty', 'batchno', 'mfd_date', 'exp_date'];
    foreach ($requiredFields as $field) {
        if (!isset($_POST[$field]) || trim($_POST[$field]) === '') {
            sendJsonResponse(false, "Missing or empty field: {$field}");
        }
    }

    // Sanitize and validate inputs
    $medicineId = filter_var($_POST['id'], FILTER_VALIDATE_INT);
    $quantity = filter_var($_POST['qty'], FILTER_VALIDATE_INT);
    $batchNo = trim($_POST['batchno']);
    $mfdDate = trim($_POST['mfd_date']);
    $expDate = trim($_POST['exp_date']);

    // Additional validation
    if ($medicineId === false || $medicineId <= 0) {
        sendJsonResponse(false, "Invalid medicine ID");
    }

    if ($quantity <= 0) {
        sendJsonResponse(false, "Quantity must be greater than 0");
    }

    if (empty($batchNo)) {
        sendJsonResponse(false, "Batch number cannot be empty");
    }

    // Validate dates
    $mfdDateTime = strtotime($mfdDate);
    $expDateTime = strtotime($expDate);
    
    if (!$mfdDateTime || !$expDateTime) {
        sendJsonResponse(false, "Invalid date format");
    }

    if ($expDateTime <= $mfdDateTime) {
        sendJsonResponse(false, "Expiry date must be after manufacturing date");
    }

    // First check if medicine exists in items table
    $checkStmt = $conn->prepare("SELECT Id FROM items WHERE Id = ?");
    if (!$checkStmt) {
        sendJsonResponse(false, "Database error", $conn->error);
    }

    $checkStmt->bind_param("i", $medicineId);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows === 0) {
        $checkStmt->close();
        sendJsonResponse(false, "Medicine not found in database");
    }
    $checkStmt->close();

    // Check if batch number already exists for this medicine
    $batchCheckStmt = $conn->prepare("SELECT id FROM stock WHERE id = ? AND batchno = ?");
    if (!$batchCheckStmt) {
        sendJsonResponse(false, "Database error", $conn->error);
    }

    $batchCheckStmt->bind_param("is", $medicineId, $batchNo);
    $batchCheckStmt->execute();
    $batchResult = $batchCheckStmt->get_result();

    if ($batchResult->num_rows > 0) {
        $batchCheckStmt->close();
        sendJsonResponse(false, "This batch number already exists for this medicine");
    }
    $batchCheckStmt->close();

    // Insert into stock table
    $insertStmt = $conn->prepare("INSERT INTO stock (id, qty, batchno, mfd_date, exp_date) VALUES (?, ?, ?, ?, ?)");
    if (!$insertStmt) {
        sendJsonResponse(false, "Database error", $conn->error);
    }

    $insertStmt->bind_param("iisss", $medicineId, $quantity, $batchNo, $mfdDate, $expDate);
    
    if (!$insertStmt->execute()) {
        // Check for duplicate entry error
        if ($insertStmt->errno == 1062) {
            sendJsonResponse(false, "This batch number already exists for this medicine");
        } else {
            sendJsonResponse(false, "Failed to add to inventory", $insertStmt->error);
        }
    }

    $insertStmt->close();
    $conn->close();

    sendJsonResponse(true, "Medicine added to inventory successfully");

} catch (Exception $e) {
    sendJsonResponse(false, "Server error", $e->getMessage());
}
?>
