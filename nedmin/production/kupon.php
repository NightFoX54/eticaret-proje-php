<?php include 'header.php'; ?>

<div class="page-header d-flex justify-content-between align-items-center">
    <h2>Kuponlar</h2>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#kuponEkleModal">
        <i class="fas fa-plus"></i> Yeni Kupon Ekle
    </button>
</div>

<!-- Uyarı Mesajları -->
<div class="alert alert-success" id="successAlert" style="display:none;" role="alert"></div>
<div class="alert alert-danger" id="errorAlert" style="display:none;" role="alert"></div>

<!-- Kupon Tablosu -->
<div class="card mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="kuponTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Kupon Kodu</th>
                        <th>Tür</th>
                        <th>İndirim</th>
                        <th>Min. Tutar</th>
                        <th>Kullanım Limiti</th>
                        <th>Başlangıç</th>
                        <th>Bitiş</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Tüm kuponları getir
                    $kupon_sor = $db->prepare("SELECT * FROM kupon ORDER BY id DESC");
                    $kupon_sor->execute();
                    
                    while ($kupon = $kupon_sor->fetch(PDO::FETCH_ASSOC)) {
                    ?>
                    <tr id="kupon-<?php echo $kupon['id']; ?>">
                        <td><?php echo $kupon['id']; ?></td>
                        <td><?php echo $kupon['kupon_kod']; ?></td>
                        <td>
                            <?php 
                            if ($kupon['kupon_tur'] == 'yuzde') {
                                echo '<span class="badge bg-info">Yüzde</span>';
                            } else {
                                echo '<span class="badge bg-warning">Sabit</span>';
                            }
                            ?>
                        </td>
                        <td>
                            <?php 
                            if ($kupon['kupon_tur'] == 'yuzde') {
                                echo '%' . $kupon['kupon_indirim'];
                            } else {
                                echo number_format($kupon['kupon_indirim'], 2) . ' ₺';
                            }
                            ?>
                        </td>
                        <td><?php echo number_format($kupon['kupon_min_tutar'], 2); ?> ₺</td>
                        <td><?php echo $kupon['kupon_limit'] ?: 'Limitsiz'; ?></td>
                        <td><?php echo date('d.m.Y', strtotime($kupon['kupon_baslangic_tarih'])); ?></td>
                        <td><?php echo date('d.m.Y', strtotime($kupon['kupon_bitis_tarih'])); ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning edit-btn" 
                                    data-id="<?php echo $kupon['id']; ?>">
                                <i class="fas fa-edit"></i> Düzenle
                            </button>
                            <button class="btn btn-sm btn-danger delete-btn" 
                                    data-id="<?php echo $kupon['id']; ?>" 
                                    data-kod="<?php echo $kupon['kupon_kod']; ?>">
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

<!-- Kupon Ekle Modal -->
<div class="modal fade" id="kuponEkleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Yeni Kupon Ekle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="kuponEkleForm">
                    <div class="mb-3">
                        <label for="kupon_kod" class="form-label">Kupon Kodu</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="kupon_kod" name="kupon_kod" required>
                            <button class="btn btn-outline-secondary" type="button" id="generateCodeBtn">Kod Üret</button>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="kupon_tur" class="form-label">İndirim Türü</label>
                            <select class="form-control" id="kupon_tur" name="kupon_tur">
                                <option value="yuzde">Yüzde İndirim (%)</option>
                                <option value="sabit">Sabit İndirim (₺)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="kupon_indirim" class="form-label">İndirim Miktarı</label>
                            <input type="number" class="form-control" id="kupon_indirim" name="kupon_indirim" min="0" step="0.01" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="kupon_min_tutar" class="form-label">Minimum Sepet Tutarı (₺)</label>
                            <input type="number" class="form-control" id="kupon_min_tutar" name="kupon_min_tutar" min="0" step="0.01" required>
                        </div>
                        <div class="col-md-6">
                            <label for="kupon_limit" class="form-label">Kullanım Limiti</label>
                            <input type="number" class="form-control" id="kupon_limit" name="kupon_limit" min="0" step="1" placeholder="Boş = Limitsiz">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="kupon_baslangic_tarih" class="form-label">Başlangıç Tarihi</label>
                            <input type="date" class="form-control" id="kupon_baslangic_tarih" name="kupon_baslangic_tarih" required>
                        </div>
                        <div class="col-md-6">
                            <label for="kupon_bitis_tarih" class="form-label">Bitiş Tarihi</label>
                            <input type="date" class="form-control" id="kupon_bitis_tarih" name="kupon_bitis_tarih" required>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                <button type="button" class="btn btn-primary" id="kuponEkleBtn">Kaydet</button>
            </div>
        </div>
    </div>
</div>

<!-- Kupon Düzenle Modal -->
<div class="modal fade" id="kuponDuzenleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kupon Düzenle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="kuponDuzenleForm">
                    <input type="hidden" id="duzenle_kupon_id" name="kupon_id">
                    <div class="mb-3">
                        <label for="duzenle_kupon_kod" class="form-label">Kupon Kodu</label>
                        <input type="text" class="form-control" id="duzenle_kupon_kod" name="kupon_kod" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="duzenle_kupon_tur" class="form-label">İndirim Türü</label>
                            <select class="form-control" id="duzenle_kupon_tur" name="kupon_tur">
                                <option value="yuzde">Yüzde İndirim (%)</option>
                                <option value="sabit">Sabit İndirim (₺)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="duzenle_kupon_indirim" class="form-label">İndirim Miktarı</label>
                            <input type="number" class="form-control" id="duzenle_kupon_indirim" name="kupon_indirim" min="0" step="0.01" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="duzenle_kupon_min_tutar" class="form-label">Minimum Sepet Tutarı (₺)</label>
                            <input type="number" class="form-control" id="duzenle_kupon_min_tutar" name="kupon_min_tutar" min="0" step="0.01" required>
                        </div>
                        <div class="col-md-6">
                            <label for="duzenle_kupon_limit" class="form-label">Kullanım Limiti</label>
                            <input type="number" class="form-control" id="duzenle_kupon_limit" name="kupon_limit" min="0" step="1" placeholder="Boş = Limitsiz">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="duzenle_kupon_baslangic_tarih" class="form-label">Başlangıç Tarihi</label>
                            <input type="date" class="form-control" id="duzenle_kupon_baslangic_tarih" name="kupon_baslangic_tarih" required>
                        </div>
                        <div class="col-md-6">
                            <label for="duzenle_kupon_bitis_tarih" class="form-label">Bitiş Tarihi</label>
                            <input type="date" class="form-control" id="duzenle_kupon_bitis_tarih" name="kupon_bitis_tarih" required>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                <button type="button" class="btn btn-warning" id="kuponGuncelleBtn">Güncelle</button>
            </div>
        </div>
    </div>
</div>

<!-- Kupon Sil Modal -->
<div class="modal fade" id="kuponSilModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kupon Sil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Bu kuponu silmek istediğinizden emin misiniz?</p>
                <p id="silinecekKuponKod" class="fw-bold"></p>
                <input type="hidden" id="silinecekKuponId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                <button type="button" class="btn btn-danger" id="kuponSilBtn">Sil</button>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
$(document).ready(function() {
    // Bugünün tarihini başlangıç tarihi olarak ayarla
    const today = new Date().toISOString().split('T')[0];
    $("#kupon_baslangic_tarih").val(today);
    
    // Bitiş tarihi için varsayılan olarak 1 ay sonrasını ayarla
    const nextMonth = new Date();
    nextMonth.setMonth(nextMonth.getMonth() + 1);
    $("#kupon_bitis_tarih").val(nextMonth.toISOString().split('T')[0]);
    
    // Rastgele kupon kodu üretme
    $("#generateCodeBtn").on("click", function() {
        const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        let result = '';
        const length = 8;
        
        for (let i = 0; i < length; i++) {
            result += characters.charAt(Math.floor(Math.random() * characters.length));
        }
        
        $("#kupon_kod").val(result);
    });
    
    // Kupon Ekleme
    $("#kuponEkleBtn").on("click", function() {
        var formData = {
            islem: "kupon_ekle",
            kupon_kod: $("#kupon_kod").val(),
            kupon_tur: $("#kupon_tur").val(),
            kupon_indirim: $("#kupon_indirim").val(),
            kupon_min_tutar: $("#kupon_min_tutar").val(),
            kupon_limit: $("#kupon_limit").val(),
            kupon_baslangic_tarih: $("#kupon_baslangic_tarih").val(),
            kupon_bitis_tarih: $("#kupon_bitis_tarih").val()
        };
        
        // Form validasyonu
        if (!formData.kupon_kod) {
            $("#errorAlert").text("Kupon kodu boş olamaz!");
            $("#errorAlert").show();
            setTimeout(function() { $("#errorAlert").hide(); }, 3000);
            return;
        }
        
        if (!formData.kupon_indirim || formData.kupon_indirim <= 0) {
            $("#errorAlert").text("İndirim miktarı sıfırdan büyük olmalıdır!");
            $("#errorAlert").show();
            setTimeout(function() { $("#errorAlert").hide(); }, 3000);
            return;
        }
        
        if (!formData.kupon_min_tutar) {
            $("#errorAlert").text("Minimum sepet tutarı gereklidir!");
            $("#errorAlert").show();
            setTimeout(function() { $("#errorAlert").hide(); }, 3000);
            return;
        }
        
        if (!formData.kupon_baslangic_tarih || !formData.kupon_bitis_tarih) {
            $("#errorAlert").text("Başlangıç ve bitiş tarihleri gereklidir!");
            $("#errorAlert").show();
            setTimeout(function() { $("#errorAlert").hide(); }, 3000);
            return;
        }
        
        if (new Date(formData.kupon_baslangic_tarih) > new Date(formData.kupon_bitis_tarih)) {
            $("#errorAlert").text("Başlangıç tarihi, bitiş tarihinden sonra olamaz!");
            $("#errorAlert").show();
            setTimeout(function() { $("#errorAlert").hide(); }, 3000);
            return;
        }
        
        $.ajax({
            type: "POST",
            url: "../netting/islem.php",
            data: formData,
            dataType: "json",
            success: function(response) {
                if (response.durum === "success") {
                    $("#successAlert").text(response.mesaj);
                    $("#successAlert").show();
                    
                    // Modalı kapat
                    $("#kuponEkleModal").modal("hide");
                    
                    // Kupon listesini güncelle
                    setTimeout(function() {
                        window.location.reload();
                    }, 1500);
                } else {
                    $("#errorAlert").text(response.mesaj);
                    $("#errorAlert").show();
                }
                
                // 3 saniye sonra uyarıyı gizle
                setTimeout(function() {
                    $("#successAlert, #errorAlert").hide();
                }, 3000);
            },
            error: function(xhr, status, error) {
                console.log("AJAX hatası:", status, error);
                console.log("Yanıt:", xhr.responseText);
                $("#errorAlert").text("İşlem sırasında bir hata oluştu.");
                $("#errorAlert").show();
            }
        });
    });
    
    // Düzenle butonuna tıklanınca
    $(document).on("click", ".edit-btn", function() {
        var kupon_id = $(this).data("id");
        
        // Kupon bilgilerini getir
        $.ajax({
            type: "GET",
            url: "../netting/islem.php",
            data: {
                islem: "kupon_getir",
                kupon_id: kupon_id
            },
            dataType: "json",
            success: function(response) {
                if (response.durum === "success") {
                    const kupon = response.kupon;
                    
                    $("#duzenle_kupon_id").val(kupon.id);
                    $("#duzenle_kupon_kod").val(kupon.kupon_kod);
                    $("#duzenle_kupon_tur").val(kupon.kupon_tur);
                    $("#duzenle_kupon_indirim").val(kupon.kupon_indirim);
                    $("#duzenle_kupon_min_tutar").val(kupon.kupon_min_tutar);
                    $("#duzenle_kupon_limit").val(kupon.kupon_limit);
                    $("#duzenle_kupon_baslangic_tarih").val(kupon.kupon_baslangic_tarih);
                    $("#duzenle_kupon_bitis_tarih").val(kupon.kupon_bitis_tarih);
                    
                    // Modal göster
                    $("#kuponDuzenleModal").modal("show");
                } else {
                    $("#errorAlert").text(response.mesaj);
                    $("#errorAlert").show();
                    setTimeout(function() { $("#errorAlert").hide(); }, 3000);
                }
            },
            error: function(xhr, status, error) {
                console.log("AJAX hatası:", status, error);
                $("#errorAlert").text("Kupon bilgileri alınırken bir hata oluştu.");
                $("#errorAlert").show();
            }
        });
    });
    
    // Kupon Güncelleme
    $("#kuponGuncelleBtn").on("click", function() {
        var formData = {
            islem: "kupon_guncelle",
            kupon_id: $("#duzenle_kupon_id").val(),
            kupon_kod: $("#duzenle_kupon_kod").val(),
            kupon_tur: $("#duzenle_kupon_tur").val(),
            kupon_indirim: $("#duzenle_kupon_indirim").val(),
            kupon_min_tutar: $("#duzenle_kupon_min_tutar").val(),
            kupon_limit: $("#duzenle_kupon_limit").val(),
            kupon_baslangic_tarih: $("#duzenle_kupon_baslangic_tarih").val(),
            kupon_bitis_tarih: $("#duzenle_kupon_bitis_tarih").val()
        };
        
        // Form validasyonu
        if (!formData.kupon_kod) {
            $("#errorAlert").text("Kupon kodu boş olamaz!");
            $("#errorAlert").show();
            setTimeout(function() { $("#errorAlert").hide(); }, 3000);
            return;
        }
        
        if (!formData.kupon_indirim || formData.kupon_indirim <= 0) {
            $("#errorAlert").text("İndirim miktarı sıfırdan büyük olmalıdır!");
            $("#errorAlert").show();
            setTimeout(function() { $("#errorAlert").hide(); }, 3000);
            return;
        }
        
        if (new Date(formData.kupon_baslangic_tarih) > new Date(formData.kupon_bitis_tarih)) {
            $("#errorAlert").text("Başlangıç tarihi, bitiş tarihinden sonra olamaz!");
            $("#errorAlert").show();
            setTimeout(function() { $("#errorAlert").hide(); }, 3000);
            return;
        }
        
        $.ajax({
            type: "POST",
            url: "../netting/islem.php",
            data: formData,
            dataType: "json",
            success: function(response) {
                if (response.durum === "success") {
                    $("#successAlert").text(response.mesaj);
                    $("#successAlert").show();
                    
                    // Modalı kapat
                    $("#kuponDuzenleModal").modal("hide");
                    
                    // Kupon listesini güncelle
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
    
    // Sil butonuna tıklanınca
    $(document).on("click", ".delete-btn", function() {
        var kupon_id = $(this).data("id");
        var kupon_kod = $(this).data("kod");
        
        $("#silinecekKuponId").val(kupon_id);
        $("#silinecekKuponKod").text(kupon_kod);
        
        // Modal göster
        $("#kuponSilModal").modal("show");
    });
    
    // Kupon Silme
    $("#kuponSilBtn").on("click", function() {
        var kupon_id = $("#silinecekKuponId").val();
        
        $.ajax({
            type: "POST",
            url: "../netting/islem.php",
            data: {
                islem: "kupon_sil",
                kupon_id: kupon_id
            },
            dataType: "json",
            success: function(response) {
                if (response.durum === "success") {
                    $("#successAlert").text(response.mesaj);
                    $("#successAlert").show();
                    
                    // Silinen satırı tablodan kaldır
                    $("#kupon-" + kupon_id).remove();
                    
                    // Modalı kapat
                    $("#kuponSilModal").modal("hide");
                } else {
                    $("#errorAlert").text(response.mesaj);
                    $("#errorAlert").show();
                    
                    // Modalı kapat
                    $("#kuponSilModal").modal("hide");
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
});
</script> 