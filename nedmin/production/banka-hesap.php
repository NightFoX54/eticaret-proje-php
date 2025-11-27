<?php
include 'header.php';
?>

<div class="page-header">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-bank me-2"></i>Banka Hesapları Yönetimi
    </h1>
    <p class="text-muted">Banka hesaplarını yönetin</p>
</div>

<!-- Success/Error Messages -->
<div id="message-container"></div>

<!-- Add New Bank Account Button -->
<div class="mb-4">
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bankaHesapModal" onclick="yeniHesap()">
        <i class="fas fa-plus me-2"></i>Yeni Banka Hesabı Ekle
    </button>
</div>

<!-- Bank Accounts Table -->
<div class="card shadow">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Banka Hesapları Listesi</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="bankaHesaplariTable" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Banka Adı</th>
                        <th>Hesap Sahibi</th>
                        <th>Şube Adı</th>
                        <th>Şube Kodu</th>
                        <th>Hesap No</th>
                        <th>IBAN</th>
                        <th>Durum</th>
                        <th>Oluşturulma Tarihi</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be loaded via AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Bank Account Modal -->
<div class="modal fade" id="bankaHesapModal" tabindex="-1" aria-labelledby="bankaHesapModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bankaHesapModalLabel">Banka Hesabı Ekle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="bankaHesapForm">
                <div class="modal-body">
                    <input type="hidden" id="hesap_id" name="hesap_id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="banka_adi" class="form-label">Banka Adı *</label>
                                <input type="text" class="form-control" id="banka_adi" name="banka_adi" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="hesap_sahibi" class="form-label">Hesap Sahibi *</label>
                                <input type="text" class="form-control" id="hesap_sahibi" name="hesap_sahibi" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sube_adi" class="form-label">Şube Adı</label>
                                <input type="text" class="form-control" id="sube_adi" name="sube_adi">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sube_kodu" class="form-label">Şube Kodu</label>
                                <input type="text" class="form-control" id="sube_kodu" name="sube_kodu">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="hesap_no" class="form-label">Hesap Numarası</label>
                                <input type="text" class="form-control" id="hesap_no" name="hesap_no">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="iban" class="form-label">IBAN *</label>
                                <input type="text" class="form-control" id="iban" name="iban" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="durum" class="form-label">Durum</label>
                        <select class="form-select" id="durum" name="durum">
                            <option value="1">Aktif</option>
                            <option value="0">Pasif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Hesap Sil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Bu banka hesabını silmek istediğinizden emin misiniz?</p>
                <p class="text-danger"><strong>Bu işlem geri alınamaz!</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Sil</button>
            </div>
        </div>
    </div>
</div>

<script>
let currentDeleteId = null;
let dataTable = null;

$(document).ready(function() {
    // Initialize DataTable
    dataTable = $('#bankaHesaplariTable').DataTable({
        "ajax": {
            "url": "../netting/islem.php",
            "type": "POST",
            "data": {
                "islem": "banka_hesaplari_getir"
            },
            "dataSrc": function(json) {
                if (json.durum === 'success') {
                    return json.data;
                } else {
                    showMessage('error', json.mesaj || 'Veriler yüklenirken hata oluştu');
                    return [];
                }
            }
        },
        "columns": [
            {"data": "id"},
            {"data": "banka_adi"},
            {"data": "hesap_sahibi"},
            {"data": "sube_adi"},
            {"data": "sube_kodu"},
            {"data": "hesap_no"},
            {"data": "iban"},
            {
                "data": "durum",
                "render": function(data, type, row) {
                    return data == 1 ? 
                        '<span class="badge bg-success">Aktif</span>' : 
                        '<span class="badge bg-secondary">Pasif</span>';
                }
            },
            {
                "data": "created_at",
                "render": function(data, type, row) {
                    return new Date(data).toLocaleDateString('tr-TR');
                }
            },
            {
                "data": null,
                "render": function(data, type, row) {
                    return `
                        <button class="btn btn-sm btn-primary" onclick="hesapDuzenle(${row.id})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="hesapSil(${row.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    `;
                }
            }
        ],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/tr.json"
        },
        "order": [[0, "desc"]],
        "pageLength": 25
    });

    // Form submission
    $('#bankaHesapForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        formData.append('islem', 'banka_hesap_kaydet');
        
        $.ajax({
            url: '../netting/islem.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                console.log('Response:', response);
                try {
                    // Check if response is already an object or needs parsing
                    const result = typeof response === 'string' ? JSON.parse(response) : response;
                    if (result.durum === 'success') {
                        showMessage('success', result.mesaj);
                        $('#bankaHesapModal').modal('hide');
                        dataTable.ajax.reload();
                        $('#bankaHesapForm')[0].reset();
                    } else {
                        showMessage('error', result.mesaj);
                    }
                } catch (e) {
                    console.error('JSON Parse Error:', e);
                    console.error('Raw Response:', response);
                    showMessage('error', 'Sunucu yanıtı işlenirken hata oluştu');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                console.error('Response Text:', xhr.responseText);
                showMessage('error', 'Sunucu hatası oluştu');
            }
        });
    });

    // Delete confirmation
    $('#confirmDelete').on('click', function() {
        if (currentDeleteId) {
            $.ajax({
                url: '../netting/islem.php',
                type: 'POST',
                data: {
                    islem: 'banka_hesap_sil',
                    hesap_id: currentDeleteId
                },
                success: function(response) {
                    console.log('Delete Response:', response);
                    try {
                        // Check if response is already an object or needs parsing
                        const result = typeof response === 'string' ? JSON.parse(response) : response;
                        if (result.durum === 'success') {
                            showMessage('success', result.mesaj);
                            $('#deleteModal').modal('hide');
                            dataTable.ajax.reload();
                        } else {
                            showMessage('error', result.mesaj);
                        }
                    } catch (e) {
                        console.error('Delete JSON Parse Error:', e);
                        console.error('Delete Raw Response:', response);
                        showMessage('error', 'Sunucu yanıtı işlenirken hata oluştu');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Delete AJAX Error:', status, error);
                    console.error('Delete Response Text:', xhr.responseText);
                    showMessage('error', 'Sunucu hatası oluştu');
                }
            });
        }
    });
});

function yeniHesap() {
    $('#bankaHesapModalLabel').text('Banka Hesabı Ekle');
    $('#bankaHesapForm')[0].reset();
    $('#hesap_id').val('');
}

function hesapDuzenle(id) {
    $.ajax({
        url: '../netting/islem.php',
        type: 'POST',
        data: {
            islem: 'banka_hesap_detay',
            hesap_id: id
        },
        success: function(response) {
            console.log('Edit Response:', response);
            try {
                // Check if response is already an object or needs parsing
                const result = typeof response === 'string' ? JSON.parse(response) : response;
                if (result.durum === 'success') {
                    const hesap = result.data;
                    $('#hesap_id').val(hesap.id);
                    $('#banka_adi').val(hesap.banka_adi);
                    $('#hesap_sahibi').val(hesap.hesap_sahibi);
                    $('#sube_adi').val(hesap.sube_adi);
                    $('#sube_kodu').val(hesap.sube_kodu);
                    $('#hesap_no').val(hesap.hesap_no);
                    $('#iban').val(hesap.iban);
                    $('#durum').val(hesap.durum);
                    
                    $('#bankaHesapModalLabel').text('Banka Hesabı Düzenle');
                    $('#bankaHesapModal').modal('show');
                } else {
                    showMessage('error', result.mesaj);
                }
            } catch (e) {
                console.error('Edit JSON Parse Error:', e);
                console.error('Edit Raw Response:', response);
                showMessage('error', 'Sunucu yanıtı işlenirken hata oluştu');
            }
        },
        error: function(xhr, status, error) {
            console.error('Edit AJAX Error:', status, error);
            console.error('Edit Response Text:', xhr.responseText);
            showMessage('error', 'Sunucu hatası oluştu');
        }
    });
}

function hesapSil(id) {
    currentDeleteId = id;
    $('#deleteModal').modal('show');
}

function showMessage(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const icon = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-triangle';
    
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            <i class="${icon} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    $('#message-container').html(alertHtml);
    
    // Auto hide after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut();
    }, 5000);
}
</script>

<?php include 'footer.php'; ?>
