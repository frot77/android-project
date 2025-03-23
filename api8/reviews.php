<!DOCTYPE html>
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
        .star-rating {
            color: #ffc107;
        }
        .product-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 4px;
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
                                        <th>Trạng thái</th>
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
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Chi tiết đánh giá #<span id="reviewDetailId"></span></h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6>Thông tin sản phẩm</h6>
                            <div class="d-flex align-items-center mb-2">
                                <img id="reviewProductImage" src="" alt="" 
                                     style="width: 100px; height: 100px; object-fit: cover; border-radius: 4px; margin-right: 10px;">
                                <div>
                                    <p class="mb-0" id="reviewProductName"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6>Thông tin người dùng</h6>
                            <p>Tên: <span id="reviewCustomerName"></span></p>
                            <p>Username: <span id="reviewUsername"></span></p>
                        </div>
                    </div>
                    <div class="mb-3">
                        <h6>Đánh giá</h6>
                        <div class="star-rating mb-2" id="reviewRating"></div>
                        <p class="mt-2" id="reviewContent"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <select id="reviewStatus" class="form-control mr-2" style="width: auto;">
                        <option value="Pending">Chờ duyệt</option>
                        <option value="Approved">Đã duyệt</option>
                        <option value="Rejected">Từ chối</option>
                    </select>
                    <button type="button" class="btn btn-primary" id="updateStatusBtn">Cập nhật trạng thái</button>
                    <button type="button" class="btn btn-danger" id="deleteReviewBtn">Xóa đánh giá</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

    <script>
        function loadReviews() {
            $.get('review_api.php?action=getReviews', function(data) {
                const reviews = JSON.parse(data);
                let html = '';
                reviews.forEach(function(review) {
                    const statusClass = {
                        'Pending': 'warning',
                        'Approved': 'success',
                        'Rejected': 'danger'
                    }[review.status] || 'secondary';

                    const statusText = {
                        'Pending': 'Chờ duyệt',
                        'Approved': 'Đã duyệt',
                        'Rejected': 'Từ chối'
                    }[review.status] || review.status;

                    const stars = '★'.repeat(review.rating) + '☆'.repeat(5 - review.rating);
                    
                    html += `
                        <tr>
                            <td>#${review.id}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="${review.image_url || ''}" alt="${review.product_name || 'N/A'}" 
                                         class="product-image mr-2">
                                    <span>${review.product_name || 'N/A'}</span>
                                </div>
                            </td>
                            <td>${review.full_name || review.username || 'N/A'}</td>
                            <td>
                                <span class="star-rating">${stars}</span>
                            </td>
                            <td>${review.content || 'N/A'}</td>
                            <td><span class="badge badge-${statusClass}">${statusText}</span></td>
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
            });
        }

        $(document).ready(function() {
            loadReviews();

            $('#searchInput').on('keyup', function() {
                let search = $(this).val().toLowerCase();
                $('#reviewTable tr').filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(search) > -1);
                });
            });

            $(document).on('click', '.view-btn', function() {
                const review = $(this).data('review');
                
                $('#reviewDetailId').text(review.id);
                $('#reviewProductImage').attr('src', review.image_url || '');
                $('#reviewProductName').text(review.product_name || 'N/A');
                $('#reviewCustomerName').text(review.full_name || 'N/A');
                $('#reviewUsername').text(review.username || 'N/A');
                $('#reviewRating').html('★'.repeat(review.rating) + '☆'.repeat(5 - review.rating));
                $('#reviewContent').text(review.content || 'N/A');
                $('#reviewStatus').val(review.status || 'Pending');
                
                $('#reviewDetailModal').modal('show');
            });

            $('#updateStatusBtn').click(function() {
                const reviewId = $('#reviewDetailId').text();
                const newStatus = $('#reviewStatus').val();
                
                $.post('review_api.php?action=updateStatus', {
                    id: reviewId,
                    status: newStatus
                }, function(response) {
                    const result = JSON.parse(response);
                    if (result.success) {
                        alert('Cập nhật trạng thái thành công!');
                        $('#reviewDetailModal').modal('hide');
                        loadReviews();
                    } else {
                        alert('Không thể cập nhật trạng thái!');
                    }
                });
            });

            $('#deleteReviewBtn').click(function() {
                const reviewId = $('#reviewDetailId').text();
                
                if (confirm('Bạn có chắc muốn xóa đánh giá này?')) {
                    $.get('review_api.php?action=deleteReview&id=' + reviewId, function(response) {
                        const result = JSON.parse(response);
                        if (result.success) {
                            alert('Xóa đánh giá thành công!');
                            $('#reviewDetailModal').modal('hide');
                            loadReviews();
                        } else {
                            alert('Không thể xóa đánh giá!');
                        }
                    });
                }
            });
        });
    </script>
</body>
</html> 