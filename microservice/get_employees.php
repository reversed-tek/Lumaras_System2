<?php


header('Content-Type: application/json');
require_once 'db_config.php';

$includeAll = isset($_GET['all']) && $_GET['all'] == '1';

try {
    if ($includeAll) {
        $sql = "SELECT id, employee_name, employee_contact, status 
                FROM employees 
                ORDER BY employee_name ASC";
        $stmt = $pdo->query($sql);
    } else {
        $sql = "SELECT id, employee_name, employee_contact, status 
                FROM employees 
                WHERE status = 'active' 
                ORDER BY employee_name ASC";
        $stmt = $pdo->query($sql);
    }

    $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'employees' => $employees
    ]);

} catch (PDOException $e) {
    error_log("Get Employees Error: " . $e->getMessage());

    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch employees'
    ]);
}
?>