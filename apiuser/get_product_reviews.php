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
if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];

    try {
        // Lấy danh sách reviews của sản phẩm
        $stmt = $conn->prepare("
            SELECT r.*, u.username, u.full_name
            FROM reviews r
            JOIN user u ON r.user_id = u.id
            WHERE r.product_id = ?
            ORDER BY r.created_at DESC
        ");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $reviews = array();
        while ($review = $result->fetch_assoc()) {
            $reviews[] = array(
                'review_id' => $review['id'],
                'user_id' => $review['user_id'],
                'username' => $review['username'],
                'full_name' => $review['full_name'],
                'rating' => $review['rating'],
                'comment' => $review['comment'],
                'created_at' => $review['created_at']
            );
        }

        // Tính rating trung bình
        $stmt = $conn->prepare("
            SELECT AVG(rating) as average_rating, COUNT(*) as total_reviews
            FROM reviews
            WHERE product_id = ?
        ");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $stats = $stmt->get_result()->fetch_assoc();

        $response['success'] = 1;
        $response['message'] = "Lấy danh sách đánh giá thành công!";
        $response['average_rating'] = round($stats['average_rating'], 1);
        $response['total_reviews'] = $stats['total_reviews'];
        $response['reviews'] = $reviews;

    } catch (Exception $e) {
        $response['success'] = 0;
        $response['message'] = "Lỗi: " . $e->getMessage();
    }

} else {
    $response['success'] = 0;
    $response['message'] = "Thiếu tham số product_id!";
}

echo json_encode($response);
$conn->close();
?> 