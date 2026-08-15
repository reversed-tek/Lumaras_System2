<?php


header('Content-Type: application/json');
require_once 'db_config.php';

$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Valid id is required']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id, employee_name, employee_contact, status FROM employees WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $employee = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($employee) {
        echo json_encode(['success' => true, 'employee' => $employee]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Employee not found']);
    }

} catch (PDOException $e) {
    error_log("Get Employee Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
}
?>