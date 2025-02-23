<?php
header("Content-Type: application/json");

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'db_connect.php';

if (!$conn) {
    echo json_encode([
        "success" => false, 
        "message" => "Database connection failed."
    ]);
    exit();
}

$rawData = file_get_contents("php://input");
$data = json_decode($rawData, true);

if (!$data || !isset($data["items"]) || !is_array($data["items"])) {
    echo json_encode([
        "success" => false, 
        "message" => "Invalid JSON format or missing items."
    ]);
    exit();
}

try {
    $conn->begin_transaction();
    
    $errors = [];
    foreach ($data["items"] as $item) {
        // Check for required item name
        if (!isset($item["name"])) {
            $errors[] = "Missing item name";
            continue;
        }
        
        $itemName = $item["name"];
        
        // Get the item ID and check if it exists
        $getItem = $conn->prepare("SELECT Id FROM items WHERE Name = ?");
        if (!$getItem) {
            throw new Exception("Failed to prepare item query: " . $conn->error);
        }
        
        $getItem->bind_param("s", $itemName);
        $getItem->execute();
        $itemResult = $getItem->get_result();
        
        if ($itemResult->num_rows === 0) {
            $errors[] = "Item not found: $itemName";
            $getItem->close();
            continue;
        }

        $itemData = $itemResult->fetch_assoc();
        $itemId = $itemData['Id'];
        $getItem->close();

        // Get the requested quantity from stock records
        $getStockQty = $conn->prepare("
            SELECT s.Id as stock_id, s.qty 
            FROM stock s 
            WHERE s.id = ? AND s.qty > 0 
            ORDER BY s.mfd_date ASC
        ");
        $getStockQty->bind_param("i", $itemId);
        $getStockQty->execute();
        $stockResult = $getStockQty->get_result();
        
        $totalAvailable = 0;
        $stockRecords = [];
        while ($row = $stockResult->fetch_assoc()) {
            $totalAvailable += $row['qty'];
            $stockRecords[] = $row;
        }
        $getStockQty->close();

        $requestedQty = isset($item["requested_qty"]) ? intval($item["requested_qty"]) : 0;

        if ($totalAvailable >= $requestedQty) {
            $remainingQty = $requestedQty;
            
            // Update each stock record using FIFO until the requested quantity is fulfilled
            foreach ($stockRecords as $record) {
                if ($remainingQty <= 0) break;
                
                $deductQty = min($remainingQty, $record['qty']);
                
                $updateStock = $conn->prepare("
                    UPDATE stock 
                    SET qty = qty - ?
                    WHERE Id = ?
                ");
                
                $updateStock->bind_param("ii", $deductQty, $record['stock_id']);
                if (!$updateStock->execute()) {
                    throw new Exception("Failed to update stock for item: $itemName");
                }
                $updateStock->close();
                
                $remainingQty -= $deductQty;
            }
        } else {
            $errors[] = "Not enough stock for: $itemName (Available: $totalAvailable, Requested: $requestedQty)";
        }
    }
    
    if (empty($errors)) {
        $conn->commit();
        echo json_encode([
            "success" => true, 
            "message" => "Stock updated successfully."
        ]);
    } else {
        $conn->rollback();
        echo json_encode([
            "success" => false, 
            "message" => implode("; ", $errors)
        ]);
    }

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode([
        "success" => false,
        "message" => "Error processing request: " . $e->getMessage()
    ]);
} finally {
    $conn->close();
}
?>