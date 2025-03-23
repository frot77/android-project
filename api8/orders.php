<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Đơn hàng</title>
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
                    <a class="nav-link" href="users.php">
                        <i class="fas fa-users"></i> Quản lý người dùng
                    </a>
                    <a class="nav-link active-menu" href="orders.php">
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
                    <h2>Quản lý Đơn hàng</h2>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="mb-3">
                            <input type="text" id="searchInput" class="form-control" placeholder="Tìm kiếm đơn hàng...">
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Khách hàng</th>
                                        <th>Người nhận</th>
                                        <th>Địa chỉ</th>
                                        <th>SĐT</th>
                                        <th>Tổng tiền</th>
                                        <th>Trạng thái</th>
                                        <th>Ngày tạo</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody id="orderTable">
                                    <!-- Dữ liệu AJAX cập nhật vào đây -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Chi tiết đơn hàng -->
    <div class="modal fade" id="orderDetailModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Chi tiết đơn hàng #<span id="orderDetailId"></span></h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6>Thông tin khách hàng</h6>
                            <p>Tên: <span id="orderCustomerName"></span></p>
                            <p>Username: <span id="orderUsername"></span></p>
                        </div>
                        <div class="col-md-6">
                            <h6>Thông tin người nhận</h6>
                            <p>Tên: <span id="orderRecipientName"></span></p>
                            <p>SĐT: <span id="orderRecipientPhone"></span></p>
                            <p>Địa chỉ: <span id="orderRecipientAddress"></span></p>
                        </div>
                    </div>
                    <h6>Sản phẩm</h6>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th>Hình ảnh</th>
                                    <th>Số lượng</th>
                                    <th>Đơn giá</th>
                                    <th>Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody id="orderItems">
                                <!-- Chi tiết sản phẩm sẽ được thêm vào đây -->
                            </tbody>
                        </table>
                    </div>
                    <div class="text-right">
                        <h5>Tổng tiền: <span id="orderTotal"></span></h5>
                    </div>
                </div>
                <div class="modal-footer">
                    <select id="orderStatus" class="form-control mr-2" style="width: auto;">
                        <option value="Pending">Chờ xử lý</option>
                        <option value="Processing">Đang xử lý</option>
                        <option value="Completed">Hoàn thành</option>
                        <option value="Cancelled">Đã hủy</option>
                    </select>
                    <button type="button" class="btn btn-primary" id="updateStatusBtn">Cập nhật trạng thái</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

    <script>
        function loadOrders() {
            $.get('order_api.php?action=getOrders', function(response) {
                if (response.success) {
                    const orders = response.data;
                    let html = '';
                    orders.forEach(function(order) {
                        const statusClass = {
                            'Pending': 'warning',
                            'Processing': 'info',
                            'Completed': 'success',
                            'Cancelled': 'danger'
                        }[order.status] || 'secondary';

                        const statusText = {
                            'Pending': 'Chờ xử lý',
                            'Processing': 'Đang xử lý',
                            'Completed': 'Hoàn thành',
                            'Cancelled': 'Đã hủy'
                        }[order.status] || order.status;
                        
                        html += `
                            <tr>
                                <td>#${order.id}</td>
                                <td>${order.full_name || order.username || 'N/A'}</td>
                                <td>${order.recipient_name || 'N/A'}</td>
                                <td>${order.recipient_address || 'N/A'}</td>
                                <td>${order.recipient_phone || 'N/A'}</td>
                                <td>${order.total_price ? Number(order.total_price).toLocaleString('vi-VN') + 'đ' : '0đ'}</td>
                                <td><span class="badge badge-${statusClass}">${statusText}</span></td>
                                <td>${new Date(order.created_at).toLocaleString('vi-VN')}</td>
                                <td>
                                    <button class='btn btn-info btn-sm view-btn' data-id='${order.id}'>
                                        <i class="fas fa-eye"></i> Chi tiết
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                    $('#orderTable').html(html);
                } else {
                    alert('Lỗi: ' + response.error);
                    console.error('Error:', response.error);
                }
            }).fail(function(jqXHR, textStatus, errorThrown) {
                alert('Lỗi kết nối đến server');
                console.error('Error:', textStatus, errorThrown);
            });
        }

        function viewOrderDetail(orderId) {
            $.get(`order_api.php?action=getOrderDetail&id=${orderId}`, function(response) {
                if (response.success) {
                    const order = response.data;
                    
                    $('#orderDetailId').text(order.id);
                    $('#orderCustomerName').text(order.full_name || 'N/A');
                    $('#orderUsername').text(order.username || 'N/A');
                    $('#orderRecipientName').text(order.recipient_name || 'N/A');
                    $('#orderRecipientPhone').text(order.recipient_phone || 'N/A');
                    $('#orderRecipientAddress').text(order.recipient_address || 'N/A');
                    $('#orderStatus').val(order.status || 'Pending');
                    $('#orderTotal').text(order.total_price ? Number(order.total_price).toLocaleString('vi-VN') + 'đ' : '0đ');
                    
                    let itemsHtml = '';
                    if (order.items && order.items.length > 0) {
                        order.items.forEach(item => {
                            itemsHtml += `
                                <tr>
                                    <td>${item.product_name || 'N/A'}</td>
                                    <td>
                                        <img src="${item.image_url || ''}" alt="${item.product_name || 'N/A'}" 
                                             style="width: 50px; height: 50px; object-fit: cover;">
                                    </td>
                                    <td>${item.quantity || 0}</td>
                                    <td>${item.price ? Number(item.price).toLocaleString('vi-VN') + 'đ' : '0đ'}</td>
                                    <td>${order.total_price? Number(order.total_price).toLocaleString('vi-VN') + 'đ' : '0đ'}</td>
                                </tr>
                            `;
                        });
                    } else {
                        itemsHtml = '<tr><td colspan="5" class="text-center">Không có sản phẩm nào</td></tr>';
                    }
                    $('#orderItems').html(itemsHtml);
                    
                    $('#orderDetailModal').modal('show');
                } else {
                    alert('Lỗi: ' + response.error);
                    console.error('Error:', response.error);
                }
            }).fail(function(jqXHR, textStatus, errorThrown) {
                alert('Lỗi kết nối đến server');
                console.error('Error:', textStatus, errorThrown);
            });
        }

        $(document).ready(function() {
            loadOrders();

            $('#searchInput').on('keyup', function() {
                let search = $(this).val().toLowerCase();
                $('#orderTable tr').filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(search) > -1);
                });
            });

            $(document).on('click', '.view-btn', function() {
                const orderId = $(this).data('id');
                viewOrderDetail(orderId);
            });

            $('#updateStatusBtn').click(function() {
                const orderId = $('#orderDetailId').text();
                const status = $('#orderStatus').val();
                
                $.ajax({
                    url: 'order_api.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'updateStatus',
                        id: orderId,
                        status: status
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('Cập nhật trạng thái thành công');
                            $('#orderDetailModal').modal('hide');
                            loadOrders();
                        } else {
                            alert('Lỗi: ' + response.error);
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('Lỗi kết nối: ' + error);
                    }
                });
            });
        });
    </script>
</body>
</html>
