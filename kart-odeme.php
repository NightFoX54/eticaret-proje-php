<?php
include 'header.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Redirect if user is not logged in
if(!isset($_SESSION['kullanici_id'])) {
    header("Location: login.php?durum=giris");
    exit;
}

// Check if order ID is provided
if(!isset($_GET['siparis_id'])) {
    header("Location: cart.php");
    exit;
}

$siparis_id = intval($_GET['siparis_id']);
$kullanici_id = $_SESSION['kullanici_id'];


?>

<div class="container mt-4 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-credit-card me-2"></i>Kredi Kartı ile Ödeme
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Order Summary -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Sipariş Bilgileri</h5>
                            <div id="order-details">
                                <!-- Order details will be loaded here -->
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5>Ödeme Tutarı</h5>
                            <div id="payment-amount">
                                <!-- Payment amount will be loaded here -->
                            </div>
                        </div>
                    </div>

                    <!-- Payment Form -->
                    <div class="card payment-card">
                        <div class="card-body">
                            <div class="text-center mb-4">
                                <i class="fas fa-credit-card fa-4x text-primary"></i>
                                <p class="text-muted mt-2">Güvenli ödeme için kart bilgilerinizi giriniz</p>
                            </div>

                            <form id="card-payment-form">
                                <div class="mb-3">
                                    <label for="card_number" class="form-label">Kart Numarası *</label>
                                    <input type="text" class="form-control" id="card_number" name="card_number" 
                                           placeholder="1234 5678 9012 3456" maxlength="19" required>
                                    <small class="text-muted">16 haneli kart numaranızı giriniz</small>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="card_expiry" class="form-label">Son Kullanma Tarihi *</label>
                                        <input type="text" class="form-control" id="card_expiry" name="card_expiry" 
                                               placeholder="MM/YY" maxlength="5" required>
                                        <small class="text-muted">Ay/Yıl formatında giriniz</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="card_cvv" class="form-label">CVV *</label>
                                        <input type="text" class="form-control" id="card_cvv" name="card_cvv" 
                                               placeholder="123" maxlength="4" required>
                                        <small class="text-muted">Kartınızın arkasındaki 3 haneli güvenlik kodu</small>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="card_name" class="form-label">Kart Üzerindeki İsim *</label>
                                    <input type="text" class="form-control" id="card_name" name="card_name" 
                                           placeholder="AD SOYAD" required>
                                    <small class="text-muted">Kartınızın üzerinde yazan isim</small>
                                </div>

                                <div class="alert alert-info">
                                    <i class="fas fa-lock me-2"></i>
                                    <strong>Güvenli Ödeme:</strong> Kart bilgileriniz şifrelenerek işlenmektedir.
                                </div>

                                <div class="text-center mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg" id="pay-button">
                                        <i class="fas fa-lock me-2"></i>Ödemeyi Tamamla
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.payment-card {
    border: 2px solid #e9ecef;
    border-radius: 10px;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
}

.payment-card .card-body {
    padding: 30px;
}

.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.order-status {
    display: inline-block;
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
}

.status-beklemede {
    background-color: #fff3cd;
    color: #856404;
}

.status-onaylandi {
    background-color: #d4edda;
    color: #155724;
}

#card_number {
    font-size: 18px;
    letter-spacing: 2px;
    font-family: 'Courier New', monospace;
}

#card_expiry {
    font-size: 16px;
    letter-spacing: 1px;
}

#card_cvv {
    font-size: 16px;
    letter-spacing: 1px;
}

#card_name {
    text-transform: uppercase;
}
</style>

<script>
let orderData = null;

$(document).ready(function() {
    loadOrderDetails();
    
    // Card number formatting
    $('#card_number').on('input', function() {
        let value = $(this).val().replace(/\s/g, '');
        let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
        if (formattedValue.length <= 19) {
            $(this).val(formattedValue);
        }
    });
    
    // Expiry date formatting
    $('#card_expiry').on('input', function() {
        let value = $(this).val().replace(/\D/g, '');
        if (value.length >= 2) {
            value = value.substring(0, 2) + '/' + value.substring(2, 4);
        }
        $(this).val(value);
    });
    
    // CVV formatting (only numbers)
    $('#card_cvv').on('input', function() {
        $(this).val($(this).val().replace(/\D/g, ''));
    });
    
    // Card name formatting (uppercase)
    $('#card_name').on('input', function() {
        $(this).val($(this).val().toUpperCase());
    });
    
    // Form submission
    $('#card-payment-form').on('submit', function(e) {
        e.preventDefault();
        processPayment();
    });
});

function loadOrderDetails() {
    $.ajax({
        url: 'nedmin/netting/islem.php',
        type: 'POST',
        data: {
            islem: 'siparis_detay_getir',
            siparis_id: <?php echo $siparis_id; ?>
        },
        dataType: 'json',
        success: function(response) {
            if (response.durum === 'success') {
                orderData = response.data;
                displayOrderDetails();
            } else {
                showMessage('error', response.mesaj);
            }
        },
        error: function() {
            showMessage('error', 'Sipariş bilgileri yüklenirken hata oluştu');
        }
    });
}

function displayOrderDetails() {
    if (!orderData) return;
    
    let orderHtml = `
        <div class="mb-3">
            <strong>Sipariş No:</strong> ${orderData.fatura_no}
        </div>
        <div class="mb-3">
            <strong>Tarih:</strong> ${new Date(orderData.tarih).toLocaleDateString('tr-TR')}
        </div>
        <div class="mb-3">
            <strong>Durum:</strong> 
            <span class="order-status status-${orderData.siparis_durumu}">
                ${getStatusText(orderData.siparis_durumu)}
            </span>
        </div>
        <div class="mb-3">
            <strong>Teslimat Adresi:</strong><br>
            ${orderData.adres.ad_soyad}<br>
            ${orderData.adres.telefon}<br>
            ${orderData.adres.il} / ${orderData.adres.ilce}<br>
            ${orderData.adres.adres}
        </div>
    `;
    
    let paymentHtml = `
        <div class="alert alert-primary">
            <h4 class="mb-0">${orderData.toplam} TL</h4>
            <small>Ödenecek Tutar</small>
        </div>
        <div class="mb-2">
            <small class="text-muted">Ara Toplam: ${orderData.ara_toplam} TL</small>
        </div>
        ${orderData.indirim_tutari > 0 ? `
        <div class="mb-2">
            <small class="text-muted">İndirim: -${orderData.indirim_tutari} TL</small>
        </div>
        ` : ''}
    `;
    
    $('#order-details').html(orderHtml);
    $('#payment-amount').html(paymentHtml);
}

function getStatusText(status) {
    const statusMap = {
        'beklemede': 'Ödeme Bekleniyor',
        'onaylandi': 'Onaylandı',
        'hazirlaniyor': 'Hazırlanıyor',
        'kargolandi': 'Kargolandı',
        'teslim_edildi': 'Teslim Edildi',
        'iptal_edildi': 'İptal Edildi'
    };
    return statusMap[status] || status;
}

function validateCardForm() {
    const cardNumber = $('#card_number').val().replace(/\s/g, '');
    const cardExpiry = $('#card_expiry').val();
    const cardCvv = $('#card_cvv').val();
    const cardName = $('#card_name').val().trim();
    
    // Validate card number (16 digits)
    if (cardNumber.length !== 16 || !/^\d+$/.test(cardNumber)) {
        showMessage('error', 'Lütfen geçerli bir kart numarası giriniz (16 haneli)');
        return false;
    }
    
    // Validate expiry date (MM/YY format)
    const expiryRegex = /^(0[1-9]|1[0-2])\/([0-9]{2})$/;
    if (!expiryRegex.test(cardExpiry)) {
        showMessage('error', 'Lütfen geçerli bir son kullanma tarihi giriniz (MM/YY formatında)');
        return false;
    }
    
    // Check if expiry date is not in the past
    const [month, year] = cardExpiry.split('/');
    const expiryDate = new Date(2000 + parseInt(year), parseInt(month) - 1);
    const now = new Date();
    if (expiryDate < now) {
        showMessage('error', 'Kartınızın son kullanma tarihi geçmiş');
        return false;
    }
    
    // Validate CVV (3 or 4 digits)
    if (cardCvv.length < 3 || cardCvv.length > 4 || !/^\d+$/.test(cardCvv)) {
        showMessage('error', 'Lütfen geçerli bir CVV kodu giriniz (3-4 haneli)');
        return false;
    }
    
    // Validate card name
    if (cardName.length < 3) {
        showMessage('error', 'Lütfen kart üzerindeki ismi giriniz');
        return false;
    }
    
    return true;
}

function processPayment() {
    if (!validateCardForm()) {
        return;
    }
    
    const cardNumber = $('#card_number').val().replace(/\s/g, '');
    const cardExpiry = $('#card_expiry').val();
    const cardCvv = $('#card_cvv').val();
    const cardName = $('#card_name').val().trim();
    
    // Disable button and show loading
    $('#pay-button').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>İşleniyor...');
    
    // Process payment via AJAX
    $.ajax({
        url: 'nedmin/netting/islem.php',
        type: 'POST',
        data: {
            islem: 'siparis_odeme_onayla',
            siparis_id: <?php echo $siparis_id; ?>,
            odeme_yontemi: 'kart'
        },
        dataType: 'json',
        success: function(response) {
            if (response.durum === 'success') {
                showMessage('success', 'Ödemeniz başarıyla tamamlandı!');
                // Redirect to orders page after 2 seconds
                setTimeout(function() {
                    window.location.href = 'odeme-basarili.php';
                }, 2000);
            } else {
                showMessage('error', response.mesaj);
                $('#pay-button').prop('disabled', false).html('<i class="fas fa-lock me-2"></i>Ödemeyi Tamamla');
            }
        },
        error: function() {
            showMessage('error', 'Ödeme işlemi sırasında bir hata oluştu. Lütfen tekrar deneyiniz.');
            $('#pay-button').prop('disabled', false).html('<i class="fas fa-lock me-2"></i>Ödemeyi Tamamla');
        }
    });
}

function showMessage(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const icon = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-triangle';
    
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            <i class="${icon} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    // Show alert at the top of the page
    $('.container.mt-4.mb-5').prepend(alertHtml);
    
    // Auto hide after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut();
    }, 5000);
}
</script>

<?php include 'footer.php'; ?>

