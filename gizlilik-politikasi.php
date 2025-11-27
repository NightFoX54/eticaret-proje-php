<?php
include 'header.php';
?>

<style>
    .privacy-hero {
        background: linear-gradient(135deg, #9D7FC7 0%, #8B6FC7 100%);
        color: white;
        padding: 4rem 0;
        margin-bottom: 3rem;
        position: relative;
        overflow: hidden;
    }
    
    .privacy-hero::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 500px;
        height: 500px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        animation: float 6s ease-in-out infinite;
    }
    
    .privacy-hero::after {
        content: '';
        position: absolute;
        bottom: -30%;
        left: -5%;
        width: 400px;
        height: 400px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 50%;
        animation: float 8s ease-in-out infinite reverse;
    }
    
    @keyframes float {
        0%, 100% {
            transform: translateY(0) rotate(0deg);
        }
        50% {
            transform: translateY(-20px) rotate(5deg);
        }
    }
    
    .privacy-hero h1 {
        font-size: 3rem;
        font-weight: 700;
        margin-bottom: 1rem;
        position: relative;
        z-index: 1;
        animation: fadeInDown 0.8s ease-out;
    }
    
    .privacy-hero p {
        font-size: 1.2rem;
        opacity: 0.95;
        position: relative;
        z-index: 1;
        animation: fadeInUp 1s ease-out;
    }
    
    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .privacy-content {
        max-width: 900px;
        margin: 0 auto;
    }
    
    .privacy-section {
        background: white;
        border-radius: 16px;
        padding: 2.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 20px rgba(157, 127, 199, 0.1);
        border: 1px solid rgba(157, 127, 199, 0.2);
        transition: all 0.3s ease;
        animation: fadeIn 0.6s ease-out;
        animation-fill-mode: both;
    }
    
    .privacy-section:nth-child(1) { animation-delay: 0.1s; }
    .privacy-section:nth-child(2) { animation-delay: 0.2s; }
    .privacy-section:nth-child(3) { animation-delay: 0.3s; }
    .privacy-section:nth-child(4) { animation-delay: 0.4s; }
    .privacy-section:nth-child(5) { animation-delay: 0.5s; }
    .privacy-section:nth-child(6) { animation-delay: 0.6s; }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .privacy-section:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(157, 127, 199, 0.2);
    }
    
    .privacy-section h2 {
        color: #8B6FC7;
        font-weight: 700;
        font-size: 1.8rem;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 3px solid;
        border-image: linear-gradient(90deg, #9D7FC7, transparent) 1;
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    
    .privacy-section h2 i {
        font-size: 1.5rem;
        background: linear-gradient(135deg, #9D7FC7, #8B6FC7);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .privacy-section h3 {
        color: #7A5FB8;
        font-weight: 600;
        font-size: 1.3rem;
        margin-top: 2rem;
        margin-bottom: 1rem;
    }
    
    .privacy-section p {
        color: #555;
        line-height: 1.8;
        margin-bottom: 1.5rem;
        font-size: 1.05rem;
    }
    
    .privacy-section ul {
        list-style: none;
        padding-left: 0;
    }
    
    .privacy-section ul li {
        padding: 0.75rem 0;
        padding-left: 2rem;
        position: relative;
        color: #555;
        line-height: 1.8;
    }
    
    .privacy-section ul li::before {
        content: '✓';
        position: absolute;
        left: 0;
        color: #9D7FC7;
        font-weight: bold;
        font-size: 1.2rem;
    }
    
    .privacy-section ol {
        padding-left: 1.5rem;
    }
    
    .privacy-section ol li {
        color: #555;
        line-height: 1.8;
        margin-bottom: 1rem;
        padding-left: 0.5rem;
    }
    
    .highlight-box {
        background: linear-gradient(135deg, #E8DFF0 0%, #D9CEE8 100%);
        border-left: 4px solid #9D7FC7;
        padding: 1.5rem;
        border-radius: 8px;
        margin: 1.5rem 0;
    }
    
    .highlight-box p {
        margin: 0;
        color: #7A5FB8;
        font-weight: 500;
    }
    
    .contact-info {
        background: linear-gradient(135deg, #F5F0FA 0%, #E8DFF0 100%);
        padding: 2rem;
        border-radius: 12px;
        margin-top: 2rem;
        text-align: center;
    }
    
    .contact-info h3 {
        color: #8B6FC7;
        margin-bottom: 1rem;
    }
    
    .contact-info p {
        color: #555;
        margin-bottom: 0.5rem;
    }
    
    .contact-info a {
        color: #9D7FC7;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .contact-info a:hover {
        color: #7A5FB8;
        text-decoration: underline;
    }
    
    @media (max-width: 768px) {
        .privacy-hero h1 {
            font-size: 2rem;
        }
        
        .privacy-hero p {
            font-size: 1rem;
        }
        
        .privacy-section {
            padding: 1.5rem;
        }
        
        .privacy-section h2 {
            font-size: 1.5rem;
        }
    }
</style>

<!-- Hero Section -->
<div class="privacy-hero">
    <div class="container">
        <div class="text-center">
            <h1><i class="fas fa-shield-alt me-3"></i>Gizlilik Politikası</h1>
            <p>Kişisel verilerinizin korunması bizim için önemlidir</p>
            <p class="small mt-2" style="opacity: 0.8;">Son Güncelleme: <?php echo date('d.m.Y'); ?></p>
        </div>
    </div>
</div>

<!-- Content -->
<div class="container privacy-content mb-5">
    <div class="privacy-section">
        <h2><i class="fas fa-info-circle"></i>Genel Bilgiler</h2>
        <p>
            Bu gizlilik politikası, E-Ticaret platformu olarak kişisel verilerinizin nasıl toplandığı, kullanıldığı, 
            korunduğu ve paylaşıldığı hakkında bilgi vermek amacıyla hazırlanmıştır. Sitemizi kullanarak bu politikayı 
            kabul etmiş sayılırsınız.
        </p>
        <p>
            Kişisel verilerinizin güvenliği bizim için son derece önemlidir. 6698 sayılı Kişisel Verilerin Korunması 
            Kanunu (KVKK) kapsamında, kişisel verilerinizin işlenmesi ve korunması konusunda gerekli tüm önlemleri 
            almaktayız.
        </p>
    </div>

    <div class="privacy-section">
        <h2><i class="fas fa-database"></i>Toplanan Kişisel Veriler</h2>
        <p>Hizmetlerimizi sunabilmek için aşağıdaki kişisel verileri topluyoruz:</p>
        
        <h3>Kimlik Bilgileri</h3>
        <ul>
            <li>Ad ve soyad</li>
            <li>E-posta adresi</li>
            <li>Telefon numarası</li>
            <li>TC Kimlik Numarası (teslimat için)</li>
        </ul>
        
        <h3>Adres Bilgileri</h3>
        <ul>
            <li>Teslimat adresi</li>
            <li>Fatura adresi</li>
            <li>İl, ilçe ve posta kodu bilgileri</li>
        </ul>
        
        <h3>Finansal Bilgiler</h3>
        <ul>
            <li>Ödeme bilgileri (güvenli ödeme sistemleri üzerinden işlenir)</li>
            <li>Sipariş geçmişi</li>
        </ul>
        
        <h3>Teknik Bilgiler</h3>
        <ul>
            <li>IP adresi</li>
            <li>Tarayıcı bilgileri</li>
            <li>Çerez (cookie) verileri</li>
            <li>Site kullanım verileri</li>
        </ul>
    </div>

    <div class="privacy-section">
        <h2><i class="fas fa-cog"></i>Kişisel Verilerin Kullanım Amaçları</h2>
        <p>Toplanan kişisel verileriniz aşağıdaki amaçlarla kullanılmaktadır:</p>
        
        <ol>
            <li><strong>Sipariş İşleme:</strong> Siparişlerinizin alınması, işlenmesi ve teslim edilmesi</li>
            <li><strong>Müşteri Hizmetleri:</strong> Sorularınıza yanıt verilmesi ve destek sağlanması</li>
            <li><strong>Hesap Yönetimi:</strong> Hesabınızın oluşturulması ve yönetilmesi</li>
            <li><strong>İletişim:</strong> Sipariş durumu, kampanyalar ve önemli bildirimler hakkında bilgilendirme</li>
            <li><strong>İyileştirme:</strong> Hizmetlerimizin geliştirilmesi ve kullanıcı deneyiminin iyileştirilmesi</li>
            <li><strong>Yasal Yükümlülükler:</strong> Yasal gerekliliklerin yerine getirilmesi</li>
            <li><strong>Güvenlik:</strong> Dolandırıcılık ve kötüye kullanımın önlenmesi</li>
        </ol>
        
        <div class="highlight-box">
            <p><i class="fas fa-info-circle me-2"></i>Kişisel verileriniz, yukarıda belirtilen amaçlar dışında kullanılmamaktadır.</p>
        </div>
    </div>

    <div class="privacy-section">
        <h2><i class="fas fa-share-alt"></i>Kişisel Verilerin Paylaşımı</h2>
        <p>
            Kişisel verileriniz, aşağıdaki durumlar dışında üçüncü kişilerle paylaşılmamaktadır:
        </p>
        
        <h3>Hizmet Sağlayıcılar</h3>
        <ul>
            <li>Kargo firmaları (teslimat için)</li>
            <li>Ödeme işlemcileri (güvenli ödeme için)</li>
            <li>E-posta servis sağlayıcıları (bildirimler için)</li>
        </ul>
        
        <h3>Yasal Gereklilikler</h3>
        <ul>
            <li>Yasal zorunluluklar gereği yetkili kurumlarla paylaşım</li>
            <li>Mahkeme kararları ve yasal süreçler</li>
        </ul>
        
        <p>
            Tüm hizmet sağlayıcılarımız, kişisel verilerinizin korunması konusunda yükümlülük altındadır ve 
            verilerinizi yalnızca belirtilen amaçlar için kullanmaktadır.
        </p>
    </div>

    <div class="privacy-section">
        <h2><i class="fas fa-lock"></i>Veri Güvenliği</h2>
        <p>
            Kişisel verilerinizin güvenliği için aşağıdaki teknik ve idari önlemleri almaktayız:
        </p>
        
        <ul>
            <li><strong>SSL Şifreleme:</strong> Tüm veri aktarımları SSL/TLS protokolü ile şifrelenmektedir</li>
            <li><strong>Güvenli Sunucular:</strong> Verileriniz güvenli sunucularda saklanmaktadır</li>
            <li><strong>Erişim Kontrolü:</strong> Verilere yalnızca yetkili personel erişebilmektedir</li>
            <li><strong>Düzenli Yedekleme:</strong> Verileriniz düzenli olarak yedeklenmektedir</li>
            <li><strong>Güvenlik Güncellemeleri:</strong> Sistem güvenliği sürekli güncellenmektedir</li>
        </ul>
        
        <div class="highlight-box">
            <p><i class="fas fa-shield-alt me-2"></i>Ödeme bilgileriniz asla sistemimizde saklanmamaktadır. Tüm ödemeler 
            güvenli ödeme sistemleri (3D Secure) üzerinden işlenmektedir.</p>
        </div>
    </div>

    <div class="privacy-section">
        <h2><i class="fas fa-user-shield"></i>Haklarınız</h2>
        <p>
            KVKK kapsamında kişisel verilerinizle ilgili aşağıdaki haklara sahipsiniz:
        </p>
        
        <ul>
            <li><strong>Bilgi Alma Hakkı:</strong> Kişisel verilerinizin işlenip işlenmediğini öğrenme</li>
            <li><strong>Erişim Hakkı:</strong> İşlenen kişisel verilerinize erişim talep etme</li>
            <li><strong>Düzeltme Hakkı:</strong> Yanlış veya eksik verilerin düzeltilmesini isteme</li>
            <li><strong>Silme Hakkı:</strong> Kişisel verilerinizin silinmesini talep etme</li>
            <li><strong>İtiraz Hakkı:</strong> Kişisel verilerinizin işlenmesine itiraz etme</li>
            <li><strong>Veri Taşınabilirliği:</strong> Verilerinizi başka bir hizmet sağlayıcıya aktarma</li>
        </ul>
        
        <p>
            Haklarınızı kullanmak için <a href="mailto:info@eticaret.com" style="color: #9D7FC7; font-weight: 600;">info@eticaret.com</a> 
            adresine e-posta gönderebilir veya iletişim formumuzu kullanabilirsiniz.
        </p>
    </div>

    <div class="privacy-section">
        <h2><i class="fas fa-cookie"></i>Çerezler (Cookies)</h2>
        <p>
            Web sitemiz, kullanıcı deneyimini iyileştirmek ve site performansını analiz etmek için çerezler kullanmaktadır.
        </p>
        
        <h3>Kullanılan Çerez Türleri</h3>
        <ul>
            <li><strong>Zorunlu Çerezler:</strong> Sitenin temel işlevlerinin çalışması için gerekli</li>
            <li><strong>Performans Çerezleri:</strong> Site kullanımını analiz etmek için</li>
            <li><strong>Fonksiyonel Çerezler:</strong> Tercihlerinizi hatırlamak için</li>
        </ul>
        
        <p>
            Tarayıcı ayarlarınızdan çerezleri yönetebilirsiniz. Ancak bazı çerezlerin devre dışı bırakılması 
            sitenin bazı özelliklerinin çalışmamasına neden olabilir.
        </p>
    </div>

    <div class="privacy-section">
        <h2><i class="fas fa-sync"></i>Politika Güncellemeleri</h2>
        <p>
            Bu gizlilik politikası, yasal değişiklikler veya hizmetlerimizdeki güncellemeler nedeniyle 
            zaman zaman güncellenebilir. Önemli değişiklikler durumunda e-posta veya site üzerinden 
            bilgilendirileceksiniz.
        </p>
        <p>
            Güncel gizlilik politikamızı bu sayfadan takip edebilirsiniz. Politikadaki değişiklikler, 
            yayınlandığı tarihten itibaren geçerlidir.
        </p>
    </div>

    <div class="contact-info">
        <h3><i class="fas fa-envelope me-2"></i>İletişim</h3>
        <p>Gizlilik politikamız hakkında sorularınız için bizimle iletişime geçebilirsiniz:</p>
        <p><strong>E-posta:</strong> <a href="mailto:info@eticaret.com">info@eticaret.com</a></p>
        <p><strong>Telefon:</strong> <a href="tel:+905551234567">+90 555 123 45 67</a></p>
        <p class="mt-3 mb-0"><small>Çalışma Saatleri: Hafta içi 09:00 - 18:00</small></p>
    </div>
</div>

<?php
include 'footer.php';
?>

