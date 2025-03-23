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
if (isset($_POST['user_id']) && isset($_POST['product_id']) && 
    isset($_POST['rating']) && isset($_POST['comment'])) {
    
    $user_id = $_POST['user_id'];
    $product_id = $_POST['product_id'];
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];

    try {
        // Kiểm tra xem user đã mua sản phẩm này chưa
        $stmt = $conn->prepare("
            SELECT COUNT(*) as has_purchased
            FROM orders o
            JOIN orderdetail od ON o.id = od.order_id
            WHERE o.user_id = ? AND od.product_id = ? AND o.status = 'Completed'
        ");
        $stmt->bind_param("ii", $user_id, $product_id);
        $stmt->execute();
        $purchase_check = $stmt->get_result()->fetch_assoc();

        if ($purchase_check['has_purchased'] > 0) {
            // Kiểm tra xem user đã review sản phẩm này chưa
            $stmt = $conn->prepare("
                SELECT id FROM reviews
                WHERE user_id = ? AND product_id = ?
            ");
            $stmt->bind_param("ii", $user_id, $product_id);
            $stmt->execute();
            $existing_review = $stmt->get_result();

            if ($existing_review->num_rows > 0) {
                // Cập nhật review cũ
                $stmt = $conn->prepare("
                    UPDATE reviews
                    SET rating = ?, comment = ?, created_at = CURRENT_TIMESTAMP
                    WHERE user_id = ? AND product_id = ?
                ");
                $stmt->bind_param("isii", $rating, $comment, $user_id, $product_id);
                $stmt->execute();
                
                $response['success'] = 1;
                $response['message'] = "Cập nhật đánh giá thành công!";
            } else {
                // Thêm review mới
                $stmt = $conn->prepare("
                    INSERT INTO reviews (user_id, product_id, rating, comment)
                    VALUES (?, ?, ?, ?)
                ");
                $stmt->bind_param("iiis", $user_id, $product_id, $rating, $comment);
                $stmt->execute();
                
                $response['success'] = 1;
                $response['message'] = "Thêm đánh giá mới thành công!";
            }
        } else {
            $response['success'] = 0;
            $response['message'] = "Bạn cần mua sản phẩm này trước khi đánh giá!";
        }

    } catch (Exception $e) {
        $response['success'] = 0;
        $response['message'] = "Lỗi: " . $e->getMessage();
    }

} else {
    $response['success'] = 0;
    $response['message'] = "Thiếu tham số đầu vào!";
}

echo json_encode($response);
$conn->close();
?> 