<?php


header('Content-Type: application/json');
require_once 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'POST only']);
    exit;
}

$employeeName = trim($_POST['employee_name'] ?? '');
$employeeContact = trim($_POST['employee_contact'] ?? '');
$status = trim($_POST['status'] ?? 'active');

if (empty($employeeName) || empty($employeeContact)) {
    echo json_encode(['success' => false, 'message' => 'employee_name and employee_contact are required']);
    exit;
}

if (!in_array($status, ['active', 'inactive'])) {
    $status = 'active';
}

try {
    $stmt = $pdo->prepare("INSERT INTO employees (employee_name, employee_contact, status) 
                            VALUES (:employee_name, :employee_contact, :status)");
    $stmt->execute([
        'employee_name' => $employeeName,
        'employee_contact' => $employeeContact,
        'status' => $status
    ]);

    if ($redis) {
        $redis->del('employees_list');
        $redis->del('all_employees_by_id');
    }

    echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);

} catch (PDOException $e) {
    error_log("Add Employee Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
}
?>