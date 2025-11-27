<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Redirect if user is not logged in
if(!isset($_SESSION['kullanici_id'])) {
    header("Location: login.php?durum=giris");
    exit;
}

include 'header.php';

$kullanici_id = $_SESSION['kullanici_id'];
?>

<!-- Favoriler Content -->
<div class="container mt-4 mb-5">
    <div class="row">
        <div class="col-md-12">
            <h2 class="mb-4">Favori Ürünlerim</h2>
            
            <div id="favori-urunler-container">
                <!-- Favori ürünler AJAX ile yüklenecek -->
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Yükleniyor...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Favori ürünleri yükle
    loadFavoriteProducts();

    // Sayfa yüklendikten sonra oluşacak dinamik elementler için event delegation
    $(document).on('click', '.remove-favorite', function(e) {
        e.preventDefault();
        
        const urunId = $(this).data('id');
        removeFavorite(urunId);
    });

    $(document).on('click', '.add-to-cart', function(e) {
        e.preventDefault();
        
        const urunId = $(this).data('id');
        addToCart(urunId, 1);
    });
});

function loadFavoriteProducts() {
    $.ajax({
        url: 'nedmin/netting/islem.php',
        type: 'POST',
        data: { islem: 'favorileri_getir' },
        dataType: 'json',
        success: function(response) {
            if (response.durum === 'success') {
                displayFavoriteProducts(response.urunler);
            } else {
                $('#favori-urunler-container').html('<div class="alert alert-info">Favori ürününüz bulunmamaktadır.</div>');
            }
        },
        error: function() {
            $('#favori-urunler-container').html('<div class="alert alert-danger">Favori ürünler yüklenirken bir hata oluştu.</div>');
        }
    });
}

function displayFavoriteProducts(products) {
    if (products.length === 0) {
        $('#favori-urunler-container').html('<div class="alert alert-info">Favori ürününüz bulunmamaktadır.</div>');
        return;
    }

    let html = '<div class="row">';
    
    products.forEach(product => {
        html += `
            <div class="col-md-3 mb-4">
                <div class="card h-100">
                    <img src="${product.urun_resim}" class="card-img-top" alt="${product.urun_adi}" style="height: 200px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="card-title">${product.urun_adi}</h5>
                        <p class="card-text text-truncate">${product.urun_aciklama}</p>
                        <p class="card-text fw-bold">${parseFloat(product.urun_fiyat).toLocaleString('tr-TR', {minimumFractionDigits: 2})} TL</p>
                        <div class="d-flex justify-content-between">
                            <button class="btn btn-sm btn-danger remove-favorite" data-id="${product.urun_id}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });

    html += '</div>';
    $('#favori-urunler-container').html(html);
}

function removeFavorite(urunId) {
    $.ajax({
        url: 'nedmin/netting/islem.php',
        type: 'POST',
        data: {
            islem: 'favori_sil',
            urun_id: urunId
        },
        dataType: 'json',
        success: function(response) {
            if (response.durum === 'success') {
                // Favorilerden başarıyla silindi, listeyi yeniden yükle
                loadFavoriteProducts();
                
                // Başarılı bildirim göster
                showNotification('Ürün favorilerinizden kaldırıldı', 'success');
            } else {
                showNotification('Ürün favorilerinizden kaldırılırken bir hata oluştu', 'danger');
            }
        },
        error: function() {
            showNotification('Bir hata oluştu', 'danger');
        }
    });
}



function showNotification(message, type) {
    // Mevcut bildirimleri temizle
    $('.notification-container').remove();
    
    // Bildirim container oluştur
    const notificationContainer = $('<div class="notification-container position-fixed bottom-0 end-0 p-3" style="z-index: 5"></div>');
    $('body').append(notificationContainer);
    
    // Bildirim toast oluştur
    const toast = $(`
        <div class="toast align-items-center text-white bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `);
    
    notificationContainer.append(toast);
    
    // Toast'u göster
    const bsToast = new bootstrap.Toast(toast, {
        autohide: true,
        delay: 3000
    });
    bsToast.show();
}
</script>

<?php include 'footer.php'; ?> 