<?php
try {
    $db = new PDO("mysql:host=localhost;dbname=eticaret", "root", "123");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->exec("SET NAMES 'utf8'; SET CHARSET 'utf8'");
} catch (PDOException $e) {
    echo "Bağlantı hatası: " . $e->getMessage();
    exit;
}
?>
