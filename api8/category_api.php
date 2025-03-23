<?php
function connectDB() {
    return new mysqli('localhost', 'root', '', 'jewelry_hong_prm392');
}

function getAllCategories() {
    $conn = connectDB();
    $result = $conn->query('SELECT * FROM categories');
    return $result;
}

function addCategory($name, $description) {
    $conn = connectDB();
    $stmt = $conn->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $description);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

function updateCategory($id, $name, $description) {
    $conn = connectDB();
    $stmt = $conn->prepare("UPDATE categories SET name=?, description=? WHERE id=?");
    $stmt->bind_param("ssi", $name, $description, $id);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

function deleteCategory($id) {
    $conn = connectDB();
    // Kiểm tra xem category có sản phẩm không
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM product WHERE category_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    if ($row['count'] > 0) {
        return false; // Không thể xóa vì có sản phẩm liên quan
    }
    
    // Nếu không có sản phẩm, tiến hành xóa
    $stmt = $conn->prepare("DELETE FROM categories WHERE id=?");
    $stmt->bind_param("i", $id);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

// Xử lý các request
$action = $_GET['action'] ?? '';

if ($action == 'getCategories') {
    $categories = getAllCategories();
    $rows = array();
    while ($row = $categories->fetch_assoc()) {
        // Đếm số sản phẩm trong mỗi category
        $conn = connectDB();
        $stmt = $conn->prepare("SELECT COUNT(*) as product_count FROM product WHERE category_id = ?");
        $stmt->bind_param("i", $row['id']);
        $stmt->execute();
        $count_result = $stmt->get_result();
        $count = $count_result->fetch_assoc();
        $row['product_count'] = $count['product_count'];
        
        $rows[] = $row;
    }
    echo json_encode($rows);
    exit;
} elseif ($action == 'addCategory') {
    $result = addCategory($_POST['name'], $_POST['description']);
    echo json_encode(['success' => $result]);
    exit;
} elseif ($action == 'updateCategory') {
    $result = updateCategory($_POST['id'], $_POST['name'], $_POST['description']);
    echo json_encode(['success' => $result]);
    exit;
} elseif ($action == 'deleteCategory') {
    $result = deleteCategory($_GET['id']);
    echo json_encode(['success' => $result]);
    exit;
}
?> 