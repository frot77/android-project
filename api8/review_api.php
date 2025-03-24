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

function getAllReviews() {
    $conn = connectDB();
    $sql = "SELECT r.*, u.username, u.full_name, p.name as product_name, p.image_url 
            FROM reviews r 
            LEFT JOIN user u ON r.user_id = u.id 
            LEFT JOIN product p ON r.product_id = p.id 
            ORDER BY r.created_at DESC";
    $result = $conn->query($sql);
    if (!$result) {
        throw new Exception("Lỗi query: " . $conn->error);
    }
    return $result;
}

function deleteReview($reviewId) {
    $conn = connectDB();
    
    $stmt = $conn->prepare("DELETE FROM reviews WHERE id = ?");
    if (!$stmt) {
        throw new Exception("Lỗi prepare statement: " . $conn->error);
    }
    
    $stmt->bind_param("i", $reviewId);
    $result = $stmt->execute();
    if (!$result) {
        throw new Exception("Lỗi xóa đánh giá: " . $stmt->error);
    }
    
    $stmt->close();
    return $result;
}

// Xử lý các request
$action = $_REQUEST['action'] ?? '';

try {
    if ($action == 'getReviews') {
        $reviews = getAllReviews();
        $rows = array();
        
        while ($row = $reviews->fetch_assoc()) {
            $rows[] = $row;
        }
        
        echo json_encode(['success' => true, 'data' => $rows]);
    } elseif ($action == 'deleteReview') {
        $reviewId = $_POST['id'] ?? 0;
        
        if (empty($reviewId)) {
            throw new Exception("Thiếu ID đánh giá");
        }
        
        $result = deleteReview($reviewId);
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