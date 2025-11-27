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
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 0;
        }
        .register-container {
            max-width: 650px;
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
        .btn-register {
            background-color: #4e73df;
            border-color: #4e73df;
        }
        .btn-register:hover {
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
    <div class="register-container">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0"><i class="fas fa-store me-2"></i> E-Ticaret</h4>
                <p class="mb-0 mt-2">Yeni Hesap Oluştur</p>
            </div>
            <div class="card-body">
                <div class="alert alert-danger" id="error-alert" role="alert"></div>
                <div class="alert alert-success" id="success-alert" role="alert"></div>
                
                <form id="register-form">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="ad" class="form-label">Adınız <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control" id="ad" name="ad" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="soyad" class="form-label">Soyadınız <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control" id="soyad" name="soyad" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="mail" class="form-label">E-posta Adresiniz <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control" id="mail" name="mail" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="tel_no" class="form-label">Telefon Numarası</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                <input type="tel" class="form-control" id="tel_no" name="tel_no" placeholder="İsteğe Bağlı">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="sifre" class="form-label">Şifre <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="sifre" name="sifre" required>
                                <span class="input-group-text toggle-password" style="cursor: pointer;">
                                    <i class="fas fa-eye"></i>
                                </span>
                            </div>
                            <div class="form-text">Şifreniz en az 8 karakter uzunluğunda olmalıdır.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="sifre_tekrar" class="form-label">Şifre Tekrar <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="sifre_tekrar" name="sifre_tekrar" required>
                                <span class="input-group-text toggle-password" style="cursor: pointer;">
                                    <i class="fas fa-eye"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="terms" required>
                        <label class="form-check-label" for="terms">
                            <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">Üyelik şartlarını</a> okudum ve kabul ediyorum
                        </label>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-register btn-lg">
                            <i class="fas fa-user-plus me-2"></i> Üye Ol
                        </button>
                    </div>
                </form>
            </div>
            <div class="card-footer text-center py-3">
                <div class="text-muted mb-2">Zaten hesabınız var mı?</div>
                <a href="login.php" class="btn btn-outline-primary">
                    <i class="fas fa-sign-in-alt me-2"></i> Giriş Yap
                </a>
            </div>
        </div>
        <div class="text-center mt-3">
            <a href="index.php" class="text-decoration-none">
                <i class="fas fa-home me-1"></i> Ana Sayfaya Dön
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
        $(document).ready(function() {
            // Show/hide password
            $('.toggle-password').click(function() {
                const passwordField = $(this).closest('.input-group').find('input');
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
            
            // Registration form submission
            $('#register-form').submit(function(e) {
                e.preventDefault();
                
                const ad = $('#ad').val().trim();
                const soyad = $('#soyad').val().trim();
                const mail = $('#mail').val().trim();
                const tel_no = $('#tel_no').val().trim();
                const sifre = $('#sifre').val();
                const sifre_tekrar = $('#sifre_tekrar').val();
                
                // Hide alerts
                $('#error-alert, #success-alert').hide();
                
                // Basic validation
                if (!ad || !soyad || !mail || !sifre) {
                    $('#error-alert').text('Lütfen zorunlu alanları doldurun.').slideDown();
                    return;
                }
                
                if (sifre.length < 8) {
                    $('#error-alert').text('Şifreniz en az 8 karakter uzunluğunda olmalıdır.').slideDown();
                    return;
                }
                
                if (sifre !== sifre_tekrar) {
                    $('#error-alert').text('Şifreler eşleşmiyor.').slideDown();
                    return;
                }
                
                if (!$('#terms').is(':checked')) {
                    $('#error-alert').text('Üyelik şartlarını kabul etmelisiniz.').slideDown();
                    return;
                }
                
                // Show loading state
                const submitBtn = $(this).find('button[type="submit"]');
                const originalBtnText = submitBtn.html();
                submitBtn.html('<i class="fas fa-spinner fa-spin me-2"></i> Kaydınız Yapılıyor...');
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
                        if (response.durum === 'success') {
                            $('#success-alert').text(response.mesaj).slideDown();
                            $('#register-form')[0].reset();
                            
                            // Redirect to login page after successful registration
                            setTimeout(function() {
                                window.location.href = 'login.php';
                            }, 2000);
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