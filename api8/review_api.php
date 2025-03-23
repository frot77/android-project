<?php
function connectDB() {
    return new mysqli('localhost', 'root', '', 'jewelry_hong_prm392');
}

function getAllReviews() {
    $conn = connectDB();
    $sql = "SELECT r.*, u.username, u.full_name, p.name as product_name, p.image_url 
            FROM review r 
            LEFT JOIN user u ON r.user_id = u.id 
            LEFT JOIN product p ON r.product_id = p.id 
            ORDER BY r.created_at DESC";
    $result = $conn->query($sql);
    return $result;
}

function deleteReview($id) {
    $conn = connectDB();
    $stmt = $conn->prepare("DELETE FROM review WHERE id = ?");
    $stmt->bind_param("i", $id);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

function updateReviewStatus($id, $status) {
    $conn = connectDB();
    
    // Validate status
    $validStatuses = ['Pending', 'Approved', 'Rejected'];
    if (!in_array($status, $validStatuses)) {
        return false;
    }
    
    $stmt = $conn->prepare("UPDATE review SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

// Xử lý các request
$action = $_GET['action'] ?? '';

if ($action == 'getReviews') {
    $reviews = getAllReviews();
    $rows = array();
    while ($row = $reviews->fetch_assoc()) {
        $rows[] = $row;
    }
    echo json_encode($rows);
    exit;
} elseif ($action == 'deleteReview') {
    $id = $_GET['id'] ?? 0;
    $result = deleteReview($id);
    echo json_encode(['success' => $result]);
    exit;
} elseif ($action == 'updateStatus') {
    $id = $_POST['id'] ?? 0;
    $status = $_POST['status'] ?? '';
    $result = updateReviewStatus($id, $status);
    echo json_encode(['success' => $result]);
    exit;
}
?> 