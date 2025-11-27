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
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: linear-gradient(135deg, #FAF8FC 0%, #E8DFF0 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            position: relative;
            overflow: hidden;
        }
        
        body::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(157, 127, 199, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            animation: float 8s ease-in-out infinite;
        }
        
        body::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -15%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(139, 111, 199, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            animation: float 10s ease-in-out infinite reverse;
        }
        
        @keyframes float {
            0%, 100% {
                transform: translateY(0) rotate(0deg);
            }
            50% {
                transform: translateY(-30px) rotate(5deg);
            }
        }
        
        .login-wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 450px;
        }
        
        .login-box {
            position: relative;
            background: white;
            border-radius: 20px;
            padding: 0;
            overflow: visible;
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 20px 60px rgba(157, 127, 199, 0.2);
            cursor: pointer;
        }
        
        .login-box::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 20px;
            background: linear-gradient(45deg, #9D7FC7, #8B6FC7, #B8A5D9, #D9CEE8, #9D7FC7, #7A5FB8, #9D7FC7, #8B6FC7);
            background-size: 400% 400%;
            filter: drop-shadow(0 15px 50px rgba(157, 127, 199, 0.4));
            animation: rotating 4s linear infinite;
            animation-delay: -1s;
            z-index: -1;
            padding: 3px;
            -webkit-mask: 
                linear-gradient(#fff 0 0) content-box, 
                linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            box-sizing: border-box;
            top: 0;
            left: 0;
        }
        
        @keyframes rotating {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }
        
        .login-box:hover::before {
            animation: rotating 4s linear infinite;
            animation-delay: -1s;
        }
        
        .login-box::after {
            content: '';
            position: absolute;
            inset: 3px;
            background: linear-gradient(135deg, #FFFFFF 0%, #F5F0FA 100%);
            border-radius: 17px;
            border: 2px solid rgba(157, 127, 199, 0.2);
            z-index: 0;
        }
        
        .login-box:hover {
            transform: translateY(-10px);
            box-shadow: 0 30px 80px rgba(157, 127, 199, 0.4);
        }
        
        .login-box:hover .login-header {
            padding-bottom: 1rem;
        }
        
        .login-box:hover .login-form-container {
            max-height: 1000px;
            opacity: 1;
            padding: 2rem;
        }
        
        .login-header {
            position: relative;
            z-index: 1;
            padding: 2.5rem 2rem;
            text-align: center;
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            background: transparent;
        }
        
        .login-header h2 {
            color: #8B6FC7;
            font-size: 2rem;
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            position: relative;
            z-index: 1;
        }
        
        .login-header h2 i {
            color: #9D7FC7;
            font-size: 1.8rem;
            animation: pulse 2s ease-in-out infinite;
        }
        
        .login-header h2 .heart-icon {
            color: #ff6b9d;
            font-size: 1.5rem;
            animation: heartbeat 1.5s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
        }
        
        @keyframes heartbeat {
            0%, 100% {
                transform: scale(1);
            }
            25% {
                transform: scale(1.2);
            }
            50% {
                transform: scale(1);
            }
        }
        
        .login-form-container {
            position: relative;
            z-index: 1;
            max-height: 0;
            opacity: 0;
            overflow: hidden;
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            padding: 0 2rem;
        }
        
        .login-form {
            background: transparent;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
            animation: slideInUp 0.5s ease-out;
            animation-fill-mode: both;
        }
        
        .form-group:nth-child(1) {
            animation-delay: 0.1s;
        }
        
        .form-group:nth-child(2) {
            animation-delay: 0.2s;
        }
        
        .form-group:nth-child(3) {
            animation-delay: 0.3s;
        }
        
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .form-label {
            color: #7A5FB8;
            font-weight: 600;
            margin-bottom: 0.5rem;
            display: block;
        }
        
        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
            background: white;
            border: 2px solid rgba(157, 127, 199, 0.3);
            border-radius: 12px;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }
        
        .input-wrapper:focus-within {
            border-color: #9D7FC7;
            background: #F5F0FA;
            box-shadow: 0 0 0 3px rgba(157, 127, 199, 0.15), 0 0 20px rgba(157, 127, 199, 0.3);
            transform: translateY(-2px);
            outline: none !important;
        }
        
        .input-wrapper i {
            color: #9D7FC7;
            margin-right: 0.75rem;
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
        }
        
        .form-control {
            background: transparent;
            border: none;
            color: #333;
            flex: 1;
            padding: 0;
            font-size: 1rem;
            outline: none !important;
            box-shadow: none !important;
        }
        
        .form-control::placeholder {
            color: rgba(157, 127, 199, 0.5);
        }
        
        .form-control:focus {
            outline: none !important;
            color: #333;
            box-shadow: none !important;
        }
        
        .form-control:focus-visible {
            outline: none !important;
            box-shadow: none !important;
        }
        
        input:focus,
        input:focus-visible,
        input:active {
            outline: none !important;
            box-shadow: none !important;
        }
        
        .toggle-password {
            cursor: pointer;
            color: rgba(157, 127, 199, 0.7);
            transition: color 0.3s ease;
            padding: 0 0.5rem;
        }
        
        .toggle-password:hover {
            color: #9D7FC7;
        }
        
        .btn-login {
            width: 100%;
            background: linear-gradient(135deg, #9D7FC7 0%, #8B6FC7 100%);
            border: none;
            color: white;
            padding: 1rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1.1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(157, 127, 199, 0.4);
            position: relative;
            overflow: hidden;
        }
        
        .btn-login::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }
        
        .btn-login:hover::before {
            width: 300px;
            height: 300px;
        }
        
        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(157, 127, 199, 0.5);
        }
        
        .btn-login:active {
            transform: translateY(-1px);
        }
        
        .btn-login span {
            position: relative;
            z-index: 1;
        }
        
        .alert {
            margin-bottom: 1rem;
            display: none;
            border-radius: 12px;
            padding: 1rem;
            animation: slideDown 0.3s ease-out;
            border: 1px solid;
            position: relative;
            padding-left: 3rem;
        }
        
        .alert::before {
            content: '';
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .alert-danger {
            background: linear-gradient(135deg, #FFF0F0 0%, #FFE8E8 100%);
            border-color: #FFB3B3;
            color: #8B0000;
            box-shadow: 0 4px 15px rgba(255, 179, 179, 0.3);
        }
        
        .alert-danger::before {
            content: '\f06a';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            background: linear-gradient(135deg, #FF6B6B 0%, #FF8E8E 100%);
            color: white;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .alert-success {
            background: linear-gradient(135deg, #F0E8FF 0%, #E8DFF0 100%);
            border-color: #B8A5D9;
            color: #7A5FB8;
            box-shadow: 0 4px 15px rgba(184, 165, 217, 0.3);
        }
        
        .alert-success::before {
            content: '\f00c';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            background: linear-gradient(135deg, #9D7FC7 0%, #8B6FC7 100%);
            color: white;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .input-error {
            border-color: #FF6B6B !important;
            background: #FFF0F0 !important;
            box-shadow: 0 0 0 3px rgba(255, 107, 107, 0.15), 0 0 20px rgba(255, 107, 107, 0.3) !important;
        }
        
        .input-error-message {
            color: #FF6B6B;
            font-size: 0.85rem;
            margin-top: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            animation: slideDown 0.3s ease-out;
        }
        
        .input-error-message i {
            font-size: 0.9rem;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .login-footer {
            position: relative;
            z-index: 1;
            padding: 1.5rem 2rem;
            text-align: center;
            border-top: 1px solid rgba(157, 127, 199, 0.2);
            margin-top: 1rem;
        }
        
        .login-footer p {
            color: #7A5FB8;
            margin-bottom: 1rem;
            font-size: 0.95rem;
        }
        
        .btn-register {
            background: transparent;
            border: 2px solid #9D7FC7;
            color: #9D7FC7;
            padding: 0.75rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        
        .btn-register:hover {
            background: #9D7FC7;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(157, 127, 199, 0.4);
        }
        
        .back-link {
            text-align: center;
            margin-top: 2rem;
            position: relative;
            z-index: 1;
        }
        
        .back-link a {
            color: #8B6FC7;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1.5rem;
            background: rgba(255, 255, 255, 0.8);
            border: 2px solid rgba(157, 127, 199, 0.3);
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(157, 127, 199, 0.15);
            backdrop-filter: blur(10px);
            position: relative;
            overflow: hidden;
        }
        
        .back-link a::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(157, 127, 199, 0.1), transparent);
            transition: left 0.5s ease;
        }
        
        .back-link a:hover::before {
            left: 100%;
        }
        
        .back-link a:hover {
            color: #7A5FB8;
            background: rgba(255, 255, 255, 1);
            border-color: #9D7FC7;
            transform: translateX(-8px) translateY(-2px);
            box-shadow: 0 6px 20px rgba(157, 127, 199, 0.3);
        }
        
        .back-link a i {
            transition: transform 0.3s ease;
            font-size: 1.1rem;
        }
        
        .back-link a:hover i {
            transform: translateX(-3px);
        }
        
        @media (max-width: 768px) {
            .login-header h2 {
                font-size: 1.5rem;
            }
            
            .login-box {
                border-radius: 15px;
            }
            
            .login-box::before {
                border-radius: 15px;
            }
            
            .login-box::after {
                border-radius: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-box">
            <div class="login-header">
                <h2>
                    <i class="fas fa-store"></i>
                    GİRİŞ YAP
                    <i class="fas fa-heart heart-icon"></i>
                </h2>
            </div>
            
            <div class="login-form-container">
                <div class="alert alert-danger" id="error-alert" role="alert"></div>
                <div class="alert alert-success" id="success-alert" role="alert"></div>
                
                <form id="login-form" class="login-form">
                    <div class="form-group">
                        <label for="mail" class="form-label">
                            <i class="fas fa-envelope me-2"></i>E-posta Adresiniz
                        </label>
                        <div class="input-wrapper" id="mail-wrapper">
                            <i class="fas fa-envelope"></i>
                            <input type="email" class="form-control" id="mail" name="mail" placeholder="ornek@email.com" required>
                        </div>
                        <div class="input-error-message" id="mail-error" style="display: none;">
                            <i class="fas fa-exclamation-circle"></i>
                            <span></span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="sifre" class="form-label">
                            <i class="fas fa-lock me-2"></i>Şifreniz
                        </label>
                        <div class="input-wrapper" id="sifre-wrapper">
                            <i class="fas fa-lock"></i>
                            <input type="password" class="form-control" id="sifre" name="sifre" placeholder="Şifrenizi girin" required>
                            <span class="toggle-password" onclick="togglePassword()">
                                <i class="fas fa-eye" id="toggle-icon"></i>
                            </span>
                        </div>
                        <div class="input-error-message" id="sifre-error" style="display: none;">
                            <i class="fas fa-exclamation-circle"></i>
                            <span></span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn-login">
                            <span><i class="fas fa-sign-in-alt me-2"></i>Giriş Yap</span>
                        </button>
                    </div>
                </form>
                
                <div class="login-footer">
                    <p>Henüz hesabınız yok mu?</p>
                    <a href="register.php" class="btn-register">
                        <i class="fas fa-user-plus me-2"></i>Hesap Oluştur
                    </a>
                </div>
            </div>
        </div>
        
        <div class="back-link">
            <a href="index.php">
                <i class="fas fa-arrow-left"></i>
                Ana Sayfaya Dön
            </a>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Toggle password visibility
        function togglePassword() {
            const passwordField = document.getElementById('sifre');
            const toggleIcon = document.getElementById('toggle-icon');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
        
        // Custom email validation messages
        function setCustomEmailValidation() {
            const emailInput = document.getElementById('mail');
            const emailWrapper = document.getElementById('mail-wrapper');
            const emailError = document.getElementById('mail-error');
            
            emailInput.addEventListener('invalid', function(e) {
                e.preventDefault();
                
                if (!emailInput.validity.valid) {
                    emailWrapper.classList.add('input-error');
                    
                    let errorMessage = '';
                    if (emailInput.validity.valueMissing) {
                        errorMessage = 'E-posta adresi gereklidir.';
                    } else if (emailInput.validity.typeMismatch) {
                        errorMessage = 'Lütfen geçerli bir e-posta adresi girin. (örn: ornek@email.com)';
                    } else if (emailInput.value && !emailInput.value.includes('@')) {
                        errorMessage = 'Lütfen e-posta adresine bir "@" işareti ekleyin.';
                    } else {
                        errorMessage = 'Geçersiz e-posta formatı.';
                    }
                    
                    emailError.querySelector('span').textContent = errorMessage;
                    emailError.style.display = 'flex';
                }
            });
            
            emailInput.addEventListener('input', function() {
                if (emailInput.validity.valid) {
                    emailWrapper.classList.remove('input-error');
                    emailError.style.display = 'none';
                } else {
                    emailWrapper.classList.add('input-error');
                }
            });
            
            emailInput.addEventListener('blur', function() {
                if (!emailInput.validity.valid && emailInput.value) {
                    let errorMessage = '';
                    if (emailInput.validity.typeMismatch) {
                        if (!emailInput.value.includes('@')) {
                            errorMessage = 'Lütfen e-posta adresine bir "@" işareti ekleyin. "' + emailInput.value + '" adresinde "@" eksik.';
                        } else {
                            errorMessage = 'Lütfen geçerli bir e-posta adresi girin. (örn: ornek@email.com)';
                        }
                    } else {
                        errorMessage = 'Geçersiz e-posta formatı.';
                    }
                    
                    emailError.querySelector('span').textContent = errorMessage;
                    emailError.style.display = 'flex';
                }
            });
        }
        
        // Custom password validation
        function setCustomPasswordValidation() {
            const passwordInput = document.getElementById('sifre');
            const passwordWrapper = document.getElementById('sifre-wrapper');
            const passwordError = document.getElementById('sifre-error');
            
            passwordInput.addEventListener('invalid', function(e) {
                e.preventDefault();
                
                if (!passwordInput.validity.valid) {
                    passwordWrapper.classList.add('input-error');
                    
                    let errorMessage = '';
                    if (passwordInput.validity.valueMissing) {
                        errorMessage = 'Şifre gereklidir.';
                    } else {
                        errorMessage = 'Lütfen şifrenizi girin.';
                    }
                    
                    passwordError.querySelector('span').textContent = errorMessage;
                    passwordError.style.display = 'flex';
                }
            });
            
            passwordInput.addEventListener('input', function() {
                if (passwordInput.validity.valid) {
                    passwordWrapper.classList.remove('input-error');
                    passwordError.style.display = 'none';
                }
            });
        }
        
        $(document).ready(function() {
            // Set custom validation messages
            setCustomEmailValidation();
            setCustomPasswordValidation();
            
            // Login form submission
            $('#login-form').submit(function(e) {
                e.preventDefault();
                
                const mail = $('#mail').val();
                const sifre = $('#sifre').val();
                
                // Clear previous errors
                $('#mail-wrapper, #sifre-wrapper').removeClass('input-error');
                $('#mail-error, #sifre-error').hide();
                
                // Validate email
                const emailInput = document.getElementById('mail');
                if (!emailInput.validity.valid) {
                    emailInput.reportValidity();
                    return false;
                }
                
                // Validate password
                const passwordInput = document.getElementById('sifre');
                if (!passwordInput.validity.valid) {
                    passwordInput.reportValidity();
                    return false;
                }
                
                // Hide alerts - stop any ongoing animations first
                $('#error-alert, #success-alert').stop(true, true).hide();
                
                // Show loading state
                const submitBtn = $(this).find('button[type="submit"]');
                const originalBtnText = submitBtn.html();
                submitBtn.html('<span><i class="fas fa-spinner fa-spin me-2"></i>Giriş Yapılıyor...</span>');
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
                        // Stop any ongoing animations and hide all alerts first
                        $('#error-alert, #success-alert').stop(true, true).hide();
                        
                        if (response.durum === 'success') {
                            $('#success-alert').text(response.mesaj).stop(true, true).fadeIn(300);
                            submitBtn.html('<span><i class="fas fa-check me-2"></i>Giriş Başarılı!</span>');
                            
                            // Redirect to index page after successful login
                            setTimeout(function() {
                                window.location.href = 'index.php';
                            }, 1500);
                        } else {
                            $('#error-alert').text(response.mesaj).stop(true, true).fadeIn(300);
                            submitBtn.html(originalBtnText);
                            submitBtn.prop('disabled', false);
                        }
                    },
                    error: function(xhr, status, error) {
                        // Stop any ongoing animations and hide all alerts first
                        $('#error-alert, #success-alert').stop(true, true).hide();
                        $('#error-alert').text('Bir hata oluştu, lütfen tekrar deneyin.').stop(true, true).fadeIn(300);
                        submitBtn.html(originalBtnText);
                        submitBtn.prop('disabled', false);
                    }
                });
            });
            
            // Auto-expand on input focus
            $('#mail, #sifre').on('focus', function() {
                $('.login-box').addClass('expanded');
            });
        });
    </script>
</body>
</html> 