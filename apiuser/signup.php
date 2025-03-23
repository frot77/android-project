<?php
$response = array();
$host = "localhost";
$u = "root";
$p = "";
$db = "jewelry_hong_prm392"; 

$conn = new mysqli($host, $u, $p, $db);

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Kiểm tra tham số đầu vào
if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['email']) && isset($_POST['full_name'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Băm mật khẩu
    $email = $_POST['email'];
    $full_name = $_POST['full_name'];

    // Kiểm tra trùng lặp username hoặc email
    $checkUser = $conn->prepare("SELECT id FROM user WHERE username = ? OR email = ?");
    $checkUser->bind_param("ss", $username, $email);
    $checkUser->execute();
    $result = $checkUser->get_result();

    if ($result->num_rows > 0) {
        $response['success'] = 0;
        $response['message'] = "Tên đăng nhập hoặc email đã tồn tại!";
    } else {
        // Chèn dữ liệu vào database
        $sql = "INSERT INTO user (username, password, email, full_name,role_id) VALUES (?, ?, ?, ?,?)";
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            $role = 2; // Khai báo biến trước
$stmt->bind_param("ssssi", $username, $password, $email, $full_name, $role);
            if ($stmt->execute()) {
                $response['success'] = 1;
                $response['message'] = "Đăng ký thành công!";
            } else {
                $response['success'] = 0;
                $response['message'] = "Lỗi khi đăng ký: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $response['success'] = 0;
            $response['message'] = "Lỗi prepare statement: " . $conn->error;
        }
    }
    $checkUser->close();
} else {
    $response['success'] = 0;
    $response['message'] = "Thiếu tham số đầu vào!";
}

echo json_encode($response);
$conn->close();
?>
