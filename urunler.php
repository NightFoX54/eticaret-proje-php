<?php
include 'header.php';
?>

<!-- Product Listing Section -->
<div class="container py-4">
    <div class="row">
        <!-- Sidebar Filters -->
        <div class="col-lg-3">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Filtreler</h5>
                </div>
                <div class="card-body">
                    <!-- Category Filter -->
                    <div class="mb-4">
                        <h6 class="fw-bold mb-3">Kategoriler</h6>
                        <div id="kategori-filtreler" class="overflow-auto" style="max-height: 250px;">
                            <!-- Categories will be loaded via AJAX -->
                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                <span class="visually-hidden">Yükleniyor...</span>
                            </div>
                        </div>
                    </div>

                    <!-- Price Range Filter -->
                    <div class="mb-4">
                        <h6 class="fw-bold mb-3">Fiyat Aralığı</h6>
                        <div class="d-flex align-items-center">
                            <input type="number" id="min-fiyat" class="form-control form-control-sm" placeholder="Min">
                            <span class="mx-2">-</span>
                            <input type="number" id="max-fiyat" class="form-control form-control-sm" placeholder="Max">
                        </div>
                        <button id="fiyat-filtrele" class="btn btn-sm btn-outline-primary mt-2 w-100">Uygula</button>
                    </div>

                    <!-- Attributes Filter -->
                    <div id="nitelik-filtreler">
                        <!-- Attributes will be loaded via AJAX -->
                    </div>
                    
                    <!-- Brands Filter -->
                    <div class="mb-4">
                        <h6 class="fw-bold mb-3">Markalar</h6>
                        <div id="marka-filtreler" class="overflow-auto" style="max-height: 200px;">
                            <!-- Brands will be loaded via AJAX -->
                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                <span class="visually-hidden">Yükleniyor...</span>
                            </div>
                        </div>
                    </div>

                    <!-- Reset Filters Button -->
                    <button id="reset-filtreler" class="btn btn-danger w-100">Filtreleri Temizle</button>
                </div>
            </div>
        </div>

        <!-- Product Listing -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 id="urun-baslik">Tüm Ürünler</h4>
                    <small id="urun-sayisi">Yükleniyor...</small>
                    <div id="search-info" class="mt-2" style="display: none;">
                        <span class="badge bg-primary">Arama sonuçları</span>
                        <small class="ms-2" id="search-query-display"></small>
                        <button class="btn btn-sm btn-outline-secondary ms-2" id="clear-search">Aramayı Temizle</button>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <label for="siralama" class="me-2">Sırala:</label>
                    <select id="siralama" class="form-select form-select-sm" style="width: 200px;">
                        <option value="default">Varsayılan Sıralama</option>
                        <option value="newest">En Yeniler</option>
                        <option value="price_asc">Fiyat (Düşükten Yükseğe)</option>
                        <option value="price_desc">Fiyat (Yüksekten Düşüğe)</option>
                        <option value="popular">En Çok Ziyaret Edilenler</option>
                    </select>
                </div>
            </div>

            <!-- Products Grid -->
            <div id="urun-listesi" class="row g-3">
                <!-- Products will be loaded via AJAX -->
                <div class="col-12 text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Yükleniyor...</span>
                    </div>
                    <p class="mt-2">Ürünler yükleniyor...</p>
                </div>
            </div>

            <!-- Pagination -->
            <div id="sayfalama" class="d-flex justify-content-center mt-4">
                <!-- Pagination will be added via AJAX -->
            </div>
        </div>
    </div>
</div>

<!-- Product Template (Hidden) -->
<template id="urun-template">
    <div class="col-md-4 col-sm-6">
        <div class="card h-100 product-card">
            <a href="" class="product-link text-decoration-none text-dark d-flex flex-column h-100">
                <div class="position-relative">
                    <img src="" class="card-img-top urun-resim" alt="Ürün Resmi" style="height: 200px; object-fit: contain;">
                    <button class="btn btn-sm position-absolute top-0 end-0 m-2 favorite-btn">
                        <i class="far fa-heart"></i>
                    </button>
                </div>
                <div class="card-body d-flex flex-column">
                    <h6 class="card-title urun-isim">Ürün Adı</h6>
                    <div class="small text-muted mb-2 urun-marka">Marka Adı</div>
                    <div class="urun-rating mb-2">
                        <div class="stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                            <i class="far fa-star"></i>
                        </div>
                        <small class="text-muted rating-count">(0)</small>
                    </div>
                    <div class="mt-auto">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold urun-fiyat">₺0.00</span>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</template>

<!-- AJAX Scripts -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Store filter state
    const filterState = {
        kategori: null,
        fiyat: {
            min: null,
            max: null
        },
        nitelikler: {},
        marka: null,
        siralama: 'default',
        sayfa: 1,
        search: null // Add search parameter to filter state
    };

    // Check for URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    const categoryParam = urlParams.get('kategori');
    const searchParam = urlParams.get('search'); // Get search parameter from URL
    
    // Set the category from URL if present
    if (categoryParam) {
        filterState.kategori = parseInt(categoryParam);
    }
    
    // Set search parameter from URL if present
    if (searchParam) {
        filterState.search = searchParam;
        document.getElementById('search-query-display').textContent = `"${searchParam}"`;
        document.getElementById('search-info').style.display = 'block';
    }

    // Load initial data
    loadKategoriler();
    // If category is selected from URL, refresh filters accordingly
    if (filterState.kategori) {
        // First load products, then load filtered options
        loadUrunler();
        loadMarkalar();
        loadNitelikler();
    } else {
        // Standard loading sequence when no category is pre-selected
        loadMarkalar();
        loadNitelikler();
        loadUrunler();
    }

    // Event listeners
    document.getElementById('siralama').addEventListener('change', function() {
        filterState.siralama = this.value;
        filterState.sayfa = 1;
        loadUrunler();
    });
    
    // Clear search button event listener
    document.getElementById('clear-search').addEventListener('click', function() {
        filterState.search = null;
        document.getElementById('search-info').style.display = 'none';
        
        // Update URL by removing search parameter
        const url = new URL(window.location);
        url.searchParams.delete('search');
        window.history.pushState({}, '', url);
        
        loadUrunler();
    });

    document.getElementById('fiyat-filtrele').addEventListener('click', function() {
        const minFiyat = document.getElementById('min-fiyat').value;
        const maxFiyat = document.getElementById('max-fiyat').value;
        
        filterState.fiyat.min = minFiyat !== '' ? parseInt(minFiyat) : null;
        filterState.fiyat.max = maxFiyat !== '' ? parseInt(maxFiyat) : null;
        filterState.sayfa = 1;
        
        loadUrunler();
        // Refresh filter options when price filter changes
        refreshFilters();
    });

    document.getElementById('reset-filtreler').addEventListener('click', function() {
        // Reset filter state
        filterState.kategori = null;
        filterState.fiyat.min = null;
        filterState.fiyat.max = null;
        filterState.nitelikler = {};
        filterState.marka = null;
        filterState.siralama = 'default';
        filterState.sayfa = 1;
        // Don't reset search parameter here to allow filter reset while keeping search
        
        // Reset UI
        document.getElementById('min-fiyat').value = '';
        document.getElementById('max-fiyat').value = '';
        document.getElementById('siralama').value = 'default';
        
        // Reset checkboxes
        const checkboxes = document.querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        
        // Reload products and filters
        loadUrunler();
        refreshFilters();
    });

    // Function to refresh all filter options based on current filters
    function refreshFilters() {
        loadMarkalar();
        loadNitelikler();
    }

    // Load categories
    function loadKategoriler() {
        fetch('nedmin/netting/islem.php?islem=kategori_getir')
            .then(response => response.json())
            .then(data => {
                if (data.durum === 'success') {
                    renderKategoriler(data.kategoriler);
                } else {
                    console.error('Kategoriler yüklenirken hata oluştu:', data.mesaj);
                }
            })
            .catch(error => {
                console.error('Kategoriler yüklenirken hata oluştu:', error);
            });
    }

    // Render categories as a tree
    function renderKategoriler(kategoriler) {
        const container = document.getElementById('kategori-filtreler');
        container.innerHTML = '';

        // Build category tree
        const categoryTree = buildCategoryTree(kategoriler);
        
        // Render category tree
        const ul = document.createElement('ul');
        ul.className = 'list-unstyled mb-0';
        
        categoryTree.forEach(category => {
            renderCategoryItem(category, ul);
        });
        
        container.appendChild(ul);

        // Add event listeners to category checkboxes
        const categoryCheckboxes = container.querySelectorAll('input[type="checkbox"]');
        categoryCheckboxes.forEach(checkbox => {
            // If this is the category from URL, check it
            if (filterState.kategori && parseInt(checkbox.value) === filterState.kategori) {
                checkbox.checked = true;
            }
            
            checkbox.addEventListener('change', function() {
                // If this category is checked, uncheck others
                if (this.checked) {
                    categoryCheckboxes.forEach(cb => {
                        if (cb !== this) cb.checked = false;
                    });
                    filterState.kategori = parseInt(this.value);
                } else {
                    filterState.kategori = null;
                }
                
                filterState.sayfa = 1;
                loadUrunler();
                // Refresh filter options when category changes
                refreshFilters();
                
                // Update URL with category parameter
                const url = new URL(window.location);
                if (filterState.kategori) {
                    url.searchParams.set('kategori', filterState.kategori);
                } else {
                    url.searchParams.delete('kategori');
                }
                window.history.pushState({}, '', url);
            });
        });
    }

    // Helper function to build category tree
    function buildCategoryTree(kategoriler) {
        const categoryMap = {};
        const rootCategories = [];
        
        // First pass: create category objects
        kategoriler.forEach(kategori => {
            categoryMap[kategori.id] = {
                ...kategori,
                children: []
            };
        });
        
        // Second pass: build tree structure
        kategoriler.forEach(kategori => {
            if (kategori.parent_kategori_id) {
                // This is a child category
                if (categoryMap[kategori.parent_kategori_id]) {
                    categoryMap[kategori.parent_kategori_id].children.push(categoryMap[kategori.id]);
                }
            } else {
                // This is a root category
                rootCategories.push(categoryMap[kategori.id]);
            }
        });
        
        return rootCategories;
    }

    // Helper function to render a category item
    function renderCategoryItem(category, parentElement) {
        const li = document.createElement('li');
        li.className = 'mb-2';
        
        const checkboxWrapper = document.createElement('div');
        checkboxWrapper.className = 'd-flex align-items-center';
        
        const checkbox = document.createElement('input');
        checkbox.type = 'checkbox';
        checkbox.className = 'form-check-input me-2';
        checkbox.id = `category-${category.id}`;
        checkbox.value = category.id;
        
        const label = document.createElement('label');
        label.className = 'form-check-label';
        label.htmlFor = `category-${category.id}`;
        label.textContent = category.kategori_isim;
        
        checkboxWrapper.appendChild(checkbox);
        checkboxWrapper.appendChild(label);
        li.appendChild(checkboxWrapper);
        
        // If this category has children
        if (category.children && category.children.length > 0) {
            const childrenUl = document.createElement('ul');
            childrenUl.className = 'list-unstyled ms-3 mt-2';
            
            category.children.forEach(childCategory => {
                renderCategoryItem(childCategory, childrenUl);
            });
            
            li.appendChild(childrenUl);
        }
        
        parentElement.appendChild(li);
    }

    // Load brands
    function loadMarkalar() {
        // Show loading spinner
        const container = document.getElementById('marka-filtreler');
        container.innerHTML = `
            <div class="spinner-border spinner-border-sm text-primary" role="status">
                <span class="visually-hidden">Yükleniyor...</span>
            </div>
        `;
        
        // Build query parameters based on current filters
        const params = new URLSearchParams();
        params.append('islem', 'marka_getir');
        
        if (filterState.kategori !== null) {
            params.append('kategori', filterState.kategori);
        }
        
        if (filterState.fiyat.min !== null) {
            params.append('fiyat_min', filterState.fiyat.min);
        }
        
        if (filterState.fiyat.max !== null) {
            params.append('fiyat_max', filterState.fiyat.max);
        }
        
        if (Object.keys(filterState.nitelikler).length > 0) {
            params.append('nitelikler', JSON.stringify(filterState.nitelikler));
        }
        
        // Add search parameter
        if (filterState.search !== null) {
            params.append('search', filterState.search);
        }

        fetch(`nedmin/netting/islem.php?${params.toString()}`)
            .then(response => response.json())
            .then(data => {
                if (data.durum === 'success') {
                    renderMarkalar(data.markalar);
                } else {
                    console.error('Markalar yüklenirken hata oluştu:', data.mesaj);
                    container.innerHTML = '<p class="text-danger small">Markalar yüklenemedi</p>';
                }
            })
            .catch(error => {
                console.error('Markalar yüklenirken hata oluştu:', error);
                container.innerHTML = '<p class="text-danger small">Markalar yüklenemedi</p>';
            });
    }

    // Render brands
    function renderMarkalar(markalar) {
        const container = document.getElementById('marka-filtreler');
        container.innerHTML = '';
        
        if (markalar.length === 0) {
            container.innerHTML = '<p class="text-muted small">Bu filtrelerde marka bulunamadı</p>';
            return;
        }
        
        markalar.forEach(marka => {
            const div = document.createElement('div');
            div.className = 'form-check mb-2';
            
            const input = document.createElement('input');
            input.className = 'form-check-input';
            input.type = 'checkbox';
            input.id = `marka-${marka.id}`;
            input.value = marka.id;
            
            // If this brand is currently selected, check it
            if (filterState.marka === parseInt(marka.id)) {
                input.checked = true;
            }
            
            const label = document.createElement('label');
            label.className = 'form-check-label';
            label.htmlFor = `marka-${marka.id}`;
            label.textContent = marka.marka_isim;
            
            div.appendChild(input);
            div.appendChild(label);
            container.appendChild(div);
        });
        
        // Add event listeners to brand checkboxes
        const brandCheckboxes = container.querySelectorAll('input[type="checkbox"]');
        brandCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                // If this brand is checked, uncheck others
                if (this.checked) {
                    brandCheckboxes.forEach(cb => {
                        if (cb !== this) cb.checked = false;
                    });
                    filterState.marka = parseInt(this.value);
                } else {
                    filterState.marka = null;
                }
                
                filterState.sayfa = 1;
                loadUrunler();
                // Refresh attribute filters when brand changes
                loadNitelikler();
            });
        });
    }

    // Load attributes
    function loadNitelikler() {
        // Show loading spinner
        const container = document.getElementById('nitelik-filtreler');
        container.innerHTML = `
            <div class="spinner-border spinner-border-sm text-primary" role="status">
                <span class="visually-hidden">Yükleniyor...</span>
            </div>
        `;
        
        // Build query parameters based on current filters
        const params = new URLSearchParams();
        params.append('islem', 'nitelik_getir');
        
        if (filterState.kategori !== null) {
            params.append('kategori', filterState.kategori);
        }
        
        if (filterState.marka !== null) {
            params.append('marka', filterState.marka);
        }
        
        if (filterState.fiyat.min !== null) {
            params.append('fiyat_min', filterState.fiyat.min);
        }
        
        if (filterState.fiyat.max !== null) {
            params.append('fiyat_max', filterState.fiyat.max);
        }
        
        if (Object.keys(filterState.nitelikler).length > 0) {
            params.append('nitelikler', JSON.stringify(filterState.nitelikler));
        }
        
        // Add search parameter
        if (filterState.search !== null) {
            params.append('search', filterState.search);
        }

        fetch(`nedmin/netting/islem.php?${params.toString()}`)
            .then(response => response.json())
            .then(data => {
                if (data.durum === 'success') {
                    renderNitelikler(data.nitelikler);
                } else {
                    console.error('Nitelikler yüklenirken hata oluştu:', data.mesaj);
                    container.innerHTML = '<p class="text-danger small">Nitelikler yüklenemedi</p>';
                }
            })
            .catch(error => {
                console.error('Nitelikler yüklenirken hata oluştu:', error);
                container.innerHTML = '<p class="text-danger small">Nitelikler yüklenemedi</p>';
            });
    }

    // Render attributes
    function renderNitelikler(nitelikler) {
        const container = document.getElementById('nitelik-filtreler');
        container.innerHTML = '';
        
        if (nitelikler.length === 0) {
            container.innerHTML = '<p class="text-muted small">Bu filtrelerde nitelik bulunamadı</p>';
            return;
        }
        
        nitelikler.forEach(nitelik => {
            const attributeDiv = document.createElement('div');
            attributeDiv.className = 'mb-4';
            
            const attributeTitle = document.createElement('h6');
            attributeTitle.className = 'fw-bold mb-3';
            attributeTitle.textContent = nitelik.ad;
            
            attributeDiv.appendChild(attributeTitle);
            
            // Create checkboxes for each attribute value
            nitelik.degerler.forEach(deger => {
                const div = document.createElement('div');
                div.className = 'form-check mb-2';
                
                const input = document.createElement('input');
                input.className = 'form-check-input';
                input.type = 'checkbox';
                input.id = `nitelik-${nitelik.nitelik_id}-${deger.deger_id}`;
                input.dataset.nitelikId = nitelik.nitelik_id;
                input.dataset.degerId = deger.deger_id;
                
                // If this attribute value is currently selected, check it
                if (filterState.nitelikler[nitelik.nitelik_id] && 
                    filterState.nitelikler[nitelik.nitelik_id].includes(parseInt(deger.deger_id))) {
                    input.checked = true;
                }
                
                const label = document.createElement('label');
                label.className = 'form-check-label';
                label.htmlFor = `nitelik-${nitelik.nitelik_id}-${deger.deger_id}`;
                label.textContent = deger.deger;
                
                div.appendChild(input);
                div.appendChild(label);
                attributeDiv.appendChild(div);
            });
            
            container.appendChild(attributeDiv);
        });
        
        // Add event listeners to attribute checkboxes
        const attributeCheckboxes = container.querySelectorAll('input[type="checkbox"]');
        attributeCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const nitelikId = this.dataset.nitelikId;
                const degerId = this.dataset.degerId;
                
                if (!filterState.nitelikler[nitelikId]) {
                    filterState.nitelikler[nitelikId] = [];
                }
                
                if (this.checked) {
                    filterState.nitelikler[nitelikId].push(parseInt(degerId));
                } else {
                    filterState.nitelikler[nitelikId] = filterState.nitelikler[nitelikId].filter(id => id !== parseInt(degerId));
                    
                    if (filterState.nitelikler[nitelikId].length === 0) {
                        delete filterState.nitelikler[nitelikId];
                    }
                }
                
                filterState.sayfa = 1;
                loadUrunler();
                
                // When a nitelik changes, refresh other nitelik filters and marka filters
                // We need to pass the current nitelik_id to avoid reloading this specific attribute filter
                const params = new URLSearchParams();
                params.append('islem', 'nitelik_getir');
                params.append('selected_nitelik_id', nitelikId);
                
                if (filterState.kategori !== null) {
                    params.append('kategori', filterState.kategori);
                }
                
                if (filterState.marka !== null) {
                    params.append('marka', filterState.marka);
                }
                
                if (filterState.fiyat.min !== null) {
                    params.append('fiyat_min', filterState.fiyat.min);
                }
                
                if (filterState.fiyat.max !== null) {
                    params.append('fiyat_max', filterState.fiyat.max);
                }
                
                if (Object.keys(filterState.nitelikler).length > 0) {
                    params.append('nitelikler', JSON.stringify(filterState.nitelikler));
                }
                
                loadMarkalar();
                
                // Reload other nitelik filters except the one that was just changed
                fetch(`nedmin/netting/islem.php?${params.toString()}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.durum === 'success') {
                            updateNiteliklerExcept(data.nitelikler, parseInt(nitelikId));
                        }
                    })
                    .catch(error => {
                        console.error('Nitelikler güncellenirken hata oluştu:', error);
                    });
            });
        });
    }
    
    // Update attribute filters except the one that just changed
    function updateNiteliklerExcept(nitelikler, exceptNitelikId) {
        nitelikler.forEach(nitelik => {
            // Skip the nitelik that was just changed
            if (parseInt(nitelik.nitelik_id) === exceptNitelikId) {
                return;
            }
            
            // Find the container for this nitelik
            const nitelikContainer = document.querySelector(`[data-nitelik-id="${nitelik.nitelik_id}"]`).closest('.mb-4');
            if (!nitelikContainer) return;
            
            // Clear the existing checkboxes
            const checkboxWrapper = document.createElement('div');
            checkboxWrapper.className = 'mb-4';
            
            const attributeTitle = nitelikContainer.querySelector('h6').cloneNode(true);
            checkboxWrapper.appendChild(attributeTitle);
            
            // Create new checkboxes for each attribute value
            nitelik.degerler.forEach(deger => {
                const div = document.createElement('div');
                div.className = 'form-check mb-2';
                
                const input = document.createElement('input');
                input.className = 'form-check-input';
                input.type = 'checkbox';
                input.id = `nitelik-${nitelik.nitelik_id}-${deger.deger_id}`;
                input.dataset.nitelikId = nitelik.nitelik_id;
                input.dataset.degerId = deger.deger_id;
                
                // If this attribute value is currently selected, check it
                if (filterState.nitelikler[nitelik.nitelik_id] && 
                    filterState.nitelikler[nitelik.nitelik_id].includes(parseInt(deger.deger_id))) {
                    input.checked = true;
                }
                
                const label = document.createElement('label');
                label.className = 'form-check-label';
                label.htmlFor = `nitelik-${nitelik.nitelik_id}-${deger.deger_id}`;
                label.textContent = deger.deger;
                
                div.appendChild(input);
                div.appendChild(label);
                checkboxWrapper.appendChild(div);
            });
            
            // Replace the old container with the new one
            nitelikContainer.parentNode.replaceChild(checkboxWrapper, nitelikContainer);
            
            // Reattach event listeners
            checkboxWrapper.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const nitelikId = this.dataset.nitelikId;
                    const degerId = this.dataset.degerId;
                    
                    if (!filterState.nitelikler[nitelikId]) {
                        filterState.nitelikler[nitelikId] = [];
                    }
                    
                    if (this.checked) {
                        filterState.nitelikler[nitelikId].push(parseInt(degerId));
                    } else {
                        filterState.nitelikler[nitelikId] = filterState.nitelikler[nitelikId].filter(id => id !== parseInt(degerId));
                        
                        if (filterState.nitelikler[nitelikId].length === 0) {
                            delete filterState.nitelikler[nitelikId];
                        }
                    }
                    
                    filterState.sayfa = 1;
                    loadUrunler();
                    refreshFilters();
                });
            });
        });
    }

    // Load products
    function loadUrunler() {
        const container = document.getElementById('urun-listesi');
        const loadingIndicator = `
            <div class="col-12 text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Yükleniyor...</span>
                </div>
                <p class="mt-2">Ürünler yükleniyor...</p>
            </div>
        `;
        
        container.innerHTML = loadingIndicator;
        
        // Prepare filter data
        const filterData = {
            kategori: filterState.kategori,
            fiyat_min: filterState.fiyat.min,
            fiyat_max: filterState.fiyat.max,
            nitelikler: filterState.nitelikler,
            marka: filterState.marka,
            siralama: filterState.siralama,
            sayfa: filterState.sayfa,
            search: filterState.search // Add search parameter to filter data
        };
        
        // Convert to URL params
        const params = new URLSearchParams();
        params.append('islem', 'urun_filtrele');
        
        for (const [key, value] of Object.entries(filterData)) {
            if (value !== null && value !== undefined && (typeof value !== 'object' || Object.keys(value).length > 0)) {
                if (typeof value === 'object') {
                    params.append(key, JSON.stringify(value));
                } else {
                    params.append(key, value);
                }
            }
        }
        
        // Fetch products
        fetch(`nedmin/netting/islem.php?${params.toString()}`)
            .then(response => response.json())
            .then(data => {
                if (data.durum === 'success') {
                    renderUrunler(data.urunler);
                    renderPagination(data.toplam_sayfa, filterState.sayfa);
                    
                    // Update product count
                    document.getElementById('urun-sayisi').textContent = `${data.toplam_urun} ürün bulundu`;
                    
                    // Update title if category is selected or search is active
                    if (data.kategori_bilgisi && filterState.kategori) {
                        document.getElementById('urun-baslik').textContent = data.kategori_bilgisi.kategori_isim;
                    } else if (filterState.search) {
                        document.getElementById('urun-baslik').textContent = 'Arama Sonuçları';
                    } else {
                        document.getElementById('urun-baslik').textContent = 'Tüm Ürünler';
                    }
                } else {
                    container.innerHTML = `
                        <div class="col-12 text-center py-5">
                            <div class="alert alert-warning">
                                ${data.mesaj || 'Ürünler yüklenirken bir hata oluştu.'}
                            </div>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Ürünler yüklenirken hata oluştu:', error);
                container.innerHTML = `
                    <div class="col-12 text-center py-5">
                        <div class="alert alert-danger">
                            Ürünler yüklenirken bir hata oluştu. Lütfen daha sonra tekrar deneyin.
                        </div>
                    </div>
                `;
            });
    }

    // Render products
    function renderUrunler(urunler) {
        const container = document.getElementById('urun-listesi');
        const template = document.getElementById('urun-template');
        
        container.innerHTML = '';
        
        if (urunler.length === 0) {
            container.innerHTML = `
                <div class="col-12 text-center py-5">
                    <div class="alert alert-info">
                        Bu kriterlere uygun ürün bulunamadı.
                    </div>
                </div>
            `;
            return;
        }
        
        // First, check which products are in favorites
        checkFavoriteStatus(urunler.map(urun => urun.id))
            .then(favoriteProducts => {
                urunler.forEach(urun => {
                    const productClone = template.content.cloneNode(true);
                    
                    // Set product data
                    productClone.querySelector('.urun-isim').textContent = urun.urun_isim;
                    productClone.querySelector('.urun-marka').textContent = urun.marka_isim;
                    productClone.querySelector('.urun-fiyat').textContent = `₺${parseFloat(urun.urun_fiyat).toFixed(2)}`;
                    
                    // Set product image
                    const imgElement = productClone.querySelector('.urun-resim');
                    if (urun.foto_path) {
                        imgElement.src = urun.foto_path;
                    } else {
                        imgElement.src = 'uploads/no-image.png'; // Default image
                    }
                    
                    // Set product link
                    const productLink = productClone.querySelector('.product-link');
                    productLink.href = `urun.php?id=${urun.id}`;
                    
                    // Set rating stars if rating data is available
                    if (urun.ortalama_puan !== undefined) {
                        updateRatingStars(productClone, urun.ortalama_puan, urun.degerlendirme_sayisi || 0);
                    } else {
                        productClone.querySelector('.urun-rating').style.display = 'none';
                    }
                    
                    // Favorite button
                    const favoriteBtn = productClone.querySelector('.favorite-btn');
                    // Check if this product is in favorites
                    if (favoriteProducts.includes(parseInt(urun.id))) {
                        favoriteBtn.innerHTML = '<i class="fas fa-heart text-danger"></i>';
                    } else {
                        favoriteBtn.innerHTML = '<i class="far fa-heart"></i>';
                    }
                    favoriteBtn.dataset.id = urun.id;
                    favoriteBtn.addEventListener('click', function(e) {
                        e.stopPropagation();
                        e.preventDefault(); // Prevent the link navigation
                        toggleFavorite(urun.id, favoriteBtn);
                    });
                    
                    container.appendChild(productClone);
                });
            });
    }

    // Update rating stars based on average rating
    function updateRatingStars(productElement, rating, count) {
        const starsContainer = productElement.querySelector('.stars');
        const ratingCount = productElement.querySelector('.rating-count');
        
        // Clear existing stars
        starsContainer.innerHTML = '';
        
        // Add full, half and empty stars based on rating
        const fullStars = Math.floor(rating);
        const hasHalfStar = rating % 1 >= 0.5;
        
        // Add full stars
        for (let i = 0; i < fullStars; i++) {
            starsContainer.innerHTML += '<i class="fas fa-star text-warning"></i>';
        }
        
        // Add half star if needed
        if (hasHalfStar) {
            starsContainer.innerHTML += '<i class="fas fa-star-half-alt text-warning"></i>';
        }
        
        // Add empty stars
        const emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0);
        for (let i = 0; i < emptyStars; i++) {
            starsContainer.innerHTML += '<i class="far fa-star text-warning"></i>';
        }
        
        // Update rating count
        ratingCount.textContent = `(${count})`;
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

    // Render pagination
    function renderPagination(totalPages, currentPage) {
        const container = document.getElementById('sayfalama');
        container.innerHTML = '';
        
        if (totalPages <= 1) {
            return;
        }
        
        const nav = document.createElement('nav');
        const ul = document.createElement('ul');
        ul.className = 'pagination';
        
        // Previous button
        const prevLi = document.createElement('li');
        prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
        
        const prevLink = document.createElement('a');
        prevLink.className = 'page-link';
        prevLink.href = '#';
        prevLink.setAttribute('aria-label', 'Önceki');
        prevLink.innerHTML = '<span aria-hidden="true">&laquo;</span>';
        
        prevLi.appendChild(prevLink);
        ul.appendChild(prevLi);
        
        // Page numbers
        const maxPagesToShow = 5;
        let startPage = Math.max(1, currentPage - Math.floor(maxPagesToShow / 2));
        let endPage = Math.min(totalPages, startPage + maxPagesToShow - 1);
        
        if (endPage - startPage + 1 < maxPagesToShow) {
            startPage = Math.max(1, endPage - maxPagesToShow + 1);
        }
        
        for (let i = startPage; i <= endPage; i++) {
            const pageLi = document.createElement('li');
            pageLi.className = `page-item ${i === currentPage ? 'active' : ''}`;
            
            const pageLink = document.createElement('a');
            pageLink.className = 'page-link';
            pageLink.href = '#';
            pageLink.textContent = i;
            
            pageLi.appendChild(pageLink);
            ul.appendChild(pageLi);
        }
        
        // Next button
        const nextLi = document.createElement('li');
        nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
        
        const nextLink = document.createElement('a');
        nextLink.className = 'page-link';
        nextLink.href = '#';
        nextLink.setAttribute('aria-label', 'Sonraki');
        nextLink.innerHTML = '<span aria-hidden="true">&raquo;</span>';
        
        nextLi.appendChild(nextLink);
        ul.appendChild(nextLi);
        
        nav.appendChild(ul);
        container.appendChild(nav);
        
        // Add event listeners to pagination
        const pageLinks = container.querySelectorAll('.page-link');
        pageLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                if (link.getAttribute('aria-label') === 'Önceki' && currentPage > 1) {
                    filterState.sayfa = currentPage - 1;
                } else if (link.getAttribute('aria-label') === 'Sonraki' && currentPage < totalPages) {
                    filterState.sayfa = currentPage + 1;
                } else if (!link.getAttribute('aria-label')) {
                    filterState.sayfa = parseInt(link.textContent);
                }
                
                loadUrunler();
                
                // Scroll to top of products
                document.querySelector('.categories-nav').scrollIntoView({ behavior: 'smooth' });
            });
        });
    }

    // Toggle favorite function
    function toggleFavorite(urunId, button) {
        fetch('nedmin/netting/islem.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: `islem=favori_ekle&urun_id=${urunId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.durum === 'success') {
                // Update button
                if (data.islem === 'eklendi') {
                    button.innerHTML = '<i class="fas fa-heart text-danger"></i>';
                    showToast('Ürün favorilerinize eklendi.', 'success');
                } else {
                    button.innerHTML = '<i class="far fa-heart"></i>';
                    showToast('Ürün favorilerinizden çıkarıldı.', 'success');
                }
            } else {
                if (data.mesaj === 'giris_gerekli') {
                    // Redirect to login
                    window.location.href = 'login.php?durum=giris';
                } else {
                    showToast(data.mesaj || 'İşlem sırasında bir hata oluştu.', 'danger');
                }
            }
        })
        .catch(error => {
            console.error('Favori işlemi sırasında hata oluştu:', error);
            showToast('İşlem sırasında bir hata oluştu. Lütfen daha sonra tekrar deneyin.', 'danger');
        });
    }

    // Toast notification function
    function showToast(message, type) {
        // Create toast container if it doesn't exist
        let toastContainer = document.querySelector('.toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
            toastContainer.style.zIndex = 1050;
            document.body.appendChild(toastContainer);
        }
        
        // Create toast element
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type} border-0`;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');
        
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        `;
        
        toastContainer.appendChild(toast);
        
        // Initialize Bootstrap toast
        const bsToast = new bootstrap.Toast(toast, {
            autohide: true,
            delay: 3000
        });
        
        // Show toast
        bsToast.show();
        
        // Remove toast after it's hidden
        toast.addEventListener('hidden.bs.toast', function() {
            toast.remove();
        });
    }
});
</script>

<?php
include 'footer.php';
?> 