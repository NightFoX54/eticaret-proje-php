<?php
ob_start();
session_start();
// Tüm oturum verilerini temizle
session_destroy();
// Giriş sayfasına yönlendir
header("Location: ../../login.php");
exit;
?> 