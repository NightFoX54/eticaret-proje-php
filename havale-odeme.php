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
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-check-circle me-2"></i>Siparişiniz Alındı!
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Order Confirmation -->
                    <div class="alert alert-success">
                        <h5><i class="fas fa-check-circle me-2"></i>Siparişiniz Başarıyla Oluşturuldu</h5>
                        <p class="mb-0">Siparişiniz sistemimize kaydedildi. Ödemenizi aşağıdaki banka hesaplarından birine yapabilirsiniz.</p>
                    </div>

                    <!-- Order Summary -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Sipariş Bilgileri</h5>
                            <div id="order-details">
                                <!-- Order details will be loaded here -->
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5>Ödeme Bilgileri</h5>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Siparişinizi onaylamak için aşağıdaki hesaplardan birine havale yapabilirsiniz.
                            </div>
                        </div>
                    </div>

                    <!-- Important Notice -->
                    <div class="alert alert-warning">
                        <h6><i class="fas fa-exclamation-triangle me-2"></i>Önemli Bilgiler</h6>
                        <ul class="mb-0">
                            <li>Havale yaparken açıklama kısmına <strong>sipariş numaranızı</strong> yazmayı unutmayın.</li>
                            <li>Ödemeniz kontrol edildikten sonra siparişiniz hazırlanmaya başlanacaktır.</li>
                            <li>Ödeme onayı 1-2 iş günü içerisinde yapılacaktır.</li>
                            <li><strong>Ödeme 3 gün içinde yapılmazsa siparişiniz otomatik olarak iptal edilecektir.</strong></li>
                            <li>Herhangi bir sorunuz için müşteri hizmetlerimizle iletişime geçebilirsiniz.</li>
                        </ul>
                    </div>

                    <!-- Bank Accounts -->
                    <div class="mb-4">
                        <h5>Banka Hesapları</h5>
                        <div id="bank-accounts">
                            <!-- Bank accounts will be loaded here -->
                        </div>
                    </div>

                    <!-- Navigation -->
                    <div class="text-center">
                        <a href="orders.php" class="btn btn-primary btn-lg">
                            <i class="fas fa-list me-2"></i>Siparişlerim
                        </a>
                        <a href="index.php" class="btn btn-secondary btn-lg ms-2">
                            <i class="fas fa-shopping-cart me-2"></i>Alışverişe Devam Et
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.bank-account-card {
    border: 2px solid #e9ecef;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 15px;
    transition: all 0.3s ease;
}

.bank-account-card:hover {
    border-color: #007bff;
    box-shadow: 0 0 10px rgba(0, 123, 255, 0.1);
}

.bank-info {
    display: flex;
    justify-content-between;
    align-items: center;
    margin-bottom: 10px;
}

.bank-name {
    font-weight: bold;
    color: #007bff;
}

.account-details {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    margin-top: 10px;
}

.copy-btn {
    cursor: pointer;
    color: #007bff;
    transition: color 0.3s ease;
}

.copy-btn:hover {
    color: #0056b3;
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
</style>

<script>
let orderData = null;

$(document).ready(function() {
    loadOrderDetails();
    loadBankAccounts();
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
    
    let html = `
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
            <strong>Ara Toplam:</strong> ${orderData.ara_toplam} TL<br>
            <strong>İndirim:</strong> ${orderData.indirim_tutari} TL<br>
            <strong>Toplam:</strong> ${orderData.toplam} TL<br>
        </div>
        <div class="mb-3">
            <strong>Teslimat Adresi:</strong><br>
            ${orderData.adres.ad_soyad}<br>
            ${orderData.adres.telefon}<br>
            ${orderData.adres.il} / ${orderData.adres.ilce}<br>
            ${orderData.adres.adres}
        </div>
    `;
    
    $('#order-details').html(html);
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

function loadBankAccounts() {
    $.ajax({
        url: 'nedmin/netting/islem.php',
        type: 'POST',
        data: { islem: 'banka_hesaplari_getir' },
        dataType: 'json',
        success: function(response) {
            if (response.durum === 'success') {
                displayBankAccounts(response.data);
            }
        }
    });
}

function displayBankAccounts(accounts) {
    let html = '';
    
    accounts.forEach(function(account, index) {
        if (account.durum == 1) {
            html += `
                <div class="bank-account-card">
                    <div class="bank-info">
                        <div>
                            <div class="bank-name">${account.banka_adi}</div>
                            <div class="text-muted">${account.hesap_sahibi}</div>
                        </div>
                    </div>
                    
                    <div class="account-details">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>IBAN:</strong><br>
                                <code>${account.iban}</code>
                                <i class="fas fa-copy copy-btn ms-2" onclick="copyToClipboard('${account.iban}')" title="Kopyala"></i>
                            </div>
                            <div class="col-md-6">
                                <strong>Hesap No:</strong><br>
                                <code>${account.hesap_no || 'Belirtilmemiş'}</code>
                                ${account.hesap_no ? `<i class="fas fa-copy copy-btn ms-2" onclick="copyToClipboard('${account.hesap_no}')" title="Kopyala"></i>` : ''}
                            </div>
                        </div>
                        ${account.sube_adi ? `
                        <div class="mt-2">
                            <strong>Şube:</strong> ${account.sube_adi}
                            ${account.sube_kodu ? ` (${account.sube_kodu})` : ''}
                        </div>
                        ` : ''}
                    </div>
                </div>
            `;
        }
    });
    
    $('#bank-accounts').html(html);
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        showMessage('success', 'Kopyalandı!');
    }).catch(function() {
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        showMessage('success', 'Kopyalandı!');
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
    $('.container').prepend(alertHtml);
    
    // Auto hide after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut();
    }, 5000);
}
</script>

<?php include 'footer.php'; ?> 