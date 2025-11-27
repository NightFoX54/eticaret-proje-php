<?php
include 'header.php';

// Check if product ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$urun_id = intval($_GET['id']);

// Record visit - will be implemented in AJAX call
?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Product Detail Container -->
<div class="container my-5">
    <div class="card border-0">
        <div class="row g-0" id="product-detail">
            <!-- Loading spinner -->
            <div class="col-12 text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Related Products -->
<div class="container mb-5">
    <h3 class="mb-4">Benzer Ürünler</h3>
    <div class="row row-cols-2 row-cols-md-4 row-cols-lg-5 g-4" id="related-products">
        <!-- Products will be loaded here -->
        <div class="text-center py-5 col-12">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>
</div>

<!-- Reviews Section -->
<div class="container mb-5">
    <div class="card">
        <div class="card-header bg-white">
            <h3 class="mb-0">Değerlendirmeler</h3>
        </div>
        <div class="card-body" id="product-reviews">
            <div class="text-center py-3">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Questions Section -->
<div class="container mb-5">
    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Sorular</h3>
            <button class="btn btn-primary btn-sm" id="ask-question-btn">
                Soru Sor
            </button>
        </div>
        <div class="card-body" id="product-questions">
            <div class="text-center py-3">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white" id="questions-pagination">
            <!-- Questions pagination will be here -->
        </div>
    </div>
    
    <!-- Question Form Modal -->
    <div class="modal fade" id="questionModal" tabindex="-1" aria-labelledby="questionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="questionModalLabel">Soru Sor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="question-form">
                        <div class="mb-3">
                            <label for="question-text" class="form-label">Sorunuz</label>
                            <textarea class="form-control" id="question-text" rows="3" required></textarea>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" value="1" id="public-question">
                            <label class="form-check-label" for="public-question">
                                Bu soruyu herkes görebilsin
                            </label>
                        </div>
                        <div id="question-form-message"></div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="button" class="btn btn-primary" id="submit-question-btn">Gönder</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        const urunId = <?php echo $urun_id; ?>;
        
        // Load product details
        loadProductDetails(urunId);
        
        // Load related products
        loadRelatedProducts(urunId);
        
        // Record visit
        recordProductVisit(urunId);
        
        // Load product reviews
        loadProductReviews(urunId);
        
        // Load product questions
        loadProductQuestions(urunId);
        
        // Handle ask question button
        $('#ask-question-btn').on('click', function() {
            // Check if user is logged in
            $.ajax({
                url: 'nedmin/netting/islem.php',
                type: 'POST',
                data: { islem: 'kullanici_kontrol' },
                dataType: 'json',
                success: function(response) {
                    if (response.durum === 'success') {
                        // User is logged in, show question modal
                        $('#questionModal').modal('show');
                    } else {
                        // User is not logged in, redirect to login page
                        window.location.href = 'login.php?durum=giris';
                    }
                },
                error: function() {
                    showToast('İşlem sırasında bir hata oluştu.', 'danger');
                }
            });
        });
        
        // Handle submit question button
        $('#submit-question-btn').on('click', function() {
            const questionText = $('#question-text').val().trim();
            const isPublic = $('#public-question').is(':checked') ? 1 : 0;
            
            if (!questionText) {
                $('#question-form-message').html('<div class="alert alert-danger mt-2">Lütfen sorunuzu yazın.</div>');
                return;
            }
            
            $.ajax({
                url: 'nedmin/netting/islem.php',
                type: 'POST',
                data: { 
                    islem: 'soru_ekle',
                    urun_id: urunId,
                    soru: questionText,
                    herkese_acik: isPublic
                },
                dataType: 'json',
                success: function(response) {
                    if (response.durum === 'success') {
                        // Close modal and show success message
                        $('#questionModal').modal('hide');
                        showToast('Sorunuz başarıyla gönderildi. Yanıtlandığında bilgilendirileceksiniz.', 'success');
                        
                        // Clear form
                        $('#question-text').val('');
                        $('#public-question').prop('checked', false);
                        
                        // Reload questions list
                        loadProductQuestions(urunId);
                    } else {
                        $('#question-form-message').html(`<div class="alert alert-danger mt-2">${response.mesaj}</div>`);
                    }
                },
                error: function() {
                    $('#question-form-message').html('<div class="alert alert-danger mt-2">İşlem sırasında bir hata oluştu.</div>');
                }
            });
        });
    });
    
    // Global variables for pagination state
    let currentReviewsPage = 1;
    let currentQuestionsPage = 1;
    let currentRatingFilter = [];
    const reviewsPerPage = 5;
    const questionsPerPage = 5;
    
    // Function to load product details
    function loadProductDetails(productId) {
        $.ajax({
            url: 'nedmin/netting/islem.php',
            type: 'GET',
            data: { islem: 'urun_detay', urun_id: productId },
            dataType: 'json',
            success: function(response) {
                if (response.durum === 'success') {
                    displayProductDetails(response.urun, response.resimler, response.kategoriler);
                    // Update page title
                    document.title = response.urun.urun_isim + " - E-Ticaret";
                } else {
                    $('#product-detail').html('<div class="col-12 text-center py-5"><div class="alert alert-danger">Ürün bulunamadı!</div></div>');
                }
            },
            error: function() {
                $('#product-detail').html('<div class="col-12 text-center py-5"><div class="alert alert-danger">Ürün yüklenirken bir hata oluştu!</div></div>');
            }
        });
    }
    
    // Function to display product details
    function displayProductDetails(product, images, categories) {
        let imagesHtml = '';
        let thumbnailsHtml = '';
        
        // Main image carousel
        if (images && images.length > 0) {
            imagesHtml = `
                <div id="productCarousel" class="carousel slide">
                    <div class="carousel-inner">
            `;
            
            images.forEach((image, index) => {
                imagesHtml += `
                    <div class="carousel-item ${index === 0 ? 'active' : ''}">
                        <img src="${image.foto_path}" class="d-block w-100" alt="${product.urun_isim}">
                    </div>
                `;
                
                thumbnailsHtml += `
                    <div class="col-3 mb-2">
                        <img src="${image.foto_path}" class="img-thumbnail" data-bs-target="#productCarousel" data-bs-slide-to="${index}" alt="${product.urun_isim}" style="cursor: pointer;">
                    </div>
                `;
            });
            
            imagesHtml += `
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            `;
        } else {
            imagesHtml = `<img src="https://dummyimage.com/600x600/cccccc/333333&text=Ürün+Resmi" class="img-fluid" alt="${product.urun_isim}">`;
        }
        
        // Categories display
        let categoriesHtml = '';
        if (categories && categories.length > 0) {
            categoriesHtml = '<div class="mb-3">';
            categories.forEach(category => {
                categoriesHtml += `<a href="kategori.php?id=${category.id}" class="badge bg-secondary text-decoration-none link-light me-1">${category.kategori_isim}</a> `;
            });
            categoriesHtml += '</div>';
        }
        
        // Product details HTML
        const detailHtml = `
            <div class="col-md-5">
                <div class="product-images mb-3">
                    ${imagesHtml}
                </div>
                <div class="row product-thumbnails">
                    ${thumbnailsHtml}
                </div>
            </div>
            <div class="col-md-7">
                <div class="card-body">
                    <h1 class="card-title h2 mb-4">${product.urun_isim}</h1>
                    
                    ${categoriesHtml}
                    
                    <div class="d-flex align-items-center mb-3">
                        <span class="h3 text-primary mb-0" id="product-price">${parseFloat(product.urun_fiyat).toLocaleString('tr-TR', {style:'currency', currency:'TRY'})}</span>
                        <div class="ms-auto">
                            <span class="badge bg-success" id="product-stock">Stokta: ${product.urun_stok}</span>
                        </div>
                    </div>
                    
                    ${product.marka_isim ? `<p class="card-text mb-3"><strong>Marka:</strong> ${product.marka_isim}</p>` : ''}
                    
                    <hr>
                    
                    <div class="card-text mb-4">
                        ${product.urun_aciklama}
                    </div>
                    
                    <div id="product-variations"></div>
                    
                    <form class="d-flex mb-4">
                        <input type="hidden" id="selected-secenek-id" value="">
                        <div class="input-group me-3" style="width: 130px;">
                            <button class="btn btn-outline-secondary" type="button" id="decrease-quantity">-</button>
                            <input type="number" class="form-control text-center" id="product-quantity" value="1" min="1">
                            <button class="btn btn-outline-secondary" type="button" id="increase-quantity">+</button>
                        </div>
                        
                        <button type="button" class="btn btn-primary" id="add-to-cart-btn">
                            <i class="fas fa-cart-plus me-2"></i> Sepete Ekle
                        </button>
                        
                        <button type="button" class="btn btn-outline-danger ms-2" id="add-to-favorites">
                            <i class="far fa-heart"></i>
                        </button>
                    </form>
                    
                    <div class="card-text">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><i class="fas fa-truck text-muted me-2"></i> Ücretsiz Kargo</p>
                                <p class="mb-1"><i class="fas fa-undo text-muted me-2"></i> 30 Gün İade Garantisi</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><i class="fas fa-shield-alt text-muted me-2"></i> Güvenli Alışveriş</p>
                                <p class="mb-1"><i class="fas fa-check-circle text-muted me-2"></i> Orijinal Ürün</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        $('#product-detail').html(detailHtml);
        
        // Initialize quantity buttons
        $('#decrease-quantity').on('click', function() {
            let currentVal = parseInt($('#product-quantity').val());
            if (currentVal > 1) {
                $('#product-quantity').val(currentVal - 1);
            }
        });
        
        $('#increase-quantity').on('click', function() {
            let currentVal = parseInt($('#product-quantity').val());
            let maxStock = parseInt($('#product-quantity').attr('max'));
            if (!maxStock || currentVal < maxStock) {
                $('#product-quantity').val(currentVal + 1);
            }
        });
        
        // Check if product is in favorites
        $.ajax({
            url: 'nedmin/netting/islem.php',
            type: 'POST',
            data: { 
                islem: 'favorileri_kontrol',
                urun_id: product.id
            },
            dataType: 'json',
            success: function(response) {
                if (response.durum === 'success') {
                    // Check if this product is in favorites
                    if (response.favori_durumu) {
                        $('#add-to-favorites').html('<i class="fas fa-heart text-danger"></i>');
                    } else {
                        $('#add-to-favorites').html('<i class="far fa-heart"></i>');
                    }
                }
            }
        });
        
        // Favorites button
        $('#add-to-favorites').on('click', function() {
            toggleFavorite(product.id);
        });
        
        // Set up default add to cart handler for products with variations
        $('#add-to-cart-btn').on('click', function(e) {
            e.preventDefault();
            const quantity = parseInt($('#product-quantity').val() || 1);
            addToCart(product.id, quantity);
        });
        
        // Load product variations
        loadProductVariations(product.id);
    }
    
    // Function to load product variations
    function loadProductVariations(productId) {
        $.ajax({
            url: 'nedmin/netting/islem.php',
            type: 'GET',
            data: { 
                islem: 'urun_detay', 
                urun_id: productId
            },
            dataType: 'json',
            success: function(response) {
                if (response.durum === 'success') {
                    if (response.nitelikler && response.nitelikler.length > 0 && response.varyasyonlar && response.varyasyonlar.length > 0) {
                        // Product has variations, display them
                        displayProductVariations(response.nitelikler, response.varyasyonlar);
                    } else {
                        // No variations or only one default variation
                        const defaultVariationId = response.varyasyonlar && response.varyasyonlar.length > 0 ? 
                            response.varyasyonlar[0].secenek_id : null;
                        
                        if (defaultVariationId) {
                            // Set default variation ID and update max quantity
                            $('#selected-secenek-id').val(defaultVariationId);
                            $('#product-quantity').attr('max', response.varyasyonlar[0].stok);
                            
                            // For products with only one variation and no niteliks,
                            // remove the message that requires selecting a variation
                            $('#add-to-cart-btn').off('click').on('click', function(e) {
                                e.preventDefault();
                                const quantity = parseInt($('#product-quantity').val() || 1);
                                
                                // Directly use the default secenek_id
                                $.ajax({
                                    url: 'nedmin/netting/islem.php',
                                    type: 'POST',
                                    data: { 
                                        islem: 'sepete_ekle', 
                                        secenek_id: defaultVariationId,
                                        adet: quantity 
                                    },
                                    dataType: 'json',
                                    success: function(response) {
                                        if (response.durum === 'success') {
                                            // Update cart badge
                                            $('.cart-badge').text(response.sepet_adet || 0);
                                            
                                            // Show success message
                                            alert('Ürün sepete eklendi.');
                                        } else {
                                            alert(response.mesaj || 'Ürün sepete eklenirken bir hata oluştu.');
                                        }
                                    },
                                    error: function() {
                                        alert('İşlem sırasında bir hata oluştu.');
                                    }
                                });
                            });
                        }
                    }
                }
            }
        });
    }
    
    // Function to display product variations
    function displayProductVariations(nitelikler, varyasyonlar) {
        let html = '';
        
        // Object to keep track of available combinations
        const availableCombinations = {};
        
        // Prepare available combinations
        varyasyonlar.forEach(varyasyon => {
            const combination = {};
            varyasyon.nitelik_degerleri.forEach(nd => {
                combination[nd.nitelik_id] = nd.deger_id;
            });
            
            // Store combination with secenek_id, price and stock
            availableCombinations[JSON.stringify(combination)] = {
                secenek_id: varyasyon.secenek_id,
                fiyat: varyasyon.fiyat,
                stok: varyasyon.stok
            };
        });
        
        // Store data for filtering function
        window.productVariationData = {
            nitelikler: nitelikler,
            availableCombinations: availableCombinations
        };
        
        // Function to check if a specific option is available given current selections
        function isOptionAvailable(nitelikId, degerId, currentSelection) {
            // Create a test selection with this option
            // Exclude any existing selection for this nitelik
            const testSelection = {};
            for (let key in currentSelection) {
                if (key != nitelikId) {
                    testSelection[key] = currentSelection[key];
                }
            }
            testSelection[nitelikId] = degerId;
            
            // Check if there's any available combination that matches this test selection
            for (let combinationKey in availableCombinations) {
                const combination = JSON.parse(combinationKey);
                
                // Check if this combination is compatible with our test selection
                // All selected values must match the combination
                let isCompatible = true;
                for (let key in testSelection) {
                    if (combination[key] !== testSelection[key]) {
                        isCompatible = false;
                        break;
                    }
                }
                
                // If all selected values match this combination, this option is available
                if (isCompatible) {
                    return true;
                }
            }
            
            return false;
        }
        
        // Function to filter and update available options
        function updateAvailableOptions() {
            // Get current selection
            const currentSelection = {};
            $('.variation-option:checked').each(function() {
                const nitelikId = $(this).data('nitelik-id');
                const degerId = $(this).data('deger-id');
                currentSelection[nitelikId] = degerId;
            });
            
            // For each nitelik, check which options are available
            nitelikler.forEach(nitelik => {
                nitelik.degerler.forEach(deger => {
                    const optionElement = $(`#deger_${deger.id}`);
                    const isChecked = optionElement.is(':checked');
                    
                    // Check if this option is available with current selections
                    const isAvailable = isOptionAvailable(nitelik.id, deger.id, currentSelection);
                    
                    if (isAvailable) {
                        // Enable this option
                        optionElement.prop('disabled', false);
                        optionElement.closest('.form-check').removeClass('opacity-50 text-muted');
                        optionElement.closest('.form-check').find('label').css({
                            'cursor': 'pointer',
                            'opacity': '1'
                        });
                    } else {
                        // Disable this option (but keep it checked if it was already selected)
                        if (!isChecked) {
                            optionElement.prop('disabled', true);
                            optionElement.closest('.form-check').addClass('opacity-50 text-muted');
                            optionElement.closest('.form-check').find('label').css({
                                'cursor': 'not-allowed',
                                'opacity': '0.5'
                            });
                        }
                    }
                });
            });
        }
        
        // Generate HTML for each nitelik
        nitelikler.forEach(nitelik => {
            html += `
                <div class="mb-3">
                    <label class="form-label fw-bold">${nitelik.ad}:</label>
                    <div class="d-flex flex-wrap">
            `;
            
            nitelik.degerler.forEach(deger => {
                html += `
                    <div class="form-check me-3 mb-2">
                        <input class="form-check-input variation-option" type="radio" 
                               name="nitelik_${nitelik.id}" id="deger_${deger.id}" 
                               data-nitelik-id="${nitelik.id}" data-deger-id="${deger.id}">
                        <label class="form-check-label" for="deger_${deger.id}" style="cursor: pointer;">
                            ${deger.deger}
                        </label>
                    </div>
                `;
            });
            
            html += `
                    </div>
                </div>
            `;
        });
        
        // Add the HTML to the page
        $('#product-variations').html(html);
        
        // Handle variation selection
        $('.variation-option').on('change', function() {
            // Get the current selection
            const currentSelection = {};
            $('.variation-option:checked').each(function() {
                const nitelikId = $(this).data('nitelik-id');
                const degerId = $(this).data('deger-id');
                currentSelection[nitelikId] = degerId;
            });
            
            // Update available options based on current selection
            updateAvailableOptions();
            
            // Check if we have a complete selection
            if (Object.keys(currentSelection).length === nitelikler.length) {
                // Look up the combination
                const combinationKey = JSON.stringify(currentSelection);
                const combinationData = availableCombinations[combinationKey];
                
                if (combinationData) {
                    // Update price and stock
                    $('#product-price').text(parseFloat(combinationData.fiyat).toLocaleString('tr-TR', {style:'currency', currency:'TRY'}));
                    $('#product-stock').text('Stokta: ' + combinationData.stok);
                    $('#product-quantity').attr('max', combinationData.stok);
                    
                    // Update selected variation ID
                    $('#selected-secenek-id').val(combinationData.secenek_id);
                    
                    // Reset quantity to 1 if current quantity exceeds stock
                    if (parseInt($('#product-quantity').val()) > combinationData.stok) {
                        $('#product-quantity').val(1);
                    }
                    
                    // Enable/disable add to cart button based on stock
                    $('#add-to-cart-btn').prop('disabled', combinationData.stok <= 0);
                }
            }
        });
        
        // Initial update to show all available options
        updateAvailableOptions();
        
        // Select first option of each nitelik by default
        nitelikler.forEach(nitelik => {
            if (nitelik.degerler.length > 0) {
                const firstOption = $(`#deger_${nitelik.degerler[0].id}`);
                if (!firstOption.prop('disabled')) {
                    firstOption.prop('checked', true).trigger('change');
                }
            }
        });
    }
    
    // Function to load related products
    function loadRelatedProducts(productId) {
        $.ajax({
            url: 'nedmin/netting/islem.php',
            type: 'GET',
            data: { islem: 'benzer_urunler', urun_id: productId },
            dataType: 'json',
            success: function(response) {
                if (response.durum === 'success') {
                    displayProducts(response.urunler, 'related-products');
                } else {
                    $('#related-products').html('<div class="col-12 text-center">'+response.mesaj+'</div>');
                }
            },
            error: function() {
                $('#related-products').html('<div class="col-12 text-center">Ürünler yüklenirken bir hata oluştu.</div>');
            }
        });
    }
    
    // Function to display products (reused from index.php)
    function displayProducts(products, containerId) {
        const productsContainer = $(`#${containerId}`);
        productsContainer.empty();
        
        if (products && products.length > 0) {
            // First check favorite status
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
                                        <div class="d-flex justify-content-between align-items-center">
                                            <p class="fw-bold mb-0">${parseFloat(product.urun_fiyat).toLocaleString('tr-TR', {style:'currency', currency:'TRY'})}</p>
                                            <button class="btn btn-primary btn-sm add-to-cart" data-product-id="${product.id}">
                                                <i class="fas fa-cart-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        
                        productsContainer.append(productHtml);
                    });
                    
                    // Add event listeners to favorite buttons
                    $('.favorite-btn').on('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        const productId = $(this).data('id');
                        const button = $(this);
                        
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
                                    if (response.islem === 'eklendi') {
                                        button.html('<i class="fas fa-heart text-danger"></i>');
                                        showToast('Ürün favorilerinize eklendi.', 'success');
                                    } else {
                                        button.html('<i class="far fa-heart"></i>');
                                        showToast('Ürün favorilerinizden çıkarıldı.', 'success');
                                    }
                                } else {
                                    if (response.kod === 'giris_gerekli') {
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
    
    // Function to record product visit
    function recordProductVisit(productId) {
        $.ajax({
            url: 'nedmin/netting/islem.php',
            type: 'POST',
            data: { islem: 'urun_ziyaret_kaydet', urun_id: productId },
            dataType: 'json'
        });
    }
    
    // Function to add product to cart
    function addToCart(productId, quantity) {
        // Get the selected secenek_id
        const secenekId = $('#selected-secenek-id').val();
        
        if (!secenekId) {
            alert('Lütfen önce bir ürün varyasyonu seçin.');
            return;
        }
        
        $.ajax({
            url: 'nedmin/netting/islem.php',
            type: 'POST',
            data: { 
                islem: 'sepete_ekle', 
                secenek_id: secenekId,
                adet: quantity 
            },
            dataType: 'json',
            success: function(response) {
                if (response.durum === 'success') {
                    // Update cart badge
                    $('.cart-badge').text(response.sepet_adet || 0);
                    
                    // Show success message
                    alert('Ürün sepete eklendi.');
                } else {
                    alert(response.mesaj || 'Ürün sepete eklenirken bir hata oluştu.');
                }
            },
            error: function() {
                alert('İşlem sırasında bir hata oluştu.');
            }
        });
    }
    
    // Function to toggle favorite status
    function toggleFavorite(productId) {
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
                        $('#add-to-favorites').html('<i class="fas fa-heart text-danger"></i>');
                        showToast('Ürün favorilerinize eklendi.', 'success');
                    } else {
                        $('#add-to-favorites').html('<i class="far fa-heart"></i>');
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
    
    // Function to show toast notification
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
    
    // Function to load product reviews
    function loadProductReviews(productId, page = 1, ratingFilter = []) {
        currentReviewsPage = page;
        currentRatingFilter = ratingFilter;
        
        let url = `nedmin/netting/islem.php?islem=urun_degerlendirme&urun_id=${productId}&sayfa=${page}&limit=${reviewsPerPage}`;
        
        // Add rating filter if any
        if (ratingFilter.length > 0) {
            url += `&puan_filtre=${ratingFilter.join(',')}`;
        }
        
        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.durum === 'success') {
                    displayProductReviews(response);
                } else {
                    $('#product-reviews').html(`<div class="text-center py-3">${response.mesaj || 'Değerlendirme bulunamadı.'}</div>`);
                }
            },
            error: function() {
                $('#product-reviews').html('<div class="text-center py-3">Değerlendirmeler yüklenirken bir hata oluştu.</div>');
            }
        });
    }
    
    // Function to display product reviews
    function displayProductReviews(data) {
        if (!data.degerlendirmeler || data.degerlendirmeler.length === 0 && currentReviewsPage === 1 && currentRatingFilter.length === 0) {
            $('#product-reviews').html('<div class="text-center py-3">Bu ürün için henüz değerlendirme yapılmamış.</div>');
            return;
        }
        
        // Calculate average rating
        const avgRating = parseFloat(data.ortalama_puan).toFixed(1);
        const totalRatings = data.pagination.toplam_kayit;
        
        let html = `
            <div class="row mb-4">
                <div class="col-md-4 text-center">
                    <h2 class="display-4 fw-bold">${avgRating}</h2>
                    <div class="mb-2">
        `;
        
        // Show stars for average rating
        for (let i = 1; i <= 5; i++) {
            if (i <= Math.round(avgRating)) {
                html += '<i class="fas fa-star text-warning"></i>';
            } else {
                html += '<i class="far fa-star text-warning"></i>';
            }
        }
        
        html += `
                    </div>
                    <p class="text-muted">${totalRatings} değerlendirme</p>
                </div>
                <div class="col-md-8">
                    <div class="rating-bars mb-3">
        `;
        
        // Rating distribution with clickable filter
        for (let i = 5; i >= 1; i--) {
            const ratingCount = data.puanlar && data.puanlar[i] ? data.puanlar[i] : 0;
            const percentage = totalRatings > 0 ? Math.round((ratingCount / totalRatings) * 100) : 0;
            const isSelected = currentRatingFilter.includes(i.toString());
            
            html += `
                <div class="d-flex align-items-center mb-1">
                    <div class="me-2">
                        <div class="form-check">
                            <input class="form-check-input rating-filter" type="checkbox" value="${i}" id="filter-${i}" ${isSelected ? 'checked' : ''}>
                            <label class="form-check-label" for="filter-${i}">
                                ${i} <i class="fas fa-star text-warning small"></i>
                            </label>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: ${percentage}%" aria-valuenow="${percentage}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    <div class="ms-2 small text-muted">${ratingCount}</div>
                </div>
            `;
        }
        
        html += `
                    </div>
                    <div class="d-flex justify-content-end">
                        <button class="btn btn-sm btn-outline-secondary filter-clear" ${currentRatingFilter.length === 0 ? 'disabled' : ''}>Filtreleri Temizle</button>
                    </div>
                </div>
            </div>
            <hr>
            <div class="reviews-list">
        `;
        
        // Display reviews with comments
        const reviewsWithComments = data.degerlendirmeler.filter(review => review.yorum);
        
        if (reviewsWithComments.length === 0) {
            if (currentRatingFilter.length > 0) {
                html += '<div class="text-center py-3">Seçili filtreye uygun yorum bulunamadı.</div>';
            } else if (currentReviewsPage > 1) {
                html += '<div class="text-center py-3">Bu sayfada yorum bulunamadı.</div>';
            } else {
                html += '<div class="text-center py-3">Bu ürün için henüz yorum yapılmamış.</div>';
            }
        } else {
            reviewsWithComments.forEach(review => {
                // Format date
                const reviewDate = new Date(review.tarih);
                const formattedDate = reviewDate.toLocaleDateString('tr-TR');
                
                html += `
                    <div class="review-item mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <strong>${review.kullanici_ad || 'Müşteri'}</strong>
                                <span class="text-muted ms-2 small">${formattedDate}</span>
                            </div>
                            <div class="rating">
                `;
                
                // Show stars for this review
                for (let i = 1; i <= 5; i++) {
                    if (i <= review.puan) {
                        html += '<i class="fas fa-star text-warning"></i>';
                    } else {
                        html += '<i class="far fa-star text-warning"></i>';
                    }
                }
                
                html += `
                            </div>
                        </div>
                        <p>${review.yorum}</p>
                `;
                
                // Display store response if exists
                if (review.magaza_yanit) {
                    const replyDate = new Date(review.magaza_yanit_tarih);
                    const formattedReplyDate = replyDate.toLocaleDateString('tr-TR');
                    
                    html += `
                        <div class="store-response bg-light p-3 mt-2 rounded">
                            <div class="d-flex justify-content-between mb-1">
                                <strong>Mağaza Yanıtı</strong>
                                <span class="text-muted small">${formattedReplyDate}</span>
                            </div>
                            <p class="mb-0">${review.magaza_yanit}</p>
                        </div>
                    `;
                }
                
                html += `
                    </div>
                    <hr>
                `;
            });
        }
        
        // Add pagination
        html += `</div>`;
        
        if (data.pagination.toplam_sayfa > 1) {
            html += `
                <nav aria-label="Değerlendirme Sayfaları">
                    <ul class="pagination pagination-sm justify-content-center mt-4">
            `;
            
            // Previous page button
            html += `
                <li class="page-item ${data.pagination.aktif_sayfa === 1 ? 'disabled' : ''}">
                    <a class="page-link reviews-page" href="#" data-page="${data.pagination.aktif_sayfa - 1}">Önceki</a>
                </li>
            `;
            
            // Page numbers
            let startPage = Math.max(1, data.pagination.aktif_sayfa - 2);
            let endPage = Math.min(data.pagination.toplam_sayfa, startPage + 4);
            
            if (endPage - startPage < 4) {
                startPage = Math.max(1, endPage - 4);
            }
            
            for (let i = startPage; i <= endPage; i++) {
                html += `
                    <li class="page-item ${i === data.pagination.aktif_sayfa ? 'active' : ''}">
                        <a class="page-link reviews-page" href="#" data-page="${i}">${i}</a>
                    </li>
                `;
            }
            
            // Next page button
            html += `
                <li class="page-item ${data.pagination.aktif_sayfa === data.pagination.toplam_sayfa ? 'disabled' : ''}">
                    <a class="page-link reviews-page" href="#" data-page="${data.pagination.aktif_sayfa + 1}">Sonraki</a>
                </li>
            `;
            
            html += `
                    </ul>
                </nav>
            `;
        }
        
        $('#product-reviews').html(html);
        
        // Add event listeners for pagination
        $('.reviews-page').on('click', function(e) {
            e.preventDefault();
            const page = parseInt($(this).data('page'));
            const urunId = <?php echo $urun_id; ?>;
            loadProductReviews(urunId, page, currentRatingFilter);
            
            // Scroll back to top of reviews
            $('html, body').animate({
                scrollTop: $('#product-reviews').offset().top - 100
            }, 200);
        });
        
        // Add event listeners for rating filters
        $('.rating-filter').on('change', function() {
            const selectedRatings = [];
            $('.rating-filter:checked').each(function() {
                selectedRatings.push($(this).val());
            });
            
            const urunId = <?php echo $urun_id; ?>;
            loadProductReviews(urunId, 1, selectedRatings);
        });
        
        // Clear filter button
        $('.filter-clear').on('click', function() {
            const urunId = <?php echo $urun_id; ?>;
            loadProductReviews(urunId, 1, []);
        });
    }
    
    // Function to load product questions
    function loadProductQuestions(productId, page = 1) {
        currentQuestionsPage = page;
        
        $.ajax({
            url: `nedmin/netting/islem.php?islem=urun_sorular&urun_id=${productId}&sayfa=${page}&limit=${questionsPerPage}`,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.durum === 'success') {
                    displayProductQuestions(response.sorular, response.pagination);
                } else {
                    $('#product-questions').html(`<div class="text-center py-3">${response.mesaj || 'Soru bulunamadı.'}</div>`);
                    $('#questions-pagination').html('');
                }
            },
            error: function() {
                $('#product-questions').html('<div class="text-center py-3">Sorular yüklenirken bir hata oluştu.</div>');
                $('#questions-pagination').html('');
            }
        });
    }
    
    // Function to display product questions
    function displayProductQuestions(questions, pagination) {
        if (!questions || questions.length === 0) {
            $('#product-questions').html('<div class="text-center py-3">Bu ürün için henüz soru sorulmamış.</div>');
            $('#questions-pagination').html('');
            return;
        }
        
        let html = '';
        
        questions.forEach(question => {
            // Format dates
            const questionDate = new Date(question.tarih);
            const formattedQuestionDate = questionDate.toLocaleDateString('tr-TR');
            
            html += `
                <div class="question-item mb-4">
                    <div class="d-flex align-items-center mb-2">
                        <div>
                            <i class="fas fa-question-circle text-primary me-2"></i>
                            <strong>${question.kullanici_ad || 'Müşteri'}</strong>
                            <span class="text-muted ms-2 small">${formattedQuestionDate}</span>
                        </div>
                    </div>
                    <p class="mb-2">${question.soru}</p>
            `;
            
            // Display answer if exists
            if (question.cevap) {
                const answerDate = new Date(question.cevap_tarih);
                const formattedAnswerDate = answerDate.toLocaleDateString('tr-TR');
                
                html += `
                    <div class="answer bg-light p-3 mt-2 rounded">
                        <div class="d-flex align-items-center mb-1">
                            <i class="fas fa-store text-success me-2"></i>
                            <strong>Mağaza Yanıtı</strong>
                            <span class="text-muted ms-2 small">${formattedAnswerDate}</span>
                        </div>
                        <p class="mb-0">${question.cevap}</p>
                    </div>
                `;
            }
            
            html += `
                </div>
                <hr>
            `;
        });
        
        $('#product-questions').html(html);
        
        // Generate pagination
        if (pagination && pagination.toplam_sayfa > 1) {
            let paginationHtml = `
                <nav aria-label="Soru Sayfaları">
                    <ul class="pagination pagination-sm justify-content-center mb-0">
            `;
            
            // Previous page button
            paginationHtml += `
                <li class="page-item ${pagination.aktif_sayfa === 1 ? 'disabled' : ''}">
                    <a class="page-link questions-page" href="#" data-page="${pagination.aktif_sayfa - 1}">Önceki</a>
                </li>
            `;
            
            // Page numbers
            let startPage = Math.max(1, pagination.aktif_sayfa - 2);
            let endPage = Math.min(pagination.toplam_sayfa, startPage + 4);
            
            if (endPage - startPage < 4) {
                startPage = Math.max(1, endPage - 4);
            }
            
            for (let i = startPage; i <= endPage; i++) {
                paginationHtml += `
                    <li class="page-item ${i === pagination.aktif_sayfa ? 'active' : ''}">
                        <a class="page-link questions-page" href="#" data-page="${i}">${i}</a>
                    </li>
                `;
            }
            
            // Next page button
            paginationHtml += `
                <li class="page-item ${pagination.aktif_sayfa === pagination.toplam_sayfa ? 'disabled' : ''}">
                    <a class="page-link questions-page" href="#" data-page="${pagination.aktif_sayfa + 1}">Sonraki</a>
                </li>
            `;
            
            paginationHtml += `
                    </ul>
                </nav>
            `;
            
            $('#questions-pagination').html(paginationHtml);
            
            // Add event listeners for pagination
            $('.questions-page').on('click', function(e) {
                e.preventDefault();
                const page = parseInt($(this).data('page'));
                const urunId = <?php echo $urun_id; ?>;
                loadProductQuestions(urunId, page);
                
                // Scroll back to top of questions
                $('html, body').animate({
                    scrollTop: $('#product-questions').offset().top - 100
                }, 200);
            });
        } else {
            $('#questions-pagination').html('');
        }
    }
</script>

<?php
include 'footer.php';
?> 