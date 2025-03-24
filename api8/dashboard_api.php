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

function getStatistics() {
    $conn = connectDB();
    $stats = array();
    
    // Tổng số đơn hàng và doanh thu
    $sql = "SELECT 
            COUNT(*) as total_orders,
            SUM(total_price) as total_revenue,
            COUNT(CASE WHEN status = 'Completed' THEN 1 END) as completed_orders,
            COUNT(CASE WHEN status = 'Processing' THEN 1 END) as processing_orders,
            COUNT(CASE WHEN status = 'Pending' THEN 1 END) as pending_orders
            FROM orders";
    $result = $conn->query($sql);
    if (!$result) {
        throw new Exception("Lỗi query orders: " . $conn->error);
    }
    $stats['orders'] = $result->fetch_assoc();

    // Tổng số sản phẩm và danh mục
    $sql = "SELECT 
            (SELECT COUNT(*) FROM product) as total_products,
            (SELECT COUNT(*) FROM categories) as total_categories";
    $result = $conn->query($sql);
    if (!$result) {
        throw new Exception("Lỗi query products: " . $conn->error);
    }
    $stats['products'] = $result->fetch_assoc();

    // Tổng số người dùng
    $sql = "SELECT COUNT(*) as total_users FROM user";
    $result = $conn->query($sql);
    if (!$result) {
        throw new Exception("Lỗi query users: " . $conn->error);
    }
    $stats['users'] = $result->fetch_assoc();

    // Tổng số đánh giá và trung bình rating
    $sql = "SELECT 
            COUNT(*) as total_reviews,
            AVG(rating) as avg_rating
            FROM reviews";
    $result = $conn->query($sql);
    if (!$result) {
        throw new Exception("Lỗi query reviews: " . $conn->error);
    }
    $stats['reviews'] = $result->fetch_assoc();

    // Top 5 sản phẩm bán chạy
    $sql = "SELECT p.id, p.name, p.image_url, COUNT(o.id) as order_count
            FROM product p
            LEFT JOIN orderdetail o ON p.id = o.product_id
            GROUP BY p.id
            ORDER BY order_count DESC
            LIMIT 5";
    $result = $conn->query($sql);
    if (!$result) {
        throw new Exception("Lỗi query top products: " . $conn->error);
    }
    $stats['top_products'] = array();
    while ($row = $result->fetch_assoc()) {
        $stats['top_products'][] = $row;
    }

    // Doanh thu theo tháng trong năm nay
    $sql = "SELECT 
            MONTH(created_at) as month,
            SUM(total_price) as revenue
            FROM orders
            WHERE YEAR(created_at) = YEAR(CURRENT_DATE)
            AND status = 'Completed'
            GROUP BY MONTH(created_at)
            ORDER BY month";
    $result = $conn->query($sql);
    if (!$result) {
        throw new Exception("Lỗi query monthly revenue: " . $conn->error);
    }
    $stats['monthly_revenue'] = array();
    while ($row = $result->fetch_assoc()) {
        $stats['monthly_revenue'][] = $row;
    }

    return $stats;
}

// Xử lý các request
$action = $_REQUEST['action'] ?? '';

try {
    if ($action == 'getStatistics') {
        $stats = getStatistics();
        echo json_encode(['success' => true, 'data' => $stats]);
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