<?php include 'header.php'; ?>

<div class="page-header d-flex justify-content-between align-items-center">
    <h2>Siparişler</h2>
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
        <form id="siparisFilterForm">
            <div class="row g-3 align-items-center">
                <div class="col-md-3">
                    <label for="durumFilter" class="form-label">Sipariş Durumu</label>
                    <select class="form-select" id="durumFilter">
                        <option value="">Tümü</option>
                        <option value="beklemede">Beklemede</option>
                        <option value="onaylandi">Onaylandı</option>
                        <option value="hazirlaniyor">Hazırlanıyor</option>
                        <option value="kargolandi">Kargolandı</option>
                        <option value="teslim_edildi">Teslim Edildi</option>
                        <option value="iptal_edildi">İptal Edildi</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="baslangicTarih" class="form-label">Başlangıç Tarihi</label>
                    <input type="date" class="form-control" id="baslangicTarih">
                </div>
                <div class="col-md-3">
                    <label for="bitisTarih" class="form-label">Bitiş Tarihi</label>
                    <input type="date" class="form-control" id="bitisTarih">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="button" id="filterBtn" class="btn btn-primary me-2">
                        <i class="fas fa-search me-1"></i> Filtrele
                    </button>
                    <button type="button" id="resetBtn" class="btn btn-secondary">
                        <i class="fas fa-undo me-1"></i> Sıfırla
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Sipariş Tablosu -->
<div class="card mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="siparisTable">
                <thead>
                    <tr>
                        <th class="sortable" data-sort="id">ID <i class="fas fa-sort"></i></th>
                        <th class="sortable" data-sort="fatura_no">Fatura No <i class="fas fa-sort"></i></th>
                        <th>Müşteri</th>
                        <th>Teslimat Adresi</th>
                        <th class="sortable" data-sort="toplam">Toplam <i class="fas fa-sort"></i></th>
                        <th class="sortable" data-sort="odeme_yontemi">Ödeme Yöntemi <i class="fas fa-sort"></i></th>
                        <th class="sortable" data-sort="tarih">Tarih <i class="fas fa-sort"></i></th>
                        <th class="sortable" data-sort="siparis_durumu">Durum <i class="fas fa-sort"></i></th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody id="siparisData">
                    <!-- Siparişler AJAX ile doldurulacak -->
                </tbody>
            </table>
            <div id="noDataMessage" class="text-center p-3" style="display: none;">
                <p class="text-muted">Bu kriterlere uygun sipariş bulunamadı.</p>
            </div>
            <div id="loading" class="text-center p-3" style="display: none;">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Yükleniyor...</span>
                </div>
            </div>
        </div>
        
        <!-- Sayfalama -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="dataTables_info" id="siparisCount">
                <!-- Toplam sipariş sayısı burada gösterilecek -->
            </div>
            <div class="dataTables_paginate">
                <ul class="pagination" id="pagination">
                    <!-- Sayfa numaraları AJAX ile doldurulacak -->
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Sipariş Detay Modal -->
<div class="modal fade" id="siparisDetayModal" tabindex="-1" aria-hidden="true">
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
                                <th style="width: 40%">Fatura No:</th>
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
                        <h6>Müşteri Bilgileri</h6>
                        <table class="table table-sm">
                            <tr>
                                <th style="width: 40%">Ad Soyad:</th>
                                <td id="detay_musteri_adsoyad"></td>
                            </tr>
                            <tr>
                                <th>E-posta:</th>
                                <td id="detay_musteri_email"></td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <!-- Teslimat Bilgileri -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h6>Teslimat Bilgileri</h6>
                        <div id="teslimat_bilgileri_container">
                            <div class="alert alert-warning">Teslimat adresi bilgisi bulunamadı.</div>
                        </div>
                        <div id="adres_bilgileri" style="display:none;">
                            <table class="table table-sm">
                                <tr>
                                    <th style="width: 20%">Adres Adı:</th>
                                    <td id="detay_adres_adi"></td>
                                </tr>
                                <tr>
                                    <th>Alıcı Ad Soyad:</th>
                                    <td id="detay_teslimat_adsoyad"></td>
                                </tr>
                                <tr>
                                    <th>Telefon:</th>
                                    <td id="detay_teslimat_telefon"></td>
                                </tr>
                                <tr>
                                    <th>T.C. Kimlik No:</th>
                                    <td id="detay_tc_no"></td>
                                </tr>
                                <tr>
                                    <th>İl/İlçe:</th>
                                    <td id="detay_il_ilce"></td>
                                </tr>
                                <tr>
                                    <th>Adres:</th>
                                    <td id="detay_teslimat_adres"></td>
                                </tr>
                                <tr>
                                    <th>Posta Kodu:</th>
                                    <td id="detay_posta_kodu"></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                
                <h6>Ürünler</h6>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Ürün</th>
                                <th>Birim Fiyat</th>
                                <th>Adet</th>
                                <th>Toplam</th>
                            </tr>
                        </thead>
                        <tbody id="detay_urunler">
                            <!-- Ürünler burada listelenecek -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Ara Toplam:</th>
                                <td id="detay_ara_toplam"></td>
                            </tr>
                            <tr id="detay_indirim_row">
                                <th colspan="3" class="text-end">İndirim (Kupon: <span id="detay_kupon_kod"></span>):</th>
                                <td id="detay_indirim"></td>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-end">Genel Toplam:</th>
                                <td id="detay_toplam" class="fw-bold"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                <div id="siparisIslemBtns" class="mt-3">
                    <!-- İşlem butonları sipariş durumuna göre burada gösterilecek -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
            </div>
        </div>
    </div>
</div>

<!-- Sipariş Durum Değiştirme Modal -->
<div class="modal fade" id="siparisDurumModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="durumModalTitle">Sipariş Durumu Değiştir</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="durumModalMessage"></p>
                <input type="hidden" id="durum_siparis_id">
                <input type="hidden" id="durum_yeni_durum">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                <button type="button" class="btn" id="durumOnayBtn">Onayla</button>
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
    
    // Sayfa yüklendiğinde siparişleri getir
    loadSiparisler();
    
    // Filtrele butonuna tıklandığında
    $("#filterBtn").on("click", function() {
        currentPage = 1; // Filtreleme yapıldığında ilk sayfaya dön
        loadSiparisler();
    });
    
    // Sıfırla butonuna tıklandığında
    $("#resetBtn").on("click", function() {
        $("#durumFilter").val("");
        $("#baslangicTarih").val("");
        $("#bitisTarih").val("");
        currentPage = 1;
        loadSiparisler();
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
        
        // Siparişleri yeniden yükle
        loadSiparisler();
    });
    
    // Siparişleri getirme fonksiyonu
    function loadSiparisler() {
        const durumFilter = $("#durumFilter").val();
        const baslangicTarih = $("#baslangicTarih").val();
        const bitisTarih = $("#bitisTarih").val();
        
        $("#loading").show();
        $("#siparisData").empty();
        $("#noDataMessage").hide();
        
        $.ajax({
            type: "GET",
            url: "../netting/islem.php",
            data: {
                islem: "siparis_listele",
                durum: durumFilter,
                baslangic_tarih: baslangicTarih,
                bitis_tarih: bitisTarih,
                sayfa: currentPage,
                limit: itemsPerPage,
                sort: currentSort,
                order: currentOrder
            },
            dataType: "json",
            success: function(response) {
                $("#loading").hide();
                
                if (response.durum === "success") {
                    displaySiparisler(response.siparisler);
                    updatePagination(response.toplam_kayit);
                } else {
                    $("#errorAlert").text(response.mesaj);
                    $("#errorAlert").show();
                    setTimeout(function() { $("#errorAlert").hide(); }, 3000);
                }
            },
            error: function(xhr, status, error) {
                $("#loading").hide();
                $("#errorAlert").text("Siparişler getirilirken bir hata oluştu.");
                $("#errorAlert").show();
                setTimeout(function() { $("#errorAlert").hide(); }, 3000);
            }
        });
    }
    
    // Siparişleri tabloya yazdırma fonksiyonu
    function displaySiparisler(siparisler) {
        const siparisData = $("#siparisData");
        siparisData.empty();
        
        if (siparisler.length === 0) {
            $("#noDataMessage").show();
            return;
        }
        
        siparisler.forEach(function(siparis) {
            const durum = getDurumBadge(siparis.siparis_durumu);
            const formatTarih = formatDate(siparis.tarih);
            
            // Teslimat adresi formatı
            let adresHtml = "-";
            if (siparis.il && siparis.ilce) {
                adresHtml = `<span class="badge bg-secondary"><i class="fas fa-map-marker-alt me-1"></i> ${siparis.il} / ${siparis.ilce}</span>`;
            }
            
            let row = `<tr id="siparis-${siparis.id}">
                <td>${siparis.id}</td>
                <td>${siparis.fatura_no}</td>
                <td>${siparis.musteri_ad} ${siparis.musteri_soyad}</td>
                <td>${adresHtml}</td>
                <td>${parseFloat(siparis.toplam).toFixed(2)} ₺</td>
                <td>${siparis.odeme_yontemi}</td>
                <td data-sort="${siparis.tarih}">${formatTarih}</td>
                <td>${durum}</td>
                <td>
                    <button class="btn btn-sm btn-info detay-btn" data-id="${siparis.id}">
                        <i class="fas fa-search"></i> Detay
                    </button>
                </td>
            </tr>`;
            
            siparisData.append(row);
        });
    }
    
    // Durum badge'leri için yardımcı fonksiyon
    function getDurumBadge(durum) {
        switch(durum) {
            case 'onaylandi':
                return '<span class="badge bg-success">Onaylandı</span>';
            case 'iptal_edildi':
                return '<span class="badge bg-danger">İptal Edildi</span>';
            case 'beklemede':
                return '<span class="badge bg-warning text-dark">Beklemede</span>';
            case 'hazirlaniyor':
                return '<span class="badge bg-primary">Hazırlanıyor</span>';
            case 'kargolandi':
                return '<span class="badge bg-info">Kargolandı</span>';
            case 'teslim_edildi':
                return '<span class="badge bg-dark">Teslim Edildi</span>';
            default:
                return '<span class="badge bg-secondary">Bilinmiyor</span>';
        }
    }
    
    // Tarih formatı için yardımcı fonksiyon
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('tr-TR') + ' ' + date.toLocaleTimeString('tr-TR', {hour: '2-digit', minute:'2-digit'});
    }
    
    // Sayfalama için yardımcı fonksiyon
    function updatePagination(totalItems) {
        const totalPages = Math.ceil(totalItems / itemsPerPage);
        const pagination = $("#pagination");
        pagination.empty();
        
        // Sayfa bilgisini güncelle
        $("#siparisCount").text(`Toplam ${totalItems} sipariş, ${Math.min((currentPage - 1) * itemsPerPage + 1, totalItems)} - ${Math.min(currentPage * itemsPerPage, totalItems)} arası gösteriliyor.`);
        
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
                loadSiparisler();
            }
        });
    }
    
    // Detay butonuna tıklandığında
    $(document).on("click", ".detay-btn", function() {
        const siparisId = $(this).data("id");
        getSiparisDetay(siparisId);
    });
    
    // Sipariş detaylarını getirme fonksiyonu
    function getSiparisDetay(siparisId) {
        $.ajax({
            type: "GET",
            url: "../netting/islem.php",
            data: {
                islem: "siparis_detay",
                siparis_id: siparisId
            },
            dataType: "json",
            success: function(response) {
                if (response.durum === "success") {
                    displaySiparisDetay(response.siparis, response.urunler);
                    $("#siparisDetayModal").modal("show");
                } else {
                    $("#errorAlert").text(response.mesaj);
                    $("#errorAlert").show();
                    setTimeout(function() { $("#errorAlert").hide(); }, 3000);
                }
            },
            error: function(xhr, status, error) {
                $("#errorAlert").text("Sipariş detayları alınırken bir hata oluştu.");
                $("#errorAlert").show();
                setTimeout(function() { $("#errorAlert").hide(); }, 3000);
            }
        });
    }
    
    // Sipariş detaylarını gösterme fonksiyonu
    function displaySiparisDetay(siparis, urunler) {
        // Sipariş bilgileri
        $("#detay_fatura_no").text(siparis.fatura_no);
        $("#detay_tarih").text(formatDate(siparis.tarih));
        $("#detay_durum").html(getDurumBadge(siparis.siparis_durumu));
        $("#detay_odeme_yontemi").text(siparis.odeme_yontemi);
        
        // Müşteri bilgileri
        $("#detay_musteri_adsoyad").text(`${siparis.musteri_ad} ${siparis.musteri_soyad}`);
        $("#detay_musteri_email").text(siparis.musteri_mail);
        
        // Teslimat bilgileri
        if (siparis.adres_id && siparis.teslimat_adres) {
            $("#teslimat_bilgileri_container").hide();
            $("#adres_bilgileri").show();
            
            $("#detay_adres_adi").text(siparis.adres_adi || "-");
            $("#detay_teslimat_adsoyad").text(siparis.teslimat_ad_soyad || "-");
            $("#detay_teslimat_telefon").text(siparis.teslimat_telefon || "-");
            $("#detay_tc_no").text(siparis.tc_no || "-");
            $("#detay_il_ilce").text(`${siparis.il} / ${siparis.ilce}` || "-");
            $("#detay_teslimat_adres").text(siparis.teslimat_adres || "-");
            $("#detay_posta_kodu").text(siparis.posta_kodu || "-");
        } else {
            $("#teslimat_bilgileri_container").show();
            $("#adres_bilgileri").hide();
        }
        
        // Ürünler tablosu
        const urunlerTable = $("#detay_urunler");
        urunlerTable.empty();
        
        urunler.forEach(function(urun) {
            const birimFiyat = parseFloat(urun.birim_fiyat);
            const miktar = parseInt(urun.miktar);
            const toplam = birimFiyat * miktar;
            
            // Ürün adı ve varyasyon bilgisini göster
            let urunBilgisi = urun.urun_isim;
            if (urun.varyasyon_bilgisi) {
                urunBilgisi += `<br><small class="text-muted">${urun.varyasyon_bilgisi}</small>`;
            }
            
            urunlerTable.append(`
                <tr>
                    <td>${urunBilgisi}</td>
                    <td>${birimFiyat.toFixed(2)} ₺</td>
                    <td>${miktar}</td>
                    <td>${toplam.toFixed(2)} ₺</td>
                </tr>
            `);
        });
        
        // Toplam bilgileri
        $("#detay_ara_toplam").text(`${parseFloat(siparis.ara_toplam).toFixed(2)} ₺`);
        
        if (parseFloat(siparis.indirim_tutari) > 0) {
            $("#detay_kupon_kod").text(siparis.kupon_kod || "-");
            $("#detay_indirim").text(`${parseFloat(siparis.indirim_tutari).toFixed(2)} ₺`);
            $("#detay_indirim_row").show();
        } else {
            $("#detay_indirim_row").hide();
        }
        
        $("#detay_toplam").text(`${parseFloat(siparis.toplam).toFixed(2)} ₺`);
        
        // İşlem butonlarını ayarla
        const islemBtns = $("#siparisIslemBtns");
        islemBtns.empty();
        
        // Her durumda gösterilecek butonları ayarla
        switch(siparis.siparis_durumu) {
            case 'beklemede':
                islemBtns.append(`
                    <button type="button" class="btn btn-success durum-btn" data-id="${siparis.id}" data-durum="onaylandi">
                        <i class="fas fa-check"></i> Siparişi Onayla
                    </button>
                    <button type="button" class="btn btn-danger ms-2 durum-btn" data-id="${siparis.id}" data-durum="iptal_edildi">
                        <i class="fas fa-times"></i> Siparişi İptal Et
                    </button>
                `);
                break;
            case 'onaylandi':
                islemBtns.append(`
                    <button type="button" class="btn btn-primary durum-btn" data-id="${siparis.id}" data-durum="hazirlaniyor">
                        <i class="fas fa-box"></i> Hazırlanıyor
                    </button>
                    <button type="button" class="btn btn-danger ms-2 durum-btn" data-id="${siparis.id}" data-durum="iptal_edildi">
                        <i class="fas fa-times"></i> İptal Et
                    </button>
                `);
                break;
            case 'hazirlaniyor':
                islemBtns.append(`
                    <button type="button" class="btn btn-info text-white durum-btn" data-id="${siparis.id}" data-durum="kargolandi">
                        <i class="fas fa-shipping-fast"></i> Kargolandı
                    </button>
                    <button type="button" class="btn btn-danger ms-2 durum-btn" data-id="${siparis.id}" data-durum="iptal_edildi">
                        <i class="fas fa-times"></i> İptal Et
                    </button>
                `);
                break;
            case 'kargolandi':
                islemBtns.append(`
                    <button type="button" class="btn btn-dark durum-btn" data-id="${siparis.id}" data-durum="teslim_edildi">
                        <i class="fas fa-check-double"></i> Teslim Edildi
                    </button>
                `);
                break;
            case 'teslim_edildi':
                // Teslim edilmiş siparişler için işlem butonu gösterme
                break;
            case 'iptal_edildi':
                // İptal edilmiş siparişler için işlem butonu gösterme
                break;
        }
        
        // Durum değiştirme seçeneği ekle (sadece iptal edilmiş ve teslim edilmiş siparişler hariç)
        if (siparis.siparis_durumu != 'iptal_edildi' && siparis.siparis_durumu != 'teslim_edildi') {
            islemBtns.append(`
                <button type="button" class="btn btn-outline-secondary ms-2" data-bs-toggle="collapse" data-bs-target="#durumDegistirCollapse">
                    <i class="fas fa-exchange-alt"></i> Durumu Değiştir
                </button>
                <div class="collapse mt-2" id="durumDegistirCollapse">
                    <div class="card card-body">
                        <h6>Sipariş Durumunu Değiştir</h6>
                        <select class="form-select mb-2" id="durumDegistirSelect">
                            <option value="">Durum Seçin</option>
                            <option value="beklemede" ${siparis.siparis_durumu === 'beklemede' ? 'disabled' : ''}>Beklemede</option>
                            <option value="onaylandi" ${siparis.siparis_durumu === 'onaylandi' ? 'disabled' : ''}>Onaylandı</option>
                            <option value="hazirlaniyor" ${siparis.siparis_durumu === 'hazirlaniyor' ? 'disabled' : ''}>Hazırlanıyor</option>
                            <option value="kargolandi" ${siparis.siparis_durumu === 'kargolandi' ? 'disabled' : ''}>Kargolandı</option>
                            <option value="teslim_edildi" ${siparis.siparis_durumu === 'teslim_edildi' ? 'disabled' : ''}>Teslim Edildi</option>
                            <option value="iptal_edildi" ${siparis.siparis_durumu === 'iptal_edildi' ? 'disabled' : ''}>İptal Edildi</option>
                        </select>
                        <button type="button" class="btn btn-sm btn-secondary" id="manuelDurumBtn">Durumu Güncelle</button>
                    </div>
                </div>
            `);
        }
    }
    
    // Sipariş durum değiştirme butonlarına tıklandığında
    $(document).on("click", ".durum-btn", function() {
        const siparisId = $(this).data("id");
        const yeniDurum = $(this).data("durum");
        
        $("#durum_siparis_id").val(siparisId);
        $("#durum_yeni_durum").val(yeniDurum);
        
        if (yeniDurum === 'onaylandi') {
            $("#durumModalTitle").text("Siparişi Onayla");
            $("#durumModalMessage").text("Bu siparişi onaylamak istediğinizden emin misiniz?");
            $("#durumOnayBtn").removeClass("btn-danger btn-primary btn-info btn-dark").addClass("btn-success").text("Onayla");
        } else if (yeniDurum === 'iptal_edildi') {
            $("#durumModalTitle").text("Siparişi İptal Et");
            $("#durumModalMessage").text("Bu siparişi iptal etmek istediğinizden emin misiniz?");
            $("#durumOnayBtn").removeClass("btn-success btn-primary btn-info btn-dark").addClass("btn-danger").text("İptal Et");
        } else if (yeniDurum === 'hazirlaniyor') {
            $("#durumModalTitle").text("Sipariş Hazırlanıyor");
            $("#durumModalMessage").text("Sipariş durumunu 'Hazırlanıyor' olarak değiştirmek istediğinizden emin misiniz?");
            $("#durumOnayBtn").removeClass("btn-success btn-danger btn-info btn-dark").addClass("btn-primary").text("Onayla");
        } else if (yeniDurum === 'kargolandi') {
            $("#durumModalTitle").text("Sipariş Kargolandı");
            $("#durumModalMessage").text("Sipariş durumunu 'Kargolandı' olarak değiştirmek istediğinizden emin misiniz?");
            $("#durumOnayBtn").removeClass("btn-success btn-danger btn-primary btn-dark").addClass("btn-info").text("Onayla");
        } else if (yeniDurum === 'teslim_edildi') {
            $("#durumModalTitle").text("Sipariş Teslim Edildi");
            $("#durumModalMessage").text("Sipariş durumunu 'Teslim Edildi' olarak değiştirmek istediğinizden emin misiniz?");
            $("#durumOnayBtn").removeClass("btn-success btn-danger btn-primary btn-info").addClass("btn-dark").text("Onayla");
        } else if (yeniDurum === 'beklemede') {
            $("#durumModalTitle").text("Sipariş Beklemede");
            $("#durumModalMessage").text("Sipariş durumunu 'Beklemede' olarak değiştirmek istediğinizden emin misiniz?");
            $("#durumOnayBtn").removeClass("btn-success btn-danger btn-primary btn-info btn-dark").addClass("btn-warning").text("Onayla");
        }
        
        $("#siparisDurumModal").modal("show");
    });
    
    // Manuel durum değiştirme için event listener
    $(document).on("click", "#manuelDurumBtn", function() {
        const siparisId = $(".durum-btn").first().data("id");
        const yeniDurum = $("#durumDegistirSelect").val();
        
        if (yeniDurum) {
            $("#durum_siparis_id").val(siparisId);
            $("#durum_yeni_durum").val(yeniDurum);
            
            // Durum değişiklik modalını ayarla
            if (yeniDurum === 'onaylandi') {
                $("#durumModalTitle").text("Siparişi Onayla");
                $("#durumModalMessage").text("Bu siparişi onaylamak istediğinizden emin misiniz?");
                $("#durumOnayBtn").removeClass("btn-danger btn-primary btn-info btn-dark btn-warning").addClass("btn-success").text("Onayla");
            } else if (yeniDurum === 'iptal_edildi') {
                $("#durumModalTitle").text("Siparişi İptal Et");
                $("#durumModalMessage").text("Bu siparişi iptal etmek istediğinizden emin misiniz?");
                $("#durumOnayBtn").removeClass("btn-success btn-primary btn-info btn-dark btn-warning").addClass("btn-danger").text("İptal Et");
            } else if (yeniDurum === 'hazirlaniyor') {
                $("#durumModalTitle").text("Sipariş Hazırlanıyor");
                $("#durumModalMessage").text("Sipariş durumunu 'Hazırlanıyor' olarak değiştirmek istediğinizden emin misiniz?");
                $("#durumOnayBtn").removeClass("btn-success btn-danger btn-info btn-dark btn-warning").addClass("btn-primary").text("Onayla");
            } else if (yeniDurum === 'kargolandi') {
                $("#durumModalTitle").text("Sipariş Kargolandı");
                $("#durumModalMessage").text("Sipariş durumunu 'Kargolandı' olarak değiştirmek istediğinizden emin misiniz?");
                $("#durumOnayBtn").removeClass("btn-success btn-danger btn-primary btn-dark btn-warning").addClass("btn-info").text("Onayla");
            } else if (yeniDurum === 'teslim_edildi') {
                $("#durumModalTitle").text("Sipariş Teslim Edildi");
                $("#durumModalMessage").text("Sipariş durumunu 'Teslim Edildi' olarak değiştirmek istediğinizden emin misiniz?");
                $("#durumOnayBtn").removeClass("btn-success btn-danger btn-primary btn-info btn-warning").addClass("btn-dark").text("Onayla");
            } else if (yeniDurum === 'beklemede') {
                $("#durumModalTitle").text("Sipariş Beklemede");
                $("#durumModalMessage").text("Sipariş durumunu 'Beklemede' olarak değiştirmek istediğinizden emin misiniz?");
                $("#durumOnayBtn").removeClass("btn-success btn-danger btn-primary btn-info btn-dark").addClass("btn-warning").text("Onayla");
            }
            
            $("#siparisDurumModal").modal("show");
        }
    });
    
    // Durum değiştirme onay butonuna tıklandığında
    $("#durumOnayBtn").on("click", function() {
        const siparisId = $("#durum_siparis_id").val();
        const yeniDurum = $("#durum_yeni_durum").val();
        
        $.ajax({
            type: "POST",
            url: "../netting/islem.php",
            data: {
                islem: "siparis_durum_degistir",
                siparis_id: siparisId,
                yeni_durum: yeniDurum
            },
            dataType: "json",
            success: function(response) {
                $("#siparisDurumModal").modal("hide");
                
                if (response.durum === "success") {
                    $("#successAlert").text(response.mesaj);
                    $("#successAlert").show();
                    
                    // Hem detay modalındaki hem de tablodaki durum bilgisini güncelle
                    $("#detay_durum").html(getDurumBadge(yeniDurum));
                    $("#siparisIslemBtns").empty(); // Durum değiştiğinde butonları kaldır
                    
                    // Tablodaki satırı güncelle veya sayfayı yeniden yükle
                    loadSiparisler();
                    
                    // Detay modalını kapat
                    $("#siparisDetayModal").modal("hide");
                } else {
                    $("#errorAlert").text(response.mesaj);
                    $("#errorAlert").show();
                }
                
                setTimeout(function() {
                    $("#successAlert, #errorAlert").hide();
                }, 3000);
            },
            error: function(xhr, status, error) {
                $("#siparisDurumModal").modal("hide");
                $("#errorAlert").text("İşlem sırasında bir hata oluştu.");
                $("#errorAlert").show();
                setTimeout(function() { $("#errorAlert").hide(); }, 3000);
            }
        });
    });
});
</script> 