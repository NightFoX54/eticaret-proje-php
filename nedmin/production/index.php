<?php include 'header.php'; ?>

<div class="page-header">
    <h2>Dashboard</h2>
</div>

<div class="row">
    <div class="col-md-3 mb-4">
        <div class="card bg-primary text-white h-100">
            <div class="card-body py-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">Ürünler</h5>
                        <?php
                        $urun_say = $db->prepare("SELECT COUNT(*) as adet FROM urun");
                        $urun_say->execute();
                        $urun_adet = $urun_say->fetch(PDO::FETCH_ASSOC)['adet'];
                        ?>
                        <h2 class="mt-3 mb-0"><?php echo $urun_adet; ?></h2>
                    </div>
                    <i class="fas fa-box fa-3x"></i>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between bg-primary">
                <a href="urun.php" class="text-white text-decoration-none">Detay Görüntüle</a>
                <i class="fas fa-angle-right text-white"></i>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card bg-success text-white h-100">
            <div class="card-body py-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">Kategoriler</h5>
                        <?php
                        $kategori_say = $db->prepare("SELECT COUNT(*) as adet FROM urun_kategoriler");
                        $kategori_say->execute();
                        $kategori_adet = $kategori_say->fetch(PDO::FETCH_ASSOC)['adet'];
                        ?>
                        <h2 class="mt-3 mb-0"><?php echo $kategori_adet; ?></h2>
                    </div>
                    <i class="fas fa-list fa-3x"></i>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between bg-success">
                <a href="kategori.php" class="text-white text-decoration-none">Detay Görüntüle</a>
                <i class="fas fa-angle-right text-white"></i>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card bg-warning text-white h-100">
            <div class="card-body py-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">Kuponlar</h5>
                        <?php
                        $kupon_say = $db->prepare("SELECT COUNT(*) as adet FROM kupon");
                        $kupon_say->execute();
                        $kupon_adet = $kupon_say->fetch(PDO::FETCH_ASSOC)['adet'];
                        ?>
                        <h2 class="mt-3 mb-0"><?php echo $kupon_adet; ?></h2>
                    </div>
                    <i class="fas fa-tag fa-3x"></i>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between bg-warning">
                <a href="kupon.php" class="text-white text-decoration-none">Detay Görüntüle</a>
                <i class="fas fa-angle-right text-white"></i>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card bg-danger text-white h-100">
            <div class="card-body py-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">Kullanıcılar</h5>
                        <?php
                        $kullanici_say = $db->prepare("SELECT COUNT(*) as adet FROM kullanicilar");
                        $kullanici_say->execute();
                        $kullanici_adet = $kullanici_say->fetch(PDO::FETCH_ASSOC)['adet'];
                        ?>
                        <h2 class="mt-3 mb-0"><?php echo $kullanici_adet; ?></h2>
                    </div>
                    <i class="fas fa-users fa-3x"></i>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between bg-danger">
                <a href="kullanici.php" class="text-white text-decoration-none">Detay Görüntüle</a>
                <i class="fas fa-angle-right text-white"></i>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?> 