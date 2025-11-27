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
                    <a href="orders.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-shopping-bag me-2"></i> Siparişlerim
                    </a>
                    <a href="address.php" class="list-group-item list-group-item-action active">
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
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Adreslerim</h5>
                    <button type="button" class="btn btn-sm btn-primary" id="addAddressBtn" style="background: linear-gradient(135deg, #9D7FC7 0%, #8B6FC7 100%); border: none;">
                        <i class="fas fa-plus"></i> Yeni Adres Ekle
                    </button>
                </div>
                <div class="card-body">
                    <div id="addressList" class="row">
                        <!-- Addresses will be loaded dynamically -->
                        <div class="col-12 text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Yükleniyor...</span>
                            </div>
                            <p>Adresleriniz yükleniyor...</p>
                        </div>
                    </div>
                    
                    <!-- No Addresses Message -->
                    <div id="noAddressesMessage" class="text-center p-4" style="display:none;">
                        <i class="fas fa-map-marker-alt fa-3x text-muted mb-3"></i>
                        <h5>Henüz adres kaydınız bulunmuyor.</h5>
                        <p class="text-muted">Adres ekleyerek alışverişlerinizde hızlıca tercih edebilirsiniz.</p>
                        <button type="button" class="btn btn-primary mt-2" id="noAddressAddBtn" style="background: linear-gradient(135deg, #9D7FC7 0%, #8B6FC7 100%); border: none;">
                            <i class="fas fa-plus"></i> Yeni Adres Ekle
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Address Form Modal -->
<div class="modal fade" id="addressModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addressModalTitle">Yeni Adres Ekle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addressForm">
                    <input type="hidden" id="adres_id" name="adres_id">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="adres_adi" class="form-label">Adres Adı <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="adres_adi" name="adres_adi" placeholder="Ev, İş, vb." required>
                        </div>
                        <div class="col-md-6">
                            <label for="ad_soyad" class="form-label">Ad Soyad <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="ad_soyad" name="ad_soyad" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="telefon" class="form-label">Telefon <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="telefon" name="telefon" placeholder="05XX XXX XX XX" required>
                        </div>
                        <div class="col-md-6">
                            <label for="tc_no" class="form-label">T.C. Kimlik No <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="tc_no" name="tc_no" required>
                            <small class="text-muted">Teslimat için gereklidir</small>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="il" class="form-label">İl <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="il" name="il" required>
                        </div>
                        <div class="col-md-6">
                            <label for="ilce" class="form-label">İlçe <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="ilce" name="ilce" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="adres" class="form-label">Adres <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="adres" name="adres" rows="3" required></textarea>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="posta_kodu" class="form-label">Posta Kodu</label>
                            <input type="text" class="form-control" id="posta_kodu" name="posta_kodu">
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="varsayilan" name="varsayilan">
                                <label class="form-check-label" for="varsayilan">
                                    Varsayılan adres olarak ayarla
                                </label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                <button type="button" class="btn btn-primary" id="saveAddressBtn" style="background: linear-gradient(135deg, #9D7FC7 0%, #8B6FC7 100%); border: none;">Kaydet</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Address Confirmation Modal -->
<div class="modal fade" id="deleteAddressModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Adres Sil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Bu adresi silmek istediğinizden emin misiniz?</p>
                <p id="deleteAddressName" class="fw-bold"></p>
                <input type="hidden" id="deleteAddressId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Sil</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Check authentication
    checkAuth();
    
    // Load user addresses
    loadAddresses();
    
    // Add/Edit Address button click events
    $("#addAddressBtn, #noAddressAddBtn").on("click", function() {
        openAddressModal('add');
    });
    
    // Save address button click
    $("#saveAddressBtn").on("click", function() {
        saveAddress();
    });
    
    // Confirm delete button click
    $("#confirmDeleteBtn").on("click", function() {
        deleteAddress();
    });
    
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
    
    // Load user addresses
    function loadAddresses() {
        $.ajax({
            type: "GET",
            url: "nedmin/netting/islem.php",
            data: {
                islem: "get_user_addresses"
            },
            dataType: "json",
            success: function(response) {
                if (response.durum === 'success') {
                    displayAddresses(response.adresler);
                } else {
                    showError(response.mesaj || 'Adresler yüklenirken bir hata oluştu.');
                }
            },
            error: function() {
                showError('Sunucu hatası: Adresler yüklenemedi.');
            }
        });
    }
    
    // Display addresses
    function displayAddresses(addresses) {
        const addressList = $("#addressList");
        addressList.empty();
        
        if (addresses.length === 0) {
            $("#noAddressesMessage").show();
            return;
        }
        
        $("#noAddressesMessage").hide();
        
        addresses.forEach(function(address) {
            const defaultBadge = address.varsayilan == 1 
                ? '<span class="badge bg-success me-2">Varsayılan</span>' 
                : '';
                
            const addressCard = `
                <div class="col-md-6 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title">
                                    ${defaultBadge}
                                    ${address.adres_adi}
                                </h5>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        İşlemler
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a class="dropdown-item edit-address" href="#" data-id="${address.id}">
                                                <i class="fas fa-edit me-2"></i> Düzenle
                                            </a>
                                        </li>
                                        ${address.varsayilan != 1 ? `
                                        <li>
                                            <a class="dropdown-item set-default-address" href="#" data-id="${address.id}">
                                                <i class="fas fa-check me-2"></i> Varsayılan Yap
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item text-danger delete-address" href="#" data-id="${address.id}" data-name="${address.adres_adi}">
                                                <i class="fas fa-trash me-2"></i> Sil
                                            </a>
                                        </li>
                                        ` : ''}
                                    </ul>
                                </div>
                            </div>
                            <p class="card-text mb-1">
                                <strong>${address.ad_soyad}</strong>
                            </p>
                            <p class="card-text mb-1">${address.adres}</p>
                            <p class="card-text mb-1">${address.il} / ${address.ilce}</p>
                            <p class="card-text mb-0">Tel: ${address.telefon}</p>
                        </div>
                    </div>
                </div>
            `;
            
            addressList.append(addressCard);
        });
        
        // Set event listeners for address actions
        $(".edit-address").on("click", function(e) {
            e.preventDefault();
            const addressId = $(this).data('id');
            openAddressModal('edit', addressId);
        });
        
        $(".set-default-address").on("click", function(e) {
            e.preventDefault();
            const addressId = $(this).data('id');
            setDefaultAddress(addressId);
        });
        
        $(".delete-address").on("click", function(e) {
            e.preventDefault();
            const addressId = $(this).data('id');
            const addressName = $(this).data('name');
            openDeleteModal(addressId, addressName);
        });
    }
    
    // Open address modal (add or edit)
    function openAddressModal(mode, addressId = null) {
        // Reset form
        $("#addressForm")[0].reset();
        $("#adres_id").val('');
        
        if (mode === 'add') {
            $("#addressModalTitle").text("Yeni Adres Ekle");
        } else {
            $("#addressModalTitle").text("Adres Düzenle");
            
            // Fetch address details
            $.ajax({
                type: "GET",
                url: "nedmin/netting/islem.php",
                data: {
                    islem: "get_address",
                    adres_id: addressId
                },
                dataType: "json",
                success: function(response) {
                    if (response.durum === 'success') {
                        const address = response.adres;
                        $("#adres_id").val(address.id);
                        $("#adres_adi").val(address.adres_adi);
                        $("#ad_soyad").val(address.ad_soyad);
                        $("#telefon").val(address.telefon);
                        $("#tc_no").val(address.tc_no);
                        $("#il").val(address.il);
                        $("#ilce").val(address.ilce);
                        $("#adres").val(address.adres);
                        $("#posta_kodu").val(address.posta_kodu);
                        $("#varsayilan").prop('checked', address.varsayilan == 1);
                    } else {
                        showError(response.mesaj || 'Adres bilgileri alınırken bir hata oluştu.');
                    }
                },
                error: function() {
                    showError('Sunucu hatası: Adres bilgileri alınamadı.');
                }
            });
        }
        
        // Show modal
        const addressModal = new bootstrap.Modal(document.getElementById('addressModal'));
        addressModal.show();
    }
    
    // Save address (both add and edit)
    function saveAddress() {
        // Validate form
        if (!validateAddressForm()) {
            return;
        }
        
        const isEdit = $("#adres_id").val() !== '';
        const formData = {
            islem: isEdit ? "update_address" : "add_address",
            adres_id: $("#adres_id").val(),
            adres_adi: $("#adres_adi").val(),
            ad_soyad: $("#ad_soyad").val(),
            telefon: $("#telefon").val(),
            tc_no: $("#tc_no").val(),
            il: $("#il").val(),
            ilce: $("#ilce").val(),
            adres: $("#adres").val(),
            posta_kodu: $("#posta_kodu").val(),
            varsayilan: $("#varsayilan").is(':checked') ? 1 : 0
        };
        
        $.ajax({
            type: "POST",
            url: "nedmin/netting/islem.php",
            data: formData,
            dataType: "json",
            success: function(response) {
                if (response.durum === 'success') {
                    // Hide modal
                    bootstrap.Modal.getInstance(document.getElementById('addressModal')).hide();
                    
                    // Show success message
                    showSuccess(response.mesaj || (isEdit ? 'Adres başarıyla güncellendi.' : 'Yeni adres başarıyla eklendi.'));
                    
                    // Reload addresses
                    loadAddresses();
                } else {
                    showError(response.mesaj || 'İşlem sırasında bir hata oluştu.');
                }
            },
            error: function() {
                showError('Sunucu hatası: İşlem gerçekleştirilemedi.');
            }
        });
    }
    
    // Validate address form
    function validateAddressForm() {
        const requiredFields = ["adres_adi", "ad_soyad", "telefon", "tc_no", "il", "ilce", "adres"];
        let isValid = true;
        
        requiredFields.forEach(function(field) {
            const input = $(`#${field}`);
            if (!input.val().trim()) {
                input.addClass('is-invalid');
                isValid = false;
            } else {
                input.removeClass('is-invalid');
            }
        });
        
        if (!isValid) {
            showError('Lütfen tüm zorunlu alanları doldurun.');
        }
        
        return isValid;
    }
    
    // Set default address
    function setDefaultAddress(addressId) {
        $.ajax({
            type: "POST",
            url: "nedmin/netting/islem.php",
            data: {
                islem: "set_default_address",
                adres_id: addressId
            },
            dataType: "json",
            success: function(response) {
                if (response.durum === 'success') {
                    showSuccess(response.mesaj || 'Varsayılan adres güncellendi.');
                    loadAddresses();
                } else {
                    showError(response.mesaj || 'İşlem sırasında bir hata oluştu.');
                }
            },
            error: function() {
                showError('Sunucu hatası: Varsayılan adres değiştirilemedi.');
            }
        });
    }
    
    // Open delete confirmation modal
    function openDeleteModal(addressId, addressName) {
        $("#deleteAddressId").val(addressId);
        $("#deleteAddressName").text(addressName);
        
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteAddressModal'));
        deleteModal.show();
    }
    
    // Delete address
    function deleteAddress() {
        const addressId = $("#deleteAddressId").val();
        
        $.ajax({
            type: "POST",
            url: "nedmin/netting/islem.php",
            data: {
                islem: "delete_address",
                adres_id: addressId
            },
            dataType: "json",
            success: function(response) {
                // Hide modal
                bootstrap.Modal.getInstance(document.getElementById('deleteAddressModal')).hide();
                
                if (response.durum === 'success') {
                    showSuccess(response.mesaj || 'Adres başarıyla silindi.');
                    loadAddresses();
                } else {
                    showError(response.mesaj || 'İşlem sırasında bir hata oluştu.');
                }
            },
            error: function() {
                // Hide modal
                bootstrap.Modal.getInstance(document.getElementById('deleteAddressModal')).hide();
                showError('Sunucu hatası: Adres silinemedi.');
            }
        });
    }
    
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
    
    // Remove validation on input
    $(document).on('input', '.form-control', function() {
        $(this).removeClass('is-invalid');
    });
});
</script>

<?php include 'footer.php'; ?> 