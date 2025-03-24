<!DOCTYPE html>
<?php
require_once 'check_auth.php';
?>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Danh mục</title>
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
                    <a class="nav-link active-menu" href="categories.php">
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
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Quản lý Danh mục</h2>
                    <button class="btn btn-success" data-toggle="modal" data-target="#addCategoryModal">
                        <i class="fas fa-plus"></i> Thêm danh mục
                    </button>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="mb-3">
                            <input type="text" id="searchInput" class="form-control" placeholder="Tìm kiếm danh mục...">
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Tên danh mục</th>
                                        <th>Mô tả</th>
                                        <th>Số sản phẩm</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody id="categoryTable">
                                    <!-- Dữ liệu AJAX cập nhật vào đây -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Thêm Danh mục -->
    <div class="modal fade" id="addCategoryModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm danh mục</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="addCategoryForm">
                        <input type="text" name="name" placeholder="Tên danh mục" class="form-control mb-2" required>
                        <textarea name="description" placeholder="Mô tả" class="form-control mb-2" required></textarea>
                        <button type="submit" class="btn btn-success">Thêm</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Cập Nhật Danh mục -->
    <div class="modal fade" id="editCategoryModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Chỉnh sửa danh mục</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="editCategoryForm">
                        <input type="hidden" name="id">
                        <input type="text" name="name" class="form-control mb-2" required>
                        <textarea name="description" class="form-control mb-2" required></textarea>
                        <button type="submit" class="btn btn-primary">Lưu</button>
                    </form>
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
        
        function loadCategories() {
            $.get('category_api.php?action=getCategories', function(data) {
                const categories = JSON.parse(data);
                let html = '';
                categories.forEach(function(category) {
                    html += `
                        <tr>
                            <td>${category.id}</td>
                            <td>${category.name}</td>
                            <td>${category.description}</td>
                            <td>${category.product_count}</td>
                            <td>
                                <button class='btn btn-primary btn-sm edit-btn' data-category='${JSON.stringify(category)}'>
                                    <i class="fas fa-edit"></i> Sửa
                                </button>
                                <button class='btn btn-danger btn-sm delete-btn' data-id='${category.id}'>
                                    <i class="fas fa-trash"></i> Xóa
                                </button>
                            </td>
                        </tr>
                    `;
                });
                $('#categoryTable').html(html);
            });
        }

        $(document).ready(function() {
            loadCategories();

            $('#searchInput').on('keyup', function() {
                let search = $(this).val().toLowerCase();
                $('#categoryTable tr').filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(search) > -1);
                });
            });

            $('#addCategoryForm').submit(function(e) {
                e.preventDefault();
                $.post('category_api.php?action=addCategory', $(this).serialize(), function(response) {
                    const result = JSON.parse(response);
                    if (result.success) {
                        loadCategories();
                        $('#addCategoryModal').modal('hide');
                        $('#addCategoryForm')[0].reset();
                    } else {
                        alert('Không thể thêm danh mục!');
                    }
                });
            });

            $(document).on('click', '.edit-btn', function() {
                const category = $(this).data('category');
                const form = $('#editCategoryForm');
                form.find('[name="id"]').val(category.id);
                form.find('[name="name"]').val(category.name);
                form.find('[name="description"]').val(category.description);
                $('#editCategoryModal').modal('show');
            });

            $('#editCategoryForm').submit(function(e) {
                e.preventDefault();
                $.post('category_api.php?action=updateCategory', $(this).serialize(), function(response) {
                    const result = JSON.parse(response);
                    if (result.success) {
                        loadCategories();
                        $('#editCategoryModal').modal('hide');
                    } else {
                        alert('Không thể cập nhật danh mục!');
                    }
                });
            });

            $(document).on('click', '.delete-btn', function() {
                const id = $(this).data('id');
                if (confirm('Bạn có chắc muốn xóa danh mục này?')) {
                    $.get('category_api.php?action=deleteCategory&id=' + id, function(response) {
                        const result = JSON.parse(response);
                        if (result.success) {
                            loadCategories();
                        } else {
                            alert('Không thể xóa danh mục này vì có sản phẩm đang sử dụng!');
                        }
                    });
                }
            });
        });
    </script>
</body>
</html> 