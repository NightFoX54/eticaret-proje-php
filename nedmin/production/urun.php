<?php include 'header.php'; ?>

<div class="page-header d-flex justify-content-between align-items-center">
    <h2>Ürünler</h2>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#urunEkleModal">
        <i class="fas fa-plus"></i> Yeni Ürün Ekle
    </button>
</div>

<!-- Uyarı Mesajları -->
<div class="alert alert-success" id="successAlert" style="display:none;" role="alert"></div>
<div class="alert alert-danger" id="errorAlert" style="display:none;" role="alert"></div>

<!-- Ürün Tablosu -->
<div class="card mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="urunTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Görsel</th>
                        <th>Ürün Adı</th>
                        <th>Kategoriler</th>
                        <th>Stok</th>
                        <th>Fiyat</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Tüm ürünleri getir
                    $urun_sor = $db->prepare("SELECT * FROM urun ORDER BY id DESC");
                    $urun_sor->execute();
                    
                    while ($urun = $urun_sor->fetch(PDO::FETCH_ASSOC)) {
                        // Ürünün ana resmini al
                        $resim_sor = $db->prepare("SELECT foto_path FROM urun_fotolar WHERE urun_id = :urun_id ORDER BY id ASC LIMIT 1");
                        $resim_sor->execute(['urun_id' => $urun['id']]);
                        $resim = $resim_sor->fetch(PDO::FETCH_ASSOC);
                        
                        // Ürünün kategorilerini al
                        $kategori_sor = $db->prepare("SELECT k.kategori_isim 
                                                     FROM urun_kategori_baglantisi kb
                                                     INNER JOIN urun_kategoriler k ON kb.kategori_id = k.id
                                                     WHERE kb.urun_id = :urun_id");
                        $kategori_sor->execute(['urun_id' => $urun['id']]);
                        $kategoriler = $kategori_sor->fetchAll(PDO::FETCH_ASSOC);
                    ?>
                    <tr id="urun-<?php echo $urun['id']; ?>">
                        <td><?php echo $urun['id']; ?></td>
                        <td>
                            <?php if ($resim): ?>
                                <img src="../../<?php echo $resim['foto_path']; ?>" alt="Ürün Görseli" width="50">
                            <?php else: ?>
                                <span class="text-muted">Resim Yok</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $urun['urun_isim']; ?></td>
                        <td>
                            <?php 
                            $kategori_isimleri = [];
                            foreach ($kategoriler as $kategori) {
                                $kategori_isimleri[] = $kategori['kategori_isim'];
                            }
                            echo !empty($kategori_isimleri) ? implode(', ', $kategori_isimleri) : '<span class="text-muted">Kategori Yok</span>';
                            ?>
                        </td>
                        <td><?php echo $urun['urun_stok']; ?></td>
                        <td><?php echo number_format($urun['urun_fiyat'], 2); ?> ₺</td>
                        <td>
                            <button class="btn btn-sm btn-warning edit-btn" 
                                    data-id="<?php echo $urun['id']; ?>">
                                <i class="fas fa-edit"></i> Düzenle
                            </button>
                            <button class="btn btn-sm btn-danger delete-btn" 
                                    data-id="<?php echo $urun['id']; ?>" 
                                    data-isim="<?php echo $urun['urun_isim']; ?>">
                                <i class="fas fa-trash"></i> Sil
                            </button>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Ürün Ekle Modal -->
<div class="modal fade" id="urunEkleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Yeni Ürün Ekle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="urunEkleForm" enctype="multipart/form-data">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="urun_isim" class="form-label">Ürün Adı <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="urun_isim" name="urun_isim" required>
                        </div>
                        <div class="col-md-6">
                            <label for="urun_fiyat" class="form-label">Temel Fiyat (₺) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="urun_fiyat" name="urun_fiyat" min="0.01" step="0.01" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="urun_stok" class="form-label">Temel Stok Miktarı <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="urun_stok" name="urun_stok" min="0" step="1" value="0" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="kategori_ids" class="form-label">Kategoriler</label>
                        <div id="kategori_tree_container" class="border rounded p-2" style="max-height: 200px; overflow-y: auto;">
                            <?php
                            // Tüm kategorileri getir
                            $kategori_sor = $db->prepare("SELECT * FROM urun_kategoriler ORDER BY COALESCE(parent_kategori_id, 0), kategori_isim ASC");
                            $kategori_sor->execute();
                            
                            // Kategori ağacını oluşturmak için yardımcı fonksiyon
                            function buildCategoryTree($parent_id = null, $level = 0) {
                                global $db;
                                
                                $kategori_sor = $db->prepare("SELECT * FROM urun_kategoriler WHERE parent_kategori_id " . 
                                                           ($parent_id === null ? "IS NULL" : "= :parent_id") . 
                                                           " ORDER BY kategori_isim ASC");
                                
                                if ($parent_id !== null) {
                                    $kategori_sor->bindParam(':parent_id', $parent_id);
                                }
                                
                                $kategori_sor->execute();
                                $kategoriler = $kategori_sor->fetchAll(PDO::FETCH_ASSOC);
                                
                                $html = '';
                                foreach ($kategoriler as $kategori) {
                                    $indent = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level);
                                    $html .= '<div class="form-check">';
                                    $html .= $indent . '<input class="form-check-input kategori-checkbox" type="checkbox" name="kategori_ids[]" value="' . $kategori['id'] . '" id="kategori_' . $kategori['id'] . '">';
                                    $html .= '<label class="form-check-label" for="kategori_' . $kategori['id'] . '">' . $kategori['kategori_isim'] . '</label>';
                                    $html .= '</div>';
                                    
                                    // Alt kategorileri ekle
                                    $html .= buildCategoryTree($kategori['id'], $level + 1);
                                }
                                
                                return $html;
                            }
                            
                            echo buildCategoryTree();
                            ?>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="urun_aciklama" class="form-label">Ürün Açıklaması</label>
                        <textarea class="form-control" id="urun_aciklama" name="urun_aciklama" rows="4"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="urun_resimler" class="form-label">Ürün Görselleri</label>
                        <input type="file" class="form-control" id="urun_resimler" name="urun_resimler[]" multiple accept=".jpg,.jpeg,.png,.webp">
                        <div class="form-text">En fazla 5 adet, her biri en fazla 5MB boyutunda görsel yükleyebilirsiniz.</div>
                    </div>

                    <div class="mb-3">
                        <label for="urun_marka" class="form-label">Marka</label>
                        <div class="input-group">
                            <select class="form-select" id="urun_marka" name="urun_marka">
                                <option value="">Marka Seçin</option>
                                <!-- Markalar AJAX ile doldurulacak -->
                            </select>
                            <button class="btn btn-outline-secondary" type="button" id="yeniMarkaBtn">
                                <i class="fas fa-plus"></i> Yeni
                            </button>
                        </div>
                    </div>
                    
                    <!-- Ürün Varyasyonları Bölümü -->
                    <div class="card mt-4 mb-3">
                        <div class="card-header">
                            <h5 class="mb-0">Ürün Varyasyonları</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="form-label">Nitelikler</label>
                                    <button type="button" class="btn btn-sm btn-primary" id="yeniNitelikBtn">
                                        <i class="fas fa-plus"></i> Yeni Nitelik Ekle
                                    </button>
                                </div>
                                <div id="nitelikler_container">
                                    <!-- Nitelikler JavaScript ile eklenecek -->
                                    <div class="alert alert-info">
                                        Ürün için nitelik ekleyerek (renk, beden, vb.) varyasyonlar oluşturabilirsiniz.
                                    </div>
                                </div>
                            </div>
                            
                            <div id="varyasyonlar_container" style="display: none;">
                                <hr>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5>Oluşturulan Varyasyonlar</h5>
                                    <button type="button" class="btn btn-sm btn-success" id="varyasyonOlusturBtn">
                                        <i class="fas fa-sync"></i> Varyasyonları Oluştur
                                    </button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover" id="varyasyonlarTable">
                                        <thead>
                                            <tr>
                                                <th>Kombinasyon</th>
                                                <th>Fiyat (₺)</th>
                                                <th>Stok</th>
                                                <th>İşlemler</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Varyasyonlar JavaScript ile eklenecek -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                <button type="button" class="btn btn-primary" id="urunEkleBtn">Kaydet</button>
            </div>
        </div>
    </div>
</div>

<!-- Ürün Düzenle Modal -->
<div class="modal fade" id="urunDuzenleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ürün Düzenle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="urunDuzenleForm" enctype="multipart/form-data">
                    <input type="hidden" id="duzenle_urun_id" name="urun_id">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="duzenle_urun_isim" class="form-label">Ürün Adı <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="duzenle_urun_isim" name="urun_isim" required>
                        </div>
                        <div class="col-md-6">
                            <label for="duzenle_urun_fiyat" class="form-label">Temel Fiyat (₺) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="duzenle_urun_fiyat" name="urun_fiyat" min="0.01" step="0.01" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="duzenle_urun_stok" class="form-label">Temel Stok Miktarı <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="duzenle_urun_stok" name="urun_stok" min="0" step="1" value="0" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="duzenle_kategori_ids" class="form-label">Kategoriler</label>
                        <div id="duzenle_kategori_container" class="border rounded p-2" style="max-height: 200px; overflow-y: auto;">
                            <!-- Kategoriler JavaScript ile eklenecek -->
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="duzenle_urun_aciklama" class="form-label">Ürün Açıklaması</label>
                        <textarea class="form-control" id="duzenle_urun_aciklama" name="urun_aciklama" rows="4"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Mevcut Görseller</label>
                        <div id="mevcut_resimler" class="row g-2 mb-2">
                            <!-- Mevcut resimler JavaScript ile eklenecek -->
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="duzenle_urun_resimler" class="form-label">Yeni Görseller Ekle</label>
                        <input type="file" class="form-control" id="duzenle_urun_resimler" name="urun_resimler[]" multiple accept=".jpg,.jpeg,.png,.webp">
                        <div class="form-text">En fazla 5 adet, her biri en fazla 5MB boyutunda görsel yükleyebilirsiniz.</div>
                    </div>

                    <div class="mb-3">
                        <label for="duzenle_urun_marka" class="form-label">Marka</label>
                        <div class="input-group">
                            <select class="form-select" id="duzenle_urun_marka" name="urun_marka">
                                <option value="">Marka Seçin</option>
                                <!-- Markalar AJAX ile doldurulacak -->
                            </select>
                            <button class="btn btn-outline-secondary" type="button" id="duzenleYeniMarkaBtn">
                                <i class="fas fa-plus"></i> Yeni
                            </button>
                        </div>
                    </div>
                    
                    <!-- Ürün Varyasyonları Bölümü (Düzenleme) -->
                    <div class="card mt-4 mb-3">
                        <div class="card-header">
                            <h5 class="mb-0">Ürün Varyasyonları</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="form-label">Nitelikler</label>
                                    <button type="button" class="btn btn-sm btn-primary" id="duzenleYeniNitelikBtn">
                                        <i class="fas fa-plus"></i> Yeni Nitelik Ekle
                                    </button>
                                </div>
                                <div id="duzenle_nitelikler_container">
                                    <!-- Nitelikler JavaScript ile eklenecek -->
                                </div>
                            </div>
                            
                            <div id="duzenle_varyasyonlar_container">
                                <hr>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5>Ürün Varyasyonları</h5>
                                    <button type="button" class="btn btn-sm btn-success" id="duzenleVaryasyonOlusturBtn">
                                        <i class="fas fa-sync"></i> Varyasyonları Yeniden Oluştur
                                    </button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover" id="duzenleVaryasyonlarTable">
                                        <thead>
                                            <tr>
                                                <th>Kombinasyon</th>
                                                <th>Fiyat (₺)</th>
                                                <th>Stok</th>
                                                <th>İşlemler</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Varyasyonlar JavaScript ile eklenecek -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                <button type="button" class="btn btn-warning" id="urunGuncelleBtn">Güncelle</button>
            </div>
        </div>
    </div>
</div>

<!-- Yeni Nitelik Modal -->
<div class="modal fade" id="nitelikModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nitelik Ekle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="nitelikEkleForm">
                    <div class="mb-3">
                        <label for="nitelik_secim" class="form-label">Nitelik Seçimi</label>
                        <div class="input-group mb-2">
                            <select class="form-select" id="nitelik_secim">
                                <option value="yeni">Yeni Nitelik Ekle</option>
                                <!-- Mevcut nitelikler AJAX ile yüklenecek -->
                            </select>
                        </div>
                    </div>
                    
                    <div id="yeniNitelikAlani">
                        <div class="mb-3">
                            <label for="nitelik_ad" class="form-label">Nitelik Adı <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nitelik_ad" name="nitelik_ad" placeholder="Renk, Beden, Malzeme vb." required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Nitelik Değerleri <span class="text-danger">*</span></label>
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" id="yeni_deger" placeholder="Yeni değer ekle">
                                <button class="btn btn-outline-success" type="button" id="degerEkleBtn">
                                    <i class="fas fa-plus"></i> Ekle
                                </button>
                            </div>
                            <div class="form-text mb-2">Her bir değeri eklemek için "Ekle" butonuna basın. Örn: Kırmızı, Mavi, XL, XXL</div>
                            
                            <div id="degerler_container" class="border rounded p-2" style="min-height: 100px;">
                                <div class="alert alert-info">Henüz değer eklenmedi</div>
                            </div>
                        </div>
                    </div>
                    
                    <div id="mevcutNitelikAlani" style="display: none;">
                        <div class="mb-3">
                            <h6 class="fw-bold" id="seciliNitelikAd"></h6>
                            
                            <div class="mb-3">
                                <label class="form-label">Mevcut Değerler</label>
                                <div id="mevcut_degerler_container" class="border rounded p-2 mb-3" style="min-height: 60px;">
                                    <!-- Mevcut değerler JavaScript ile yüklenecek -->
                                </div>
                                
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="tumDegerleriSec">
                                    <label class="form-check-label" for="tumDegerleriSec">
                                        Tüm değerleri seç
                                    </label>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Yeni Değer Ekle</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="mevcut_yeni_deger" placeholder="Yeni değer">
                                    <button class="btn btn-outline-success" type="button" id="mevcutDegerEkleBtn">
                                        <i class="fas fa-plus"></i> Ekle
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                <button type="button" class="btn btn-primary" id="nitelikKaydetBtn">Kaydet</button>
            </div>
        </div>
    </div>
</div>

<!-- Ürün Sil Modal -->
<div class="modal fade" id="urunSilModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ürün Sil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Bu ürünü silmek istediğinizden emin misiniz?</p>
                <p id="silinecekUrunIsim" class="fw-bold"></p>
                <input type="hidden" id="silinecekUrunId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                <button type="button" class="btn btn-danger" id="urunSilBtn">Sil</button>
            </div>
        </div>
    </div>
</div>

<!-- Yeni Marka Ekleme Modal -->
<div class="modal fade" id="yeniMarkaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Yeni Marka Ekle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="marka_isim" class="form-label">Marka Adı</label>
                    <input type="text" class="form-control" id="marka_isim" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                <button type="button" class="btn btn-primary" id="markaEkleBtn">Ekle</button>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
$(document).ready(function() {
    // Bugünün tarihini ayarla
    const today = new Date().toISOString().split('T')[0];

    // Ürün Ekleme
    $("#urunEkleBtn").on("click", function() {
        var formData = new FormData($("#urunEkleForm")[0]);
        formData.append('islem', 'urun_ekle');
        
        // Kategori seçimlerini al
        var kategori_ids = [];
        $("input[name='kategori_ids[]']:checked").each(function() {
            kategori_ids.push($(this).val());
        });
        
        // Nitelikleri ekle
        if (nitelikler.length > 0) {
            formData.append('nitelikler', JSON.stringify(nitelikler));
        }
        
        // Varyasyonları ekle
        var varyasyonlar = [];
        $("#varyasyonlarTable tbody tr").each(function(index) {
            var kombinasyon = $(this).find("input[name^='varyasyon[" + index + "][kombinasyon]']").val();
            var fiyat = $(this).find("input[name^='varyasyon[" + index + "][fiyat]']").val();
            var stok = $(this).find("input[name^='varyasyon[" + index + "][stok]']").val();
            
            var nitelikDegerleri = [];
            $(this).find("input[name^='varyasyon[" + index + "][nitelik_degerleri]']").each(function() {
                var parts = $(this).val().split(':');
                nitelikDegerleri.push({
                    nitelik_id: parts[0],
                    deger_id: parts[1]
                });
            });
            
            varyasyonlar.push({
                kombinasyon: kombinasyon,
                fiyat: fiyat,
                stok: stok,
                nitelik_degerleri: nitelikDegerleri
            });
        });
        
        if (varyasyonlar.length > 0) {
            formData.append('varyasyonlar', JSON.stringify(varyasyonlar));
        }
        
        // Form validasyonu
        if (!formData.get('urun_isim')) {
            $("#errorAlert").text("Ürün adı boş olamaz!");
            $("#errorAlert").show();
            setTimeout(function() { $("#errorAlert").hide(); }, 3000);
            return;
        }
        
        if (parseFloat(formData.get('urun_fiyat')) <= 0) {
            $("#errorAlert").text("Ürün fiyatı sıfırdan büyük olmalıdır!");
            $("#errorAlert").show();
            setTimeout(function() { $("#errorAlert").hide(); }, 3000);
            return;
        }
        
        $.ajax({
            type: "POST",
            url: "../netting/islem.php",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function(response) {
                if (response.durum === "success") {
                    $("#successAlert").text(response.mesaj);
                    $("#successAlert").show();
                    
                    // Modalı kapat
                    $("#urunEkleModal").modal("hide");
                    
                    // Ürün listesini güncelle
                    setTimeout(function() {
                        window.location.reload();
                    }, 1500);
                } else {
                    $("#errorAlert").text(response.mesaj);
                    $("#errorAlert").show();
                }
                
                setTimeout(function() {
                    $("#successAlert, #errorAlert").hide();
                }, 3000);
            },
            error: function(xhr, status, error) {
                console.log("AJAX hatası:", status, error);
                $("#errorAlert").text("İşlem sırasında bir hata oluştu.");
                $("#errorAlert").show();
            }
        });
    });
    
    // Düzenle butonuna tıklanınca
    $(document).on("click", ".edit-btn", function() {
        var urun_id = $(this).data("id");
        
        // Ürün bilgilerini getir
        getUrunDetay(urun_id);
    });
    
    // Düzenleme modalı için kategorileri yükle
    function loadKategoriler(seciliKategoriler) {
        // AJAX çağrısı ile tüm kategorileri al
        $.ajax({
            type: "GET",
            url: "../netting/islem.php",
            data: {
                islem: "kategori_getir"
            },
            dataType: "json",
            success: function(response) {
                if (response.durum === "success") {
                    const kategoriler = response.kategoriler;
                    
                    // Kategoriler ağacını oluştur
                    let html = '';
                    
                    // Kategori ağacını oluşturmak için yardımcı fonksiyon
                    function buildCategoryTree(parentId = null, level = 0) {
                        const childCategories = kategoriler.filter(k => {
                            if (parentId === null) {
                                return k.parent_kategori_id === null || k.parent_kategori_id === '';
                            } else {
                                return k.parent_kategori_id == parentId;
                            }
                        });
                        
                        if (childCategories.length === 0) return '';
                        
                        let html = '';
                        childCategories.forEach(category => {
                            const indent = '&nbsp;'.repeat(level * 4);
                            const isChecked = seciliKategoriler.includes(category.id) ? 'checked' : '';
                            
                            html += `<div class="form-check">
                                ${indent}<input class="form-check-input" type="checkbox" name="kategori_ids[]" 
                                    value="${category.id}" id="duzenle_kategori_${category.id}" ${isChecked}>
                                <label class="form-check-label" for="duzenle_kategori_${category.id}">
                                    ${category.kategori_isim}
                                </label>
                            </div>`;
                            
                            // Alt kategorileri ekle
                            html += buildCategoryTree(category.id, level + 1);
                        });
                        
                        return html;
                    }
                    
                    // Tüm kategori ağacını oluştur
                    html = buildCategoryTree();
                    
                    // HTML'i container'a ekle
                    $("#duzenle_kategori_container").html(html);
                }
            }
        });
    }
    
    // Mevcut resimleri göster
    function showMevcutResimler(resimler) {
        let html = '';
        
        if (resimler.length > 0) {
            resimler.forEach(resim => {
                html += `
                <div class="col-md-3 mb-2">
                    <div class="card">
                        <img src="../../${resim.foto_path}" class="card-img-top" alt="Ürün Resmi" style="height: 120px; object-fit: cover;">
                        <div class="card-body p-2 text-center">
                            <div class="form-check d-inline-block">
                                <input class="form-check-input resim-sil-checkbox" type="checkbox" 
                                       value="${resim.id}" id="resim_sil_${resim.id}" name="silinecek_resimler[]">
                                <label class="form-check-label" for="resim_sil_${resim.id}">
                                    Sil
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                `;
            });
        } else {
            html = '<div class="col-12"><p class="text-muted">Ürüne ait resim bulunmamaktadır.</p></div>';
        }
        
        $("#mevcut_resimler").html(html);
    }
    
    // Ürün Güncelleme
    $("#urunGuncelleBtn").on("click", function() {
        var formData = new FormData($("#urunDuzenleForm")[0]);
        formData.append('islem', 'urun_guncelle');
        
        // Silinecek resimleri al
        var silinecek_resimler = [];
        $("input[name='silinecek_resimler[]']:checked").each(function() {
            silinecek_resimler.push($(this).val());
        });
        
        for (let i = 0; i < silinecek_resimler.length; i++) {
            formData.append('silinecek_resimler[]', silinecek_resimler[i]);
        }
        
        // Nitelikleri ekle
        if (duzenle_nitelikler.length > 0) {
            formData.append('nitelikler', JSON.stringify(duzenle_nitelikler));
        }
        
        // Varyasyonları ekle
        var varyasyonlar = [];
        $("#duzenleVaryasyonlarTable tbody tr").each(function(index) {
            var secenek_id = $(this).find("input[name^='varyasyon[" + index + "][secenek_id]']").val() || null;
            var kombinasyon = $(this).find("input[name^='varyasyon[" + index + "][kombinasyon]']").val();
            var fiyat = $(this).find("input[name^='varyasyon[" + index + "][fiyat]']").val();
            var stok = $(this).find("input[name^='varyasyon[" + index + "][stok]']").val();
            
            var nitelikDegerleri = [];
            $(this).find("input[name^='varyasyon[" + index + "][nitelik_degerleri]']").each(function() {
                var parts = $(this).val().split(':');
                nitelikDegerleri.push({
                    nitelik_id: parts[0],
                    deger_id: parts[1]
                });
            });
            
            varyasyonlar.push({
                secenek_id: secenek_id,
                kombinasyon: kombinasyon,
                fiyat: fiyat,
                stok: stok,
                nitelik_degerleri: nitelikDegerleri
            });
        });
        console.log(varyasyonlar);
        
        if (varyasyonlar.length > 0) {
            formData.append('varyasyonlar', JSON.stringify(varyasyonlar));
        }
        
        // Form validasyonu
        if (!formData.get('urun_isim')) {
            $("#errorAlert").text("Ürün adı boş olamaz!");
            $("#errorAlert").show();
            setTimeout(function() { $("#errorAlert").hide(); }, 3000);
            return;
        }
        
        if (parseFloat(formData.get('urun_fiyat')) <= 0) {
            $("#errorAlert").text("Ürün fiyatı sıfırdan büyük olmalıdır!");
            $("#errorAlert").show();
            setTimeout(function() { $("#errorAlert").hide(); }, 3000);
            return;
        }
        
        $.ajax({
            type: "POST",
            url: "../netting/islem.php",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function(response) {
                if (response.durum === "success") {
                    $("#successAlert").text(response.mesaj);
                    $("#successAlert").show();
                    
                    // Modalı kapat
                    $("#urunDuzenleModal").modal("hide");
                    
                    // Ürün listesini güncelle
                    setTimeout(function() {
                        window.location.reload();
                    }, 15000000);
                } else {
                    $("#errorAlert").text(response.mesaj);
                    $("#errorAlert").show();
                }
                
                setTimeout(function() {
                    $("#successAlert, #errorAlert").hide();
                }, 3000);
            },
            error: function(xhr, status, error) {
                console.log("AJAX hatası:", status, error);
                $("#errorAlert").text("İşlem sırasında bir hata oluştu.");
                $("#errorAlert").show();
            }
        });
    });
    
    // Sil butonuna tıklanınca
    $(document).on("click", ".delete-btn", function() {
        var urun_id = $(this).data("id");
        var urun_isim = $(this).data("isim");
        
        $("#silinecekUrunId").val(urun_id);
        $("#silinecekUrunIsim").text(urun_isim);
        
        // Modal göster
        $("#urunSilModal").modal("show");
    });
    
    // Ürün Silme
    $("#urunSilBtn").on("click", function() {
        var urun_id = $("#silinecekUrunId").val();
        
        $.ajax({
            type: "POST",
            url: "../netting/islem.php",
            data: {
                islem: "urun_sil",
                urun_id: urun_id
            },
            dataType: "json",
            success: function(response) {
                if (response.durum === "success") {
                    $("#successAlert").text(response.mesaj);
                    $("#successAlert").show();
                    
                    // Silinen satırı tablodan kaldır
                    $("#urun-" + urun_id).remove();
                    
                    // Modalı kapat
                    $("#urunSilModal").modal("hide");
                } else {
                    $("#errorAlert").text(response.mesaj);
                    $("#errorAlert").show();
                    
                    // Modalı kapat
                    $("#urunSilModal").modal("hide");
                }
                
                setTimeout(function() {
                    $("#successAlert, #errorAlert").hide();
                }, 3000);
            },
            error: function() {
                $("#errorAlert").text("İşlem sırasında bir hata oluştu.");
                $("#errorAlert").show();
            }
        });
    });

    // Markaları yükle
    loadMarkalar();
    
    // Yeni marka butonuna tıklandığında modal göster
    $("#yeniMarkaBtn").on("click", function() {
        $("#marka_isim").val('');
        $("#yeniMarkaModal").modal("show");
    });
    
    // Marka ekleme butonuna tıklandığında
    $("#markaEkleBtn").on("click", function() {
        const markaIsim = $("#marka_isim").val().trim();
        
        if (!markaIsim) {
            $("#errorAlert").text("Marka adı boş olamaz!");
            $("#errorAlert").show();
            setTimeout(function() { $("#errorAlert").hide(); }, 3000);
            return;
        }
        
        $.ajax({
            type: "POST",
            url: "../netting/islem.php",
            data: {
                islem: "marka_ekle",
                marka_isim: markaIsim
            },
            dataType: "json",
            success: function(response) {
                if (response.durum === "success") {
                    // Modalı kapat
                    $("#yeniMarkaModal").modal("hide");
                    
                    // Markalar listesini güncelle
                    loadMarkalar();
                    
                    // Yeni marka seçili gelsin
                    setTimeout(function() {
                        $("#urun_marka").val(response.marka_id);
                        $("#duzenle_urun_marka").val(response.marka_id);
                    }, 500);
                    
                    // Başarılı mesajı göster
                    $("#successAlert").text(response.mesaj);
                    $("#successAlert").show();
                    setTimeout(function() { $("#successAlert").hide(); }, 3000);
                } else {
                    $("#errorAlert").text(response.mesaj);
                    $("#errorAlert").show();
                    setTimeout(function() { $("#errorAlert").hide(); }, 3000);
                }
            },
            error: function() {
                $("#errorAlert").text("İşlem sırasında bir hata oluştu.");
                $("#errorAlert").show();
                setTimeout(function() { $("#errorAlert").hide(); }, 3000);
            }
        });
    });
    
    // Markaları yükleme fonksiyonu
    function loadMarkalar() {
        $.ajax({
            type: "GET",
            url: "../netting/islem.php",
            data: {
                islem: "marka_listele"
            },
            dataType: "json",
            success: function(response) {
                if (response.durum === "success") {
                    const markalar = response.markalar;
                    const selectEkle = $("#urun_marka");
                    const selectDuzenle = $("#duzenle_urun_marka");
                    
                    selectEkle.find('option').not(':first').remove();
                    selectDuzenle.find('option').not(':first').remove();
                    
                    markalar.forEach(function(marka) {
                        const option = `<option value="${marka.id}">${marka.marka_isim}</option>`;
                        selectEkle.append(option);
                        selectDuzenle.append(option);
                    });
                }
            },
            error: function() {
                console.log("Markalar yüklenirken bir hata oluştu.");
            }
        });
    }

    function getUrunDetay(urunId) {
        $.ajax({
            type: "GET",
            url: "../netting/islem.php",
            data: {
                islem: "urun_getir",
                urun_id: urunId
            },
            dataType: "json",
            success: function(response) {
                if (response.durum === "success") {
                    console.log(response);
                    const urun = response.urun;
                    const kategoriler = response.kategoriler || [];
                    const resimler = response.resimler || [];
                    
                    // Form alanlarını doldur
                    $("#duzenle_urun_id").val(urun.id);
                    $("#duzenle_urun_isim").val(urun.urun_isim);
                    $("#duzenle_urun_aciklama").val(urun.urun_aciklama);
                    $("#duzenle_urun_fiyat").val(urun.urun_fiyat);
                    $("#duzenle_urun_stok").val(urun.urun_stok);
                    
                    // Marka seçimini belirle
                    if (urun.urun_marka) {
                        $("#duzenle_urun_marka").val(urun.urun_marka);
                    } else {
                        $("#duzenle_urun_marka").val("");
                    }
                    
                    // Kategorileri işaretle
                    loadKategoriler(kategoriler);
                    
                    // Resimleri göster
                    showMevcutResimler(resimler);
                    
                    // Nitelikleri yükle ve düzgün formatla (bu önemli!)
                    duzenle_nitelikler = [];
                    if (response.nitelikler && response.nitelikler.length > 0) {
                        response.nitelikler.forEach(nitelik => {
                            duzenle_nitelikler.push({
                                id: nitelik.id,
                                ad: nitelik.ad,
                                degerler: nitelik.degerler.map(deger => ({
                                    id: deger.id,
                                    deger: deger.deger
                                }))
                            });
                        });
                    }
                    
                    renderNitelikler(duzenle_nitelikler, "duzenle_nitelikler_container");
                    
                    // Varyasyonları yükle
                    if (response.varyasyonlar && response.varyasyonlar.length > 0) {
                        let varyasyonHtml = '';
                        
                        response.varyasyonlar.forEach((varyasyon, index) => {
                            let combinationDetail = '';
                            
                            varyasyon.nitelik_degerleri.forEach(nitelikDeger => {
                                combinationDetail += `<input type="hidden" name="varyasyon[${index}][nitelik_degerleri][]" value="${nitelikDeger.nitelik_id}:${nitelikDeger.deger_id}">`;
                            });
                            
                            varyasyonHtml += `
                            <tr>
                                <td>
                                    ${varyasyon.kombinasyon}
                                    <input type="hidden" name="varyasyon[${index}][kombinasyon]" value="${varyasyon.kombinasyon}">
                                    <input type="hidden" name="varyasyon[${index}][secenek_id]" value="${varyasyon.secenek_id}">
                                    ${combinationDetail}
                                </td>
                                <td>
                                    <input type="number" class="form-control form-control-sm" name="varyasyon[${index}][fiyat]" value="${varyasyon.fiyat}" min="0" step="0.01">
                                </td>
                                <td>
                                    <input type="number" class="form-control form-control-sm" name="varyasyon[${index}][stok]" value="${varyasyon.stok}" min="0" step="1">
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-danger varyasyon-sil-btn" data-id="${varyasyon.secenek_id}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            `;
                        });
                        
                        $("#duzenleVaryasyonlarTable tbody").html(varyasyonHtml);
                        $("#duzenle_varyasyonlar_container").show();
                    } else if (duzenle_nitelikler.length > 0) {
                        // Nitelik var ama varyasyon yoksa, varyasyonları otomatik oluştur
                        $("#duzenle_varyasyonlar_container").show();
                        generateVariations(duzenle_nitelikler, "duzenleVaryasyonlarTable");
                    } else {
                        $("#duzenle_varyasyonlar_container").hide();
                    }
                    
                    // Modal göster
                    $("#urunDuzenleModal").modal("show");
                } else {
                    $("#errorAlert").text(response.mesaj || "Ürün bilgileri alınamadı.");
                    $("#errorAlert").show();
                    setTimeout(function() { $("#errorAlert").hide(); }, 3000);
                }
            },
            error: function() {
                $("#errorAlert").text("Ürün bilgileri alınırken bir hata oluştu.");
                $("#errorAlert").show();
                setTimeout(function() { $("#errorAlert").hide(); }, 3000);
            }
        });
    }

    // Add an event handler for the "duzenleYeniMarkaBtn"
    $(document).on("click", "#duzenleYeniMarkaBtn", function() {
        $("#marka_isim").val('');
        $("#yeniMarkaModal").modal("show");
    });

    // ------------------- Ürün Varyasyonları için JavaScript -------------------
    
    // Nitelik ve değerler için veri yapısı
    let nitelikler = [];
    let duzenle_nitelikler = [];
    let geciciDegerler = [];
    let activeModalMode = null; // Add this variable to track which modal is active
    let mevcutNitelikler = []; // Mevcut nitelikleri saklamak için
    
    // Mevcut nitelikleri yükle
    function loadMevcutNitelikler() {
        $.ajax({
            type: "GET",
            url: "../netting/islem.php",
            data: {
                islem: "nitelik_listele"
            },
            dataType: "json",
            success: function(response) {
                if (response.durum === "success") {
                    mevcutNitelikler = response.nitelikler;
                    
                    // Dropdown'ı doldur
                    const select = $("#nitelik_secim");
                    select.find('option').not(':first').remove();
                    
                    mevcutNitelikler.forEach(nitelik => {
                        select.append(`<option value="${nitelik.id}">${nitelik.ad}</option>`);
                    });
                }
            },
            error: function() {
                console.log("Nitelikler yüklenirken bir hata oluştu.");
            }
        });
    }
    
    // Sayfa yüklendiğinde mevcut nitelikleri yükle
    $(document).ready(function() {
        loadMevcutNitelikler();
    });
    
    // Nitelik seçimi değiştiğinde
    $("#nitelik_secim").on("change", function() {
        const seciliDeger = $(this).val();
        
        if (seciliDeger === "yeni") {
            // Yeni nitelik ekleme alanını göster
            $("#yeniNitelikAlani").show();
            $("#mevcutNitelikAlani").hide();
        } else {
            // Mevcut niteliği seçtiğini göster ve değerlerini yükle
            $("#yeniNitelikAlani").hide();
            $("#mevcutNitelikAlani").show();
            
            const seciliNitelik = mevcutNitelikler.find(n => n.id == seciliDeger);
            if (seciliNitelik) {
                $("#seciliNitelikAd").text(seciliNitelik.ad);
                
                // Mevcut değerleri göster
                let degerlerHtml = '';
                seciliNitelik.degerler.forEach(deger => {
                    degerlerHtml += `
                    <div class="form-check">
                        <input class="form-check-input mevcut-deger-checkbox" type="checkbox" 
                               value="${deger.deger_id}" id="deger_${deger.deger_id}" 
                               data-nitelik-id="${seciliNitelik.id}" data-deger="${deger.deger}">
                        <label class="form-check-label" for="deger_${deger.deger_id}">
                            ${deger.deger}
                        </label>
                    </div>`;
                });
                
                if (degerlerHtml) {
                    $("#mevcut_degerler_container").html(degerlerHtml);
                } else {
                    $("#mevcut_degerler_container").html('<div class="alert alert-info">Bu nitelik için henüz değer eklenmemiş.</div>');
                }
            }
        }
    });
    
    // Tüm değerleri seç/kaldır
    $("#tumDegerleriSec").on("click", function() {
        const checked = $(this).prop("checked");
        $(".mevcut-deger-checkbox").prop("checked", checked);
    });
    
    // Mevcut niteliğe yeni değer ekle
    $("#mevcutDegerEkleBtn").on("click", function() {
        const deger = $("#mevcut_yeni_deger").val().trim();
        const nitelikId = $("#nitelik_secim").val();
        
        if (!deger) {
            $("#errorAlert").text("Değer boş olamaz!");
            $("#errorAlert").show();
            setTimeout(() => $("#errorAlert").hide(), 3000);
            return;
        }
        
        $.ajax({
            type: "POST",
            url: "../netting/islem.php",
            data: {
                islem: "nitelik_deger_ekle",
                nitelik_id: nitelikId,
                deger: deger
            },
            dataType: "json",
            success: function(response) {
                if (response.durum === "success") {
                    // Yeni değeri listeye ekle
                    const yeniDeger = response.deger;
                    const yeniHTML = `
                    <div class="form-check">
                        <input class="form-check-input mevcut-deger-checkbox" type="checkbox" 
                               value="${yeniDeger.deger_id}" id="deger_${yeniDeger.deger_id}" 
                               data-nitelik-id="${yeniDeger.nitelik_id}" data-deger="${yeniDeger.deger}" checked>
                        <label class="form-check-label" for="deger_${yeniDeger.deger_id}">
                            ${yeniDeger.deger}
                        </label>
                    </div>`;
                    
                    // Eğer alert mesajı varsa, onu kaldır
                    if ($("#mevcut_degerler_container .alert").length) {
                        $("#mevcut_degerler_container").empty();
                    }
                    
                    $("#mevcut_degerler_container").append(yeniHTML);
                    $("#mevcut_yeni_deger").val("");
                    
                    // mevcutNitelikler listesini güncelle
                    const nitelikIndex = mevcutNitelikler.findIndex(n => n.id == nitelikId);
                    if (nitelikIndex !== -1) {
                        mevcutNitelikler[nitelikIndex].degerler.push({
                            deger_id: yeniDeger.deger_id,
                            nitelik_id: yeniDeger.nitelik_id,
                            deger: yeniDeger.deger
                        });
                    }
                } else {
                    $("#errorAlert").text(response.mesaj);
                    $("#errorAlert").show();
                    setTimeout(() => $("#errorAlert").hide(), 3000);
                }
            },
            error: function() {
                $("#errorAlert").text("İşlem sırasında bir hata oluştu.");
                $("#errorAlert").show();
                setTimeout(() => $("#errorAlert").hide(), 3000);
            }
        });
    });
    
    // Enter tuşu ile değer ekleme (mevcut niteliğe)
    $("#mevcut_yeni_deger").on("keypress", function(e) {
        if (e.which === 13) {
            e.preventDefault();
            $("#mevcutDegerEkleBtn").click();
        }
    });
    
    // Yeni Nitelik Ekle butonu tıklandığında
    $("#yeniNitelikBtn, #duzenleYeniNitelikBtn").on("click", function() {
        // Formu temizle
        $("#nitelik_ad").val("");
        $("#nitelik_secim").val("yeni").change();
        geciciDegerler = [];
        renderGeciciDegerler();
        
        // Hangi moddan geldiğini belirle (ekleme/düzenleme)
        activeModalMode = $(this).attr("id") === "duzenleYeniNitelikBtn" ? "duzenle" : "ekle";
        
        // Modalı göster
        $("#nitelikModal").modal("show");
    });
    
    // Değer ekle butonu tıklandığında
    $("#degerEkleBtn").on("click", function() {
        const deger = $("#yeni_deger").val().trim();
        if (deger) {
            geciciDegerler.push(deger);
            $("#yeni_deger").val("");
            renderGeciciDegerler();
        }
    });
    
    // Enter tuşu ile değer ekleme
    $("#yeni_deger").on("keypress", function(e) {
        if (e.which === 13) {
            e.preventDefault();
            $("#degerEkleBtn").click();
        }
    });
    
    // Geçici değerleri render et
    function renderGeciciDegerler() {
        if (geciciDegerler.length === 0) {
            $("#degerler_container").html('<div class="alert alert-info">Henüz değer eklenmedi</div>');
            return;
        }
        
        let html = '';
        geciciDegerler.forEach((deger, index) => {
            html += `
            <div class="badge bg-primary p-2 me-2 mb-2 d-inline-flex align-items-center">
                ${deger}
                <button type="button" class="btn-close btn-close-white ms-2 deger-sil" data-index="${index}" style="font-size: 0.6rem;"></button>
            </div>`;
        });
        
        $("#degerler_container").html(html);
    }
    
    // Değer silme işlemi
    $(document).on("click", ".deger-sil", function() {
        const index = $(this).data("index");
        geciciDegerler.splice(index, 1);
        renderGeciciDegerler();
    });
    
    // Nitelik kaydet butonu tıklandığında
    $("#nitelikKaydetBtn").on("click", function() {
        const nitelikSecim = $("#nitelik_secim").val();
        
        if (nitelikSecim === "yeni") {
            // Yeni nitelik ekleme işlemi
            const nitelik_ad = $("#nitelik_ad").val().trim();
            
            if (!nitelik_ad) {
                $("#errorAlert").text("Nitelik adı boş olamaz!");
                $("#errorAlert").show();
                setTimeout(() => $("#errorAlert").hide(), 3000);
                return;
            }
            
            if (geciciDegerler.length === 0) {
                $("#errorAlert").text("En az bir değer eklemelisiniz!");
                $("#errorAlert").show();
                setTimeout(() => $("#errorAlert").hide(), 3000);
                return;
            }
            
            // Yeni nitelik nesnesi oluştur
            const yeniNitelik = {
                id: Date.now(), // Geçici ID, backend tarafında değişecek
                ad: nitelik_ad,
                degerler: geciciDegerler.map(deger => ({
                    id: Date.now() + Math.floor(Math.random() * 1000), // Geçici ID
                    deger: deger
                }))
            };
            
            // Hangi listeye ekleyeceğimizi belirle
            if (activeModalMode === "duzenle") {
                duzenle_nitelikler.push(yeniNitelik);
                renderNitelikler(duzenle_nitelikler, "duzenle_nitelikler_container");
                
                // Düzenleme modunda varyasyonlar container'ını göster
                $("#duzenle_varyasyonlar_container").show();
            } else {
                nitelikler.push(yeniNitelik);
                renderNitelikler(nitelikler, "nitelikler_container");
                
                // Nitelik eklendiğinde varyasyonlar container'ını göster
                if (nitelikler.length > 0) {
                    $("#varyasyonlar_container").show();
                }
            }
        } else {
            // Mevcut nitelik seçildiyse
            const seciliNitelikId = nitelikSecim;
            const seciliNitelik = mevcutNitelikler.find(n => n.id == seciliNitelikId);
            
            if (!seciliNitelik) {
                $("#errorAlert").text("Geçerli bir nitelik seçin!");
                $("#errorAlert").show();
                setTimeout(() => $("#errorAlert").hide(), 3000);
                return;
            }
            
            // Seçilen değerleri al
            const seciliDegerler = [];
            $(".mevcut-deger-checkbox:checked").each(function() {
                const deger_id = $(this).val();
                const deger = $(this).data("deger");
                
                seciliDegerler.push({
                    id: deger_id,
                    deger: deger
                });
            });
            
            if (seciliDegerler.length === 0) {
                $("#errorAlert").text("En az bir değer seçmelisiniz!");
                $("#errorAlert").show();
                setTimeout(() => $("#errorAlert").hide(), 3000);
                return;
            }
            
            // Nitelik nesnesini oluştur
            const nitelikNesnesi = {
                id: seciliNitelikId,
                ad: seciliNitelik.ad,
                degerler: seciliDegerler
            };
            
            // Daha önce aynı nitelik eklenmişse kontrol et
            if (activeModalMode === "duzenle") {
                // Düzenleme modunda
                const mevcutIndex = duzenle_nitelikler.findIndex(n => n.id == seciliNitelikId);
                
                if (mevcutIndex !== -1) {
                    // Güncelle
                    duzenle_nitelikler[mevcutIndex] = nitelikNesnesi;
                } else {
                    // Yeni ekle
                    duzenle_nitelikler.push(nitelikNesnesi);
                }
                
                renderNitelikler(duzenle_nitelikler, "duzenle_nitelikler_container");
                $("#duzenle_varyasyonlar_container").show();
            } else {
                // Ekleme modunda
                const mevcutIndex = nitelikler.findIndex(n => n.id == seciliNitelikId);
                
                if (mevcutIndex !== -1) {
                    // Güncelle
                    nitelikler[mevcutIndex] = nitelikNesnesi;
                } else {
                    // Yeni ekle
                    nitelikler.push(nitelikNesnesi);
                }
                
                renderNitelikler(nitelikler, "nitelikler_container");
                $("#varyasyonlar_container").show();
            }
        }
        
        // Modalı kapat
        $("#nitelikModal").modal("hide");
    });
    
    // Nitelikleri render et
    function renderNitelikler(nitelikListesi, containerId) {
        const container = $(`#${containerId}`);
        
        if (nitelikListesi.length === 0) {
            container.html(`
                <div class="alert alert-info">
                    Ürün için nitelik ekleyerek (renk, beden, vb.) varyasyonlar oluşturabilirsiniz.
                </div>
            `);
            return;
        }
        
        let html = '';
        
        nitelikListesi.forEach((nitelik) => {
            let degerlerHtml = '';
            nitelik.degerler.forEach(deger => {
                degerlerHtml += `
                <span class="badge bg-secondary me-1 mb-1">${deger.deger}</span>
                `;
            });
            
            html += `
            <div class="card mb-2 nitelik-card" data-id="${nitelik.id}">
                <div class="card-header d-flex justify-content-between align-items-center py-2">
                    <h6 class="mb-0">${nitelik.ad}</h6>
                    <button type="button" class="btn btn-sm btn-outline-danger nitelik-sil-btn" data-id="${nitelik.id}">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                <div class="card-body py-2">
                    <p class="card-text">Değerler: ${degerlerHtml}</p>
                </div>
            </div>
            `;
        });
        
        container.html(html);
        
        // Düzenleme modunda gösterimi güncelle
        if (containerId === "duzenle_nitelikler_container" && duzenle_nitelikler.length > 0) {
            $("#duzenle_varyasyonlar_container").show();
        }
    }
    
    // Nitelik silme işlemi
    $(document).on("click", ".nitelik-sil-btn", function() {
        const id = $(this).data("id");
        const isEditMode = $(this).closest("#duzenle_nitelikler_container").length > 0;
        
        if (isEditMode) {
            duzenle_nitelikler = duzenle_nitelikler.filter(nitelik => nitelik.id != id);
            renderNitelikler(duzenle_nitelikler, "duzenle_nitelikler_container");
            
            if (duzenle_nitelikler.length === 0) {
                $("#duzenle_varyasyonlar_container table tbody").html('');
            }
        } else {
            nitelikler = nitelikler.filter(nitelik => nitelik.id != id);
            renderNitelikler(nitelikler, "nitelikler_container");
            
            if (nitelikler.length === 0) {
                $("#varyasyonlar_container").hide();
                $("#varyasyonlarTable tbody").html('');
            }
        }
    });
    
    // Varyasyonları oluştur butonu tıklandığında
    $("#varyasyonOlusturBtn").on("click", function() {
        generateVariations(nitelikler, "varyasyonlarTable");
    });
    
    $("#duzenleVaryasyonOlusturBtn").on("click", function() {
        generateVariations(duzenle_nitelikler, "duzenleVaryasyonlarTable");
    });
    
    // Varyasyonları oluştur
    function generateVariations(nitelikListesi, tableId) {
        if (nitelikListesi.length === 0) {
            return;
        }
        
        // Tüm niteliklerin değerlerini al
        const allValues = nitelikListesi.map(nitelik => {
            return {
                nitelik_id: nitelik.id,
                nitelik_ad: nitelik.ad,
                degerler: nitelik.degerler
            };
        });
        
        // Varyasyonları oluştur (Kartezyen çarpım)
        const variations = cartesianProduct(allValues);
        
        // HTML oluştur
        let html = '';
        
        variations.forEach((variation, index) => {
            let combinationText = '';
            let combinationDetail = '';
            
            variation.forEach(item => {
                combinationText += `${item.nitelik_ad}: ${item.deger.deger}, `;
                combinationDetail += `<input type="hidden" name="varyasyon[${index}][nitelik_degerleri][]" value="${item.nitelik_id}:${item.deger.id}">`;
            });
            
            combinationText = combinationText.slice(0, -2); // Son virgülü kaldır
            
            // Fiyat ve stok için varsayılan değerleri temel üründen al
            const basicPrice = parseFloat($("#" + (tableId === "duzenleVaryasyonlarTable" ? "duzenle_urun_fiyat" : "urun_fiyat")).val()) || 0;
            const basicStock = parseInt($("#" + (tableId === "duzenleVaryasyonlarTable" ? "duzenle_urun_stok" : "urun_stok")).val()) || 0;
            
            // Silme butonu her iki tabloda da gösterilecek
            html += `
            <tr>
                <td>
                    ${combinationText}
                    <input type="hidden" name="varyasyon[${index}][kombinasyon]" value="${combinationText}">
                    ${combinationDetail}
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm" name="varyasyon[${index}][fiyat]" value="${basicPrice}" min="0" step="0.01">
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm" name="varyasyon[${index}][stok]" value="${basicStock}" min="0" step="1">
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger varyasyon-sil-btn" ${tableId === "duzenleVaryasyonlarTable" ? `data-id="${variation.secenek_id || ''}"` : ''}>
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
            `;
        });
        
        $(`#${tableId} tbody`).html(html);
    }
    
    // Varyasyon silme 
    $(document).on("click", ".varyasyon-sil-btn", function() {
        const row = $(this).closest("tr");
        const table = row.closest("table");
        
        // Varyasyonu sil
        row.remove();
        
        // Tüm varyasyonları yeniden indeksle
        reindexVaryasyons(table);
    });

    // Varyasyonları yeniden indeksleme fonksiyonu
    function reindexVaryasyons(table) {
        const tableId = table.attr('id');
        
        // Tablodaki tüm satırları dolaş ve index'leri güncelle
        $(table).find("tbody tr").each(function(newIndex) {
            // Kombinasyon input
            $(this).find("input[name^='varyasyon['][name$='][kombinasyon]']").attr('name', `varyasyon[${newIndex}][kombinasyon]`);
            
            // Seçenek ID input (eğer varsa)
            $(this).find("input[name^='varyasyon['][name$='][secenek_id]']").attr('name', `varyasyon[${newIndex}][secenek_id]`);
            
            // Fiyat input
            $(this).find("input[name^='varyasyon['][name$='][fiyat]']").attr('name', `varyasyon[${newIndex}][fiyat]`);
            
            // Stok input
            $(this).find("input[name^='varyasyon['][name$='][stok]']").attr('name', `varyasyon[${newIndex}][stok]`);
            
            // Nitelik değerleri input'ları
            $(this).find("input[name^='varyasyon['][name$='][nitelik_degerleri][]']").each(function() {
                $(this).attr('name', `varyasyon[${newIndex}][nitelik_degerleri][]`);
            });
        });
    }
    
    // Kartezyen çarpım hesaplama (tüm olası kombinasyonları oluşturur)
    function cartesianProduct(allValues) {
        if (allValues.length === 0) {
            return [[]];
        }
        
        function helper(arr, i) {
            if (i === arr.length) {
                return [[]];
            }
            
            const result = [];
            const tailCombinations = helper(arr, i + 1);
            
            arr[i].degerler.forEach(deger => {
                tailCombinations.forEach(tail => {
                    result.push([{
                        nitelik_id: arr[i].nitelik_id,
                        nitelik_ad: arr[i].nitelik_ad,
                        deger: deger
                    }, ...tail]);
                });
            });
            
            return result;
        }
        
        return helper(allValues, 0);
    }

    // Modals closing event handlers to clear data
    $('#urunEkleModal').on('hidden.bs.modal', function () {
        // Clear nitelikler and varyasyons for add product modal
        nitelikler = [];
        renderNitelikler(nitelikler, "nitelikler_container");
        $("#varyasyonlarTable tbody").html('');
        $("#varyasyonlar_container").hide();
        $("#urunEkleForm")[0].reset();
    });

    $('#urunDuzenleModal').on('hidden.bs.modal', function () {
        // Clear nitelikler and varyasyons for edit product modal
        duzenle_nitelikler = [];
        $("#duzenleVaryasyonlarTable tbody").html('');
        $("#duzenle_varyasyonlar_container").hide();
    });

    $('#nitelikModal').on('hidden.bs.modal', function () {
        // Clear temporary data when nitelik modal is closed
        geciciDegerler = [];
        $("#nitelik_ad").val("");
        $("#yeni_deger").val("");
        $("#mevcut_yeni_deger").val("");
        $("#nitelik_secim").val("yeni");
        $("#yeniNitelikAlani").show();
        $("#mevcutNitelikAlani").hide();
        renderGeciciDegerler();
        $(".mevcut-deger-checkbox").prop("checked", false);
        $("#tumDegerleriSec").prop("checked", false);
        activeModalMode = null; // Reset the active modal mode
    });
});
</script> 