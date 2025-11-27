<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Üye Ol | E-Ticaret</title>
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
            overflow-x: hidden;
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
        
        .register-wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 750px;
        }
        
        .register-box {
            position: relative;
            background: linear-gradient(135deg, #FFFFFF 0%, #F5F0FA 100%);
            border-radius: 20px;
            padding: 0;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(157, 127, 199, 0.2);
            border: 2px solid rgba(157, 127, 199, 0.2);
            animation: fadeInUp 0.6s ease-out;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .register-header {
            background: linear-gradient(135deg, #9D7FC7 0%, #8B6FC7 100%);
            color: white;
            padding: 2.5rem 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .register-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            border-radius: 50%;
        }
        
        .register-header h2 {
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
        
        .register-header h2 i {
            font-size: 1.8rem;
            animation: pulse 2s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
        }
        
        .register-header p {
            margin-top: 0.5rem;
            font-size: 1rem;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }
        
        .register-body {
            padding: 2.5rem;
        }
        
        .form-label {
            color: #7A5FB8;
            font-weight: 600;
            margin-bottom: 0.5rem;
            display: block;
        }
        
        .form-label .text-danger {
            color: #FF6B6B !important;
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
        
        .toggle-password {
            cursor: pointer;
            color: rgba(157, 127, 199, 0.7);
            transition: color 0.3s ease;
            padding: 0 0.5rem;
        }
        
        .toggle-password:hover {
            color: #9D7FC7;
        }
        
        .form-text {
            color: rgba(157, 127, 199, 0.7);
            font-size: 0.85rem;
            margin-top: 0.5rem;
        }
        
        .form-check {
            margin: 1.5rem 0;
        }
        
        .form-check-input {
            border-color: #9D7FC7;
            cursor: pointer;
        }
        
        .form-check-input:checked {
            background-color: #9D7FC7;
            border-color: #9D7FC7;
        }
        
        .form-check-input:focus {
            box-shadow: 0 0 0 0.2rem rgba(157, 127, 199, 0.25);
        }
        
        .form-check-label {
            color: #7A5FB8;
            cursor: pointer;
        }
        
        .form-check-label a {
            color: #9D7FC7;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }
        
        .form-check-label a:hover {
            color: #7A5FB8;
            text-decoration: underline;
        }
        
        .btn-register {
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
        
        .btn-register::before {
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
        
        .btn-register:hover::before {
            width: 300px;
            height: 300px;
        }
        
        .btn-register:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(157, 127, 199, 0.5);
        }
        
        .btn-register:active {
            transform: translateY(-1px);
        }
        
        .btn-register span {
            position: relative;
            z-index: 1;
        }
        
        .alert {
            margin-bottom: 1.5rem;
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
        
        .register-footer {
            padding: 1.5rem 2rem;
            text-align: center;
            border-top: 1px solid rgba(157, 127, 199, 0.2);
            background: rgba(255, 255, 255, 0.5);
        }
        
        .register-footer p {
            color: #7A5FB8;
            margin-bottom: 1rem;
            font-size: 0.95rem;
        }
        
        .btn-login {
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
        
        .btn-login:hover {
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
        
        .modal-content {
            border-radius: 15px;
            border: none;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }
        
        .modal-header {
            background: linear-gradient(135deg, #9D7FC7 0%, #8B6FC7 100%);
            color: white;
            border-radius: 15px 15px 0 0;
            border: none;
        }
        
        .modal-header .btn-close {
            filter: brightness(0) invert(1);
        }
        
        .modal-body {
            padding: 2rem;
        }
        
        .modal-body h5, .modal-body h6 {
            color: #7A5FB8;
            margin-top: 1.5rem;
            margin-bottom: 0.75rem;
        }
        
        .modal-body h5:first-child {
            margin-top: 0;
        }
        
        .modal-body p {
            color: #555;
            line-height: 1.8;
        }
        
        .modal-footer {
            border-top: 1px solid rgba(157, 127, 199, 0.2);
        }
        
        .modal-footer .btn {
            border-radius: 8px;
        }
        
        @media (max-width: 768px) {
            .register-box {
                border-radius: 15px;
            }
            
            .register-header {
                padding: 2rem 1.5rem;
            }
            
            .register-header h2 {
                font-size: 1.5rem;
            }
            
            .register-body {
                padding: 2rem 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="register-wrapper">
        <div class="register-box">
            <div class="register-header">
                <h2>
                    <i class="fas fa-store"></i>
                    ÜYE OL
                </h2>
                <p>Yeni Hesap Oluştur</p>
            </div>
            
            <div class="register-body">
                <div class="alert alert-danger" id="error-alert" role="alert"></div>
                <div class="alert alert-success" id="success-alert" role="alert"></div>
                
                <form id="register-form">
                    <div class="row mb-3">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label for="ad" class="form-label">
                                <i class="fas fa-user me-2"></i>Adınız <span class="text-danger">*</span>
                            </label>
                            <div class="input-wrapper" id="ad-wrapper">
                                <i class="fas fa-user"></i>
                                <input type="text" class="form-control" id="ad" name="ad" placeholder="Adınızı girin" pattern="[A-Za-zÇĞİÖŞÜçğıöşü\s]+" required>
                            </div>
                            <div class="input-error-message" id="ad-error" style="display: none;">
                                <i class="fas fa-exclamation-circle"></i>
                                <span></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="soyad" class="form-label">
                                <i class="fas fa-user me-2"></i>Soyadınız <span class="text-danger">*</span>
                            </label>
                            <div class="input-wrapper" id="soyad-wrapper">
                                <i class="fas fa-user"></i>
                                <input type="text" class="form-control" id="soyad" name="soyad" placeholder="Soyadınızı girin" pattern="[A-Za-zÇĞİÖŞÜçğıöşü\s]+" required>
                            </div>
                            <div class="input-error-message" id="soyad-error" style="display: none;">
                                <i class="fas fa-exclamation-circle"></i>
                                <span></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label for="mail" class="form-label">
                                <i class="fas fa-envelope me-2"></i>E-posta Adresiniz <span class="text-danger">*</span>
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
                        <div class="col-md-6">
                            <label for="tel_no" class="form-label">
                                <i class="fas fa-phone me-2"></i>Telefon Numarası
                            </label>
                            <div class="input-wrapper" id="tel-wrapper">
                                <i class="fas fa-phone"></i>
                                <input type="tel" class="form-control" id="tel_no" name="tel_no" placeholder="+90 555 123 45 67" pattern="[\d+]+">
                            </div>
                            <div class="input-error-message" id="tel-error" style="display: none;">
                                <i class="fas fa-exclamation-circle"></i>
                                <span></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label for="sifre" class="form-label">
                                <i class="fas fa-lock me-2"></i>Şifre <span class="text-danger">*</span>
                            </label>
                            <div class="input-wrapper" id="sifre-wrapper">
                                <i class="fas fa-lock"></i>
                                <input type="password" class="form-control" id="sifre" name="sifre" placeholder="Şifrenizi girin" required>
                                <span class="toggle-password" onclick="togglePassword('sifre')">
                                    <i class="fas fa-eye" id="toggle-icon-sifre"></i>
                                </span>
                            </div>
                            <div class="form-text">Şifreniz en az 8 karakter uzunluğunda olmalıdır.</div>
                            <div class="input-error-message" id="sifre-error" style="display: none;">
                                <i class="fas fa-exclamation-circle"></i>
                                <span></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="sifre_tekrar" class="form-label">
                                <i class="fas fa-lock me-2"></i>Şifre Tekrar <span class="text-danger">*</span>
                            </label>
                            <div class="input-wrapper" id="sifre_tekrar-wrapper">
                                <i class="fas fa-lock"></i>
                                <input type="password" class="form-control" id="sifre_tekrar" name="sifre_tekrar" placeholder="Şifrenizi tekrar girin" required>
                                <span class="toggle-password" onclick="togglePassword('sifre_tekrar')">
                                    <i class="fas fa-eye" id="toggle-icon-sifre_tekrar"></i>
                                </span>
                            </div>
                            <div class="input-error-message" id="sifre_tekrar-error" style="display: none;">
                                <i class="fas fa-exclamation-circle"></i>
                                <span></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="terms" required>
                        <label class="form-check-label" for="terms">
                            <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">Üyelik şartlarını</a> okudum ve kabul ediyorum
                        </label>
                    </div>
                    
                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn-register">
                            <span><i class="fas fa-user-plus me-2"></i>Üye Ol</span>
                        </button>
                    </div>
                </form>
            </div>
            
            <div class="register-footer">
                <p>Zaten hesabınız var mı?</p>
                <a href="login.php" class="btn-login">
                    <i class="fas fa-sign-in-alt me-2"></i>Giriş Yap
                </a>
            </div>
        </div>
        
        <div class="back-link">
            <a href="index.php">
                <i class="fas fa-arrow-left"></i>
                Ana Sayfaya Dön
            </a>
        </div>
    </div>

    <!-- Terms Modal -->
    <div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="termsModalLabel">Üyelik Şartları</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h5>Üyelik Sözleşmesi</h5>
                    <p>Bu Üyelik Sözleşmesi, sitemize üye olmanız durumunda geçerli olacak kuralları içermektedir.</p>
                    
                    <h6>1. Genel Hükümler</h6>
                    <p>
                        Bu sözleşme, E-Ticaret platformuna üye olmak isteyen kullanıcıların üyelik koşullarını ve tarafların karşılıklı hak ve yükümlülüklerini düzenlemektedir.
                        Üyelik işlemi sırasında bu sözleşmedeki koşulları kabul ettiğinizi beyan edersiniz.
                    </p>
                    
                    <h6>2. Üyelik</h6>
                    <p>
                        Üye olmak için gerekli bilgilerin eksiksiz ve doğru olarak doldurulması gerekmektedir. Hatalı veya yanıltıcı bilgi verilmesi durumunda site yönetimi üyeliği sonlandırma hakkına sahiptir.
                        Üyelik bilgilerinizin gizliliği ve güvenliği sizin sorumluluğunuzdadır. Şifrenizin başkaları tarafından kullanılması sonucu oluşabilecek zararlardan site yönetimi sorumlu tutulamaz.
                    </p>
                    
                    <h6>3. Kişisel Veriler</h6>
                    <p>
                        Üyelik sırasında verdiğiniz kişisel bilgileriniz, sitenin kullanımı ve hizmetlerin sunulması amacıyla kullanılacaktır. Bilgileriniz yasal zorunluluklar dışında üçüncü kişilerle paylaşılmayacaktır.
                        Kişisel verilerin korunması hakkında daha fazla bilgi için Gizlilik Politikamızı inceleyebilirsiniz.
                    </p>
                    
                    <h6>4. Üyelik İptali</h6>
                    <p>
                        Üyeliğinizi dilediğiniz zaman sonlandırabilirsiniz. Üyeliğin sonlandırılması durumunda da bu sözleşmedeki yükümlülükleriniz geçerliliğini korur.
                        Site yönetimi, üyelik sözleşmesinin ihlali durumunda önceden bildirmeksizin üyeliğinizi sonlandırma hakkına sahiptir.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle password visibility
        function togglePassword(fieldId) {
            const passwordField = document.getElementById(fieldId);
            const toggleIcon = document.getElementById('toggle-icon-' + fieldId);
            
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
        
        // Show error message for input
        function showInputError(wrapperId, errorId, message) {
            $('#' + wrapperId).addClass('input-error');
            $('#' + errorId + ' span').text(message);
            $('#' + errorId).fadeIn(300);
        }
        
        // Hide error message for input
        function hideInputError(wrapperId, errorId) {
            $('#' + wrapperId).removeClass('input-error');
            $('#' + errorId).fadeOut(300);
        }
        
        $(document).ready(function() {
            // Prevent numbers in name fields
            $('#ad, #soyad').on('input', function(e) {
                let value = $(this).val();
                // Remove any numbers
                value = value.replace(/[0-9]/g, '');
                $(this).val(value);
            });
            
            // Validate name fields (only letters and spaces)
            $('#ad, #soyad').on('blur', function() {
                const value = $(this).val().trim();
                const fieldId = $(this).attr('id');
                const wrapperId = fieldId + '-wrapper';
                const errorId = fieldId + '-error';
                
                if (!value) {
                    showInputError(wrapperId, errorId, 'Bu alan zorunludur.');
                } else if (!/^[A-Za-zÇĞİÖŞÜçğıöşü\s]+$/.test(value)) {
                    showInputError(wrapperId, errorId, 'Bu alana sadece harf girilebilir.');
                } else {
                    hideInputError(wrapperId, errorId);
                }
            });
            
            // Prevent letters and special characters (except +) in phone field
            $('#tel_no').on('input', function(e) {
                let value = $(this).val();
                // Remove any characters except numbers and +
                value = value.replace(/[^0-9+]/g, '');
                $(this).val(value);
            });
            
            // Validate phone field
            $('#tel_no').on('blur', function() {
                const value = $(this).val().trim();
                if (value && !/^[\d+]+$/.test(value)) {
                    showInputError('tel-wrapper', 'tel-error', 'Telefon numarası sadece rakam ve + karakteri içerebilir.');
                } else {
                    hideInputError('tel-wrapper', 'tel-error');
                }
            });
            
            $('#mail').on('blur', function() {
                const value = $(this).val().trim();
                const emailInput = document.getElementById('mail');
                
                if (!value) {
                    showInputError('mail-wrapper', 'mail-error', 'E-posta adresi gereklidir.');
                } else if (!emailInput.validity.valid) {
                    let errorMessage = 'Lütfen geçerli bir e-posta adresi girin.';
                    if (!value.includes('@')) {
                        errorMessage = 'Lütfen e-posta adresine bir "@" işareti ekleyin.';
                    }
                    showInputError('mail-wrapper', 'mail-error', errorMessage);
                } else {
                    hideInputError('mail-wrapper', 'mail-error');
                }
            });
            
            $('#sifre').on('input blur', function() {
                const value = $(this).val();
                
                if (!value) {
                    showInputError('sifre-wrapper', 'sifre-error', 'Şifre gereklidir.');
                } else if (value.length < 8) {
                    showInputError('sifre-wrapper', 'sifre-error', 'Şifreniz en az 8 karakter uzunluğunda olmalıdır.');
                } else {
                    hideInputError('sifre-wrapper', 'sifre-error');
                    
                    // Check if passwords match
                    const sifreTekrar = $('#sifre_tekrar').val();
                    if (sifreTekrar && value !== sifreTekrar) {
                        showInputError('sifre_tekrar-wrapper', 'sifre_tekrar-error', 'Şifreler eşleşmiyor.');
                    } else if (sifreTekrar) {
                        hideInputError('sifre_tekrar-wrapper', 'sifre_tekrar-error');
                    }
                }
            });
            
            $('#sifre_tekrar').on('input blur', function() {
                const value = $(this).val();
                const sifre = $('#sifre').val();
                
                if (!value) {
                    showInputError('sifre_tekrar-wrapper', 'sifre_tekrar-error', 'Şifre tekrar gereklidir.');
                } else if (value !== sifre) {
                    showInputError('sifre_tekrar-wrapper', 'sifre_tekrar-error', 'Şifreler eşleşmiyor.');
                } else {
                    hideInputError('sifre_tekrar-wrapper', 'sifre_tekrar-error');
                }
            });
            
            // Registration form submission
            $('#register-form').submit(function(e) {
                e.preventDefault();
                
                const ad = $('#ad').val().trim();
                const soyad = $('#soyad').val().trim();
                const mail = $('#mail').val().trim();
                const tel_no = $('#tel_no').val().trim();
                const sifre = $('#sifre').val();
                const sifre_tekrar = $('#sifre_tekrar').val();
                
                // Clear previous errors
                $('.input-wrapper').removeClass('input-error');
                $('.input-error-message').hide();
                
                let hasError = false;
                
                // Validate all fields
                if (!ad) {
                    showInputError('ad-wrapper', 'ad-error', 'Adınız gereklidir.');
                    hasError = true;
                } else if (!/^[A-Za-zÇĞİÖŞÜçğıöşü\s]+$/.test(ad)) {
                    showInputError('ad-wrapper', 'ad-error', 'Adınız sadece harf içermelidir.');
                    hasError = true;
                }
                
                if (!soyad) {
                    showInputError('soyad-wrapper', 'soyad-error', 'Soyadınız gereklidir.');
                    hasError = true;
                } else if (!/^[A-Za-zÇĞİÖŞÜçğıöşü\s]+$/.test(soyad)) {
                    showInputError('soyad-wrapper', 'soyad-error', 'Soyadınız sadece harf içermelidir.');
                    hasError = true;
                }
                
                // Validate phone number if provided
                if (tel_no && !/^[\d+]+$/.test(tel_no)) {
                    showInputError('tel-wrapper', 'tel-error', 'Telefon numarası sadece rakam ve + karakteri içerebilir.');
                    hasError = true;
                }
                
                const emailInput = document.getElementById('mail');
                if (!mail) {
                    showInputError('mail-wrapper', 'mail-error', 'E-posta adresi gereklidir.');
                    hasError = true;
                } else if (!emailInput.validity.valid) {
                    showInputError('mail-wrapper', 'mail-error', 'Lütfen geçerli bir e-posta adresi girin.');
                    hasError = true;
                }
                
                if (!sifre) {
                    showInputError('sifre-wrapper', 'sifre-error', 'Şifre gereklidir.');
                    hasError = true;
                } else if (sifre.length < 8) {
                    showInputError('sifre-wrapper', 'sifre-error', 'Şifreniz en az 8 karakter uzunluğunda olmalıdır.');
                    hasError = true;
                }
                
                if (!sifre_tekrar) {
                    showInputError('sifre_tekrar-wrapper', 'sifre_tekrar-error', 'Şifre tekrar gereklidir.');
                    hasError = true;
                } else if (sifre !== sifre_tekrar) {
                    showInputError('sifre_tekrar-wrapper', 'sifre_tekrar-error', 'Şifreler eşleşmiyor.');
                    hasError = true;
                }
                
                if (!$('#terms').is(':checked')) {
                    $('#error-alert').text('Üyelik şartlarını kabul etmelisiniz.').stop(true, true).fadeIn(300);
                    hasError = true;
                }
                
                if (hasError) {
                    return false;
                }
                
                // Hide alerts
                $('#error-alert, #success-alert').stop(true, true).hide();
                
                // Show loading state
                const submitBtn = $(this).find('button[type="submit"]');
                const originalBtnText = submitBtn.html();
                submitBtn.html('<span><i class="fas fa-spinner fa-spin me-2"></i>Kaydınız Yapılıyor...</span>');
                submitBtn.prop('disabled', true);
                
                $.ajax({
                    type: 'POST',
                    url: 'nedmin/netting/islem.php',
                    data: {
                        islem: 'kullanici_kayit',
                        ad: ad,
                        soyad: soyad,
                        mail: mail,
                        tel_no: tel_no,
                        sifre: sifre
                    },
                    dataType: 'json',
                    success: function(response) {
                        // Stop any ongoing animations and hide all alerts first
                        $('#error-alert, #success-alert').stop(true, true).hide();
                        
                        if (response.durum === 'success') {
                            $('#success-alert').text(response.mesaj).stop(true, true).fadeIn(300);
                            $('#register-form')[0].reset();
                            $('.input-wrapper').removeClass('input-error');
                            $('.input-error-message').hide();
                            
                            // Redirect to login page after successful registration
                            setTimeout(function() {
                                window.location.href = 'login.php';
                            }, 2000);
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
        });
    </script>
</body>
</html> 