<?php
function connectDB() {
    return new mysqli('localhost', 'root', '', 'jewelry_hong_prm392');
}

function getAllUsers() {
    $conn = connectDB();
    $result = $conn->query('SELECT user.*, role.name as role_name FROM user LEFT JOIN role ON user.role_id = role.id');
    return $result;
}

function addUser($username, $password, $email, $full_name, $role_id) {
    $conn = connectDB();
    // Hash mật khẩu trước khi lưu
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Kiểm tra role_id có tồn tại không
    if ($role_id) {
        $check_role = $conn->prepare("SELECT id FROM role WHERE id = ?");
        $check_role->bind_param("i", $role_id);
        $check_role->execute();
        $role_result = $check_role->get_result();
        if ($role_result->num_rows == 0) {
            return ['success' => false, 'error' => 'Vai trò không tồn tại'];
        }
    }
    
    // Chuẩn bị câu lệnh SQL với role_id có thể NULL
    if ($role_id) {
        $stmt = $conn->prepare("INSERT INTO user (username, password, email, full_name, role_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $username, $hashed_password, $email, $full_name, $role_id);
    } else {
        $stmt = $conn->prepare("INSERT INTO user (username, password, email, full_name) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $hashed_password, $email, $full_name);
    }
    
    try {
        $result = $stmt->execute();
        $stmt->close();
        return ['success' => $result];
    } catch (mysqli_sql_exception $e) {
        // Xử lý lỗi unique constraint
        if ($e->getCode() == 1062) {
            return ['success' => false, 'error' => 'Username hoặc email đã tồn tại'];
        }
        // Trả về thông tin lỗi chi tiết hơn để debug
        return ['success' => false, 'error' => 'Lỗi: ' . $e->getMessage()];
    }
}

function updateUser($id, $email, $full_name, $role_id) {
    $conn = connectDB();
    $stmt = $conn->prepare("UPDATE user SET email=?, full_name=?, role_id=? WHERE id=?");
    $stmt->bind_param("ssii", $email, $full_name, $role_id, $id);
    
    try {
        $result = $stmt->execute();
        $stmt->close();
        return ['success' => $result];
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) {
            return ['success' => false, 'error' => 'Email đã tồn tại'];
        }
        return ['success' => false, 'error' => 'Lỗi khi cập nhật người dùng'];
    }
}

function updatePassword($id, $new_password) {
    $conn = connectDB();
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE user SET password=? WHERE id=?");
    $stmt->bind_param("si", $hashed_password, $id);
    $result = $stmt->execute();
    $stmt->close();
    return ['success' => $result];
}

function deleteUser($id) {
    $conn = connectDB();
    // Kiểm tra xem user có đơn hàng không
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM orders WHERE user_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    if ($row['count'] > 0) {
        return ['success' => false, 'error' => 'Không thể xóa người dùng này vì có đơn hàng liên quan'];
    }
    
    // Nếu không có đơn hàng, tiến hành xóa
    $stmt = $conn->prepare("DELETE FROM user WHERE id=?");
    $stmt->bind_param("i", $id);
    $result = $stmt->execute();
    $stmt->close();
    return ['success' => $result];
}

function getRoles() {
    $conn = connectDB();
    $result = $conn->query('SELECT * FROM role');
    return $result;
}

// Xử lý các request
$action = $_GET['action'] ?? '';

if ($action == 'getUsers') {
    $users = getAllUsers();
    $rows = array();
    while ($row = $users->fetch_assoc()) {
        // Loại bỏ password khỏi dữ liệu trả về
        unset($row['password']);
        $rows[] = $row;
    }
    echo json_encode($rows);
    exit;
} elseif ($action == 'getRoles') {
    $roles = getRoles();
    $rows = array();
    while ($row = $roles->fetch_assoc()) {
        $rows[] = $row;
    }
    echo json_encode($rows);
    exit;
} elseif ($action == 'addUser') {
    $result = addUser(
        $_POST['username'],
        $_POST['password'],
        $_POST['email'],
        $_POST['full_name'],
        $_POST['role_id']
    );
    echo json_encode($result);
    exit;
} elseif ($action == 'updateUser') {
    $result = updateUser(
        $_POST['id'],
        $_POST['email'],
        $_POST['full_name'],
        $_POST['role_id']
    );
    echo json_encode($result);
    exit;
} elseif ($action == 'updatePassword') {
    $result = updatePassword($_POST['id'], $_POST['new_password']);
    echo json_encode($result);
    exit;
} elseif ($action == 'deleteUser') {
    $result = deleteUser($_GET['id']);
    echo json_encode($result);
    exit;
}
?> 