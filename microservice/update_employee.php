<?php


header('Content-Type: application/json');
require_once 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'POST only']);
    exit;
}

$id = intval($_POST['id'] ?? 0);
$employeeName = trim($_POST['employee_name'] ?? '');
$employeeContact = trim($_POST['employee_contact'] ?? '');
$status = trim($_POST['status'] ?? 'active');

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Valid id is required']);
    exit;
}

if (empty($employeeName) || empty($employeeContact)) {
    echo json_encode(['success' => false, 'message' => 'employee_name and employee_contact are required']);
    exit;
}

if (!in_array($status, ['active', 'inactive'])) {
    $status = 'active';
}

try {
    $stmt = $pdo->prepare("UPDATE employees 
                            SET employee_name = :employee_name, 
                                employee_contact = :employee_contact, 
                                status = :status 
                            WHERE id = :id");
    $stmt->execute([
        'employee_name' => $employeeName,
        'employee_contact' => $employeeContact,
        'status' => $status,
        'id' => $id
    ]);

    if ($redis) {
        $redis->del('employees_list');
        $redis->del('all_employees_by_id');
    }

    echo json_encode(['success' => true, 'affected_rows' => $stmt->rowCount()]);

} catch (PDOException $e) {
    error_log("Update Employee Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
}
?>