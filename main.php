<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config.php';

// Set the timezone to Kolkata
date_default_timezone_set('Asia/Kolkata');

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

try {
    // Create a new database connection using the defined constants
    $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    $conn->set_charset("utf8mb4");
    $conn->query("SET SESSION sql_mode = 'STRICT_ALL_TABLES'");

    // Output a success message (optional)
    // echo "Database connection successful!";

} catch (Exception $e) {
    // Log the error message for debugging
    error_log("Database connection error: " . $e->getMessage());

    // Display a user-friendly error message
    http_response_code(500);
    die("An error occurred while connecting to the database. Please try again later.");
}

// Close the database connection when it's no longer needed
function closeDatabaseConnection($conn) {
    $conn->close();
}

// Register the function to close the database connection when the script ends
register_shutdown_function(function() use ($conn) {
    closeDatabaseConnection($conn);
});

// Example usage of the database connection
function getUserData($conn, $userId) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

$userId = 1;
$userData = getUserData($conn, $userId);
print_r($userData);

closeDatabaseConnection($conn);
?>