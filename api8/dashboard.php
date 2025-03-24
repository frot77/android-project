<!DOCTYPE html>
<?php
require_once 'check_auth.php';
?>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Thống kê</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .sidebar {
            min-height: 100vh;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
            background-color: #343a40;
        }
        .sidebar .nav-link {
            color: #fff;
            padding: 15px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar .nav-link:hover {
            background-color: rgba(255,255,255,0.1);
        }
        .sidebar .nav-link i {
            margin-right: 10px;
        }
        .content {
            padding: 20px;
        }
        .active-menu {
            background-color: rgba(255,255,255,0.2);
        }
        .stats-card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .stats-card:hover {
            transform: translateY(-5px);
        }
        .product-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
        }
        .chart-container {
            position: relative;
            height: 300px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 px-0 sidebar">
                <div class="py-4 text-center text-white">
                    <h4>ADMIN PANEL</h4>
                </div>
                <nav class="nav flex-column">
                    <a class="nav-link active-menu" href="dashboard.php">
                        <i class="fas fa-chart-line"></i> Thống kê
                    </a>
                    <a class="nav-link" href="index.php">
                        <i class="fas fa-box"></i> Quản lý sản phẩm
                    </a>
                    <a class="nav-link" href="categories.php">
                        <i class="fas fa-tags"></i> Quản lý danh mục
                    </a>
                    <a class="nav-link" href="users.php">
                        <i class="fas fa-users"></i> Quản lý người dùng
                    </a>
                    <a class="nav-link" href="orders.php">
                        <i class="fas fa-shopping-cart"></i> Quản lý đơn hàng
                    </a>
                    <a class="nav-link" href="reviews.php">
                        <i class="fas fa-star"></i> Quản lý đánh giá
                    </a>
                    <a class="nav-link" href="payments.php">
                        <i class="fas fa-money-bill"></i> Quản lý thanh toán
                    </a>
                    <a class="nav-link" href="#" id="logoutBtn">
                        <i class="fas fa-sign-out-alt"></i> Đăng xuất
                    </a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 content">
                <h2 class="mb-4">Thống kê tổng quan</h2>

                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card stats-card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="card-title">Tổng đơn hàng</h5>
                                <h3 class="card-text" id="totalOrders">0</h3>
                                <div class="mt-3">
                                    <small>Hoàn thành: <span id="completedOrders">0</span></small><br>
                                    <small>Đang xử lý: <span id="processingOrders">0</span></small><br>
                                    <small>Chờ xử lý: <span id="pendingOrders">0</span></small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stats-card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title">Doanh thu</h5>
                                <h3 class="card-text" id="totalRevenue">0đ</h3>
                                <small class="mt-2">Tính từ đơn hàng hoàn thành</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stats-card bg-info text-white">
                            <div class="card-body">
                                <h5 class="card-title">Sản phẩm & Danh mục</h5>
                                <h3 class="card-text" id="totalProducts">0</h3>
                                <small class="mt-2">Số danh mục: <span id="totalCategories">0</span></small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stats-card bg-warning text-white">
                            <div class="card-body">
                                <h5 class="card-title">Đánh giá & Người dùng</h5>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h3 class="card-text" id="totalReviews">0</h3>
                                        <small>Đánh giá trung bình: <span id="avgRating">0</span>⭐</small>
                                    </div>
                                    <div class="text-right">
                                        <h3 class="card-text" id="totalUsers">0</h3>
                                        <small>Người dùng</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Doanh thu theo tháng</h5>
                                <div class="chart-container">
                                    <canvas id="revenueChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Top 5 sản phẩm bán chạy</h5>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Sản phẩm</th>
                                                <th>Số đơn</th>
                                            </tr>
                                        </thead>
                                        <tbody id="topProducts">
                                            <!-- Top products will be loaded here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

    <script>
        // Biểu đồ doanh thu
        let revenueChart;

        function loadStatistics() {
            $.get('dashboard_api.php?action=getStatistics', function(response) {
                if (response.success) {
                    const stats = response.data;
                    
                    // Cập nhật thông tin đơn hàng
                    $('#totalOrders').text(stats.orders.total_orders);
                    $('#completedOrders').text(stats.orders.completed_orders);
                    $('#processingOrders').text(stats.orders.processing_orders);
                    $('#pendingOrders').text(stats.orders.pending_orders);
                    
                    // Cập nhật doanh thu
                    $('#totalRevenue').text(Number(stats.orders.total_revenue).toLocaleString('vi-VN') + 'đ');
                    
                    // Cập nhật sản phẩm và danh mục
                    $('#totalProducts').text(stats.products.total_products);
                    $('#totalCategories').text(stats.products.total_categories);
                    
                    // Cập nhật đánh giá và người dùng
                    $('#totalReviews').text(stats.reviews.total_reviews);
                    $('#avgRating').text(Number(stats.reviews.avg_rating).toFixed(1));
                    $('#totalUsers').text(stats.users.total_users);
                    
                    // Cập nhật top sản phẩm
                    let topProductsHtml = '';
                    stats.top_products.forEach(product => {
                        topProductsHtml += `
                            <tr>
                                <td>
                                    <img src="${product.image_url || ''}" alt="${product.name}" class="product-image mr-2">
                                    ${product.name}
                                </td>
                                <td>${product.order_count}</td>
                            </tr>
                        `;
                    });
                    $('#topProducts').html(topProductsHtml);
                    
                    // Cập nhật biểu đồ doanh thu
                    const monthLabels = ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12'];
                    const revenueData = new Array(12).fill(0);
                    
                    stats.monthly_revenue.forEach(item => {
                        revenueData[item.month - 1] = item.revenue;
                    });
                    
                    if (revenueChart) {
                        revenueChart.destroy();
                    }
                    
                    const ctx = document.getElementById('revenueChart').getContext('2d');
                    revenueChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: monthLabels,
                            datasets: [{
                                label: 'Doanh thu (VNĐ)',
                                data: revenueData,
                                borderColor: 'rgb(75, 192, 192)',
                                tension: 0.1,
                                fill: false
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value) {
                                            return value.toLocaleString('vi-VN') + 'đ';
                                        }
                                    }
                                }
                            },
                            plugins: {
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return context.parsed.y.toLocaleString('vi-VN') + 'đ';
                                        }
                                    }
                                }
                            }
                        }
                    });
                } else {
                    alert('Lỗi: ' + response.error);
                    console.error('Error:', response.error);
                }
            }).fail(function(jqXHR, textStatus, errorThrown) {
                alert('Lỗi kết nối đến server');
                console.error('Error:', textStatus, errorThrown);
            });
        }

        // Load dữ liệu khi trang được tải
        $(document).ready(function() {
            loadStatistics();
        });

        // Thêm xử lý đăng xuất
        $('#logoutBtn').click(function(e) {
                e.preventDefault();
                if (confirm('Bạn có chắc muốn đăng xuất?')) {
                    $.post('auth.php', { action: 'logout' }, function(response) {
                        if (response.success) {
                            window.location.href = 'login.php';
                        }
                    });
                }
            });
    </script>
</body>
</html> 