<?php
include 'header.php';

// Redirect if user is not logged in
if(!isset($_SESSION['kullanici_id'])) {
    header("Location: login.php?durum=giris");
    exit;
}

$kullanici_id = $_SESSION['kullanici_id'];
?>

<!-- Notifications Content -->
<div class="container mt-4 mb-5">
    <div class="row">
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
                    <a href="address.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-map-marker-alt me-2"></i> Adreslerim
                    </a>
                    <a href="wishlist.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-heart me-2"></i> Favorilerim
                    </a>
                    <a href="notifications.php" class="list-group-item list-group-item-action active">
                        <i class="fas fa-bell me-2"></i> Bildirimlerim
                    </a>
                    <a href="#" class="list-group-item list-group-item-action text-danger logout-link">
                        <i class="fas fa-sign-out-alt me-2"></i> Çıkış Yap
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-9">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Bildirimlerim</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-success" id="successAlert" style="display:none;" role="alert"></div>
                    <div class="alert alert-danger" id="errorAlert" style="display:none;" role="alert"></div>
                    
                    <div id="notifications-container">
                        <!-- Notifications will be loaded here -->
                        <div class="text-center">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Yükleniyor...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Load notifications
    loadNotifications();
    
    // Mark notification as read when clicked
    $(document).on('click', '.notification-link', function(e) {
        const notificationId = $(this).data('id');
        markAsRead(notificationId);
        // Don't prevent default so the link is followed
    });
    
    // Mark notification as read when button clicked
    $(document).on('click', '.mark-read-btn', function(e) {
        e.preventDefault();
        const notificationId = $(this).data('id');
        markAsRead(notificationId, true);
    });
    
    // Delete notification
    $(document).on('click', '.delete-notification-btn', function(e) {
        e.preventDefault();
        const notificationId = $(this).data('id');
        deleteNotification(notificationId);
    });
});

function loadNotifications() {
    $.ajax({
        url: 'nedmin/netting/islem.php',
        type: 'POST',
        data: { islem: 'bildirimleri_getir' },
        dataType: 'json',
        success: function(response) {
            if (response.durum === 'success') {
                displayNotifications(response.bildirimler);
                // Update notification badge in the header
                loadNotificationsCount();
            } else {
                $('#notifications-container').html('<div class="alert alert-info">Bildiriminiz bulunmamaktadır.</div>');
            }
        },
        error: function() {
            $('#notifications-container').html('<div class="alert alert-danger">Bildirimler yüklenirken bir hata oluştu.</div>');
        }
    });
}

function displayNotifications(notifications) {
    if (notifications.length === 0) {
        $('#notifications-container').html('<div class="alert alert-info">Bildiriminiz bulunmamaktadır.</div>');
        return;
    }

    let html = '<div class="list-group">';
    
    notifications.forEach(notification => {
        const readClass = notification.okundu == 1 ? '' : 'fw-bold';
        const readBadge = notification.okundu == 1 ? '' : '<span class="badge bg-primary ms-2">Yeni</span>';
        const date = new Date(notification.tarih).toLocaleDateString('tr-TR');
        const time = new Date(notification.tarih).toLocaleTimeString('tr-TR');
        
        html += `
            <div class="list-group-item list-group-item-action">
                <div class="d-flex w-100 justify-content-between">
                    <h6 class="mb-2 ${readClass}">
                        <a href="${notification.bildirim_link}" class="notification-link" data-id="${notification.id}">
                            ${notification.bildirim_mesaj}
                        </a>
                        ${readBadge}
                    </h6>
                    <small>${date} ${time}</small>
                </div>
                <div class="mt-2 d-flex justify-content-end">
                    ${notification.okundu == 0 ? 
                        `<button class="btn btn-sm btn-outline-primary me-2 mark-read-btn" data-id="${notification.id}">
                            <i class="fas fa-check"></i> Okundu Olarak İşaretle
                        </button>` : ''
                    }
                    <button class="btn btn-sm btn-outline-danger delete-notification-btn" data-id="${notification.id}">
                        <i class="fas fa-trash"></i> Bildirimi Sil
                    </button>
                </div>
            </div>
        `;
    });

    html += '</div>';
    $('#notifications-container').html(html);
}

function markAsRead(notificationId, reload = false) {
    $.ajax({
        url: 'nedmin/netting/islem.php',
        type: 'POST',
        data: {
            islem: 'bildirim_okundu',
            bildirim_id: notificationId
        },
        dataType: 'json',
        success: function(response) {
            if (response.durum === 'success') {
                if (reload) {
                    loadNotifications();
                    showSuccess('Bildirim okundu olarak işaretlendi.');
                }
                // Update notification badge in header
                loadNotificationsCount();
            } else {
                showError(response.mesaj || 'İşlem sırasında bir hata oluştu.');
            }
        },
        error: function() {
            showError('Sunucu hatası.');
        }
    });
}

function deleteNotification(notificationId) {
    if (confirm('Bu bildirimi silmek istediğinizden emin misiniz?')) {
        $.ajax({
            url: 'nedmin/netting/islem.php',
            type: 'POST',
            data: {
                islem: 'bildirim_sil',
                bildirim_id: notificationId
            },
            dataType: 'json',
            success: function(response) {
                if (response.durum === 'success') {
                    loadNotifications();
                    showSuccess('Bildirim başarıyla silindi.');
                } else {
                    showError(response.mesaj || 'İşlem sırasında bir hata oluştu.');
                }
            },
            error: function() {
                showError('Sunucu hatası.');
            }
        });
    }
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
</script>

<?php include 'footer.php'; ?> 