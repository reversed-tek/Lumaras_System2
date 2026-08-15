<?php


// Set JSON response header
header('Content-Type: application/json');

// Include database configuration
require_once 'db_config.php';

try {
    // Get optional parameter for including a specific slot (used in update forms)
    $includeSlotId = isset($_GET['include_slot_id']) ? intval($_GET['include_slot_id']) : null;

  
    if ($includeSlotId) {
        // Include the specified slot even if taken (for edit forms)
        $sql = "SELECT id, start_time, end_time, role, is_taken, taken_by_employee_id 
                FROM shift_slots 
                WHERE is_taken = 0 OR id = :include_id
                ORDER BY start_time ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['include_id' => $includeSlotId]);
    } else {
        // Only fetch available slots
        $sql = "SELECT id, start_time, end_time, role, is_taken, taken_by_employee_id 
                FROM shift_slots 
                WHERE is_taken = 0 
                ORDER BY start_time ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
    }

    // Fetch all results
    $slots = $stmt->fetchAll();

    // Format each slot with a display label for the dropdown
    $formattedSlots = [];
    foreach ($slots as $slot) {
        // Format times for better readability (remove seconds)
        $startTime = substr($slot['start_time'], 0, 5);  // HH:MM
        $endTime = substr($slot['end_time'], 0, 5);      // HH:MM

        // Create a human-readable display label (no date - just time + role)
        $displayLabel = $startTime . ' - ' . $endTime . ' | ' . $slot['role'];

        $formattedSlots[] = [
            'id' => (int)$slot['id'],
            'start_time' => $slot['start_time'],
            'end_time' => $slot['end_time'],
            'role' => $slot['role'],
            'is_taken' => (int)$slot['is_taken'],
            'taken_by_employee_id' => $slot['taken_by_employee_id'] !== null ? (int)$slot['taken_by_employee_id'] : null,
            'display_label' => $displayLabel
        ];
    }

    // Return successful JSON response
    echo json_encode([
        'success' => true,
        'count' => count($formattedSlots),
        'slots' => $formattedSlots
    ]);

} catch (PDOException $e) {
    // Log error and return JSON error response
    error_log("Microservice API Error: " . $e->getMessage());

    echo json_encode([
        'success' => false,
        'error' => 'Database query failed',
        'message' => $e->getMessage()
    ]);
}
?>