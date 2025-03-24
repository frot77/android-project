<!DOCTYPE html>
<?php
require_once 'check_auth.php';
include 'product.php';

$action = $_GET['action'] ?? '';
$categories = getCategories(); // Lấy danh sách categories

if ($action == 'getProducts') {
    $products = displayProducts();
    while ($row = $products->fetch_assoc()) {
        echo "<tr>
            <td>{$row['id']}</td>
            <td>{$row['name']}</td>
            <td>{$row['description']}</td>
            <td>{$row['price']}</td>
            <td>{$row['stock']}</td>
            <td><img src='{$row['image_url']}' width='50'></td>
            <td>{$row['category_name']}</td>
            <td>
                <button class='btn btn-primary btn-sm edit-btn' data-product='" . json_encode($row) . "'>Sửa</button>
                <button class='btn btn-danger btn-sm delete-btn' data-id='{$row['id']}'>Xóa</button>
            </td>
        </tr>";
    }
    exit;
} elseif ($action == 'addProduct') {
    addProduct($_POST['name'], $_POST['description'], $_POST['price'], $_POST['stock'], $_POST['image_url'], $_POST['category_id']);
    exit;
} elseif ($action == 'updateProduct') {
    updateProduct($_POST['id'], $_POST['name'], $_POST['description'], $_POST['price'], $_POST['stock'], $_POST['image_url'], $_POST['category_id']);
    exit;
} elseif ($action == 'deleteProduct') {
    deleteProduct($_GET['id']);
    exit;
}
?>

<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ thống quản lý</title>
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
                    <a class="nav-link active-menu" href="index.php">
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
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Quản lý Sản phẩm</h2>
                    <button class="btn btn-success" data-toggle="modal" data-target="#addProductModal">
                        <i class="fas fa-plus"></i> Thêm sản phẩm
                    </button>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="mb-3">
                            <input type="text" id="searchInput" class="form-control" placeholder="Tìm kiếm sản phẩm...">
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Tên</th>
                                        <th>Mô tả</th>
                                        <th>Giá</th>
                                        <th>Số lượng</th>
                                        <th>Ảnh</th>
                                        <th>Danh mục</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody id="productTable">
                                    <!-- Dữ liệu AJAX cập nhật vào đây -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal Thêm Sản Phẩm -->
    <div class="modal fade" id="addProductModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm sản phẩm</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="addProductForm">
                        <input type="text" name="name" placeholder="Tên sản phẩm" class="form-control mb-2" required>
                        <input type="text" name="description" placeholder="Mô tả" class="form-control mb-2" required>
                        <input type="number" name="price" placeholder="Giá" class="form-control mb-2" required>
                        <input type="number" name="stock" placeholder="Số lượng" class="form-control mb-2" required>
                        <input type="text" name="image_url" placeholder="Ảnh URL" class="form-control mb-2" required>
                        <select name="category_id" class="form-control mb-2" required>
                            <option value="">Chọn danh mục</option>
                            <?php 
                            $categories = getCategories();
                            while($cat = $categories->fetch_assoc()) {
                                echo "<option value='{$cat['id']}'>{$cat['name']}</option>";
                            }
                            ?>
                        </select>
                        <button type="submit" class="btn btn-success">Thêm</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Cập Nhật Sản Phẩm -->
    <div class="modal fade" id="editProductModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Chỉnh sửa sản phẩm</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="editProductForm">
                        <input type="hidden" name="id">
                        <input type="text" name="name" class="form-control mb-2" required>
                        <input type="text" name="description" class="form-control mb-2" required>
                        <input type="number" name="price" class="form-control mb-2" required>
                        <input type="number" name="stock" class="form-control mb-2" required>
                        <input type="text" name="image_url" class="form-control mb-2" required>
                        <select name="category_id" class="form-control mb-2" required>
                            <option value="">Chọn danh mục</option>
                            <?php 
                            $categories = getCategories();
                            while($cat = $categories->fetch_assoc()) {
                                echo "<option value='{$cat['id']}'>{$cat['name']}</option>";
                            }
                            ?>
                        </select>
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
        function loadProducts() {
            $.get('?action=getProducts', function(data) {
                $('#productTable').html(data);
            });
        }

        $(document).ready(function() {
            loadProducts();

            $('#searchInput').on('keyup', function() {
                let search = $(this).val().toLowerCase();
                $('#productTable tr').filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(search) > -1);
                });
            });

            $('#addProductForm').submit(function(e) {
                e.preventDefault();
                $.post('?action=addProduct', $(this).serialize(), function() {
                    loadProducts();
                    $('#addProductModal').modal('hide');
                    $('#addProductForm')[0].reset();
                });
            });

            $(document).on('click', '.edit-btn', function() {
                let product = $(this).data('product');
                let form = $('#editProductForm');
                form.find('[name="id"]').val(product.id);
                form.find('[name="name"]').val(product.name);
                form.find('[name="description"]').val(product.description);
                form.find('[name="price"]').val(product.price);
                form.find('[name="stock"]').val(product.stock);
                form.find('[name="image_url"]').val(product.image_url);
                form.find('[name="category_id"]').val(product.category_id);
                $('#editProductModal').modal('show');
            });

            $('#editProductForm').submit(function(e) {
                e.preventDefault();
                $.post('?action=updateProduct', $(this).serialize(), function() {
                    loadProducts();
                    $('#editProductModal').modal('hide');
                });
            });

            $(document).on('click', '.delete-btn', function() {
                let id = $(this).data('id');
                if (confirm('Bạn có chắc muốn xóa sản phẩm này?')) {
                    $.get('?action=deleteProduct&id=' + id, function() {
                        loadProducts();
                    });
                }
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
        });
    </script>
</body>
</html>
