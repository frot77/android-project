<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

function connectDB() {
    $conn = new mysqli('localhost', 'root', '', 'jewelry_hong_prm392');
    if ($conn->connect_error) {
        throw new Exception("Kết nối database thất bại: " . $conn->connect_error);
    }
    return $conn;
}

function getAllOrders() {
    $conn = connectDB();
    $sql = "SELECT o.*, u.username, u.full_name 
            FROM orders o 
            LEFT JOIN user u ON o.user_id = u.id 
            ORDER BY o.created_at DESC";
    $result = $conn->query($sql);
    if (!$result) {
        throw new Exception("Lỗi query: " . $conn->error);
    }
    return $result;
}

function getOrderDetail($orderId) {
    $conn = connectDB();
    
    // Get order information
    $stmt = $conn->prepare("SELECT o.*, u.username, u.full_name 
                           FROM orders o 
                           LEFT JOIN user u ON o.user_id = u.id 
                           WHERE o.id = ?");
    if (!$stmt) {
        throw new Exception("Lỗi prepare statement: " . $conn->error);
    }
    
    $stmt->bind_param("i", $orderId);
    if (!$stmt->execute()) {
        throw new Exception("Lỗi execute: " . $stmt->error);
    }
    
    $order = $stmt->get_result()->fetch_assoc();
    if (!$order) {
        return null;
    }
    
    // Get order items from order_details
    $stmt = $conn->prepare("SELECT od.*, p.name as product_name, p.image_url, p.price
                           FROM orderdetail od 
                           JOIN product p ON od.product_id = p.id 
                           WHERE od.order_id = ?");
    if (!$stmt) {
        throw new Exception("Lỗi prepare statement items: " . $conn->error);
    }
    
    $stmt->bind_param("i", $orderId);
    if (!$stmt->execute()) {
        throw new Exception("Lỗi execute items: " . $stmt->error);
    }
    
    $items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    $order['items'] = $items;
    $order['total_amount'] = $order['total_amount'] ?? 0;
    
    return $order;
}

function updateOrderStatus($orderId, $status) {
    $conn = connectDB();
    
    // Validate status
    $validStatuses = ['Pending', 'Processing', 'Completed', 'Cancelled'];
    if (!in_array($status, $validStatuses)) {
        throw new Exception("Trạng thái không hợp lệ");
    }
    
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    if (!$stmt) {
        throw new Exception("Lỗi prepare statement update: " . $conn->error);
    }
    
    $stmt->bind_param("si", $status, $orderId);
    $result = $stmt->execute();
    if (!$result) {
        throw new Exception("Lỗi update status: " . $stmt->error);
    }
    
    $stmt->close();
    return $result;
}

// Xử lý các request
$action = $_REQUEST['action'] ?? ''; // Lấy action từ cả GET và POST

try {
    if ($action == 'getOrders') {
        $orders = getAllOrders();
        $rows = array();
        
        while ($row = $orders->fetch_assoc()) {
            $rows[] = $row;
        }
        
        echo json_encode(['success' => true, 'data' => $rows]);
    } elseif ($action == 'getOrderDetail') {
        $orderId = $_GET['id'] ?? 0;
        $order = getOrderDetail($orderId);
        if ($order) {
            echo json_encode(['success' => true, 'data' => $order]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Không tìm thấy đơn hàng']);
        }
    } elseif ($action == 'updateStatus') {
        $orderId = $_POST['id'] ?? 0;
        $status = $_POST['status'] ?? '';
        
        if (empty($orderId) || empty($status)) {
            throw new Exception("Thiếu thông tin cần thiết");
        }
        
        $result = updateOrderStatus($orderId, $status);
        echo json_encode(['success' => $result]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Action không hợp lệ']);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}
?> 