<?php
    date_default_timezone_set('Asia/Kolkata');
    $servername = "localhost";
    $username = "*********";
    $password = "**********";
    $db_name = "***********";
    $baseurl = "/";
    $conn = new mysqli($servername, $username, $password, $db_name);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
}
?>