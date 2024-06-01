<?php
include ('config.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["id"]) && isset($_POST["txt"])) {
        $tx = $_POST["txt"];
        $id = $_POST["id"];
        $sql = 'SELECT text FROM sharecode WHERE id = "' . $id . '";';
        $result = $conn->query($sql);
        if ($tx != "" && $result->num_rows == 0)
            $sql = 'INSERT INTO `sharecode`(`id`, `text`, `last_edit`) VALUES("' . $id . '","' . $tx . '","' . time() . '");';
        else if ($tx == "")
            $sql = 'DELETE FROM `sharecode` WHERE id ="' . $id . '";';
        else
            $sql = 'UPDATE sharecode SET text = "' . $tx . '", last_edit = "' . time() . '" WHERE id ="' . $id . '";';
        $conn->query($sql);
    }
?>