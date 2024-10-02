<?php
include('config.php');

// Create a single database connection using mysqli
$con = new mysqli($servername, $username, $password, $db_name);

// Check connection
if ($con->connect_error) {
    die("Database connection failed: " . $con->connect_error);
}

// Validate input
if (isset($_GET['id'])) {
    $id = $con->real_escape_string($_GET['id']);

    // Prepare SQL query with prepared statement
    $stmt = $con->prepare('SELECT * FROM sharecode WHERE id = ?');
    $stmt->bind_param('s', $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result) {
        header("Content-Type: application/JSON");
        $response = array();
        while ($row = $result->fetch_assoc()) {
            $response[] = array(
                'id' => $row['id'],
                'text' => $row['text'],
                'last_edit' => $row['last_edit']
            );
        }
        echo json_encode($response, JSON_PRETTY_PRINT);
    } else {
        echo json_encode(array("status" => "error", "message" => "No data found"));
    }
} else {
    echo json_encode(array("status" => "error", "message" => "Invalid request"));
}

// Update count
$sql_update = "UPDATE codes SET count = count + 1 WHERE id = 3";
$stmt = $con->prepare($sql_update);
$stmt->execute();

// Close database connection
$con->close();
?>