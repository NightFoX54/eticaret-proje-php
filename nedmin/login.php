<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Ticaret - Giriş</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        .login-logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-logo h1 {
            color: #4e73df;
        }
        .btn-login {
            background-color: #4e73df;
            border-color: #4e73df;
            width: 100%;
            padding: 10px;
        }
        .btn-login:hover {
            background-color: #2e59d9;
            border-color: #2e59d9;
        }
        .alert {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="login-logo">
                <h1>E-Ticaret</h1>
                <p>Yönetim Paneli</p>
            </div>
            
            <div class="alert alert-danger" id="login-error" role="alert"></div>
            
            <form id="login-form">
                <div class="mb-3">
                    <label for="mail" class="form-label">E-posta</label>
                    <input type="email" class="form-control" id="mail" name="mail" required>
                </div>
                <div class="mb-3">
                    <label for="sifre" class="form-label">Şifre</label>
                    <input type="password" class="form-control" id="sifre" name="sifre" required>
                </div>
                <button type="submit" class="btn btn-primary btn-login">Giriş Yap</button>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#login-form").on("submit", function(e) {
                e.preventDefault();
                
                var mail = $("#mail").val();
                var sifre = $("#sifre").val();
                
                $.ajax({
                    type: "POST",
                    url: "netting/islem.php",
                    data: {
                        mail: mail,
                        sifre: sifre,
                        islem: "admin_giris"
                    },
                    dataType: "json",
                    success: function(response) {
                        if (response.durum === "success") {
                            window.location.href = "production/index.php";
                        } else {
                            $("#login-error").text(response.mesaj);
                            $("#login-error").show();
                            setTimeout(function() {
                                $("#login-error").hide();
                            }, 3000);
                        }
                    },
                    error: function() {
                        $("#login-error").text("Giriş işlemi sırasında bir hata oluştu. Lütfen tekrar deneyin.");
                        $("#login-error").show();
                    }
                });
            });
        });
    </script>
</body>
</html>
