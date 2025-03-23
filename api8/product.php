<?php
function connectDB(){
    return new mysqli('localhost', 'root', '', 'jewelry_hong_prm392');
}

function addProduct($name, $description, $price, $stock, $image_url, $category_id){
    $conn = connectDB();
    $stmt = $conn->prepare("INSERT INTO product (name, description, price, stock, image_url, category_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdiss", $name, $description, $price, $stock, $image_url, $category_id);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

function displayProducts(){
    $conn = connectDB();
    $result = $conn->query('SELECT p.*, c.name as category_name FROM product p LEFT JOIN categories c ON p.category_id = c.id');
    return $result;
}

function updateProduct($id, $name, $description, $price, $stock, $image_url, $category_id){
    $conn = connectDB();
    $stmt = $conn->prepare("UPDATE product SET name=?, description=?, price=?, stock=?, image_url=?, category_id=? WHERE id=?");
    $stmt->bind_param("ssdissi", $name, $description, $price, $stock, $image_url, $category_id, $id);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

function deleteProduct($id){
    $conn = connectDB();
    $stmt = $conn->prepare("DELETE FROM product WHERE id=?");
    $stmt->bind_param("i", $id);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

function getCategories() {
    $conn = connectDB();
    $result = $conn->query('SELECT * FROM categories');
    return $result;
}
?>
