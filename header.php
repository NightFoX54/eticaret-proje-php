<?php
include 'nedmin/netting/baglan.php';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Ticaret</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Custom CSS -->
    <style>
        .top-bar {
            background-color: #f8f9fa;
            padding: 8px 0;
            font-size: 0.9rem;
        }
        .navbar-custom {
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .search-form {
            width: 100%;
            max-width: 600px;
        }
        .categories-nav {
            background-color: #f8f9fa;
            padding: 10px 0;
            border-bottom: 1px solid #e5e5e5;
        }
        .category-menu .dropdown-menu {
            width: 100%;
            padding: 15px;
            border-radius: 0;
            margin-top: 0;
            box-shadow: 0 6px 12px rgba(0,0,0,0.1);
        }
        .category-menu .dropdown-toggle::after {
            display: none;
        }
        .category-menu .nav-link {
            color: #333;
        }
        .category-menu .nav-link:hover {
            color: #f04e31;
        }
        .category-submenu {
            display: none;
            position: absolute;
            left: 100%;
            top: 0;
            width: 200px;
            background: white;
            box-shadow: 0 6px 12px rgba(0,0,0,0.1);
            z-index: 1000;
            padding: 10px 0;
        }
        .category-item:hover .category-submenu {
            display: block;
        }
        .cart-icon {
            position: relative;
        }
        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: #f04e31;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 0.7rem;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .notification-icon {
            position: relative;
        }
        .notification-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: #f04e31;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 0.7rem;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .dropdown-menu {
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
        }
        .user-dropdown .dropdown-item {
            padding: 0.5rem 1rem;
        }
        .user-dropdown .dropdown-item i {
            width: 20px;
            text-align: center;
            margin-right: 8px;
        }
        /* Search results styling */
        #search-results a:hover {
            text-decoration: none;
        }
        #search-results .hover-bg-light:hover {
            background-color: #f8f9fa;
        }
        #search-input:focus {
            box-shadow: none;
            border-color: #80bdff;
        }
    </style>
</head>
<body>
    <?php 
    // Start session if not already started
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    // Check if user is logged in
    $user_logged_in = isset($_SESSION['kullanici_id']);
    ?>
    
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <span><i class="fas fa-phone-alt me-1"></i> +90 555 123 45 67</span>
                    <span class="ms-3"><i class="fas fa-envelope me-1"></i> info@eticaret.com</span>
                </div>
                <div class="col-md-6 text-end">
                    <?php if ($user_logged_in): ?>
                        <div class="dropdown d-inline-block">
                            <a href="#" class="text-dark dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle me-1"></i> 
                                <?php echo $_SESSION['kullanici_ad'] . ' ' . $_SESSION['kullanici_soyad']; ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end user-dropdown" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user"></i> Profilim</a></li>
                                <li><a class="dropdown-item" href="orders.php"><i class="fas fa-shopping-bag"></i> Siparişlerim</a></li>
                                <li><a class="dropdown-item" href="address.php"><i class="fas fa-map-marker-alt"></i> Adreslerim</a></li>
                                <li><a class="dropdown-item" href="wishlist.php"><i class="fas fa-heart"></i> Favori Ürünlerim</a></li>
                                <li><a class="dropdown-item" href="notifications.php"><i class="fas fa-bell"></i> Bildirimlerim</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger logout-link" href="#"><i class="fas fa-sign-out-alt"></i> Çıkış Yap</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="login.php" class="text-dark me-3"><i class="fas fa-user me-1"></i> Giriş Yap</a>
                        <a href="register.php" class="text-dark"><i class="fas fa-user-plus me-1"></i> Kayıt Ol</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="index.php">E-Ticaret</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <form class="d-flex search-form mx-auto position-relative" role="search" id="search-form" action="urunler.php" method="GET">
                    <div class="input-group">
                        <input class="form-control" type="search" id="search-input" name="search" placeholder="Ürün, kategori veya marka ara" aria-label="Search" autocomplete="off">
                        <button class="btn btn-outline-primary" type="submit"><i class="fas fa-search"></i></button>
                    </div>
                    <!-- Live search results dropdown -->
                    <div id="search-results" class="position-absolute w-100 mt-1 bg-white shadow-sm rounded" style="top: 100%; left: 0; z-index: 1050; display: none; max-height: 400px; overflow-y: auto;"></div>
                </form>
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="wishlist.php"><i class="fas fa-heart"></i> Favoriler</a>
                    </li>
                    <?php if ($user_logged_in): ?>
                    <li class="nav-item">
                        <a class="nav-link notification-icon" href="notifications.php">
                            <i class="fas fa-bell"></i>
                            <span class="notification-badge" id="notification-badge" style="display: none;"></span>
                        </a>
                    </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link cart-icon" href="cart.php">
                            <i class="fas fa-shopping-cart"></i> Sepet
                            <?php if(isset($_SESSION['kullanici_id'])): ?>
                                <?php 
                                $sepet_adet = $db->prepare("SELECT SUM(adet) as adet FROM sepet WHERE kullanici_id = :kullanici_id");
                                $sepet_adet->execute(['kullanici_id' => $_SESSION['kullanici_id']]);
                                $sepet_adet = $sepet_adet->fetch(PDO::FETCH_ASSOC)['adet'] ?? 0;
                                ?>
                                <span class="cart-badge"><?php echo $sepet_adet; ?></span>
                            <?php else: ?>
                                <?php
                                $sepet_adet = 0;
                                if(isset($_SESSION['sepet'])){
                                    foreach($_SESSION['sepet'] as $item){
                                        $sepet_adet += $item['adet'];
                                    }
                                }
                                ?>
                                <span class="cart-badge"><?php echo $sepet_adet; ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Categories Navigation -->
    <div class="categories-nav">
        <div class="container">
            <ul class="nav category-menu" id="main-categories">
                <!-- Categories will be loaded dynamically -->
                <li class="nav-item">
                    <a class="nav-link" href="urunler.php">
                        <i class="fas fa-bars me-2"></i> Tüm Ürünler
                    </a>
                </li>
                <!-- Additional category items will be added here -->
            </ul>
        </div>
    </div>

    <!-- Main Content Container -->
    <div class="container mt-4">
    
    <!-- AJAX Logout Script -->
    <script>
        $(document).ready(function() {
            // Load main categories
            loadMainCategories();
            saveVisit();
            // Load notifications if user is logged in
            <?php if ($user_logged_in): ?>
            loadNotificationsCount();
            <?php endif; ?>

            // Live search functionality
            let searchTimeout;
            const searchInput = $('#search-input');
            const searchResults = $('#search-results');

            searchInput.on('input', function() {
                const query = $(this).val();
                clearTimeout(searchTimeout);

                if (query.length >= 2) {
                    searchTimeout = setTimeout(function() {
                        $.ajax({
                            url: 'nedmin/netting/islem.php',
                            type: 'GET',
                            data: { 
                                islem: 'live_search', 
                                query: query 
                            },
                            dataType: 'json',
                            success: function(response) {
                                if (response.durum === 'success') {
                                    displayLiveSearchResults(response.results);
                                } else {
                                    searchResults.html('<div class="p-3 text-muted">Sonuç bulunamadı</div>').show();
                                }
                            },
                            error: function() {
                                searchResults.html('<div class="p-3 text-danger">Bir hata oluştu</div>').show();
                            }
                        });
                    }, 300);
                } else {
                    searchResults.hide();
                }
            });

            // Hide search results when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#search-form').length) {
                    searchResults.hide();
                }
            });

            // Handle form submission
            $('#search-form').on('submit', function(e) {
                const query = searchInput.val();
                if (query.trim() === '') {
                    e.preventDefault();
                }
            });

            // Function to display live search results
            function displayLiveSearchResults(results) {
                if (results.length === 0) {
                    searchResults.html('<div class="p-3 text-muted">Sonuç bulunamadı</div>').show();
                    return;
                }

                let html = '';
                results.forEach(function(item) {
                    html += `
                    <a href="urun.php?id=${item.id}" class="text-decoration-none text-dark">
                        <div class="d-flex align-items-center p-2 border-bottom hover-bg-light">
                            <div class="flex-shrink-0">
                                <img src="${item.foto_path || 'uploads/no-image.png'}" alt="${item.urun_isim}" width="50" height="50" style="object-fit: contain;">
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="fw-semibold">${item.urun_isim}</div>
                                <div class="small text-muted">${item.kategori_isim || ''} ${item.marka_isim ? '- ' + item.marka_isim : ''}</div>
                                <div class="text-primary fw-bold">₺${parseFloat(item.urun_fiyat).toFixed(2)}</div>
                            </div>
                        </div>
                    </a>`;
                });

                html += `
                <div class="p-2 bg-light text-center">
                    <a href="urunler.php?search=${encodeURIComponent(searchInput.val())}" class="text-primary">
                        Tüm sonuçları görüntüle <i class="fas fa-arrow-right"></i>
                    </a>
                </div>`;

                searchResults.html(html).show();
            }
        });
        
        document.addEventListener('DOMContentLoaded', function() {
            const logoutLinks = document.querySelectorAll('.logout-link');
            
            logoutLinks.forEach(function(link) {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // AJAX request for logout
                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', 'logout.php', true);
                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    
                    xhr.onload = function() {
                        if (xhr.status === 200) {
                            const response = JSON.parse(xhr.responseText);
                            if (response.durum === 'success') {
                                window.location.href = response.redirect;
                            }
                        }
                    };
                    
                    xhr.send();
                });
            });
        });

        function loadMainCategories() {
            $.ajax({
                url: 'nedmin/netting/islem.php',
                type: 'GET',
                data: { islem: 'kategori_getir' },
                dataType: 'json',
                success: function(response) {
                    if (response.durum === 'success') {
                        displayMainCategories(response.kategoriler);
                    }
                }
            });
        }
        
        // Function to display main categories
        function displayMainCategories(categories) {
            const mainCategoriesContainer = $('#main-categories');
            // Keep the "Tüm Kategoriler" item
            const allCategoriesItem = mainCategoriesContainer.html();
            mainCategoriesContainer.html(allCategoriesItem);
            
            // Get only main categories (no parent)
            const mainCategories = categories.filter(category => !category.parent_kategori_id);
            
            // Add main categories to the menu
            mainCategories.forEach(category => {
                const hasChildren = categories.some(cat => cat.parent_kategori_id == category.id);
                const categoryItem = `
                    <li class="nav-item">
                        <a class="nav-link" href="urunler.php?kategori=${category.id}">
                            ${category.kategori_isim}
                        </a>
                    </li>
                `;
                mainCategoriesContainer.append(categoryItem);
            });
        }
        
        // Function to load notification count
        function loadNotificationsCount() {
            $.ajax({
                url: 'nedmin/netting/islem.php',
                type: 'POST',
                data: { islem: 'bildirim_sayisi' },
                dataType: 'json',
                success: function(response) {
                    if (response.durum === 'success') {
                        const count = response.bildirim_sayisi;
                        if (count > 0) {
                            $('#notification-badge').text(count).show();
                        } else {
                            $('#notification-badge').hide();
                        }
                    }
                }
            });
        }
        function saveVisit(){
            $.ajax({
                url: 'nedmin/netting/islem.php',
                type: 'POST',
                data: { islem: 'ziyaret_kayit' },
                success: function(response){
                    console.log(response);
                },
                error: function(xhr, status, error){
                    console.log(xhr.responseText);
                }
            });
            
        }
    </script>
</body>
</html> 