<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include 'header.php';
?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-body text-center">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success fa-5x"></i>
                    </div>
                    
                    <h2 class="text-success mb-3">Ödeme Başarılı!</h2>
                    
                    <p class="lead mb-4">
                        Ödemeniz başarıyla alındı. Siparişiniz hazırlanmaya başlanacaktır.
                    </p>
                    
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle me-2"></i>Önemli Bilgiler</h6>
                        <ul class="text-start mb-0">
                            <li>Siparişiniz onaylandı ve sistemimize kaydedildi</li>
                            <li>Sipariş durumunu "Siparişlerim" sayfasından takip edebilirsiniz</li>
                            <li>Hazırlık sürecinde size e-posta ile bilgilendirme yapılacaktır</li>
                            <li>Herhangi bir sorunuz için müşteri hizmetlerimizle iletişime geçebilirsiniz</li>
                        </ul>
                    </div>
                    
                    <div class="mt-4">
                        <a href="orders.php" class="btn btn-primary btn-lg me-3">
                            <i class="fas fa-list me-2"></i>Siparişlerim
                        </a>
                        <a href="index.php" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-shopping-cart me-2"></i>Alışverişe Devam Et
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border: none;
    border-radius: 15px;
}

.card-body {
    padding: 3rem;
}

.text-success {
    color: #28a745 !important;
}

.btn-lg {
    padding: 12px 30px;
    font-size: 1.1rem;
}

.alert-info {
    background-color: #d1ecf1;
    border-color: #bee5eb;
    color: #0c5460;
}
</style>

<?php include 'footer.php'; ?> 