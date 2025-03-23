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
if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Lấy thông tin user từ database
    $sql = "SELECT id, password, full_name FROM user WHERE username = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();

            // Kiểm tra mật khẩu
            if (password_verify($password, $user['password'])) {
                $response['success'] = 1;
                $response['message'] = "Đăng nhập thành công!";
                $response['user'] = array(
                    "id" => $user['id'],
                    "full_name" => $user['full_name']
                );
            } else {
                $response['success'] = 0;
                $response['message'] = "Sai mật khẩu!";
            }
        } else {
            $response['success'] = 0;
            $response['message'] = "Tài khoản không tồn tại!";
        }

        $stmt->close();
    } else {
        $response['success'] = 0;
        $response['message'] = "Lỗi prepare statement: " . $conn->error;
    }
} else {
    $response['success'] = 0;
    $response['message'] = "Thiếu tham số đầu vào!";
}

echo json_encode($response);
$conn->close();
?>
