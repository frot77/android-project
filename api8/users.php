<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Người dùng</title>
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
                    <a class="nav-link" href="index.php">
                        <i class="fas fa-box"></i> Quản lý sản phẩm
                    </a>
                    <a class="nav-link" href="categories.php">
                        <i class="fas fa-tags"></i> Quản lý danh mục
                    </a>
                    <a class="nav-link active-menu" href="users.php">
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
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Quản lý Người dùng</h2>
                    <button class="btn btn-success" data-toggle="modal" data-target="#addUserModal">
                        <i class="fas fa-user-plus"></i> Thêm người dùng
                    </button>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="mb-3">
                            <input type="text" id="searchInput" class="form-control" placeholder="Tìm kiếm người dùng...">
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Họ tên</th>
                                        <th>Vai trò</th>
                                        <th>Ngày tạo</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody id="userTable">
                                    <!-- Dữ liệu AJAX cập nhật vào đây -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Thêm Người dùng -->
    <div class="modal fade" id="addUserModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm người dùng</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="addUserForm">
                        <input type="text" name="username" placeholder="Username" class="form-control mb-2" required>
                        <input type="password" name="password" placeholder="Mật khẩu" class="form-control mb-2" required>
                        <input type="email" name="email" placeholder="Email" class="form-control mb-2" required>
                        <input type="text" name="full_name" placeholder="Họ tên" class="form-control mb-2" required>
                        <select name="role_id" class="form-control mb-2" required>
                            <option value="">Chọn vai trò</option>
                            <!-- Roles sẽ được load bằng AJAX -->
                        </select>
                        <button type="submit" class="btn btn-success btn-block">
                            <i class="fas fa-plus"></i> Thêm người dùng
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Cập Nhật Người dùng -->
    <div class="modal fade" id="editUserModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Chỉnh sửa người dùng</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="editUserForm">
                        <input type="hidden" name="id">
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" class="form-control" disabled>
                        </div>
                        <div class="form-group">
                            <input type="email" name="email" placeholder="Email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <input type="text" name="full_name" placeholder="Họ tên" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <select name="role_id" class="form-control" required>
                                <option value="">Chọn vai trò</option>
                                <!-- Roles sẽ được load bằng AJAX -->
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Lưu</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Đổi mật khẩu -->
    <div class="modal fade" id="changePasswordModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Đổi mật khẩu</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="changePasswordForm">
                        <input type="hidden" name="id">
                        <input type="password" name="new_password" placeholder="Mật khẩu mới" class="form-control mb-2" required>
                        <button type="submit" class="btn btn-primary">Đổi mật khẩu</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

    <script>
        // Kiểm tra jQuery
        if (typeof jQuery == 'undefined') {
            alert('jQuery không được load!');
        } else {
            console.log('jQuery version:', jQuery.fn.jquery);
        }

        let roles = [];

        function loadRoles() {
            $.get('user_api.php?action=getRoles', function(data) {
                console.log('Loaded roles:', data); // Debug log
                roles = JSON.parse(data);
                let html = '<option value="">Chọn vai trò</option>';
                roles.forEach(function(role) {
                    html += `<option value="${role.id}">${role.name}</option>`;
                });
                $('select[name="role_id"]').html(html);
            }).fail(function(error) {
                console.error('Error loading roles:', error); // Debug log
            });
        }

        function loadUsers() {
            $.get('user_api.php?action=getUsers', function(data) {
                const users = JSON.parse(data);
                let html = '';
                users.forEach(function(user) {
                    html += `
                        <tr>
                            <td>${user.id}</td>
                            <td>${user.username}</td>
                            <td>${user.email}</td>
                            <td>${user.full_name}</td>
                            <td>${user.role_name || 'Chưa phân quyền'}</td>
                            <td>${user.created_at}</td>
                            <td>
                                <button class='btn btn-primary btn-sm edit-btn' data-user='${JSON.stringify(user)}'>
                                    <i class="fas fa-edit"></i> Sửa
                                </button>
                                <button class='btn btn-warning btn-sm change-password-btn' data-id='${user.id}'>
                                    <i class="fas fa-key"></i> Đổi MK
                                </button>
                                <button class='btn btn-danger btn-sm delete-btn' data-id='${user.id}'>
                                    <i class="fas fa-trash"></i> Xóa
                                </button>
                            </td>
                        </tr>
                    `;
                });
                $('#userTable').html(html);
            });
        }

        $(document).ready(function() {
            loadRoles();
            loadUsers();

            $('#searchInput').on('keyup', function() {
                let search = $(this).val().toLowerCase();
                $('#userTable tr').filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(search) > -1);
                });
            });

            $('#addUserForm').submit(function(e) {
                e.preventDefault();
                console.log('Form submitted'); // Debug log
                
                // Lấy dữ liệu form
                const formData = $(this).serialize();
                console.log('Form data:', formData); // Debug log
                
                // Kiểm tra role_id
                const roleId = $(this).find('[name="role_id"]').val();
                if (!roleId) {
                    alert('Vui lòng chọn vai trò cho người dùng!');
                    return;
                }
                
                $.post('user_api.php?action=addUser', formData, function(response) {
                    console.log('Server response:', response); // Debug log
                    try {
                        const result = JSON.parse(response);
                        if (result.success) {
                            loadUsers();
                            $('#addUserModal').modal('hide');
                            $('#addUserForm')[0].reset();
                            alert('Thêm người dùng thành công!');
                        } else {
                            alert(result.error || 'Không thể thêm người dùng!');
                        }
                    } catch (e) {
                        console.error('Error parsing response:', e); // Debug log
                        alert('Có lỗi xảy ra khi xử lý phản hồi từ server!');
                    }
                }).fail(function(xhr, status, error) {
                    console.error('Ajax error:', {xhr, status, error}); // Debug log
                    alert('Lỗi kết nối: ' + error);
                });
            });

            $(document).on('click', '.edit-btn', function() {
                const user = $(this).data('user');
                const form = $('#editUserForm');
                form.find('[name="id"]').val(user.id);
                form.find('input[disabled]').val(user.username);
                form.find('[name="email"]').val(user.email);
                form.find('[name="full_name"]').val(user.full_name);
                form.find('[name="role_id"]').val(user.role_id);
                $('#editUserModal').modal('show');
            });

            $('#editUserForm').submit(function(e) {
                e.preventDefault();
                $.post('user_api.php?action=updateUser', $(this).serialize(), function(response) {
                    const result = JSON.parse(response);
                    if (result.success) {
                        loadUsers();
                        $('#editUserModal').modal('hide');
                    } else {
                        alert(result.error || 'Không thể cập nhật người dùng!');
                    }
                });
            });

            $(document).on('click', '.change-password-btn', function() {
                const id = $(this).data('id');
                $('#changePasswordForm [name="id"]').val(id);
                $('#changePasswordModal').modal('show');
            });

            $('#changePasswordForm').submit(function(e) {
                e.preventDefault();
                $.post('user_api.php?action=updatePassword', $(this).serialize(), function(response) {
                    const result = JSON.parse(response);
                    if (result.success) {
                        $('#changePasswordModal').modal('hide');
                        $('#changePasswordForm')[0].reset();
                        alert('Đã đổi mật khẩu thành công!');
                    } else {
                        alert('Không thể đổi mật khẩu!');
                    }
                });
            });

            $(document).on('click', '.delete-btn', function() {
                const id = $(this).data('id');
                if (confirm('Bạn có chắc muốn xóa người dùng này?')) {
                    $.get('user_api.php?action=deleteUser&id=' + id, function(response) {
                        const result = JSON.parse(response);
                        if (result.success) {
                            loadUsers();
                        } else {
                            alert(result.error || 'Không thể xóa người dùng này!');
                        }
                    });
                }
            });
        });
    </script>
</body>
</html> 