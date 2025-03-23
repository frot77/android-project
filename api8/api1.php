<?php
//trước tên biến luôn có dấu $, ví dụ $a
//truy cập thuộc tính của đối tượng, dùng dấu ->
//nối chuỗi: dùng dấu chấm (.)
//in ra màn hình: echo
//còn lại gần giống với java
//----

header('Access-Control-Allow-Origin: *');//cho phép truy cập full control
$host="localhost";$u="root";$p="";$db="jewelry_hong_prm392";//khai báo thông tin server
$conn = new mysqli($host,$u,$p,$db);//kết nối với csdl
$result = $conn->query("select * from product");//truy vấn csdl
while($row[]=$result->fetch_assoc()){//đọc kết quả
    $json = json_encode($row);//chuyển sang json
}

echo '{"products":'.$json.'}'; //thêm tên bảng dữ liệu và in ra kết quả
$conn->close();//đóng kết nối
?>