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

// Lấy category_id từ tham số
$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : null;

try {
    if ($category_id !== null) {
        // Lấy thông tin danh mục
        $stmt = $conn->prepare("SELECT name, description FROM categories WHERE id = ?");
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }

        $stmt->bind_param("i", $category_id);
        $stmt->execute();
        $category_result = $stmt->get_result();
        
        if ($category_result->num_rows > 0) {
            $category = $category_result->fetch_assoc();
            
            // Lấy tất cả sản phẩm của danh mục
            $sql = "
                SELECT p.*, 
                       c.name as category_name,
                       
                FROM product p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.category_id = ?
                ORDER BY p.name ASC
            ";
            
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Prepare statement failed: " . $conn->error);
            }

            $stmt->bind_param("i", $category_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $products = array();
            while ($row = $result->fetch_assoc()) {
                $products[] = array(
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'description' => $row['description'],
                    'price' => $row['price'],
                    'stock' => $row['stock'],
                    'image_url' => $row['image_url'],
                    'category_name' => $row['category_name']
                    
                );
            }
            
            $response['success'] = 1;
            $response['message'] = "Lấy danh sách sản phẩm thành công!";
            $response['category'] = array(
                'id' => $category_id,
                'name' => $category['name'],
                'description' => $category['description']
            );
            $response['products'] = $products;
            
        } else {
            $response['success'] = 0;
            $response['message'] = "Không tìm thấy danh mục!";
        }
    } else {
        $response['success'] = 0;
        $response['message'] = "Thiếu tham số category_id!";
    }
    
} catch (Exception $e) {
    $response['success'] = 0;
    $response['message'] = "Lỗi: " . $e->getMessage();
}

echo json_encode($response);
$conn->close();
?> 