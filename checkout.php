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



$kullanici_id = $_SESSION['kullanici_id'];
?>

<div class="container mt-4 mb-5">
    <div class="row">
        <!-- Checkout Form -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-shopping-cart me-2"></i>Ödeme Bilgileri
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Progress Steps -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="progress-steps">
                                <div class="step active" id="step-1">
                                    <div class="step-number">1</div>
                                    <div class="step-title">Adres Seçimi</div>
                                </div>
                                <div class="step" id="step-2">
                                    <div class="step-number">2</div>
                                    <div class="step-title">Ödeme Yöntemi</div>
                                </div>
                                <div class="step" id="step-3">
                                    <div class="step-number">3</div>
                                    <div class="step-title">Onay</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 1: Address Selection -->
                    <div id="step-1-content" class="checkout-step active">
                        <h5 class="mb-3">Teslimat Adresi</h5>
                        
                        <div id="addresses-container">
                            <!-- Addresses will be loaded here -->
                        </div>
                        
                        <div class="mt-3">
                            <button type="button" class="btn btn-outline-primary" onclick="showAddAddressModal()">
                                <i class="fas fa-plus me-2"></i>Yeni Adres Ekle
                            </button>
                        </div>
                        
                        <div class="mt-4">
                            <button type="button" class="btn btn-primary" onclick="nextStep()" id="next-step-1" style="background: linear-gradient(135deg, #9D7FC7 0%, #8B6FC7 100%); border: none;">
                                Devam Et <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Step 2: Payment Method -->
                    <div id="step-2-content" class="checkout-step" style="display: none;">
                        <h5 class="mb-3">Ödeme Yöntemi</h5>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="payment-method-card" onclick="selectPaymentMethod('havale')">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <i class="fas fa-university fa-3x mb-3" style="color: #9D7FC7;"></i>
                                            <h6>Havale/EFT</h6>
                                            <p class="text-muted small">Banka hesaplarımıza havale yapabilirsiniz</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="payment-method-card" onclick="selectPaymentMethod('kart')">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <i class="fas fa-credit-card fa-3x mb-3" style="color: #8B6FC7;"></i>
                                            <h6>Kredi Kartı</h6>
                                            <p class="text-muted small">Güvenli ödeme ile kartınızla ödeyin</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <button type="button" class="btn btn-secondary" onclick="prevStep()">
                                <i class="fas fa-arrow-left me-2"></i>Geri
                            </button>
                            <button type="button" class="btn btn-primary" onclick="nextStep()" id="next-step-2" disabled style="background: linear-gradient(135deg, #9D7FC7 0%, #8B6FC7 100%); border: none;">
                                Devam Et <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Step 3: Order Confirmation -->
                    <div id="step-3-content" class="checkout-step" style="display: none;">
                        <h5 class="mb-3">Sipariş Onayı</h5>
                        
                        <div id="order-summary">
                            <!-- Order summary will be loaded here -->
                        </div>
                        
                        <div class="mt-4">
                            <button type="button" class="btn btn-secondary" onclick="prevStep()">
                                <i class="fas fa-arrow-left me-2"></i>Geri
                            </button>
                            <button type="button" class="btn btn-success" onclick="placeOrder()" id="place-order-btn" style="background: linear-gradient(135deg, #8B6FC7 0%, #7A5FB8 100%); border: none;">
                                <i class="fas fa-check me-2"></i>Siparişi Onayla
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cart Summary -->
        <div class="col-lg-4">
            <div class="card shadow-sm sticky-top" style="top: 20px;">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-shopping-bag me-2"></i>Sepet Özeti
                    </h5>
                </div>
                <div class="card-body">
                    <div id="cart-summary">
                        <!-- Cart summary will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Address Modal -->
<div class="modal fade" id="addAddressModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Yeni Adres Ekle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addAddressForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="adres_adi" class="form-label">Adres Adı *</label>
                                <input type="text" class="form-control" id="adres_adi" name="adres_adi" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="ad_soyad" class="form-label">Ad Soyad *</label>
                                <input type="text" class="form-control" id="ad_soyad" name="ad_soyad" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="telefon" class="form-label">Telefon *</label>
                                <input type="tel" class="form-control" id="telefon" name="telefon" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tc_no" class="form-label">TC Kimlik No *</label>
                                <input type="text" class="form-control" id="tc_no" name="tc_no" maxlength="11" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="il" class="form-label">İl *</label>
                                <input type="text" class="form-control" id="il" name="il" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="ilce" class="form-label">İlçe *</label>
                                <input type="text" class="form-control" id="ilce" name="ilce" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="adres" class="form-label">Adres *</label>
                        <textarea class="form-control" id="adres" name="adres" rows="3" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="posta_kodu" class="form-label">Posta Kodu</label>
                        <input type="text" class="form-control" id="posta_kodu" name="posta_kodu">
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="varsayilan" name="varsayilan">
                        <label class="form-check-label" for="varsayilan">
                            Bu adresi varsayılan adres olarak ayarla
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">Adres Ekle</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.progress-steps {
    display: flex;
    justify-content: space-between;
    margin-bottom: 30px;
}

.step {
    display: flex;
    flex-direction: column;
    align-items: center;
    flex: 1;
    position: relative;
}

.step:not(:last-child)::after {
    content: '';
    position: absolute;
    top: 15px;
    left: 50%;
    width: 100%;
    height: 2px;
    background-color: #e9ecef;
    z-index: -1;
}

.step.active:not(:last-child)::after {
    background: linear-gradient(90deg, #9D7FC7 0%, #8B6FC7 100%);
}

.step-number {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background-color: #e9ecef;
    color: #6c757d;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    margin-bottom: 5px;
}

.step.active .step-number {
    background: linear-gradient(135deg, #9D7FC7 0%, #8B6FC7 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(177, 156, 217, 0.4);
}

.step-title {
    font-size: 12px;
    text-align: center;
    color: #6c757d;
}

.step.active .step-title {
    color: #8B6FC7;
    font-weight: bold;
}

.checkout-step {
    display: none;
}

.checkout-step.active {
    display: block;
}

.payment-method-card {
    cursor: pointer;
    transition: all 0.3s ease;
}

.payment-method-card:hover {
    transform: translateY(-2px);
}

.payment-method-card.selected .card {
    border-color: #9D7FC7;
    box-shadow: 0 0 0 0.2rem rgba(177, 156, 217, 0.25);
    background: linear-gradient(135deg, #E8DFF0 0%, #D9CEE8 100%);
}

.address-card {
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.address-card:hover {
    border-color: #9D7FC7;
}

.address-card.selected {
    border-color: #9D7FC7;
    background: linear-gradient(135deg, #E8DFF0 0%, #D9CEE8 100%);
    box-shadow: 0 4px 12px rgba(177, 156, 217, 0.2);
}

@media (max-width: 768px) {
    .sticky-top {
        position: static !important;
        margin-top: 20px;
    }
}
</style>

<script>
let currentStep = 1;
let selectedAddress = null;
let selectedPaymentMethod = null;
let cartItems = [];
let cartTotal = 0;

$(document).ready(function() {
    loadCartSummary();
    loadAddresses();
    
    // Add address form submission
    $('#addAddressForm').on('submit', function(e) {
        e.preventDefault();
        addNewAddress();
    });
});

function loadCartSummary() {
    $.ajax({
        url: 'nedmin/netting/islem.php',
        type: 'POST',
        data: { islem: 'sepet_ozeti_getir' },
        dataType: 'json',
        success: function(response) {
            if (response.durum === 'success') {
                cartItems = response.urunler;
                cartTotal = response.toplam;
                cartIndirim = response.indirim;
                cartFinal = response.final;
                displayCartSummary();
            } else {
                showMessage('error', response.mesaj);
                // Redirect to cart if empty
                //window.location.href = 'cart.php';
            }
        },
        error: function() {
            showMessage('error', response.mesaj);
            //window.location.href = 'cart.php';
        }
    });
}

function displayCartSummary() {
    let html = '';
    
    cartItems.forEach(function(item) {
        html += `
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                    <h6 class="mb-0">${item.urun_isim}</h6>
                    <small class="text-muted">${item.secenek_aciklama} - ${item.adet} adet</small>
                </div>
                <span class="fw-bold">${item.toplam_fiyat} TL</span>
            </div>
        `;
    });
    
    html += `
        <hr>
        <div class="d-flex justify-content-between">
            <span>Ara Toplam:</span>
            <span>${cartTotal} TL</span>
        </div>
        <div class="d-flex justify-content-between">
            <span>İndirim:</span>
            <span>${cartIndirim} TL</span>
        </div>
        <div class="d-flex justify-content-between">
            <span>Kargo:</span>
            <span>Ücretsiz</span>
        </div>
        <hr>
        <div class="d-flex justify-content-between fw-bold">
            <span>Toplam:</span>
            <span>${cartFinal} TL</span>
        </div>
    `;
    
    $('#cart-summary').html(html);
}

function loadAddresses() {
    $.ajax({
        url: 'nedmin/netting/islem.php',
        type: 'POST',
        data: { islem: 'adresler_getir' },
        dataType: 'json',
        success: function(response) {
            if (response.durum === 'success') {
                displayAddresses(response.adresler);
            }
        }
    });
}

function displayAddresses(addresses) {
    let html = '';
    
    addresses.forEach(function(address) {
        html += `
            <div class="address-card card mb-3" onclick="selectAddress(${address.id})" data-address-id="${address.id}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1">${address.adres_adi}</h6>
                            <p class="mb-1"><strong>${address.ad_soyad}</strong></p>
                            <p class="mb-1">${address.telefon}</p>
                            <p class="mb-1">${address.il} / ${address.ilce}</p>
                            <p class="mb-0">${address.adres}</p>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="selectedAddress" value="${address.id}">
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    $('#addresses-container').html(html);
}

function selectAddress(addressId) {
    $('.address-card').removeClass('selected');
    $(`.address-card[data-address-id="${addressId}"]`).addClass('selected');
    $(`input[value="${addressId}"]`).prop('checked', true);
    selectedAddress = addressId;
    $('#next-step-1').prop('disabled', false);
}

function showAddAddressModal() {
    $('#addAddressModal').modal('show');
}

function addNewAddress() {
    const formData = new FormData($('#addAddressForm')[0]);
    formData.append('islem', 'add_address');
    
    $.ajax({
        url: 'nedmin/netting/islem.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.durum === 'success') {
                $('#addAddressModal').modal('hide');
                $('#addAddressForm')[0].reset();
                loadAddresses();
                showMessage('success', 'Adres başarıyla eklendi');
            } else {
                showMessage('error', response.mesaj);
            }
        }
    });
}

function selectPaymentMethod(method) {
    $('.payment-method-card').removeClass('selected');
    $(`.payment-method-card`).each(function() {
        if ($(this).find('h6').text().toLowerCase().includes(method)) {
            $(this).addClass('selected');
        }
    });
    selectedPaymentMethod = method;
    $('#next-step-2').prop('disabled', false);
}

function nextStep() {
    if (currentStep === 1) {
        if (!selectedAddress) {
            showMessage('error', 'Lütfen bir adres seçin');
            return;
        }
        currentStep = 2;
        showStep(2);
    } else if (currentStep === 2) {
        if (!selectedPaymentMethod) {
            showMessage('error', 'Lütfen bir ödeme yöntemi seçin');
            return;
        }
        currentStep = 3;
        showStep(3);
        loadOrderSummary();
    }
}

function prevStep() {
    if (currentStep > 1) {
        currentStep--;
        showStep(currentStep);
    }
}

function showStep(step) {
    $('.checkout-step').removeClass('active').hide();
    $(`#step-${step}-content`).addClass('active').show();
    
    $('.step').removeClass('active');
    for (let i = 1; i <= step; i++) {
        $(`#step-${i}`).addClass('active');
    }
}

function loadOrderSummary() {
    $.ajax({
        url: 'nedmin/netting/islem.php',
        type: 'POST',
        data: {
            islem: 'siparis_ozeti_getir',
            adres_id: selectedAddress,
            odeme_yontemi: selectedPaymentMethod
        },
        dataType: 'json',
        success: function(response) {
            if (response.durum === 'success') {
                displayOrderSummary(response.data);
            }
        }
    });
}

function displayOrderSummary(data) {
    let html = `
        <div class="row">
            <div class="col-md-6">
                <h6>Teslimat Adresi</h6>
                <p class="mb-2">${data.adres.ad_soyad}</p>
                <p class="mb-2">${data.adres.telefon}</p>
                <p class="mb-2">${data.adres.il} / ${data.adres.ilce}</p>
                <p class="mb-0">${data.adres.adres}</p>
            </div>
            <div class="col-md-6">
                <h6>Ödeme Yöntemi</h6>
                <p class="mb-0">${selectedPaymentMethod === 'havale' ? 'Havale/EFT' : 'Kredi Kartı'}</p>
            </div>
        </div>
        <hr>
        <h6>Ürünler</h6>
    `;
    
    data.urunler.forEach(function(item) {
        html += `
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                    <h6 class="mb-0">${item.urun_isim}</h6>
                    <small class="text-muted">${item.secenek_aciklama} - ${item.adet} adet</small>
                </div>
                <span class="fw-bold">${item.toplam_fiyat} TL</span>
            </div>
        `;
    });
    
    html += `
        <hr>
        <div class="d-flex justify-content-between">
            <span>Ara Toplam:</span>
            <span>${data.ara_toplam} TL</span>
        </div>
        <div class="d-flex justify-content-between">
            <span>İndirim:</span>
            <span>${data.indirim} TL</span>
        </div>
        <div class="d-flex justify-content-between">
            <span>Kargo:</span>
            <span>Ücretsiz</span>
        </div>
        <hr>
        <div class="d-flex justify-content-between fw-bold">
            <span>Toplam:</span>
            <span>${data.toplam} TL</span>
        </div>
    `;
    
    $('#order-summary').html(html);
}

function placeOrder() {
    $('#place-order-btn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>İşleniyor...');
    
    $.ajax({
        url: 'nedmin/netting/islem.php',
        type: 'POST',
        data: {
            islem: 'siparis_olustur',
            adres_id: selectedAddress,
            odeme_yontemi: selectedPaymentMethod
        },
        dataType: 'json',
        success: function(response) {
            if (response.durum === 'success') {
                if (selectedPaymentMethod === 'havale') {
                    window.location.href = `havale-odeme.php?siparis_id=${response.siparis_id}`;
                } else {
                    window.location.href = `kart-odeme.php?siparis_id=${response.siparis_id}`;
                }
            } else {
                showMessage('error', response.mesaj);
                $('#place-order-btn').prop('disabled', false).html('<i class="fas fa-check me-2"></i>Siparişi Onayla');
            }
        },
        error: function() {
            showMessage('error', "Bir hata oluştu");
            $('#place-order-btn').prop('disabled', false).html('<i class="fas fa-check me-2"></i>Siparişi Onayla');
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
    $('.container').prepend(alertHtml);
    
    // Auto hide after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut();
    }, 5000);
}
</script>

<?php include 'footer.php'; ?> 