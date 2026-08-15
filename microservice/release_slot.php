<?php

// Set JSON response header
header('Content-Type: application/json');

// Include database configuration
require_once 'db_config.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'error' => 'Method not allowed',
        'message' => 'This endpoint only accepts POST requests'
    ]);
    exit;
}

try {
    // Get POST parameters (supports both form data and JSON body)
    $input = $_POST;
    if (empty($input)) {
        // Try to parse JSON body
        $input = json_decode(file_get_contents('php://input'), true);
    }
    
    // Validate required parameters
    if (!isset($input['slot_id'])) {
        echo json_encode([
            'success' => false,
            'error' => 'Missing parameter',
            'message' => 'slot_id is required'
        ]);
        exit;
    }
    
    $slotId = intval($input['slot_id']);
    
    // Validate slot_id
    if ($slotId <= 0) {
        echo json_encode([
            'success' => false,
            'error' => 'Invalid slot_id',
            'message' => 'slot_id must be a positive integer'
        ]);
        exit;
    }
    
    // UPDATE query to release the slot
    $sql = "UPDATE shift_slots 
            SET is_taken = 0, 
                taken_by_employee = NULL 
            WHERE id = :slot_id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['slot_id' => $slotId]);
    
    // Check how many rows were affected
    $affectedRows = $stmt->rowCount();
    
    if ($affectedRows > 0) {
        // Success - slot was released
        echo json_encode([
            'success' => true,
            'message' => 'Slot released successfully and is now available',
            'slot_id' => $slotId,
            'affected_rows' => $affectedRows
        ]);
    } else {
        // Slot doesn't exist or was already available
        echo json_encode([
            'success' => false,
            'error' => 'Slot not found',
            'message' => 'Slot does not exist or was already available',
            'slot_id' => $slotId,
            'affected_rows' => 0
        ]);
    }
    
} catch (PDOException $e) {
    // Log error and return JSON error response
    error_log("Release Slot Error: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'error' => 'Database error',
        'message' => $e->getMessage()
    ]);
}
?>
