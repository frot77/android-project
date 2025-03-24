<!DOCTYPE html>
<?php
require_once 'check_auth.php';
?>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Đánh giá</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
        .product-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
        }
        .star-rating {
            color: #ffc107;
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
                    <a class="nav-link" href="dashboard.php">
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
                    <a class="nav-link active-menu" href="reviews.php">
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
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Quản lý Đánh giá</h2>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="mb-3">
                            <input type="text" id="searchInput" class="form-control" placeholder="Tìm kiếm đánh giá...">
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Sản phẩm</th>
                                        <th>Người dùng</th>
                                        <th>Đánh giá</th>
                                        <th>Nội dung</th>
                                        <th>Ngày tạo</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody id="reviewTable">
                                    <!-- Dữ liệu AJAX cập nhật vào đây -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Chi tiết đánh giá -->
    <div class="modal fade" id="reviewDetailModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Chi tiết đánh giá #<span id="reviewDetailId"></span></h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6>Thông tin sản phẩm</h6>
                            <img id="reviewProductImage" src="" alt="" class="product-image mb-2">
                            <p>Tên: <span id="reviewProductName"></span></p>
                        </div>
                        <div class="col-md-6">
                            <h6>Thông tin người dùng</h6>
                            <p>Tên: <span id="reviewUserName"></span></p>
                            <p>Username: <span id="reviewUsername"></span></p>
                        </div>
                    </div>
                    <div class="mb-3">
                        <h6>Đánh giá</h6>
                        <div id="reviewRating" class="star-rating"></div>
                    </div>
                    <div class="mb-3">
                        <h6>Nội dung</h6>
                        <p id="reviewContent"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger mr-2" id="deleteReviewBtn">Xóa</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

    <script>
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

        function loadReviews() {
            $.get('review_api.php?action=getReviews', function(response) {
                if (response.success) {
                    const reviews = response.data;
                    let html = '';
                    reviews.forEach(function(review) {
                        // Tạo HTML cho star rating
                        let stars = '';
                        for (let i = 1; i <= 5; i++) {
                            stars += `<i class="fas fa-star${i <= review.rating ? '' : '-o'}" style="color: ${i <= review.rating ? '#ffc107' : '#ccc'}"></i>`;
                        }
                        
                        html += `
                            <tr>
                                <td>#${review.id}</td>
                                <td>
                                    <img src="${review.image_url || ''}" alt="${review.product_name}" class="product-image d-block mb-1">
                                    ${review.product_name || 'N/A'}
                                </td>
                                <td>${review.full_name || review.username || 'N/A'}</td>
                                <td>${stars}</td>
                                <td>${review.comment|| 'N/A'}</td>
                                <td>${new Date(review.created_at).toLocaleString('vi-VN')}</td>
                                <td>
                                    <button class='btn btn-info btn-sm view-btn' data-review='${JSON.stringify(review)}'>
                                        <i class="fas fa-eye"></i> Chi tiết
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                    $('#reviewTable').html(html);

                    // Gắn sự kiện cho nút xem chi tiết
                    $('.view-btn').click(function() {
                        const review = $(this).data('review');
                        showReviewDetail(review);
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

        function showReviewDetail(review) {
            $('#reviewDetailId').text(review.id);
            $('#reviewProductImage').attr('src', review.image_url || '');
            $('#reviewProductName').text(review.product_name || 'N/A');
            $('#reviewUserName').text(review.full_name || 'N/A');
            $('#reviewUsername').text(review.username || 'N/A');
            
            // Hiển thị star rating
            let stars = '';
            for (let i = 1; i <= 5; i++) {
                stars += `<i class="fas fa-star${i <= review.rating ? '' : '-o'}" style="color: ${i <= review.rating ? '#ffc107' : '#ccc'}"></i>`;
            }
            $('#reviewRating').html(stars);
            
            $('#reviewContent').text(review.comment || 'N/A');
            
            // Lưu ID đánh giá vào nút xóa
            $('#deleteReviewBtn').data('id', review.id);
            
            $('#reviewDetailModal').modal('show');
        }

        // Xử lý xóa đánh giá
        $('#deleteReviewBtn').click(function() {
            if (confirm('Bạn có chắc chắn muốn xóa đánh giá này?')) {
                const reviewId = $(this).data('id');
                
                $.ajax({
                    url: 'review_api.php',
                    type: 'POST',
                    data: {
                        action: 'deleteReview',
                        id: reviewId
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('Xóa đánh giá thành công');
                            $('#reviewDetailModal').modal('hide');
                            loadReviews();
                        } else {
                            alert('Lỗi: ' + response.error);
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('Lỗi kết nối: ' + error);
                    }
                });
            }
        });

        // Xử lý tìm kiếm
        $('#searchInput').on('keyup', function() {
            const searchText = $(this).val().toLowerCase();
            $('#reviewTable tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(searchText) > -1);
            });
        });

        // Load dữ liệu khi trang được tải
        $(document).ready(function() {
            loadReviews();
        });
    </script>
</body>
</html> 