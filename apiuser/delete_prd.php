<?php
$response = array();
$host = "localhost"; $u="root"; $p=""; $db="jewelry_hong_prm392";
$conn = new mysqli($host, $u, $p, $db);

//truyen tham so
if(isset($_POST['id'])){
    $id=$_POST['id'];
    $sql = "delete from product where id = '$id'";
    if($conn->query($sql) === TRUE){
        $response['success'] = 1;
        $response['message'] = "Delete thanh cong";
        echo json_encode($response);
    }
    else{
        $response['success'] = 0;
        $response['message'] = $conn->error;
        echo json_encode($response);
    }
}

$conn->close();
?>
