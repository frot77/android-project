<?php
session_start();

// Kiểm tra xem người dùng đã đăng nhập và có quyền admin không
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}
?> 