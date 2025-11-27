<?php
session_start();
if (!isset($_SESSION['kullanici_id'])) {
    header("Location: login.php");
    exit();
}

if($_SESSION['kullanici_rol'] == 'admin' || $_SESSION['kullanici_rol'] == 'moderator'){
    header("Location: production/index.php");
    exit();
}else{
    header("Location: login.php");
    exit();
}
?>


