<?php
session_start();
header('Content-Type: application/json');

function connectDB() {
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $db = 'jewelry_hong_prm392';
    
    $conn = new mysqli($host, $user, $pass, $db);
    if ($conn->connect_error) {
        throw new Exception("Lỗi kết nối: " . $conn->connect_error);
    }
    $conn->set_charset("utf8mb4");
    return $conn;
}

function login($username, $password) {
    try {
        $conn = connectDB();
        
        // Chuẩn bị câu lệnh SQL với prepared statement
        $stmt = $conn->prepare("SELECT u.*, r.name as role_name FROM user u 
                              LEFT JOIN role r ON u.role_id = r.id 
                              WHERE u.username = ?");
        
        if (!$stmt) {
            throw new Exception("Lỗi chuẩn bị câu lệnh: " . $conn->error);
        }
        
        $stmt->bind_param("s", $username);
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Kiểm tra mật khẩu
            if (password_verify($password, $user['password'])) {
                // Kiểm tra xem người dùng có phải là admin không
                if ($user['role_name'] === 'ADMIN') {
                    // Lưu thông tin vào session
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = 'admin';
                    
                    return ['success' => true];
                } else {
                    return ['success' => false, 'error' => 'Bạn không có quyền truy cập trang admin'];
                }
            } else {
                return ['success' => false, 'error' => 'Mật khẩu không đúng'];
            }
        } else {
            return ['success' => false, 'error' => 'Tên đăng nhập không tồn tại'];
        }
    } catch (Exception $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    } finally {
        if (isset($stmt)) $stmt->close();
        if (isset($conn)) $conn->close();
    }
}

function checkAuth() {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        header('Location: login.php');
        exit();
    }
}

function logout() {
    session_destroy();
    return ['success' => true];
}

// Xử lý request
$action = $_POST['action'] ?? '';

if ($action === 'login') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        echo json_encode(['success' => false, 'error' => 'Vui lòng nhập đầy đủ thông tin']);
        exit;
    }
    
    $result = login($username, $password);
    echo json_encode($result);
    exit;
}

// Xử lý các request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'logout':
            echo json_encode(logout());
            break;
            
        default:
            echo json_encode(['success' => false, 'error' => 'Invalid action']);
    }
}
?> 