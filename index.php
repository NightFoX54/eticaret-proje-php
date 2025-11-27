<?php
include 'header.php';
?>

<!-- Add jQuery at the beginning -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Hero Slider/Carousel -->
<div id="heroCarousel" class="carousel slide mb-4" data-bs-ride="carousel">
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
    </div>
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img src="https://dummyimage.com/1200x400/007bff/ffffff&text=Kampanya+1" class="d-block w-100" alt="Kampanya 1">
            <div class="carousel-caption d-none d-md-block">
                <h5>Yaz Sezonu İndirimleri</h5>
                <p>Tüm yaz ürünlerinde %50'ye varan indirimler!</p>
            </div>
        </div>
        <div class="carousel-item">
            <img src="https://dummyimage.com/1200x400/28a745/ffffff&text=Kampanya+2" class="d-block w-100" alt="Kampanya 2">
            <div class="carousel-caption d-none d-md-block">
                <h5>Yeni Sezon Ürünleri</h5>
                <p>Sonbahar koleksiyonumuz mağazalarda!</p>
            </div>
        </div>
        <div class="carousel-item">
            <img src="https://dummyimage.com/1200x400/dc3545/ffffff&text=Kampanya+3" class="d-block w-100" alt="Kampanya 3">
            <div class="carousel-caption d-none d-md-block">
                <h5>Elektronik Festivali</h5>
                <p>Elektronik ürünlerde kaçırılmayacak fırsatlar!</p>
            </div>
        </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>

<!-- Popular Products -->
<section class="mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Popüler Ürünler</h2>
        <a href="#" class="btn btn-outline-primary btn-sm">Tümünü Gör</a>
    </div>
    
    <div class="row row-cols-2 row-cols-md-4 row-cols-lg-5 g-4" id="popular-products">
        <!-- Products will be loaded here -->
        <div class="text-center py-5 col-12">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>
</section>

<!-- Featured Banners -->
<section class="mb-5">
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card h-100 border-0 rounded-3 overflow-hidden">
                <img src="https://dummyimage.com/600x300/6c757d/ffffff&text=Kampanya+Banner+1" class="img-fluid" alt="Kampanya Banner 1">
                <div class="card-img-overlay d-flex flex-column justify-content-end">
                    <h4 class="card-title text-white">Yeni Sezon</h4>
                    <p class="card-text text-white">Sezonun en tarz ürünleri burada!</p>
                    <a href="#" class="btn btn-light btn-sm align-self-start">Alışverişe Başla</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card h-100 border-0 rounded-3 overflow-hidden">
                <img src="https://dummyimage.com/600x300/20c997/ffffff&text=Kampanya+Banner+2" class="img-fluid" alt="Kampanya Banner 2">
                <div class="card-img-overlay d-flex flex-column justify-content-end">
                    <h4 class="card-title text-white">Muhteşem Fırsatlar</h4>
                    <p class="card-text text-white">İndirimli ürünleri keşfedin!</p>
                    <a href="#" class="btn btn-light btn-sm align-self-start">Şimdi İncele</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- New Arrivals -->
<section class="mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Yeni Gelenler</h2>
        <a href="#" class="btn btn-outline-primary btn-sm">Tümünü Gör</a>
    </div>
    
    <div class="row row-cols-2 row-cols-md-4 row-cols-lg-5 g-4" id="new-products">
        <!-- Products will be loaded here -->
        <div class="text-center py-5 col-12">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>
</section>

<script>
    $(document).ready(function() {
        // Load popular products
        loadPopularProducts();
        
        // Load new products
        loadNewProducts();
        
    });
    
    // Function to load popular products
    function loadPopularProducts() {
        $.ajax({
            url: 'nedmin/netting/islem.php',
            type: 'GET',
            data: { islem: 'populer_urunler' },
            dataType: 'json',
            success: function(response) {
                if (response.durum === 'success') {
                    displayProducts(response.urunler, 'popular-products');
                } else {
                    $('#popular-products').html('<div class="col-12 text-center">Ürünler yüklenirken bir hata oluştu.</div>');
                }
            },
            error: function() {
                $('#popular-products').html('<div class="col-12 text-center">Ürünler yüklenirken bir hata oluştu.</div>');
            }
        });
    }
    
    // Function to load new products
    function loadNewProducts() {
        $.ajax({
            url: 'nedmin/netting/islem.php',
            type: 'GET',
            data: { islem: 'yeni_urunler' },
            dataType: 'json',
            success: function(response) {
                if (response.durum === 'success') {
                    displayProducts(response.urunler, 'new-products');
                } else {
                    $('#new-products').html('<div class="col-12 text-center">Ürünler yüklenirken bir hata oluştu.</div>');
                }
            },
            error: function() {
                $('#new-products').html('<div class="col-12 text-center">Ürünler yüklenirken bir hata oluştu.</div>');
            }
        });
    }
    
    // Function to display products
    function displayProducts(products, containerId) {
        const productsContainer = $(`#${containerId}`);
        productsContainer.empty();
        
        if (products && products.length > 0) {
            // First, check which products are in favorites
            checkFavoriteStatus(products.map(product => product.id))
                .then(favoriteProducts => {
                    products.forEach(product => {
                        // Get product image (first image or placeholder)
                        const productImage = product.foto_path ? product.foto_path : 'https://dummyimage.com/300x300/cccccc/333333&text=Ürün+Resmi';
                        
                        // Check if this product is in favorites
                        const isFavorite = favoriteProducts.includes(parseInt(product.id));
                        const heartIcon = isFavorite ? 
                            '<i class="fas fa-heart text-danger"></i>' : 
                            '<i class="far fa-heart"></i>';
                        let html = '';
                        let puan = 0;
                        let yarim_yildiz = false;
                        let tam_yildiz = 0;
                        let yarim_yildiz_sayisi = 0;
                        if(product.ortalama_puan){
                            puan = product.ortalama_puan;
                            yarim_yildiz = puan % 1 >= 0.5;
                            tam_yildiz = Math.floor(puan);
                            yarim_yildiz_sayisi = yarim_yildiz ? 1 : 0;
                            
                        }
                        if(product.ortalama_puan == null){
                            puan = 0;
                            yarim_yildiz = puan % 1 >= 0.5;
                            tam_yildiz = Math.floor(puan);
                            yarim_yildiz_sayisi = yarim_yildiz ? 1 : 0;
                        }
                        for(let i = 0; i < tam_yildiz; i++){
                            html += '<i class="fas fa-star"></i>';
                        }
                        if(yarim_yildiz){
                            html += '<i class="fas fa-star-half-alt"></i>';
                        }
                        for(let i = 0; i < 5 - tam_yildiz - yarim_yildiz_sayisi; i++){
                            html += '<i class="far fa-star"></i>';
                        }
                        const productHtml = `
                            <div class="col">
                                <div class="card h-100 product-card">
                                    <div class="position-relative">
                                        <a href="urun.php?id=${product.id}">
                                            <img src="${productImage}" class="card-img-top" alt="${product.urun_isim}">
                                        </a>
                                        <button class="btn btn-sm btn-light position-absolute top-0 end-0 m-2 rounded-circle favorite-btn" data-id="${product.id}">
                                            ${heartIcon}
                                        </button>
                                    </div>
                                    <div class="card-body">
                                        <p class="card-text text-muted small mb-1">${product.marka_isim || 'Marka'}</p>
                                        <h5 class="card-title mb-2">
                                            <a href="urun.php?id=${product.id}" class="text-decoration-none text-dark">${product.urun_isim}</a>
                                        </h5>
                                        <div class="urun-rating mb-2">
                                            <div class="stars">` + html + `
                                            </div>
                                            <small class="text-muted rating-count">(${product.degerlendirme_sayisi})</small>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <p class="fw-bold mb-0">${parseFloat(product.urun_fiyat).toLocaleString('tr-TR', {style:'currency', currency:'TRY'})}</p>
                                            <a href="urun.php?id=${product.id}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-eye"></i> İncele
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        
                        productsContainer.append(productHtml);
                    });
                    
                    // Add event listeners to favorite buttons after appending to DOM
                    $('.favorite-btn').off('click').on('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        const productId = $(this).data('id');
                        toggleFavorite(productId, $(this));
                    });
                });
        } else {
            productsContainer.html('<div class="col-12 text-center">Benzer ürün bulunamadı.</div>');
        }
    }

    // Function to check which products are in user's favorites
    function checkFavoriteStatus(productIds) {
        return new Promise((resolve) => {
            // If user is not logged in, return empty array
            if (!document.querySelector('.logout-link')) {
                resolve([]);
                return;
            }

            $.ajax({
                url: 'nedmin/netting/islem.php',
                type: 'POST',
                data: { 
                    islem: 'favorileri_kontrol'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.durum === 'success' && response.favori_urunler) {
                        resolve(response.favori_urunler);
                    } else {
                        resolve([]);
                    }
                },
                error: function() {
                    resolve([]);
                }
            });
        });
    }

    // Function to toggle favorite status
    function toggleFavorite(productId, button) {
        $.ajax({
            url: 'nedmin/netting/islem.php',
            type: 'POST',
            data: { 
                islem: 'favori_ekle',
                urun_id: productId
            },
            dataType: 'json',
            success: function(response) {
                if (response.durum === 'success') {
                    // Update button UI
                    if (response.islem === 'eklendi') {
                        button.html('<i class="fas fa-heart text-danger"></i>');
                        showToast('Ürün favorilerinize eklendi.', 'success');
                    } else {
                        button.html('<i class="far fa-heart"></i>');
                        showToast('Ürün favorilerinizden çıkarıldı.', 'success');
                    }
                } else {
                    if (response.kod === 'giris_gerekli') {
                        // Redirect to login page
                        window.location.href = 'login.php?durum=giris';
                    } else {
                        showToast(response.mesaj || 'Bir hata oluştu.', 'danger');
                    }
                }
            },
            error: function() {
                showToast('İşlem sırasında bir hata oluştu.', 'danger');
            }
        });
    }

    // Function to display toast notifications
    function showToast(message, type) {
        // Create toast container if it doesn't exist
        let toastContainer = $('.toast-container');
        if (toastContainer.length === 0) {
            toastContainer = $('<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1050;"></div>');
            $('body').append(toastContainer);
        }
        
        // Create toast
        const toast = $(`
            <div class="toast align-items-center text-white bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `);
        
        toastContainer.append(toast);
        
        // Initialize Bootstrap toast
        const bsToast = new bootstrap.Toast(toast, {
            autohide: true,
            delay: 3000
        });
        
        // Show toast
        bsToast.show();
        
        // Remove toast when hidden
        toast.on('hidden.bs.toast', function() {
            $(this).remove();
        });
    }
</script>

<?php
include 'footer.php';
?> 