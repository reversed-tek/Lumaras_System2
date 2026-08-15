<?php


// Database connection parameters
$host = 'mysql';  // Docker service name from docker-compose.yml
$dbname = 'microservice_db';
$username = 'root';
$password = 'rootpassword';
$charset = 'utf8mb4';

// Build DSN (Data Source Name)
$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

// PDO options for security and error handling
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,  
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,        
    PDO::ATTR_EMULATE_PREPARES   => false,                  
];

try {
    // Create PDO instance
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    // Log error and return JSON error response
    error_log("Microservice DB Connection Error: " . $e->getMessage());
    
    // Return JSON error for API calls
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'error' => 'Database connection failed',
        'message' => 'Unable to connect to microservice database'
    ]);
    exit;
}
?>
