<?php
include('config.php');
$con = new mysqli($servername, $username, $password, $db_name);
$sql_update = "UPDATE codes SET count = count + 1 WHERE id = 3";
$conn->query($sql_update);
$response = array();
if($con){
    $id = $_GET['id'];
    $sql = "Select *  from sharecode where id = '$id'";
    $result = mysqli_query($con, $sql);
        // echo $sql;
    if($result){
        header("Content-Type: application/JSON");
        $i =0;
        while($row = mysqli_fetch_assoc($result)){
            $response[$i]['id'] = $row['id'];
            $response[$i]['text'] = $row['text'];
            $response[$i]['last_edit'] = $row['last_edit'];
        }
        echo json_encode($response, JSON_PRETTY_PRINT);
    }
}
else{
    echo "Database connection failed!!!";
}
?>