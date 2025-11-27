<?php include 'header.php'; ?>

<div class="page-header d-flex justify-content-between align-items-center">
    <h2>Ürün Kategorileri</h2>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#kategoriEkleModal">
        <i class="fas fa-plus"></i> Yeni Kategori Ekle
    </button>
</div>

<!-- Uyarı Mesajları -->
<div class="alert alert-success" id="successAlert" style="display:none;" role="alert"></div>
<div class="alert alert-danger" id="errorAlert" style="display:none;" role="alert"></div>

<!-- Kategori Tablosu -->
<div class="card mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="kategoriTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Kategori Adı</th>
                        <th>Üst Kategori</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Tüm kategorileri getir
                    $kategori_sor = $db->prepare("SELECT k1.id, k1.kategori_isim, k2.kategori_isim as parent_kategori 
                                                FROM urun_kategoriler k1 
                                                LEFT JOIN urun_kategoriler k2 ON k1.parent_kategori_id = k2.id 
                                                ORDER BY k1.id DESC");
                    $kategori_sor->execute();
                    
                    while ($kategori = $kategori_sor->fetch(PDO::FETCH_ASSOC)) {
                    ?>
                    <tr id="kategori-<?php echo $kategori['id']; ?>">
                        <td><?php echo $kategori['id']; ?></td>
                        <td><?php echo $kategori['kategori_isim']; ?></td>
                        <td><?php echo $kategori['parent_kategori'] ? $kategori['parent_kategori'] : 'Ana Kategori'; ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning edit-btn" 
                                    data-id="<?php echo $kategori['id']; ?>" 
                                    data-isim="<?php echo $kategori['kategori_isim']; ?>">
                                <i class="fas fa-edit"></i> Düzenle
                            </button>
                            <button class="btn btn-sm btn-danger delete-btn" 
                                    data-id="<?php echo $kategori['id']; ?>" 
                                    data-isim="<?php echo $kategori['kategori_isim']; ?>">
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

<!-- Kategori Ekle Modal -->
<div class="modal fade" id="kategoriEkleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Yeni Kategori Ekle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="kategoriEkleForm">
                    <div class="mb-3">
                        <label for="kategori_isim" class="form-label">Kategori Adı</label>
                        <input type="text" class="form-control" id="kategori_isim" name="kategori_isim" required>
                    </div>
                    <div class="mb-3">
                        <label for="parent_kategori_id" class="form-label">Üst Kategori</label>
                        <input type="hidden" id="parent_kategori_id" name="parent_kategori_id" value="">
                        <div id="kategori_tree_container" class="border rounded p-2" style="max-height: 300px; overflow-y: auto;">
                            <div class="form-check">
                                <input class="form-check-input kategori-option" type="radio" name="kategori_secim" id="kategori_root" value="" checked>
                                <label class="form-check-label" for="kategori_root">Ana Kategori</label>
                            </div>
                            <div id="kategori_tree" class="mt-2">
                                <!-- Kategoriler buraya yüklenecek -->
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                <button type="button" class="btn btn-primary" id="kategoriEkleBtn">Kaydet</button>
            </div>
        </div>
    </div>
</div>

<!-- Kategori Düzenle Modal -->
<div class="modal fade" id="kategoriDuzenleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kategori Düzenle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="kategoriDuzenleForm">
                    <input type="hidden" id="duzenle_kategori_id" name="kategori_id">
                    <div class="mb-3">
                        <label for="duzenle_kategori_isim" class="form-label">Kategori Adı</label>
                        <input type="text" class="form-control" id="duzenle_kategori_isim" name="kategori_isim" required>
                    </div>
                    <div class="mb-3">
                        <label for="duzenle_parent_kategori_id" class="form-label">Üst Kategori</label>
                        <input type="hidden" id="duzenle_parent_kategori_id" name="parent_kategori_id" value="">
                        <div id="duzenle_kategori_tree_container" class="border rounded p-2" style="max-height: 300px; overflow-y: auto;">
                            <div class="form-check">
                                <input class="form-check-input kategori-option" type="radio" name="duzenle_kategori_secim" id="duzenle_kategori_root" value="" checked>
                                <label class="form-check-label" for="duzenle_kategori_root">Ana Kategori</label>
                            </div>
                            <div id="duzenle_kategori_tree" class="mt-2">
                                <!-- Kategoriler buraya yüklenecek -->
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                <button type="button" class="btn btn-warning" id="kategoriGuncelleBtn">Güncelle</button>
            </div>
        </div>
    </div>
</div>

<!-- Kategori Sil Modal -->
<div class="modal fade" id="kategoriSilModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kategori Sil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Bu kategoriyi silmek istediğinizden emin misiniz?</p>
                <p id="silinecekKategoriIsim" class="fw-bold"></p>
                <input type="hidden" id="silinecekKategoriId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                <button type="button" class="btn btn-danger" id="kategoriSilBtn">Sil</button>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<style>
.category-item {
    margin-left: 20px;
}
.category-header {
    cursor: pointer;
    display: flex;
    align-items: center;
    margin: 5px 0;
}
.category-children {
    margin-left: 20px;
    display: none;
}
.category-toggle {
    margin-right: 5px;
    font-size: 12px;
    width: 16px;
    height: 16px;
    line-height: 16px;
    text-align: center;
}
.category-toggle.empty {
    visibility: hidden;
}
</style>

<script>
$(document).ready(function() {
    // Kategori ağacını yükleme fonksiyonu
    function loadKategoriTree(containerId, treeId, radioName, inputId, excludeId = null) {
        $.ajax({
            type: "GET",
            url: "../netting/islem.php",
            data: {
                islem: "kategori_getir",
                exclude_id: excludeId
            },
            dataType: "json",
            success: function(response) {
                if (response.durum === "success") {
                    const kategoriler = response.kategoriler;
                    const container = $(`#${treeId}`);
                    container.empty();
                    
                    // Kategori ağacını oluşturmak için yardımcı fonksiyon
                    function buildCategoryTree(parentId) {
                        const childCategories = kategoriler.filter(k => k.parent_kategori_id == parentId);
                        if (childCategories.length === 0) return '';
                        
                        let html = '';
                        childCategories.forEach(category => {
                            const hasChildren = kategoriler.some(k => k.parent_kategori_id == category.id);
                            const toggleClass = hasChildren ? '' : 'empty';
                            
                            html += `
                                <div class="category-item" data-id="${category.id}">
                                    <div class="category-header">
                                        <div class="category-toggle ${toggleClass}">
                                            ${hasChildren ? '+' : ''}
                                        </div>
                                        <div class="form-check mb-0">
                                            <input class="form-check-input kategori-radio" type="radio" 
                                                name="${radioName}" id="${radioName}_${category.id}" 
                                                value="${category.id}">
                                            <label class="form-check-label" for="${radioName}_${category.id}">
                                                ${category.kategori_isim}
                                            </label>
                                        </div>
                                    </div>
                                    <div class="category-children">
                                        ${buildCategoryTree(category.id)}
                                    </div>
                                </div>
                            `;
                        });
                        return html;
                    }
                    
                    // Ana kategorileri oluştur
                    container.html(buildCategoryTree(null));
                    
                    // Toggle olayını ekle
                    $(document).off('click', '.category-toggle').on('click', '.category-toggle', function(e) {
                        e.stopPropagation();
                        if ($(this).hasClass('empty')) return;
                        
                        const childrenContainer = $(this).closest('.category-header').next('.category-children');
                        if (childrenContainer.is(':visible')) {
                            $(this).text('+');
                            childrenContainer.slideUp();
                        } else {
                            $(this).text('-');
                            childrenContainer.slideDown();
                        }
                    });
                    
                    // Radio button olayını ekle
                    $(document).off('change', `input[name="${radioName}"]`).on('change', `input[name="${radioName}"]`, function() {
                        console.log(`Radio değişti: ${$(this).val()}`);
                        $(`#${inputId}`).val($(this).val());
                        console.log(`Hidden input değeri: ${$(`#${inputId}`).val()}`);
                    });
                } else {
                    console.error("Kategoriler yüklenemedi:", response.mesaj);
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX hatası:", status, error);
            }
        });
    }
    
    // Sayfa yüklendiğinde kategorileri yükle
    loadKategoriTree('kategori_tree_container', 'kategori_tree', 'kategori_secim', 'parent_kategori_id');
    
    // Kategori ekle modalı açıldığında kategorileri tazele
    $('#kategoriEkleModal').on('show.bs.modal', function() {
        $('#kategori_isim').val('');
        $('#parent_kategori_id').val('');
        $('#kategori_root').prop('checked', true);
        loadKategoriTree('kategori_tree_container', 'kategori_tree', 'kategori_secim', 'parent_kategori_id');
    });
    
    // Düzenle butonuna tıklanınca
    $(document).on("click", ".edit-btn", function() {
        var kategori_id = $(this).data("id");
        var kategori_isim = $(this).data("isim");
        
        $("#duzenle_kategori_id").val(kategori_id);
        $("#duzenle_kategori_isim").val(kategori_isim);
        $("#duzenle_parent_kategori_id").val(''); // Reset parent value
        
        // Kategoriyi kendi kendine parent yapmasını engellemek için mevcut kategori ID'yi hariç tut
        loadKategoriTree('duzenle_kategori_tree_container', 'duzenle_kategori_tree', 'duzenle_kategori_secim', 'duzenle_parent_kategori_id', kategori_id);
        
        // Mevcut parent'ı seç
        $.ajax({
            type: "GET",
            url: "../netting/islem.php",
            data: {
                islem: "kategori_detay",
                kategori_id: kategori_id
            },
            dataType: "json",
            success: function(response) {
                if (response.durum === "success") {
                    const kategori = response.kategori;
                    if (kategori.parent_kategori_id) {
                        $("#duzenle_parent_kategori_id").val(kategori.parent_kategori_id);
                        // Radio butonunu işaretle (setTimeout ile ağaç yüklendikten sonra)
                        setTimeout(function() {
                            $(`#duzenle_kategori_secim_${kategori.parent_kategori_id}`).prop('checked', true);
                            console.log(`Parent kategori seçildi: ${kategori.parent_kategori_id}`);
                            console.log(`Hidden input değeri: ${$("#duzenle_parent_kategori_id").val()}`);
                        }, 500);
                    } else {
                        $("#duzenle_kategori_root").prop('checked', true);
                        $("#duzenle_parent_kategori_id").val('');
                        console.log('Ana kategori seçildi');
                    }
                }
            },
            error: function(xhr, status, error) {
                console.log("AJAX hatası:", status, error);
            }
        });
        
        // Modal göster
        $("#kategoriDuzenleModal").modal("show");
    });
    
    // Kategori Ekleme
    $("#kategoriEkleBtn").on("click", function() {
        var kategori_isim = $("#kategori_isim").val();
        var parent_kategori_id = $("#parent_kategori_id").val();
        
        console.log("Kategori ekleme butonuna tıklandı");
        console.log("Kategori adı:", kategori_isim);
        console.log("Üst kategori ID:", parent_kategori_id);
        
        if (!kategori_isim.trim()) {
            $("#errorAlert").text("Kategori adı boş olamaz!");
            $("#errorAlert").show();
            setTimeout(function() {
                $("#errorAlert").hide();
            }, 3000);
            return;
        }
        
        $.ajax({
            type: "POST",
            url: "../netting/islem.php",
            data: {
                islem: "kategori_ekle",
                kategori_isim: kategori_isim,
                parent_kategori_id: parent_kategori_id
            },
            dataType: "json",
            success: function(response) {
                console.log("AJAX başarılı:", response);
                if (response.durum === "success") {
                    $("#successAlert").text(response.mesaj);
                    $("#successAlert").show();
                    
                    // Modalı kapat
                    $("#kategoriEkleModal").modal("hide");
                    
                    // Kategori listesini güncelle
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
    
    // Kategori Güncelleme
    $("#kategoriGuncelleBtn").on("click", function() {
        var kategori_id = $("#duzenle_kategori_id").val();
        var kategori_isim = $("#duzenle_kategori_isim").val();
        var parent_kategori_id = $("#duzenle_parent_kategori_id").val();
        
        console.log("Güncelleme butonuna tıklandı");
        console.log("Kategori ID:", kategori_id);
        console.log("Kategori adı:", kategori_isim);
        console.log("Üst kategori ID:", parent_kategori_id);
        
        if (!kategori_isim.trim()) {
            $("#errorAlert").text("Kategori adı boş olamaz!");
            $("#errorAlert").show();
            setTimeout(function() {
                $("#errorAlert").hide();
            }, 3000);
            return;
        }
        
        if (kategori_id == parent_kategori_id) {
            $("#errorAlert").text("Bir kategori kendisini üst kategori olarak alamaz!");
            $("#errorAlert").show();
            setTimeout(function() {
                $("#errorAlert").hide();
            }, 3000);
            return;
        }
        
        $.ajax({
            type: "POST",
            url: "../netting/islem.php",
            data: {
                islem: "kategori_guncelle",
                kategori_id: kategori_id,
                kategori_isim: kategori_isim,
                parent_kategori_id: parent_kategori_id
            },
            dataType: "json",
            success: function(response) {
                if (response.durum === "success") {
                    $("#successAlert").text(response.mesaj);
                    $("#successAlert").show();
                    
                    // Modalı kapat
                    $("#kategoriDuzenleModal").modal("hide");
                    
                    // Kategori listesini güncelle
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
    
    // Sil butonuna tıklanınca
    $(document).on("click", ".delete-btn", function() {
        var kategori_id = $(this).data("id");
        var kategori_isim = $(this).data("isim");
        
        $("#silinecekKategoriId").val(kategori_id);
        $("#silinecekKategoriIsim").text(kategori_isim);
        
        // Modal göster
        $("#kategoriSilModal").modal("show");
    });
    
    // Kategori Silme
    $("#kategoriSilBtn").on("click", function() {
        var kategori_id = $("#silinecekKategoriId").val();
        
        $.ajax({
            type: "POST",
            url: "../netting/islem.php",
            data: {
                islem: "kategori_sil",
                kategori_id: kategori_id
            },
            dataType: "json",
            success: function(response) {
                if (response.durum === "success") {
                    $("#successAlert").text(response.mesaj);
                    $("#successAlert").show();
                    
                    // Silinen satırı tablodan kaldır
                    $("#kategori-" + kategori_id).remove();
                    
                    // Modalı kapat
                    $("#kategoriSilModal").modal("hide");
                } else {
                    $("#errorAlert").text(response.mesaj);
                    $("#errorAlert").show();
                    
                    // Modalı kapat
                    $("#kategoriSilModal").modal("hide");
                }
                
                // 3 saniye sonra uyarıyı gizle
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