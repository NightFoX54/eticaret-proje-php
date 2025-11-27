<?php
include 'header.php';

// Check if user is logged in
$user_logged_in = isset($_SESSION['kullanici_id']);
?>

<div class="container py-5">
    <h1 class="mb-4">Alışveriş Sepeti</h1>
    
    <div class="row">
        <div class="col-lg-8">
            <!-- Cart items container -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div id="cart-items-container">
                        <!-- Cart items will be loaded here via AJAX -->
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Yükleniyor...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Cart summary -->
        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Sipariş Özeti</h5>
                </div>
                <div class="card-body">
                    <div id="cart-summary">
                        <!-- Summary will be loaded here via AJAX -->
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Yükleniyor...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Coupon code -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">İndirim Kuponu</h5>
                </div>
                <div class="card-body">
                    <div id="coupon-alert" class="alert" role="alert" style="display:none;"></div>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" id="coupon-code" placeholder="Kupon kodu girin">
                        <button class="btn btn-primary" type="button" id="apply-coupon">Uygula</button>
                    </div>
                    <div id="active-coupon" style="display:none;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-success"><i class="fas fa-check-circle me-2"></i></span>
                                <span id="coupon-name"></span>
                            </div>
                            <button type="button" class="btn btn-sm btn-link text-danger" id="remove-coupon">Kaldır</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Continue shopping / Checkout buttons -->
            <div class="d-grid gap-2">
                <a href="index.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Alışverişe Devam Et
                </a>
                <button type="button" class="btn btn-success" id="proceed-checkout">
                    <i class="fas fa-check-circle me-2"></i>Siparişi Tamamla
                </button>
            </div>
        </div>
    </div>
</div>

<template id="empty-cart-template">
    <div class="text-center py-5">
        <div class="mb-4">
            <i class="fas fa-shopping-cart fa-4x text-muted"></i>
        </div>
        <h4>Sepetiniz Boş</h4>
        <p class="text-muted">Sepetinizde henüz ürün bulunmamaktadır.</p>
        <a href="index.php" class="btn btn-primary mt-3">
            <i class="fas fa-shopping-bag me-2"></i>Alışverişe Başla
        </a>
    </div>
</template>

<template id="cart-item-template">
    <div class="cart-item mb-3 border-bottom pb-3" data-id="${secenek_id}">
        <div class="row align-items-center">
            <div class="col-md-2 col-4 mb-2 mb-md-0">
                <a href="urun.php?id=${urun_id}">
                    <img src="${foto_path}" class="img-fluid rounded" alt="${urun_isim}">
                </a>
            </div>
            <div class="col-md-4 col-8 mb-2 mb-md-0">
                <h5 class="mb-1">
                    <a href="urun.php?id=${urun_id}" class="text-decoration-none text-dark">${urun_isim}</a>
                </h5>
                <small class="text-muted">${marka_isim}</small>
                <div><small class="text-muted">${varyasyon_bilgisi}</small></div>
            </div>
            <div class="col-md-2 col-4">
                <div class="input-group input-group-sm">
                    <button class="btn btn-outline-secondary decrease-qty" type="button" data-id="${secenek_id}">
                        <i class="fas fa-minus"></i>
                    </button>
                    <input type="text" class="form-control text-center item-qty" value="${adet}" data-id="${secenek_id}" readonly>
                    <button class="btn btn-outline-secondary increase-qty" type="button" data-id="${secenek_id}">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
            <div class="col-md-2 col-4 text-end">
                <span class="fw-bold">${birim_fiyat} ₺</span>
            </div>
            <div class="col-md-2 col-4 text-end">
                <button class="btn btn-sm btn-link text-danger remove-item" data-id="${secenek_id}">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    </div>
</template>

<template id="cart-summary-template">
    <div>
        <div class="d-flex justify-content-between mb-2">
            <span>Ara Toplam:</span>
            <span id="subtotal">${subtotal.toFixed(2)} ₺</span>
        </div>
        <div class="d-flex justify-content-between mb-2" id="discount-row" style="display:none;">
            <span>İndirim:</span>
            <span id="discount-amount" class="text-danger">-${discount.toFixed(2)} ₺</span>
        </div>
        <hr>
        <div class="d-flex justify-content-between mb-2 fw-bold">
            <span>Toplam:</span>
            <span id="total">${total.toFixed(2)} ₺</span>
        </div>
    </div>
</template>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load cart contents
    loadCart();
    
    // Event delegation for cart item interactions
    document.addEventListener('click', function(e) {
        // Increase quantity
        if (e.target.closest('.increase-qty')) {
            const btn = e.target.closest('.increase-qty');
            const productId = btn.dataset.id;
            updateCartItem(productId, 1);
        }
        
        // Decrease quantity
        if (e.target.closest('.decrease-qty')) {
            const btn = e.target.closest('.decrease-qty');
            const productId = btn.dataset.id;
            updateCartItem(productId, -1);
        }
        
        // Remove item
        if (e.target.closest('.remove-item')) {
            const btn = e.target.closest('.remove-item');
            const productId = btn.dataset.id;
            removeCartItem(productId);
        }
    });
    
    // Apply coupon
    document.getElementById('apply-coupon').addEventListener('click', function() {
        const couponCode = document.getElementById('coupon-code').value.trim();
        if (couponCode) {
            applyCoupon(couponCode);
        } else {
            showCouponAlert('error', 'Lütfen bir kupon kodu girin.');
        }
    });
    
    // Remove coupon
    document.getElementById('remove-coupon').addEventListener('click', function() {
        removeCoupon();
    });
    
    // Proceed to checkout
    document.getElementById('proceed-checkout').addEventListener('click', function() {
        window.location.href = 'checkout.php';
    });
});

// Load cart contents
function loadCart() {
    fetch('nedmin/netting/islem.php?islem=sepet_getir', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.durum === 'success') {
            if (data.sepet.length === 0) {
                // Show empty cart message
                const template = document.getElementById('empty-cart-template');
                const content = template.content.cloneNode(true);
                document.getElementById('cart-items-container').innerHTML = '';
                document.getElementById('cart-items-container').appendChild(content);
                
                // Disable checkout button
                document.getElementById('proceed-checkout').disabled = true;
                
                // Clear summary
                document.getElementById('cart-summary').innerHTML = '';
            } else {
                // Display cart items
                renderCartItems(data.sepet);
                
                // Update cart summary
                updateCartSummary(data.toplam, data.indirim || 0);
                
                // Enable checkout button
                document.getElementById('proceed-checkout').disabled = false;
                
                // Update coupon display if active
                if (data.kupon) {
                    document.getElementById('coupon-name').textContent = data.kupon.kupon_kod;
                    document.getElementById('active-coupon').style.display = 'block';
                    document.getElementById('coupon-code').value = '';
                } else {
                    document.getElementById('active-coupon').style.display = 'none';
                }
            }
        } else {
            console.error('Sepet yüklenirken hata oluştu:', data.mesaj);
        }
    })
    .catch(error => {
        console.error('Sepet yüklenirken bir hata oluştu:', error);
    });
}

// Render cart items
function renderCartItems(items) {
    const container = document.getElementById('cart-items-container');
    container.innerHTML = '';
    items.forEach(item => {
        const template = document.getElementById('cart-item-template');
        const itemHtml = template.innerHTML
            .replace(/\${secenek_id}/g, item.secenek_id)
            .replace(/\${urun_id}/g, item.urun_id)
            .replace(/\${urun_isim}/g, item.urun_isim)
            .replace(/\${marka_isim}/g, item.marka_isim || 'Bilinmiyor')
            .replace(/\${adet}/g, item.adet)
            .replace(/\${birim_fiyat}/g, parseFloat(item.birim_fiyat).toFixed(2))
            .replace(/\${foto_path}/g, item.foto_path || 'uploads/urunler/no-image.jpg')
            .replace(/\${varyasyon_bilgisi}/g, item.varyasyon_bilgisi || '');
        
        const div = document.createElement('div');
        div.innerHTML = itemHtml;
        container.appendChild(div.firstElementChild);
    });
}

// Update cart summary
function updateCartSummary(subtotal, discount = 0) {
    const total = subtotal - discount;
    
    const template = document.getElementById('cart-summary-template');
    const summaryHtml = template.innerHTML
        .replace(/\${subtotal\.toFixed\(2\)}/g, parseFloat(subtotal).toFixed(2))
        .replace(/\${discount\.toFixed\(2\)}/g, parseFloat(discount).toFixed(2))
        .replace(/\${total\.toFixed\(2\)}/g, parseFloat(total).toFixed(2));
    
    document.getElementById('cart-summary').innerHTML = summaryHtml;
    
    // Show/hide discount row
    if (discount > 0) {
        document.getElementById('discount-row').style.display = 'flex';
    } else {
        document.getElementById('discount-row').style.display = 'none';
    }
}

// Update cart item quantity
function updateCartItem(secenekId, change) {
    fetch('nedmin/netting/islem.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: `islem=sepet_guncelle&secenek_id=${secenekId}&adet_degisim=${change}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.durum === 'success') {
            // Reload cart to update the view
            loadCart();
            
            // Update cart badge in header
            const cartBadge = document.querySelector('.cart-badge');
            if (cartBadge) {
                cartBadge.textContent = data.sepet_adet;
            }
        } else {
            alert(data.mesaj);
        }
    })
    .catch(error => {
        console.error('Sepet güncellenirken bir hata oluştu:', error);
    });
}

// Remove cart item
function removeCartItem(secenekId) {
    if (confirm('Bu ürünü sepetten çıkarmak istediğinize emin misiniz?')) {
        fetch('nedmin/netting/islem.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: `islem=sepet_urun_sil&secenek_id=${secenekId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.durum === 'success') {
                // Reload cart to update the view
                loadCart();
                
                // Update cart badge in header
                const cartBadge = document.querySelector('.cart-badge');
                if (cartBadge) {
                    cartBadge.textContent = data.sepet_adet;
                }
            } else {
                alert(data.mesaj);
            }
        })
        .catch(error => {
            console.error('Ürün sepetten silinirken bir hata oluştu:', error);
        });
    }
}

// Apply coupon
function applyCoupon(couponCode) {
    fetch('nedmin/netting/islem.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: `islem=kupon_uygula&kupon_kod=${couponCode}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.durum === 'success') {
            showCouponAlert('success', data.mesaj);
            loadCart(); // Reload cart to update with new prices
        } else {
            showCouponAlert('error', data.mesaj);
        }
    })
    .catch(error => {
        console.error('Kupon uygulanırken bir hata oluştu:', error);
        showCouponAlert('error', 'Kupon uygulanırken bir hata oluştu. Lütfen tekrar deneyin.');
    });
}

// Remove coupon
function removeCoupon() {
    fetch('nedmin/netting/islem.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'islem=kupon_kaldir'
    })
    .then(response => response.json())
    .then(data => {
        if (data.durum === 'success') {
            showCouponAlert('success', data.mesaj);
            document.getElementById('active-coupon').style.display = 'none';
            document.getElementById('coupon-code').value = '';
            loadCart(); // Reload cart to update with original prices
        } else {
            showCouponAlert('error', data.mesaj);
        }
    })
    .catch(error => {
        console.error('Kupon kaldırılırken bir hata oluştu:', error);
        showCouponAlert('error', 'Kupon kaldırılırken bir hata oluştu. Lütfen tekrar deneyin.');
    });
}

// Show coupon alert
function showCouponAlert(type, message) {
    const alertEl = document.getElementById('coupon-alert');
    alertEl.className = 'alert';
    alertEl.classList.add(type === 'success' ? 'alert-success' : 'alert-danger');
    alertEl.textContent = message;
    alertEl.style.display = 'block';
    
    // Hide after 3 seconds
    setTimeout(() => {
        alertEl.style.display = 'none';
    }, 3000);
}
</script>

<?php include 'footer.php'; ?> 