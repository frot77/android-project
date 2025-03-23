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
if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    try {
        // Lấy danh sách đơn hàng của user
        $stmt = $conn->prepare("
            SELECT o.*, p.payment_method, p.payment_status, p.transaction_id
            FROM orders o
            LEFT JOIN payment p ON o.id = p.order_id
            WHERE o.user_id = ?
            ORDER BY o.created_at DESC
        ");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $orders = array();
        while ($order = $result->fetch_assoc()) {
            // Lấy chi tiết sản phẩm cho mỗi đơn hàng
            $stmt2 = $conn->prepare("
                SELECT od.*, p.name as product_name, p.image_url
                FROM orderdetail od
                JOIN product p ON od.product_id = p.id
                WHERE od.order_id = ?
            ");
            $stmt2->bind_param("i", $order['id']);
            $stmt2->execute();
            $items_result = $stmt2->get_result();
            
            $items = array();
            while ($item = $items_result->fetch_assoc()) {
                $items[] = array(
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'image_url' => $item['image_url'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price']
                );
            }
            
            $orders[] = array(
                'order_id' => $order['id'],
                'recipient_name' => $order['recipient_name'],
                'recipient_phone' => $order['recipient_phone'],
                'recipient_address' => $order['recipient_address'],
                'status' => $order['status'],
                'total_price' => $order['total_price'],
                'created_at' => $order['created_at'],
                'payment_method' => $order['payment_method'],
                'payment_status' => $order['payment_status'],
                'transaction_id' => $order['transaction_id'],
                'items' => $items
            );
        }

        $response['success'] = 1;
        $response['message'] = "Lấy danh sách đơn hàng thành công!";
        $response['orders'] = $orders;

    } catch (Exception $e) {
        $response['success'] = 0;
        $response['message'] = "Lỗi: " . $e->getMessage();
    }

} else {
    $response['success'] = 0;
    $response['message'] = "Thiếu tham số user_id!";
}

echo json_encode($response);
$conn->close();
?> 