<!-- 
Author: Rijal Imamul Haq Syamsu Alam
Lisensi Kepada: Pengadilan Tinggi Agama Makassar
-->

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIPADECENG </title>
    <link rel="icon" href="<?php echo baseUrl('public/images/sipadeceng.ico'); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #e6eaf3 0%, #001f4d 60%, #ffffff 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }

        .login-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
        }

        .login-header {
            background: linear-gradient(135deg, #001f4d 0%, #ffffff 100%);
            color: #fff;
            padding: 40px 30px;
            text-align: center;
        }

        .login-header h3,
        .login-header p {
            color: #111 !important;
        }

        .login-header i {
            font-size: 4rem;
            margin-bottom: 20px;
        }

        .login-header h3 {
            font-size: 1.8rem;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .login-header p {
            font-size: 1rem;
            opacity: 0.9;
            margin-bottom: 0;
        }

        .login-body {
            padding: 40px 30px;
        }

        .form-control {
            padding: 12px 15px;
            font-size: 1rem;
            border-radius: 8px;
            border: 1px solid #dee2e6;
            transition: all 0.3s ease;
        }

        .form-control:hover {
            border-color: #b8bfc6;
        }

        .form-control:focus {
            border-color: #001f4d;
            box-shadow: 0 0 0 0.2rem rgba(0, 31, 77, 0.15);
        }

        .btn-primary {
            background: linear-gradient(135deg, #001f4d 0%, #ffffff 100%);
            border: none;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s;
            border-radius: 8px;
            color: #fff;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #003366 0%, #e6eaf3 100%);
            color: #001f4d;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 31, 77, 0.2);
        }

        .alert {
            border-radius: 10px;
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
            z-index: 10;
            background: white;
            padding: 5px;
            border: none;
        }

        .password-toggle:hover {
            color: #495057;
        }

        .form-group {
            position: relative;
        }

        .footer-text {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            color: #6c757d;
        }

        @media (max-width: 576px) {
            .login-container {
                margin: 20px;
            }

            .login-header {
                padding: 30px 20px;
            }

            .login-header h3 {
                font-size: 1.5rem;
            }

            .login-body {
                padding: 30px 20px;
            }
        }

        .form-control:focus+.password-toggle {
            color: #001f4d;
        }

        .form-label {
            font-weight: 500;
            color: #495057;
            margin-bottom: 8px;
        }

        .login-container {
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .password-toggle {
            transition: all 0.2s ease;
        }

        .form-control:focus {
            outline: none;
        }

        .login-header i {
            display: block;
        }

        input[type="password"]::-ms-reveal,
        input[type="password"]::-ms-clear,
        input[type="password"]::-webkit-credentials-auto-fill-button {
            display: none;
        }

        input#password {
            padding-right: 45px;
        }

        .form-check-input:checked {
            background-color: #001f4d;
            border-color: #001f4d;
        }

        .form-check-input:focus {
            border-color: #001f4d;
            box-shadow: 0 0 0 0.25rem rgba(0, 31, 77, 0.15);
        }
    </style>
</head>

<body>
    <div class="container-fluid d-flex justify-content-center align-items-center min-vh-100" style="background: none;">
        <div class="row w-100"
            style="max-width: 900px; min-height: 500px; box-shadow: 0 10px 30px rgba(0,0,0,0.15); border-radius: 20px; overflow: hidden; background: #fff;">
            <!-- Left: Login Form -->
            <div class="col-md-6 p-0" style="background: #f7fafc; display: flex; align-items: center;">
                <div class="w-100 px-4 py-5" style="max-width: 400px; margin: 0 auto;">
                    <div class="text-center mb-4">
                        <img src="<?php echo baseUrl('public/images/sipadeceng.png'); ?>"
                            alt="Logo Pengadilan Tinggi Agama Makassar" style="max-width: 80px; height: auto;">
                        <h3 class="mt-3" style="font-weight: 700; color: #001f4d; letter-spacing: 2px;">WELCOME</h3>
                    </div>
                    <div id="alertContainer">
                        <?php if (isset($_GET['expired']) && $_GET['expired'] == '1'): ?>
                            <div class="alert alert-warning">Sesi Anda telah berakhir karena tidak aktif. Silakan login
                                kembali.</div>
                        <?php endif; ?>
                    </div>
                    <form id="loginForm">
                        <div class="mb-4">
                            <label for="username" class="form-label"
                                style="color: #001f4d; font-weight: 600;">Username</label>
                            <div class="form-group">
                                <input type="text" class="form-control" id="username" name="username"
                                    placeholder="Masukkan username" required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="password" class="form-label"
                                style="color: #001f4d; font-weight: 600;">Password</label>
                            <div class="form-group">
                                <input type="password" class="form-control" id="password" name="password"
                                    placeholder="Masukkan password" required>
                                <span class="password-toggle" id="togglePassword">
                                    <i class="bi bi-eye"></i>
                                </span>
                            </div>
                        </div>
                        <button type="submit" class="btn w-100"
                            style="background: #001f4d; color: #fff; font-weight: 700; font-size: 1.1rem; border-radius: 8px; letter-spacing: 1px;">Login</button>
                    </form>
                </div>
            </div>
            <!-- Right: Illustration -->
            <div class="col-md-6 d-none d-md-flex align-items-center justify-content-center p-0"
                style="background: #e6eaf3;">
                <div class="text-center w-100">
                    <img src="<?php echo baseUrl('public/images/Login.jpeg'); ?>" alt="Welcome Illustration"
                        style="max-width: 320px; width: 90%; margin-bottom: 30px;">
                    <div style="font-size: 2rem; font-weight: 700; color: #001f4d; letter-spacing: 2px;">SIPADECENG
                    </div>
                    <div style="color: #555; font-size: 1.1rem; margin-top: 10px;">Sistem Pengelolaan Data Elektronik
                        Cuti<br>Pengadilan Tinggi Agama Makassar</div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const BASE_URL = '<?php echo baseUrl(); ?>';

        function baseUrl(url = '') {
            return BASE_URL + url;
        }

        $(document).ready(function () {
            // Toggle password visibility
            $('#togglePassword').click(function (e) {
                e.preventDefault();
                e.stopPropagation();

                const passwordField = $('#password');
                const icon = $(this).find('i');

                if (passwordField.attr('type') === 'password') {
                    passwordField.attr('type', 'text');
                    icon.removeClass('bi-eye').addClass('bi-eye-slash');
                } else {
                    passwordField.attr('type', 'password');
                    icon.removeClass('bi-eye-slash').addClass('bi-eye');
                }
            });

            // Handle login form
            $('#loginForm').submit(function (e) {
                e.preventDefault();

                const formData = {
                    username: $('#username').val(),
                    password: $('#password').val()
                };

                // Disable submit button
                const submitBtn = $(this).find('button[type="submit"]');
                submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Loading...');

                $.ajax({
                    url: baseUrl('auth/login'),
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            showAlert('success', response.message);
                            setTimeout(function () {
                                window.location.href = response.redirect;
                            }, 1000);
                        } else {
                            showAlert('danger', response.message);
                            submitBtn.prop('disabled', false).html('<i class="bi bi-box-arrow-in-right me-2"></i>Login');
                        }
                    },
                    error: function () {
                        showAlert('danger', 'Terjadi kesalahan. Silakan coba lagi.');
                        submitBtn.prop('disabled', false).html('<i class="bi bi-box-arrow-in-right me-2"></i>Login');
                    }
                });
            });

            function showAlert(type, message) {
                const icon = type === 'success' ? 'success' : 'error';
                const title = type === 'success' ? 'Berhasil!' : 'Error!';

                Swal.fire({
                    icon: icon,
                    title: title,
                    text: message,
                    confirmButtonColor: '#001f4d',
                    timer: type === 'success' ? 2000 : undefined,
                    timerProgressBar: type === 'success'
                });
            }
        });
    </script>
</body>

</html>