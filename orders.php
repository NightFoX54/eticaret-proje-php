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
                    <a href="profile.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-user me-2"></i> Profilim
                    </a>
                    <a href="orders.php" class="list-group-item list-group-item-action active">
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
                <div class="card-header d-flex justify-content-between">
                    <h5 class="mb-0">Siparişlerim</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Sipariş No</th>
                                    <th>Tarih</th>
                                    <th>Tutar</th>
                                    <th>Durum</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody id="ordersList">
                                <!-- Orders will be loaded dynamically -->
                                <tr>
                                    <td colspan="5" class="text-center">Siparişleriniz yükleniyor...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- No Orders Message -->
                    <div id="noOrdersMessage" class="text-center p-4" style="display:none;">
                        <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                        <h5>Henüz siparişiniz bulunmuyor.</h5>
                        <p class="text-muted">Alışverişe başlayarak siparişlerinizi burada görebilirsiniz.</p>
                        <a href="index.php" class="btn btn-primary" style="background: linear-gradient(135deg, #9D7FC7 0%, #8B6FC7 100%); border: none;">Alışverişe Başla</a>
                    </div>
                    
                    <!-- Pagination -->
                    <nav aria-label="Page navigation" id="ordersPagination" style="display:none;">
                        <ul class="pagination justify-content-center" id="paginationContainer">
                            <!-- Pagination will be loaded dynamically -->
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Order Details Modal -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sipariş Detayı</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6>Sipariş Bilgileri</h6>
                        <table class="table table-sm">
                            <tr>
                                <th style="width: 40%">Sipariş No:</th>
                                <td id="detay_fatura_no"></td>
                            </tr>
                            <tr>
                                <th>Tarih:</th>
                                <td id="detay_tarih"></td>
                            </tr>
                            <tr>
                                <th>Durum:</th>
                                <td id="detay_durum"></td>
                            </tr>
                            <tr>
                                <th>Ödeme Yöntemi:</th>
                                <td id="detay_odeme_yontemi"></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Teslimat Bilgileri</h6>
                        <div id="detay_teslimat_bilgileri">
                            <!-- Delivery details will be loaded dynamically -->
                        </div>
                    </div>
                </div>
                
                <h6>Sipariş Özeti</h6>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Ürün</th>
                                <th>Birim Fiyat</th>
                                <th>Adet</th>
                                <th>Toplam</th>
                                <th>Değerlendirme</th>
                            </tr>
                        </thead>
                        <tbody id="detay_urunler">
                            <!-- Products will be loaded dynamically -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-end">Ara Toplam:</th>
                                <td colspan="1" id="detay_ara_toplam"></td>
                            </tr>
                            <tr id="detay_indirim_row">
                                <th colspan="4" class="text-end">İndirim (Kupon: <span id="detay_kupon_kod"></span>):</th>
                                <td colspan="1" id="detay_indirim"></td>
                            </tr>
                            <tr>
                                <th colspan="4" class="text-end">Genel Toplam:</th>
                                <td colspan="1" id="detay_toplam" class="fw-bold"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
            </div>
        </div>
    </div>
</div>

<!-- Review Modal -->
<div class="modal fade" id="reviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ürün Değerlendirme</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="reviewForm">
                    <input type="hidden" id="review_urun_id" name="urun_id">
                    <input type="hidden" id="review_fatura_id" name="fatura_id">
                    <div class="mb-3">
                        <label class="form-label">Puan</label>
                        <div class="rating">
                            <input type="radio" name="puan" value="5" id="rating5"><label for="rating5">☆</label>
                            <input type="radio" name="puan" value="4" id="rating4"><label for="rating4">☆</label>
                            <input type="radio" name="puan" value="3" id="rating3"><label for="rating3">☆</label>
                            <input type="radio" name="puan" value="2" id="rating2"><label for="rating2">☆</label>
                            <input type="radio" name="puan" value="1" id="rating1"><label for="rating1">☆</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="review_yorum" class="form-label">Yorumunuz</label>
                        <textarea class="form-control" id="review_yorum" name="yorum" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                <button type="button" class="btn btn-primary" id="submitReview" style="background: linear-gradient(135deg, #9D7FC7 0%, #8B6FC7 100%); border: none;">Gönder</button>
            </div>
        </div>
    </div>
</div>

<style>
.rating {
    display: flex;
    flex-direction: row-reverse;
    justify-content: flex-end;
}

.rating input {
    display: none;
}

.rating label {
    cursor: pointer;
    font-size: 30px;
    color: #ddd;
    margin: 0 2px;
}

.rating input:checked ~ label {
    color: #ffd700;
}

.rating label:hover,
.rating label:hover ~ label {
    color: #ffd700;
}

.product-rating {
    color: #ffd700;
    font-size: 20px;
}
</style>

<script>
$(document).ready(function() {
    // Global variables
    let currentPage = 1;
    const itemsPerPage = 10;
    
    // Check authentication
    checkAuth();
    clearUnconfirmedOrders();
    // Load user orders
    loadOrders(currentPage);

    function clearUnconfirmedOrders() {
        $.ajax({
            type: "GET",
            url: "nedmin/netting/islem.php",
            data: {
                islem: "clear_unconfirmed_orders"
            },
        });
    }
    
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
    
    // Load orders list
    function loadOrders(page) {
        $.ajax({
            type: "GET",
            url: "nedmin/netting/islem.php",
            data: {
                islem: "get_user_orders",
                page: page,
                limit: itemsPerPage
            },
            dataType: "json",
            success: function(response) {
                if (response.durum === 'success') {
                    displayOrders(response.siparisler, response.toplam_kayit);
                } else {
                    showError(response.mesaj || 'Siparişler yüklenirken bir hata oluştu.');
                }
            },
            error: function() {
                showError('Sunucu hatası: Siparişler yüklenemedi.');
            }
        });
    }
    
    // Display orders in the table
    function displayOrders(orders, totalCount) {
        const ordersList = $("#ordersList");
        ordersList.empty();
        
        if (orders.length === 0) {
            $("#noOrdersMessage").show();
            return;
        }
        
        $("#noOrdersMessage").hide();
        
        orders.forEach(function(order) {
            const formattedDate = formatDate(order.tarih);
            const statusBadge = getStatusBadge(order.siparis_durumu);
            
            const row = `
                <tr>
                    <td>${order.fatura_no}</td>
                    <td>${formattedDate}</td>
                    <td>${parseFloat(order.toplam).toFixed(2)} ₺</td>
                    <td>${statusBadge}</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-info view-order-btn" data-id="${order.id}">
                            <i class="fas fa-eye"></i> Detay
                        </button>
                    </td>
                </tr>
            `;
            
            ordersList.append(row);
        });
        
        // Create pagination
        createPagination(totalCount, currentPage, itemsPerPage);
    }
    
    // Order status badge helper
    function getStatusBadge(status) {
        const statusMap = {
            'beklemede': ['warning', 'Beklemede'],
            'onaylandi': ['info', 'Onaylandı'],
            'hazirlaniyor': ['primary', 'Hazırlanıyor'],
            'kargolandi': ['info', 'Kargoda'],
            'teslim_edildi': ['success', 'Teslim Edildi'],
            'iptal_edildi': ['danger', 'İptal Edildi']
        };
        
        const [type, text] = statusMap[status] || ['secondary', status];
        return `<span class="badge bg-${type}">${text}</span>`;
    }
    
    // Format date helper
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('tr-TR', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }
    
    // Create pagination
    function createPagination(totalItems, currentPage, itemsPerPage) {
        const totalPages = Math.ceil(totalItems / itemsPerPage);
        
        if (totalPages <= 1) {
            $("#ordersPagination").hide();
            return;
        }
        
        $("#ordersPagination").show();
        const paginationContainer = $("#paginationContainer");
        paginationContainer.empty();
        
        // Previous button
        paginationContainer.append(`
            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${currentPage - 1}" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
        `);
        
        // Page numbers
        const startPage = Math.max(1, currentPage - 2);
        const endPage = Math.min(totalPages, currentPage + 2);
        
        for (let i = startPage; i <= endPage; i++) {
            paginationContainer.append(`
                <li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>
            `);
        }
        
        // Next button
        paginationContainer.append(`
            <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${currentPage + 1}" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        `);
        
        // Add click events to pagination links
        $(".page-link").on("click", function(e) {
            e.preventDefault();
            const page = parseInt($(this).data('page'));
            
            if (page >= 1 && page <= totalPages) {
                currentPage = page;
                loadOrders(currentPage);
            }
        });
    }
    
    // View order details
    $(document).on("click", ".view-order-btn", function() {
        const orderId = $(this).data('id');
        
        $.ajax({
            type: "GET",
            url: "nedmin/netting/islem.php",
            data: {
                islem: "get_user_siparis_detay",
                siparis_id: orderId
            },
            dataType: "json",
            success: function(response) {
                if (response.durum === 'success') {
                    displayOrderDetails(response.siparis, response.urunler);
                    $("#orderDetailsModal").modal('show');
                } else {
                    showError(response.mesaj || 'Sipariş detayları yüklenirken bir hata oluştu.');
                }
            },
            error: function() {
                showError('Sunucu hatası: Sipariş detayları yüklenemedi.');
            }
        });
    });
    
    // Display order details
    function displayOrderDetails(order, products) {
        // Order information
        $("#detay_fatura_no").text(order.fatura_no);
        $("#detay_tarih").text(formatDate(order.tarih));
        $("#detay_durum").html(getStatusBadge(order.siparis_durumu));
        $("#detay_odeme_yontemi").text(order.odeme_yontemi);
        
        // Delivery information
        let deliveryHTML = '';
        if (order.adres_id) {
            deliveryHTML = `
                <address>
                    <strong>${order.teslimat_ad_soyad || order.ad_soyad || '-'}</strong><br>
                    ${order.teslimat_adres || order.adres || '-'}<br>
                    ${order.il || '-'} / ${order.ilce || '-'}<br>
                    ${order.posta_kodu ? `Posta Kodu: ${order.posta_kodu}` : ''}<br>
                    ${order.teslimat_telefon || order.telefon ? `Tel: ${order.teslimat_telefon || order.telefon}` : ''}
                </address>
            `;
        } else {
            deliveryHTML = '<div class="alert alert-warning">Teslimat adresi bilgisi bulunamadı.</div>';
        }
        $("#detay_teslimat_bilgileri").html(deliveryHTML);
        
        // Products
        const productsList = $("#detay_urunler");
        productsList.empty();
        
        products.forEach(function(product) {
            const birimFiyat = parseFloat(product.birim_fiyat);
            const miktar = parseInt(product.miktar);
            const toplam = birimFiyat * miktar;
            
            let urunBilgisi = product.urun_isim;
            if (product.varyasyon_bilgisi) {
                urunBilgisi += `<br><small class="text-muted">${product.varyasyon_bilgisi}</small>`;
            }
            
            // Review column content
            let reviewContent = '';
            if (product.degerlendirme_id) {
                // Show existing review
                const stars = '★'.repeat(product.puan) + '☆'.repeat(5 - product.puan);
                reviewContent = `
                    <div class="product-rating">
                        ${stars}<br>
                        <small class="text-muted">${product.yorum}</small><br>
                        <small class="text-muted">${formatDate(product.degerlendirme_tarih)}</small>
                    </div>
                `;
            } else if (order.siparis_durumu === 'teslim_edildi') {
                // Show review button for delivered orders
                reviewContent = `
                    <button class="btn btn-sm btn-primary add-review-btn" 
                            data-urun-id="${product.urun_id}" 
                            data-fatura-id="${order.id}">
                        Değerlendir
                    </button>
                `;
            } else {
                reviewContent = '<small class="text-muted">Ürün teslim edildikten sonra değerlendirebilirsiniz.</small>';
            }
            
            productsList.append(`
                <tr>
                    <td>${urunBilgisi}</td>
                    <td>${birimFiyat.toFixed(2)} ₺</td>
                    <td>${miktar}</td>
                    <td>${toplam.toFixed(2)} ₺</td>
                    <td>${reviewContent}</td>
                </tr>
            `);
        });
        
        // Update totals
        $("#detay_ara_toplam").text(`${parseFloat(order.ara_toplam).toFixed(2)} ₺`);
        
        if (parseFloat(order.indirim_tutari) > 0) {
            $("#detay_kupon_kod").text(order.kupon_kod || "-");
            $("#detay_indirim").text(`${parseFloat(order.indirim_tutari).toFixed(2)} ₺`);
            $("#detay_indirim_row").show();
        } else {
            $("#detay_indirim_row").hide();
        }
        
        $("#detay_toplam").text(`${parseFloat(order.toplam).toFixed(2)} ₺`);
    }
    
    // Handle review button click
    $(document).on("click", ".add-review-btn", function() {
        const urunId = $(this).data("urun-id");
        const faturaId = $(this).data("fatura-id");
        
        // Reset form
        $("#reviewForm")[0].reset();
        $("#review_urun_id").val(urunId);
        $("#review_fatura_id").val(faturaId);
        
        // Hide order details modal and show review modal
        $("#orderDetailsModal").modal("hide");
        $("#reviewModal").modal("show");
    });
    
    // Handle review submission
    $("#submitReview").click(function() {
        const formData = {
            islem: "add_review",
            urun_id: $("#review_urun_id").val(),
            fatura_id: $("#review_fatura_id").val(),
            puan: $("input[name='puan']:checked").val(),
            yorum: $("#review_yorum").val()
        };
        
        if (!formData.puan) {
            alert("Lütfen bir puan seçin.");
            return;
        }
        
        $.ajax({
            type: "POST",
            url: "nedmin/netting/islem.php",
            data: formData,
            dataType: "json",
            success: function(response) {
                if (response.durum === "success") {
                    $("#reviewModal").modal("hide");
                    // Show success message
                    showSuccess(response.mesaj || "Değerlendirmeniz başarıyla kaydedildi.");
                    // Refresh order details
                    $.ajax({
                        type: "GET",
                        url: "nedmin/netting/islem.php",
                        data: {
                            islem: "get_user_siparis_detay",
                            siparis_id: formData.fatura_id
                        },
                        dataType: "json",
                        success: function(response) {
                            if (response.durum === 'success') {
                                displayOrderDetails(response.siparis, response.urunler);
                                $("#orderDetailsModal").modal('show');
                            }
                        }
                    });
                } else {
                    showError(response.mesaj || "Değerlendirme kaydedilirken bir hata oluştu.");
                }
            },
            error: function() {
                showError("Değerlendirme gönderilirken bir hata oluştu.");
            }
        });
    });
    
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