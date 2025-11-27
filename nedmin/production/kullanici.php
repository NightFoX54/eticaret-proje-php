<?php include 'header.php'; ?>

<div class="page-header d-flex justify-content-between align-items-center">
    <h2>Kullanıcı Yönetimi</h2>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#kullaniciEkleModal">
        <i class="fas fa-user-plus"></i> Yeni Kullanıcı Ekle
    </button>
</div>

<!-- Uyarı Mesajları -->
<div class="alert alert-success" id="successAlert" style="display:none;" role="alert"></div>
<div class="alert alert-danger" id="errorAlert" style="display:none;" role="alert"></div>

<!-- Filtre Kartı -->
<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-filter me-1"></i> Filtreleme Seçenekleri
    </div>
    <div class="card-body">
        <form id="kullaniciFilterForm">
            <div class="row g-3 align-items-center">
                <div class="col-md-3">
                    <label for="rolFilter" class="form-label">Kullanıcı Rolü</label>
                    <select class="form-select" id="rolFilter">
                        <option value="">Tümü</option>
                        <option value="admin">Admin</option>
                        <option value="moderator">Moderatör</option>
                        <option value="user">Kullanıcı</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="searchText" class="form-label">Arama</label>
                    <input type="text" class="form-control" id="searchText" placeholder="Ad, soyad veya e-posta ile ara...">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="button" id="filterBtn" class="btn btn-primary me-2">
                        <i class="fas fa-search me-1"></i> Ara
                    </button>
                    <button type="button" id="resetBtn" class="btn btn-secondary">
                        <i class="fas fa-undo me-1"></i> Sıfırla
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Kullanıcı Tablosu -->
<div class="card mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="kullaniciTable">
                <thead>
                    <tr>
                        <th class="sortable" data-sort="id">ID <i class="fas fa-sort"></i></th>
                        <th class="sortable" data-sort="ad">Ad <i class="fas fa-sort"></i></th>
                        <th class="sortable" data-sort="soyad">Soyad <i class="fas fa-sort"></i></th>
                        <th class="sortable" data-sort="mail">E-posta <i class="fas fa-sort"></i></th>
                        <th class="sortable" data-sort="telefon">Telefon <i class="fas fa-sort"></i></th>
                        <th class="sortable" data-sort="rol">Rol <i class="fas fa-sort"></i></th>
                        <th class="sortable" data-sort="kayit_tarihi">Kayıt Tarihi <i class="fas fa-sort"></i></th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody id="kullaniciData">
                    <!-- Kullanıcılar AJAX ile doldurulacak -->
                </tbody>
            </table>
            <div id="noDataMessage" class="text-center p-3" style="display: none;">
                <p class="text-muted">Bu kriterlere uygun kullanıcı bulunamadı.</p>
            </div>
            <div id="loading" class="text-center p-3" style="display: none;">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Yükleniyor...</span>
                </div>
            </div>
        </div>
        
        <!-- Sayfalama -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="dataTables_info" id="kullaniciCount">
                <!-- Toplam kullanıcı sayısı burada gösterilecek -->
            </div>
            <div class="dataTables_paginate">
                <ul class="pagination" id="pagination">
                    <!-- Sayfa numaraları AJAX ile doldurulacak -->
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Kullanıcı Ekle Modal -->
<div class="modal fade" id="kullaniciEkleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Yeni Kullanıcı Ekle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="kullaniciEkleForm">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="ad" class="form-label">Ad <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="ad" name="ad" required>
                        </div>
                        <div class="col-md-6">
                            <label for="soyad" class="form-label">Soyad <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="soyad" name="soyad" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="mail" class="form-label">E-posta <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="mail" name="mail" required>
                        </div>
                        <div class="col-md-6">
                            <label for="telefon" class="form-label">Telefon</label>
                            <input type="tel" class="form-control" id="telefon" name="telefon">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="sifre" class="form-label">Şifre <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="sifre" name="sifre" required>
                        </div>
                        <div class="col-md-6">
                            <label for="sifre_tekrar" class="form-label">Şifre Tekrar <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="sifre_tekrar" name="sifre_tekrar" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="rol" class="form-label">Kullanıcı Rolü <span class="text-danger">*</span></label>
                        <select class="form-select" id="rol" name="rol" required>
                            <option value="">Rol Seçin</option>
                            <option value="admin">Admin</option>
                            <option value="moderator">Moderatör</option>
                            <option value="kullanici">Kullanıcı</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                <button type="button" class="btn btn-primary" id="kullaniciEkleBtn">Kaydet</button>
            </div>
        </div>
    </div>
</div>

<!-- Kullanıcı Düzenle Modal -->
<div class="modal fade" id="kullaniciDuzenleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kullanıcı Düzenle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="kullaniciDuzenleForm">
                    <input type="hidden" id="duzenle_kullanici_id" name="kullanici_id">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="duzenle_ad" class="form-label">Ad <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="duzenle_ad" name="ad" required>
                        </div>
                        <div class="col-md-6">
                            <label for="duzenle_soyad" class="form-label">Soyad <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="duzenle_soyad" name="soyad" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="duzenle_mail" class="form-label">E-posta <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="duzenle_mail" name="mail" required>
                        </div>
                        <div class="col-md-6">
                            <label for="duzenle_telefon" class="form-label">Telefon</label>
                            <input type="tel" class="form-control" id="duzenle_telefon" name="telefon">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="duzenle_sifre" class="form-label">Şifre (Değiştirmek istemiyorsanız boş bırakın)</label>
                            <input type="password" class="form-control" id="duzenle_sifre" name="sifre">
                        </div>
                        <div class="col-md-6">
                            <label for="duzenle_sifre_tekrar" class="form-label">Şifre Tekrar</label>
                            <input type="password" class="form-control" id="duzenle_sifre_tekrar" name="sifre_tekrar">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="duzenle_rol" class="form-label">Kullanıcı Rolü <span class="text-danger">*</span></label>
                        <select class="form-select" id="duzenle_rol" name="rol" required>
                            <option value="">Rol Seçin</option>
                            <option value="admin">Admin</option>
                            <option value="moderator">Moderatör</option>
                            <option value="kullanici">Kullanıcı</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                <button type="button" class="btn btn-warning" id="kullaniciGuncelleBtn">Güncelle</button>
            </div>
        </div>
    </div>
</div>

<!-- Kullanıcı Sil Modal -->
<div class="modal fade" id="kullaniciSilModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kullanıcı Sil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Bu kullanıcıyı silmek istediğinizden emin misiniz?</p>
                <p id="silinecekKullaniciIsim" class="fw-bold"></p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i> Bu işlem geri alınamaz! Kullanıcıya ait siparişler ve diğer veriler de silinecektir.
                </div>
                <input type="hidden" id="silinecekKullaniciId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                <button type="button" class="btn btn-danger" id="kullaniciSilBtn">Sil</button>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<style>
    .sortable {
        cursor: pointer;
    }
    .sortable i {
        margin-left: 5px;
    }
    .sort-asc i::before {
        content: "\f0de"; /* fa-sort-up */
    }
    .sort-desc i::before {
        content: "\f0dd"; /* fa-sort-down */
    }
</style>

<script>
$(document).ready(function() {
    // Global değişkenler
    let currentPage = 1;
    let itemsPerPage = 15;
    let totalItems = 0;
    let currentSort = "id";
    let currentOrder = "DESC";
    
    // Sayfa yüklendiğinde kullanıcıları getir
    loadKullanicilar();
    
    // Filtrele butonuna tıklandığında
    $("#filterBtn").on("click", function() {
        currentPage = 1; // Filtreleme yapıldığında ilk sayfaya dön
        loadKullanicilar();
    });
    
    // Sıfırla butonuna tıklandığında
    $("#resetBtn").on("click", function() {
        $("#rolFilter").val("");
        $("#searchText").val("");
        currentPage = 1;
        loadKullanicilar();
    });
    
    // Enter tuşu ile arama
    $("#searchText").on("keyup", function(e) {
        if (e.key === "Enter") {
            currentPage = 1;
            loadKullanicilar();
        }
    });
    
    // Sıralama başlıklarına tıklandığında
    $(".sortable").on("click", function() {
        const column = $(this).data("sort");
        
        // Aynı sütuna tıklandıysa sıralama yönünü değiştir
        if (currentSort === column) {
            currentOrder = currentOrder === "ASC" ? "DESC" : "ASC";
        } else {
            currentSort = column;
            currentOrder = "ASC";
        }
        
        // Sıralama ikonlarını güncelle
        $(".sortable").removeClass("sort-asc sort-desc");
        $(this).addClass(currentOrder === "ASC" ? "sort-asc" : "sort-desc");
        
        // Kullanıcıları yeniden yükle
        loadKullanicilar();
    });
    
    // Kullanıcıları getirme fonksiyonu
    function loadKullanicilar() {
        const rolFilter = $("#rolFilter").val();
        const searchText = $("#searchText").val();
        
        $("#loading").show();
        $("#kullaniciData").empty();
        $("#noDataMessage").hide();
        
        $.ajax({
            type: "GET",
            url: "../netting/islem.php",
            data: {
                islem: "kullanici_listele",
                rol: rolFilter,
                arama: searchText,
                sayfa: currentPage,
                limit: itemsPerPage,
                sort: currentSort,
                order: currentOrder
            },
            dataType: "json",
            success: function(response) {
                $("#loading").hide();
                
                if (response.durum === "success") {
                    displayKullanicilar(response.kullanicilar);
                    updatePagination(response.toplam_kayit);
                } else {
                    $("#errorAlert").text(response.mesaj);
                    $("#errorAlert").show();
                    setTimeout(function() { $("#errorAlert").hide(); }, 3000);
                }
            },
            error: function(xhr, status, error) {
                $("#loading").hide();
                $("#errorAlert").text("Kullanıcılar getirilirken bir hata oluştu.");
                $("#errorAlert").show();
                setTimeout(function() { $("#errorAlert").hide(); }, 3000);
            }
        });
    }
    
    // Kullanıcıları tabloya yazdırma fonksiyonu
    function displayKullanicilar(kullanicilar) {
        const kullaniciData = $("#kullaniciData");
        kullaniciData.empty();
        
        if (kullanicilar.length === 0) {
            $("#noDataMessage").show();
            return;
        }
        
        kullanicilar.forEach(function(kullanici) {
            const rol = getRolBadge(kullanici.rol);
            const formatTarih = formatDate(kullanici.kayit_tarihi);
            
            let row = `<tr id="kullanici-${kullanici.id}">
                <td>${kullanici.id}</td>
                <td>${kullanici.ad}</td>
                <td>${kullanici.soyad}</td>
                <td>${kullanici.mail}</td>
                <td>${kullanici.tel_no || '-'}</td>
                <td>${rol}</td>
                <td data-sort="${kullanici.kayit_tarihi}">${formatTarih}</td>
                <td>
                    <button class="btn btn-sm btn-warning edit-btn" data-id="${kullanici.id}">
                        <i class="fas fa-user-edit"></i> Düzenle
                    </button>
                    <button class="btn btn-sm btn-danger delete-btn" data-id="${kullanici.id}" 
                            data-ad="${kullanici.ad}" data-soyad="${kullanici.soyad}">
                        <i class="fas fa-user-times"></i> Sil
                    </button>
                </td>
            </tr>`;
            
            kullaniciData.append(row);
        });
    }
    
    // Rol badge'leri için yardımcı fonksiyon
    function getRolBadge(rol) {
        switch(rol) {
            case 'admin':
                return '<span class="badge bg-danger">Admin</span>';
            case 'moderator':
                return '<span class="badge bg-warning text-dark">Moderatör</span>';
            case 'kullanici':
                return '<span class="badge bg-info">Kullanıcı</span>';
            default:
                return '<span class="badge bg-secondary">Bilinmiyor</span>';
        }
    }
    
    // Tarih formatı için yardımcı fonksiyon
    function formatDate(dateString) {
        if (!dateString) return '-';
        const date = new Date(dateString);
        return date.toLocaleDateString('tr-TR');
    }
    
    // Sayfalama için yardımcı fonksiyon
    function updatePagination(totalItems) {
        const totalPages = Math.ceil(totalItems / itemsPerPage);
        const pagination = $("#pagination");
        pagination.empty();
        
        // Sayfa bilgisini güncelle
        $("#kullaniciCount").text(`Toplam ${totalItems} kullanıcı, ${Math.min((currentPage - 1) * itemsPerPage + 1, totalItems)} - ${Math.min(currentPage * itemsPerPage, totalItems)} arası gösteriliyor.`);
        
        // Önceki sayfa butonu
        pagination.append(`<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${currentPage - 1}" aria-label="Önceki">
                <span aria-hidden="true">&laquo;</span>
            </a>
        </li>`);
        
        // Sayfa numaraları
        const startPage = Math.max(1, currentPage - 2);
        const endPage = Math.min(totalPages, currentPage + 2);
        
        for (let i = startPage; i <= endPage; i++) {
            pagination.append(`<li class="page-item ${i === currentPage ? 'active' : ''}">
                <a class="page-link" href="#" data-page="${i}">${i}</a>
            </li>`);
        }
        
        // Sonraki sayfa butonu
        pagination.append(`<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${currentPage + 1}" aria-label="Sonraki">
                <span aria-hidden="true">&raquo;</span>
            </a>
        </li>`);
        
        // Sayfa numaralarına tıklama olayı
        $(".page-link").on("click", function(e) {
            e.preventDefault();
            const page = $(this).data("page");
            
            if (page >= 1 && page <= totalPages && page !== currentPage) {
                currentPage = page;
                loadKullanicilar();
            }
        });
    }
    
    // Düzenle butonuna tıklandığında
    $(document).on("click", ".edit-btn", function() {
        const kullaniciId = $(this).data("id");
        
        // Kullanıcı bilgilerini getir
        $.ajax({
            type: "GET",
            url: "../netting/islem.php",
            data: {
                islem: "kullanici_getir",
                kullanici_id: kullaniciId
            },
            dataType: "json",
            success: function(response) {
                if (response.durum === "success") {
                    const kullanici = response.kullanici;
                    
                    // Form alanlarını doldur
                    $("#duzenle_kullanici_id").val(kullanici.id);
                    $("#duzenle_ad").val(kullanici.ad);
                    $("#duzenle_soyad").val(kullanici.soyad);
                    $("#duzenle_mail").val(kullanici.mail);
                    $("#duzenle_telefon").val(kullanici.tel_no);
                    $("#duzenle_rol").val(kullanici.rol);
                    
                    // Şifre alanlarını temizle
                    $("#duzenle_sifre").val('');
                    $("#duzenle_sifre_tekrar").val('');
                    
                    // Modal göster
                    $("#kullaniciDuzenleModal").modal("show");
                } else {
                    $("#errorAlert").text(response.mesaj);
                    $("#errorAlert").show();
                    setTimeout(function() { $("#errorAlert").hide(); }, 3000);
                }
            },
            error: function(xhr, status, error) {
                $("#errorAlert").text("Kullanıcı bilgileri alınırken bir hata oluştu.");
                $("#errorAlert").show();
                setTimeout(function() { $("#errorAlert").hide(); }, 3000);
            }
        });
    });
    
    // Kullanıcı Ekleme
    $("#kullaniciEkleBtn").on("click", function() {
        // Form verilerini al
        const ad = $("#ad").val();
        const soyad = $("#soyad").val();
        const mail = $("#mail").val();
        const telefon = $("#telefon").val();
        const sifre = $("#sifre").val();
        const sifre_tekrar = $("#sifre_tekrar").val();
        const rol = $("#rol").val();
        
        // Form validasyonu
        if (!ad || !soyad || !mail || !sifre || !rol) {
            $("#errorAlert").text("Lütfen zorunlu alanları doldurun!");
            $("#errorAlert").show();
            setTimeout(function() { $("#errorAlert").hide(); }, 3000);
            return;
        }
        
        if (sifre !== sifre_tekrar) {
            $("#errorAlert").text("Girilen şifreler eşleşmiyor!");
            $("#errorAlert").show();
            setTimeout(function() { $("#errorAlert").hide(); }, 3000);
            return;
        }
        
        // E-posta formatı kontrolü
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(mail)) {
            $("#errorAlert").text("Geçerli bir e-posta adresi girin!");
            $("#errorAlert").show();
            setTimeout(function() { $("#errorAlert").hide(); }, 3000);
            return;
        }
        
        // AJAX isteği
        $.ajax({
            type: "POST",
            url: "../netting/islem.php",
            data: {
                islem: "kullanici_ekle",
                ad: ad,
                soyad: soyad,
                mail: mail,
                telefon: telefon,
                sifre: sifre,
                rol: rol
            },
            dataType: "json",
            success: function(response) {
                if (response.durum === "success") {
                    $("#successAlert").text(response.mesaj);
                    $("#successAlert").show();
                    
                    // Modalı kapat ve formu temizle
                    $("#kullaniciEkleModal").modal("hide");
                    $("#kullaniciEkleForm")[0].reset();
                    
                    // Kullanıcı listesini güncelle
                    loadKullanicilar();
                } else {
                    $("#errorAlert").text(response.mesaj);
                    $("#errorAlert").show();
                }
                
                setTimeout(function() {
                    $("#successAlert, #errorAlert").hide();
                }, 3000);
            },
            error: function(xhr, status, error) {
                $("#errorAlert").text("İşlem sırasında bir hata oluştu.");
                $("#errorAlert").show();
                setTimeout(function() { $("#errorAlert").hide(); }, 3000);
            }
        });
    });
    
    // Kullanıcı Güncelleme
    $("#kullaniciGuncelleBtn").on("click", function() {
        // Form verilerini al
        const kullanici_id = $("#duzenle_kullanici_id").val();
        const ad = $("#duzenle_ad").val();
        const soyad = $("#duzenle_soyad").val();
        const mail = $("#duzenle_mail").val();
        const telefon = $("#duzenle_telefon").val();
        const sifre = $("#duzenle_sifre").val();
        const sifre_tekrar = $("#duzenle_sifre_tekrar").val();
        const rol = $("#duzenle_rol").val();
        
        // Form validasyonu
        if (!ad || !soyad || !mail || !rol) {
            $("#errorAlert").text("Lütfen zorunlu alanları doldurun!");
            $("#errorAlert").show();
            setTimeout(function() { $("#errorAlert").hide(); }, 3000);
            return;
        }
        
        // Şifre alanı doldurulmuşsa şifrelerin eşleştiğini kontrol et
        if (sifre && sifre !== sifre_tekrar) {
            $("#errorAlert").text("Girilen şifreler eşleşmiyor!");
            $("#errorAlert").show();
            setTimeout(function() { $("#errorAlert").hide(); }, 3000);
            return;
        }
        
        // E-posta formatı kontrolü
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(mail)) {
            $("#errorAlert").text("Geçerli bir e-posta adresi girin!");
            $("#errorAlert").show();
            setTimeout(function() { $("#errorAlert").hide(); }, 3000);
            return;
        }
        
        // AJAX isteği
        $.ajax({
            type: "POST",
            url: "../netting/islem.php",
            data: {
                islem: "kullanici_guncelle",
                kullanici_id: kullanici_id,
                ad: ad,
                soyad: soyad,
                mail: mail,
                telefon: telefon,
                sifre: sifre,
                rol: rol
            },
            dataType: "json",
            success: function(response) {
                if (response.durum === "success") {
                    $("#successAlert").text(response.mesaj);
                    $("#successAlert").show();
                    
                    // Modalı kapat
                    $("#kullaniciDuzenleModal").modal("hide");
                    
                    // Kullanıcı listesini güncelle
                    loadKullanicilar();
                } else {
                    $("#errorAlert").text(response.mesaj);
                    $("#errorAlert").show();
                }
                
                setTimeout(function() {
                    $("#successAlert, #errorAlert").hide();
                }, 3000);
            },
            error: function(xhr, status, error) {
                $("#errorAlert").text("İşlem sırasında bir hata oluştu.");
                $("#errorAlert").show();
                setTimeout(function() { $("#errorAlert").hide(); }, 3000);
            }
        });
    });
    
    // Sil butonuna tıklandığında
    $(document).on("click", ".delete-btn", function() {
        const kullaniciId = $(this).data("id");
        const kullaniciAd = $(this).data("ad");
        const kullaniciSoyad = $(this).data("soyad");
        
        $("#silinecekKullaniciId").val(kullaniciId);
        $("#silinecekKullaniciIsim").text(`${kullaniciAd} ${kullaniciSoyad}`);
        
        // Modal göster
        $("#kullaniciSilModal").modal("show");
    });
    
    // Kullanıcı Silme
    $("#kullaniciSilBtn").on("click", function() {
        const kullaniciId = $("#silinecekKullaniciId").val();
        
        $.ajax({
            type: "POST",
            url: "../netting/islem.php",
            data: {
                islem: "kullanici_sil",
                kullanici_id: kullaniciId
            },
            dataType: "json",
            success: function(response) {
                if (response.durum === "success") {
                    $("#successAlert").text(response.mesaj);
                    $("#successAlert").show();
                    
                    // Silinen satırı tablodan kaldır veya listeyi yenile
                    $(`#kullanici-${kullaniciId}`).remove();
                    if ($("#kullaniciData tr").length === 0) {
                        loadKullanicilar();
                    }
                    
                    // Modalı kapat
                    $("#kullaniciSilModal").modal("hide");
                } else {
                    $("#errorAlert").text(response.mesaj);
                    $("#errorAlert").show();
                    
                    // Modalı kapat
                    $("#kullaniciSilModal").modal("hide");
                }
                
                setTimeout(function() {
                    $("#successAlert, #errorAlert").hide();
                }, 3000);
            },
            error: function(xhr, status, error) {
                $("#errorAlert").text("İşlem sırasında bir hata oluştu.");
                $("#errorAlert").show();
                setTimeout(function() { $("#errorAlert").hide(); }, 3000);
                
                // Modalı kapat
                $("#kullaniciSilModal").modal("hide");
            }
        });
    });
});
</script> 