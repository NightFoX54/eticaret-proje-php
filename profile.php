<?php include 'header.php'; ?>

<div class="container my-5">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Hesabım</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="profile.php" class="list-group-item list-group-item-action active">
                        <i class="fas fa-user me-2"></i> Profilim
                    </a>
                    <a href="orders.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-shopping-bag me-2"></i> Siparişlerim
                    </a>
                    <a href="address.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-map-marker-alt me-2"></i> Adreslerim
                    </a>
                    <a href="wishlist.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-heart me-2"></i> Favorilerim
                    </a>
                    <a href="#" class="list-group-item list-group-item-action text-danger logout-link">
                        <i class="fas fa-sign-out-alt me-2"></i> Çıkış Yap
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-md-9">
            <!-- Alert Messages -->
            <div class="alert alert-success" id="successAlert" style="display:none;" role="alert"></div>
            <div class="alert alert-danger" id="errorAlert" style="display:none;" role="alert"></div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Profil Bilgilerim</h5>
                </div>
                <div class="card-body">
                    <form id="profileUpdateForm">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="ad" class="form-label">Ad</label>
                                <input type="text" class="form-control" id="ad" name="ad">
                            </div>
                            <div class="col-md-6">
                                <label for="soyad" class="form-label">Soyad</label>
                                <input type="text" class="form-control" id="soyad" name="soyad">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="mail" class="form-label">E-posta</label>
                                <input type="email" class="form-control" id="mail" name="mail" readonly>
                                <small class="text-muted">E-posta adresi değiştirilemez.</small>
                            </div>
                            <div class="col-md-6">
                                <label for="tel_no" class="form-label">Telefon Numarası</label>
                                <input type="text" class="form-control" id="tel_no" name="tel_no" placeholder="05XX XXX XX XX">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary" id="updateProfileBtn">Bilgilerimi Güncelle</button>
                    </form>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Şifre Değiştir</h5>
                </div>
                <div class="card-body">
                    <form id="passwordUpdateForm">
                        <div class="mb-3">
                            <label for="currentPassword" class="form-label">Mevcut Şifre</label>
                            <input type="password" class="form-control" id="currentPassword" name="current_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="newPassword" class="form-label">Yeni Şifre</label>
                            <input type="password" class="form-control" id="newPassword" name="new_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirmPassword" class="form-label">Yeni Şifre (Tekrar)</label>
                            <input type="password" class="form-control" id="confirmPassword" name="confirm_password" required>
                            <div class="invalid-feedback" id="passwordMismatch">Şifreler eşleşmiyor!</div>
                        </div>
                        <button type="submit" class="btn btn-warning" id="updatePasswordBtn">Şifremi Güncelle</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Check authentication
    checkAuth();
    
    // Load user profile data
    loadUserProfile();
    
    // Update Profile Form Submit
    $('#profileUpdateForm').on('submit', function(e) {
        e.preventDefault();
        updateProfile();
    });
    
    // Password Update Form Submit
    $('#passwordUpdateForm').on('submit', function(e) {
        e.preventDefault();
        
        // Validate passwords match
        const newPassword = $('#newPassword').val();
        const confirmPassword = $('#confirmPassword').val();
        
        if (newPassword !== confirmPassword) {
            $('#confirmPassword').addClass('is-invalid');
            $('#passwordMismatch').show();
            return;
        } else {
            $('#confirmPassword').removeClass('is-invalid');
            $('#passwordMismatch').hide();
        }
        
        updatePassword();
    });
    
    // Remove validation on input
    $('#newPassword, #confirmPassword').on('input', function() {
        $('#confirmPassword').removeClass('is-invalid');
        $('#passwordMismatch').hide();
    });
    
    // Check if user is authenticated
    function checkAuth() {
        $.ajax({
            type: "GET",
            url: "nedmin/netting/islem.php",
            data: {
                islem: "check_auth"
            },
            dataType: "json",
            success: function(response) {
                if (response.durum !== 'success') {
                    window.location.href = 'login.php';
                }
            },
            error: function() {
                window.location.href = 'login.php';
            }
        });
    }
    
    // Load user profile data
    function loadUserProfile() {
        $.ajax({
            type: "GET",
            url: "nedmin/netting/islem.php",
            data: {
                islem: "get_profile"
            },
            dataType: "json",
            success: function(response) {
                if (response.durum === 'success') {
                    const user = response.kullanici;
                    $('#ad').val(user.ad);
                    $('#soyad').val(user.soyad);
                    $('#mail').val(user.mail);
                    $('#tel_no').val(user.tel_no);
                } else {
                    showError(response.mesaj || 'Profil bilgileri yüklenirken bir hata oluştu.');
                }
            },
            error: function() {
                showError('Sunucu hatası: Profil bilgileri yüklenemedi.');
            }
        });
    }
    
    // Update profile function
    function updateProfile() {
        const formData = {
            islem: "profile_guncelle",
            ad: $('#ad').val(),
            soyad: $('#soyad').val(),
            tel_no: $('#tel_no').val()
        };
        
        $.ajax({
            type: "POST",
            url: "nedmin/netting/islem.php",
            data: formData,
            dataType: "json",
            success: function(response) {
                if (response.durum === 'success') {
                    showSuccess(response.mesaj || 'Profil bilgileriniz başarıyla güncellendi.');
                } else {
                    showError(response.mesaj || 'Profil güncellenirken bir hata oluştu.');
                }
            },
            error: function() {
                showError('Sunucu hatası: Profiliniz güncellenemedi.');
            }
        });
    }
    
    // Update password function
    function updatePassword() {
        const formData = {
            islem: "sifre_guncelle",
            current_password: $('#currentPassword').val(),
            new_password: $('#newPassword').val(),
            confirm_password: $('#confirmPassword').val()
        };
        
        $.ajax({
            type: "POST",
            url: "nedmin/netting/islem.php",
            data: formData,
            dataType: "json",
            success: function(response) {
                if (response.durum === 'success') {
                    showSuccess(response.mesaj || 'Şifreniz başarıyla güncellendi.');
                    $('#passwordUpdateForm')[0].reset();
                } else {
                    showError(response.mesaj || 'Şifre güncellenirken bir hata oluştu.');
                }
            },
            error: function() {
                showError('Sunucu hatası: Şifreniz güncellenemedi.');
            }
        });
    }
    
    // Show success message
    function showSuccess(message) {
        $('#successAlert').text(message).fadeIn();
        setTimeout(function() {
            $('#successAlert').fadeOut();
        }, 3000);
    }
    
    // Show error message
    function showError(message) {
        $('#errorAlert').text(message).fadeIn();
        setTimeout(function() {
            $('#errorAlert').fadeOut();
        }, 3000);
    }
});
</script>

<?php include 'footer.php'; ?> 