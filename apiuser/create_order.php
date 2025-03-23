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
if (isset($_POST['user_id']) && isset($_POST['recipient_name']) && 
    isset($_POST['recipient_phone']) && isset($_POST['recipient_address']) && 
    isset($_POST['payment_method']) && isset($_POST['items'])) {
    
    $user_id = $_POST['user_id'];
    $recipient_name = $_POST['recipient_name'];
    $recipient_phone = $_POST['recipient_phone'];
    $recipient_address = $_POST['recipient_address'];
    $payment_method = $_POST['payment_method'];
    $items = json_decode($_POST['items'], true);

    // Bắt đầu transaction
    $conn->begin_transaction();

    try {
        // Tính tổng giá trị đơn hàng
        $total_price = 0;
        foreach ($items as $item) {
            $product_id = $item['product_id'];
            $quantity = $item['quantity'];
            
            // Lấy giá sản phẩm và kiểm tra tồn kho
            $stmt = $conn->prepare("SELECT price, stock FROM product WHERE id = ?");
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $product = $result->fetch_assoc();

            if (!$product) {
                throw new Exception("Sản phẩm không tồn tại");
            }

            if ($product['stock'] < $quantity) {
                throw new Exception("Sản phẩm không đủ số lượng trong kho");
            }

            $total_price += $product['price'] * $quantity;
        }

        // Tạo order mới
        $status = 'Pending';
        $stmt = $conn->prepare("INSERT INTO orders (user_id, recipient_name, recipient_phone, recipient_address, status, total_price) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssd", $user_id, $recipient_name, $recipient_phone, $recipient_address, $status, $total_price);
        $stmt->execute();
        $order_id = $conn->insert_id;

        // Thêm chi tiết đơn hàng và cập nhật số lượng sản phẩm
        foreach ($items as $item) {
            $product_id = $item['product_id'];
            $quantity = $item['quantity'];
            
            // Lấy giá sản phẩm
            $stmt = $conn->prepare("SELECT price FROM product WHERE id = ?");
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $product = $result->fetch_assoc();
            
            // Thêm chi tiết đơn hàng
            $stmt = $conn->prepare("INSERT INTO orderdetail (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiid", $order_id, $product_id, $quantity, $product['price']);
            $stmt->execute();

            // Cập nhật số lượng sản phẩm
            $stmt = $conn->prepare("UPDATE product SET stock = stock - ? WHERE id = ?");
            $stmt->bind_param("ii", $quantity, $product_id);
            $stmt->execute();
        }

        // Tạo payment record
        $payment_status = 'Pending';
        $transaction_id = uniqid('TRX_');
        $stmt = $conn->prepare("INSERT INTO payment (order_id, payment_method, payment_status, transaction_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $order_id, $payment_method, $payment_status, $transaction_id);
        $stmt->execute();

        // Commit transaction
        $conn->commit();

        $response['success'] = 1;
        $response['message'] = "Đặt hàng thành công!";
        $response['order_id'] = $order_id;
        $response['transaction_id'] = $transaction_id;

    } catch (Exception $e) {
        // Rollback transaction nếu có lỗi
        $conn->rollback();
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