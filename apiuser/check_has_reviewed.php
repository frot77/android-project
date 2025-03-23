<?php
header('Content-Type: application/json; charset=utf-8');
$response = array();
$host = "localhost";
$u = "root";
$p = "";
$db = "jewelry_hong_prm392";

$conn = new mysqli($host, $u, $p, $db);

if ($conn->connect_error) {
    $response['success'] = 0;
    $response['message'] = "Kết nối thất bại: " . $conn->connect_error;
    echo json_encode($response);
    exit;
}

// Kiểm tra tham số đầu vào (chấp nhận cả chữ thường và chữ hoa)
$user_id = null;
$product_id = null;

if (isset($_GET['user_id'])) {
    $user_id = intval($_GET['user_id']);
} else if (isset($_GET['user_Id'])) {
    $user_id = intval($_GET['user_Id']);
}

if (isset($_GET['product_id'])) {
    $product_id = intval($_GET['product_id']);
} else if (isset($_GET['product_Id'])) {
    $product_id = intval($_GET['product_Id']);
}

if ($user_id !== null && $product_id !== null) {
    try {
        // Đầu tiên kiểm tra xem người dùng đã mua và hoàn thành đơn hàng chưa
        $sql_purchase = "
            SELECT COUNT(*) as has_purchased
            FROM orders o
            JOIN orderdetail od ON o.id = od.order_id
            WHERE o.user_id = ? AND od.product_id = ? AND o.status = 'Completed'
        ";
        
        $stmt = $conn->prepare($sql_purchase);
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }

        $stmt->bind_param("ii", $user_id, $product_id);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        $purchase_result = $stmt->get_result();
        $purchase_check = $purchase_result->fetch_assoc();

        if ($purchase_check['has_purchased'] > 0) {
            // Nếu đã mua, kiểm tra xem đã review chưa
            $sql_review = "
                SELECT r.*, u.username, u.full_name
                FROM reviews r
                JOIN user u ON r.user_id = u.id
                WHERE r.user_id = ? AND r.product_id = ?
            ";
            
            $stmt = $conn->prepare($sql_review);
            if (!$stmt) {
                throw new Exception("Prepare statement failed: " . $conn->error);
            }

            $stmt->bind_param("ii", $user_id, $product_id);
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }

            $review_result = $stmt->get_result();
            
            if ($review_result->num_rows > 0) {
                $review = $review_result->fetch_assoc();
                $response['success'] = 1;
                $response['message'] = "Người dùng đã đánh giá sản phẩm này!";
                $response['can_review'] = true;
                $response['has_reviewed'] = true;
                $response['review'] = array(
                    'review_id' => $review['id'],
                    'rating' => $review['rating'],
                    'comment' => $review['comment'],
                    'created_at' => $review['created_at'],
                    'username' => $review['username'],
                    'full_name' => $review['full_name']
                );
            } else {
                $response['success'] = 1;
                $response['message'] = "Người dùng chưa đánh giá sản phẩm này!";
                $response['can_review'] = true;
                $response['has_reviewed'] = false;
            }
        } else {
            // Kiểm tra xem có đơn hàng đang chờ không
            $sql_pending = "
                SELECT COUNT(*) as has_pending
                FROM orders o
                JOIN orderdetail od ON o.id = od.order_id
                WHERE o.user_id = ? AND od.product_id = ? AND o.status = 'Pending'
            ";
            
            $stmt = $conn->prepare($sql_pending);
            $stmt->bind_param("ii", $user_id, $product_id);
            $stmt->execute();
            $pending_result = $stmt->get_result();
            $pending_check = $pending_result->fetch_assoc();

            if ($pending_check['has_pending'] > 0) {
                $response['success'] = 1;
                $response['message'] = "Đơn hàng đang chờ xử lý, chưa thể đánh giá!";
                $response['can_review'] = false;
                $response['has_reviewed'] = false;
            } else {
                $response['success'] = 1;
                $response['message'] = "Bạn cần mua sản phẩm trước khi đánh giá!";
                $response['can_review'] = false;
                $response['has_reviewed'] = false;
            }
        }

    } catch (Exception $e) {
        $response['success'] = 0;
        $response['message'] = "Lỗi: " . $e->getMessage();
        $response['debug']['error'] = $e->getTrace();
    }

} else {
    $response['success'] = 0;
    $response['message'] = "Thiếu tham số user_id hoặc product_id!";
    $response['debug']['get_params'] = $_GET;
}

echo json_encode($response);
$conn->close();
?> 