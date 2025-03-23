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
        // Debug: Kiểm tra giá trị tham số
        $response['debug'] = array(
            'user_id' => $user_id,
            'product_id' => $product_id
        );

        // Kiểm tra xem user đã mua sản phẩm này chưa và đơn hàng đã hoàn thành chưa
        $sql = "
            SELECT o.id as order_id, o.created_at as purchase_date, 
                   od.quantity, od.price as purchase_price,
                   o.status as order_status
            FROM orders o
            JOIN orderdetail od ON o.id = od.order_id
            WHERE o.user_id = ? AND od.product_id = ? AND o.status = 'Completed'
            ORDER BY o.created_at DESC
        ";
        
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }

        $stmt->bind_param("ii", $user_id, $product_id);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $purchase = $result->fetch_assoc();
            $response['success'] = 1;
            $response['message'] = "Người dùng đã mua sản phẩm này!";
            $response['purchase'] = array(
                'order_id' => $purchase['order_id'],
                'purchase_date' => $purchase['purchase_date'],
                'quantity' => $purchase['quantity'],
                'purchase_price' => $purchase['purchase_price'],
                'order_status' => $purchase['order_status']
            );
        } else {
            // Kiểm tra xem có đơn hàng đang chờ xử lý không
            $sql_pending = "
                SELECT o.id as order_id, o.status as order_status
                FROM orders o
                JOIN orderdetail od ON o.id = od.order_id
                WHERE o.user_id = ? AND od.product_id = ? AND o.status = 'Pending'
            ";
            
            $stmt = $conn->prepare($sql_pending);
            if (!$stmt) {
                throw new Exception("Prepare statement failed: " . $conn->error);
            }

            $stmt->bind_param("ii", $user_id, $product_id);
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }

            $pending_result = $stmt->get_result();
            
            if ($pending_result->num_rows > 0) {
                $response['success'] = 0;
                $response['message'] = "Bạn có đơn hàng đang chờ xử lý cho sản phẩm này!";
            } else {
                $response['success'] = 0;
                $response['message'] = "Bạn chưa mua sản phẩm này!";
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