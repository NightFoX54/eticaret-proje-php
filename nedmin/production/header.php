<?php 
ob_start();
session_start();
include '../netting/baglan.php';

// Oturum kontrolü
if (!isset($_SESSION['kullanici_id']) || ($_SESSION['kullanici_rol'] != 'admin' && $_SESSION['kullanici_rol'] != 'moderator')) {
    header("Location: ../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Ticaret Yönetim Paneli</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
            color: white;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,.75);
            padding: 10px 15px;
        }
        .sidebar .nav-link:hover {
            color: #fff;
            background-color: rgba(255,255,255,.1);
        }
        .sidebar .nav-link.active {
            color: #fff;
            background-color: rgba(255,255,255,.2);
        }
        .sidebar .nav-link i {
            margin-right: 10px;
        }
        .top-navbar {
            background-color: #ffffff;
            box-shadow: 0 2px 5px rgba(0,0,0,.1);
            border-bottom: 1px solid #e3e6f0;
        }
        .user-profile {
            display: flex;
            align-items: center;
        }
        .user-profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }
        .main-content {
            padding: 20px;
        }
        .page-header {
            margin-bottom: 30px;
            border-bottom: 1px solid #e3e6f0;
            padding-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky">
                    <div class="text-center p-3">
                        <h4>E-Ticaret</h4>
                        <p>Yönetim Paneli</p>
                    </div>
                    <hr class="my-2">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">
                                <i class="fas fa-tachometer-alt"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="kategori.php">
                                <i class="fas fa-list"></i>
                                Ürün Kategorileri
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="urun.php">
                                <i class="fas fa-box"></i>
                                Ürünler
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link dropdown-toggle" href="#submenu-urun-yonetimi" data-bs-toggle="collapse" role="button" aria-expanded="false">
                                <i class="fas fa-money-bill"></i>
                                Finansal Yönetim
                            </a>
                            <div class="collapse" id="submenu-urun-yonetimi">
                                <ul class="nav flex-column ps-3">
                                    <li class="nav-item">
                                        <a class="nav-link" href="siparis.php">
                                            <i class="fas fa-shopping-cart"></i>
                                            Siparişler
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="kupon.php">
                                            <i class="fas fa-tag"></i>
                                            Kuponlar
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="banka-hesap.php">
                                            <i class="fas fa-bank"></i>
                                            Banka Hesapları
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="kullanici.php">
                                <i class="fas fa-users"></i>
                                Kullanıcılar
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link dropdown-toggle" href="#submenu-urun-yonetimi" data-bs-toggle="collapse" role="button" aria-expanded="false">
                                <i class="fas fa-comments"></i>
                                Ürün Yönetimi
                            </a>
                            <div class="collapse" id="submenu-urun-yonetimi">
                                <ul class="nav flex-column ps-3">
                                    <li class="nav-item">
                                        <a class="nav-link" href="urun-degerlendirme.php">
                                            <i class="fas fa-star"></i>
                                            Değerlendirmeler
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="urun-sorular.php">
                                            <i class="fas fa-question-circle"></i>
                                            Sorular
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link dropdown-toggle" href="#submenu-istatistik" data-bs-toggle="collapse" role="button" aria-expanded="false">
                                <i class="fas fa-chart-bar"></i>
                                İstatistikler
                            </a>
                            <div class="collapse" id="submenu-istatistik">
                                <ul class="nav flex-column ps-3">
                                    <li class="nav-item">
                                        <a class="nav-link" href="site_istatistik.php">
                                            <i class="fas fa-globe"></i>
                                            Site Ziyaretleri
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="kategori_istatistik.php">
                                            <i class="fas fa-list"></i>
                                            Kategori İstatistikleri
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="urun_istatistik.php">
                                            <i class="fas fa-box"></i>
                                            Ürün İstatistikleri
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <!-- Top Navbar -->
                <nav class="navbar navbar-expand-lg navbar-light top-navbar mb-4">
                    <div class="container-fluid">
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target=".sidebar">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse justify-content-end">
                            <div class="user-profile">
                                <div class="dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-user-circle fa-2x me-2"></i>
                                        <span><?php echo $_SESSION['kullanici_ad'] . ' ' . $_SESSION['kullanici_soyad']; ?></span>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuLink">
                                        <li><a class="dropdown-item" href="profil.php"><i class="fas fa-user-edit me-2"></i>Profil</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="cikis.php"><i class="fas fa-sign-out-alt me-2"></i>Çıkış Yap</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </nav>
                
                <!-- Page Content -->
                <div class="main-content"> 