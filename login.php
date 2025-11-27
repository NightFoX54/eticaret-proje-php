<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap | E-Ticaret</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            max-width: 450px;
            width: 100%;
            padding: 20px;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        .card-header {
            background-color: #4e73df;
            color: white;
            border-radius: 10px 10px 0 0 !important;
            padding: 1.5rem;
            text-align: center;
        }
        .btn-login {
            background-color: #4e73df;
            border-color: #4e73df;
        }
        .btn-login:hover {
            background-color: #2e59d9;
            border-color: #2e59d9;
        }
        .alert {
            margin-bottom: 0;
            display: none;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0"><i class="fas fa-store me-2"></i> E-Ticaret</h4>
                <p class="mb-0 mt-2">Kullanıcı Girişi</p>
            </div>
            <div class="card-body">
                <div class="alert alert-danger" id="error-alert" role="alert"></div>
                <div class="alert alert-success" id="success-alert" role="alert"></div>
                
                <form id="login-form">
                    <div class="mb-3">
                        <label for="mail" class="form-label">E-posta Adresiniz</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" class="form-control" id="mail" name="mail" required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="sifre" class="form-label">Şifreniz</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" id="sifre" name="sifre" required>
                            <span class="input-group-text toggle-password" style="cursor: pointer;">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-login btn-lg">
                            <i class="fas fa-sign-in-alt me-2"></i> Giriş Yap
                        </button>
                    </div>
                </form>
            </div>
            <div class="card-footer text-center py-3">
                <div class="text-muted mb-2">Henüz hesabınız yok mu?</div>
                <a href="register.php" class="btn btn-outline-primary">
                    <i class="fas fa-user-plus me-2"></i> Hesap Oluştur
                </a>
            </div>
        </div>
        <div class="text-center mt-3">
            <a href="index.php" class="text-decoration-none">
                <i class="fas fa-home me-1"></i> Ana Sayfaya Dön
            </a>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Show/hide password
            $('.toggle-password').click(function() {
                const passwordField = $('#sifre');
                const passwordFieldType = passwordField.attr('type');
                const icon = $(this).find('i');
                
                if (passwordFieldType === 'password') {
                    passwordField.attr('type', 'text');
                    icon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    passwordField.attr('type', 'password');
                    icon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });
            
            // Login form submission
            $('#login-form').submit(function(e) {
                e.preventDefault();
                
                const mail = $('#mail').val();
                const sifre = $('#sifre').val();
                
                // Hide alerts
                $('#error-alert, #success-alert').hide();
                
                // Show loading state
                const submitBtn = $(this).find('button[type="submit"]');
                const originalBtnText = submitBtn.html();
                submitBtn.html('<i class="fas fa-spinner fa-spin me-2"></i> Giriş Yapılıyor...');
                submitBtn.prop('disabled', true);
                
                $.ajax({
                    type: 'POST',
                    url: 'nedmin/netting/islem.php',
                    data: {
                        islem: 'kullanici_giris',
                        mail: mail,
                        sifre: sifre
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.durum === 'success') {
                            $('#success-alert').text(response.mesaj).slideDown();
                            
                            // Redirect to index page after successful login
                            setTimeout(function() {
                                window.location.href = 'index.php';
                            }, 1500);
                        } else {
                            $('#error-alert').text(response.mesaj).slideDown();
                            submitBtn.html(originalBtnText);
                            submitBtn.prop('disabled', false);
                        }
                    },
                    error: function(xhr, status, error) {
                        $('#error-alert').text('Bir hata oluştu, lütfen tekrar deneyin.').slideDown();
                        submitBtn.html(originalBtnText);
                        submitBtn.prop('disabled', false);
                    }
                });
            });
        });
    </script>
</body>
</html> 