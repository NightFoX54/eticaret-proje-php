<?php
ob_start();
session_start();

// Tüm oturum verilerini temizle
session_destroy();

// AJAX isteği mi kontrol et
$is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

if ($is_ajax) {
    // AJAX isteği ise JSON yanıt döndür
    header('Content-Type: application/json');
    echo json_encode([
        'durum' => 'success',
        'mesaj' => 'Başarıyla çıkış yapıldı.',
        'redirect' => 'index.php'
    ]);
} else {
    // Normal istek ise sayfaya yönlendir
    header("Location: index.php");
}
exit;
?> 