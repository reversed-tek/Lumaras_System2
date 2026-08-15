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
    if (!isset($input['slot_id']) || !isset($input['employee_id'])) {
        echo json_encode([
            'success' => false,
            'error' => 'Missing parameters',
            'message' => 'slot_id and employee_id are required'
        ]);
        exit;
    }

    $slotId = intval($input['slot_id']);
    $employeeId = intval($input['employee_id']);

    // Validate slot_id
    if ($slotId <= 0) {
        echo json_encode([
            'success' => false,
            'error' => 'Invalid slot_id',
            'message' => 'slot_id must be a positive integer'
        ]);
        exit;
    }

    // Validate employee_id
    if ($employeeId <= 0) {
        echo json_encode([
            'success' => false,
            'error' => 'Invalid employee_id',
            'message' => 'employee_id must be a positive integer'
        ]);
        exit;
    }

    // Confirm the employee actually exists (and is active) before locking the slot
    $checkStmt = $pdo->prepare("SELECT id FROM employees WHERE id = :employee_id AND status = 'active'");
    $checkStmt->execute(['employee_id' => $employeeId]);

    if (!$checkStmt->fetch()) {
        echo json_encode([
            'success' => false,
            'error' => 'Invalid employee',
            'message' => 'No active employee found for the given employee_id'
        ]);
        exit;
    }


    $sql = "UPDATE shift_slots 
            SET is_taken = 1, 
                taken_by_employee_id = :employee_id 
            WHERE id = :slot_id 
            AND is_taken = 0";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'employee_id' => $employeeId,
        'slot_id' => $slotId
    ]);

    // Check how many rows were affected
    $affectedRows = $stmt->rowCount();

    if ($affectedRows > 0) {
        // Success - slot was available and is now taken
        echo json_encode([
            'success' => true,
            'message' => 'Slot marked as taken successfully',
            'slot_id' => $slotId,
            'employee_id' => $employeeId,
            'affected_rows' => $affectedRows
        ]);
    } else {
        // Slot was already taken or doesn't exist
        echo json_encode([
            'success' => false,
            'error' => 'Slot unavailable',
            'message' => 'This slot was just taken by another employee or does not exist',
            'slot_id' => $slotId,
            'affected_rows' => 0
        ]);
    }

} catch (PDOException $e) {
    // Log error and return JSON error response
    error_log("Mark Slot Taken Error: " . $e->getMessage());

    echo json_encode([
        'success' => false,
        'error' => 'Database error',
        'message' => $e->getMessage()
    ]);
}
?>