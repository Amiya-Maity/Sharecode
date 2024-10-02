<?php
include ('config.php');

// Validate input
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["id"]) && isset($_POST["txt"])) {
    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);
    $tx = filter_input(INPUT_POST, 'txt', FILTER_SANITIZE_STRING);

    // Prepare SQL queries with prepared statements
    $stmt = $conn->prepare('SELECT text FROM sharecode WHERE id = ?');
    $stmt->bind_param('s', $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($tx != "" && $result->num_rows == 0) {
        $stmt = $conn->prepare('INSERT INTO `sharecode`(`id`, `text`, `last_edit`) VALUES(?, ?, ?)');
        $stmt->bind_param('sss', $id, $tx, time());
        $stmt->execute();
    } else if ($tx == "") {
        $stmt = $conn->prepare('DELETE FROM `sharecode` WHERE id = ?');
        $stmt->bind_param('s', $id);
        $stmt->execute();
    } else {
        $stmt = $conn->prepare('UPDATE sharecode SET text = ?, last_edit = ? WHERE id = ?');
        $stmt->bind_param('ssi', $tx, time(), $id);
        $stmt->execute();
    }

    echo json_encode(array("status" => "success", "message" => "Text uploaded successfully"));

} else {
    // Return error response if the required parameters are not provided
    echo json_encode(array("status" => "error", "message" => "A problem arises when uploading.."));
}
?>