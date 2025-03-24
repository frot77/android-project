<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Trang quản trị</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            background-color: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .login-header h1 {
            font-size: 1.5rem;
            color: #343a40;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>ĐĂNG NHẬP QUẢN TRỊ</h1>
        </div>
        <div id="loginMessage"></div>
        <form id="loginForm">
            <div class="form-group">
                <label for="username">Tên đăng nhập</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Mật khẩu</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Đăng nhập</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#loginForm').submit(function(e) {
                e.preventDefault();
                
                $.ajax({
                    url: 'auth.php',
                    type: 'POST',
                    data: {
                        action: 'login',
                        username: $('#username').val(),
                        password: $('#password').val()
                    },
                    success: function(response) {
                        if (response.success) {
                            window.location.href = 'dashboard.php';
                        } else {
                            $('#loginMessage').html(`
                                <div class="alert alert-danger">
                                    ${response.error || 'Đăng nhập thất bại'}
                                </div>
                            `);
                        }
                    },
                    error: function() {
                        $('#loginMessage').html(`
                            <div class="alert alert-danger">
                                Có lỗi xảy ra. Vui lòng thử lại.
                            </div>
                        `);
                    }
                });
            });
        });
    </script>
</body>
</html> 