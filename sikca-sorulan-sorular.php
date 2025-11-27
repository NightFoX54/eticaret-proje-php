<?php
include 'header.php';
?>

<style>
    .faq-hero {
        background: linear-gradient(135deg, #9D7FC7 0%, #8B6FC7 100%);
        color: white;
        padding: 4rem 0;
        margin-bottom: 3rem;
        position: relative;
        overflow: hidden;
    }
    
    .faq-hero::before {
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
    
    .faq-hero::after {
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
    
    .faq-hero h1 {
        font-size: 3rem;
        font-weight: 700;
        margin-bottom: 1rem;
        position: relative;
        z-index: 1;
        animation: fadeInDown 0.8s ease-out;
    }
    
    .faq-hero p {
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
    
    .faq-container {
        max-width: 900px;
        margin: 0 auto;
    }
    
    .faq-category {
        margin-bottom: 3rem;
        animation: fadeIn 0.6s ease-out;
        animation-fill-mode: both;
    }
    
    .faq-category:nth-child(1) { animation-delay: 0.1s; }
    .faq-category:nth-child(2) { animation-delay: 0.2s; }
    .faq-category:nth-child(3) { animation-delay: 0.3s; }
    .faq-category:nth-child(4) { animation-delay: 0.4s; }
    .faq-category:nth-child(5) { animation-delay: 0.5s; }
    
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
    
    .faq-category-title {
        background: linear-gradient(135deg, #9D7FC7 0%, #8B6FC7 100%);
        color: white;
        padding: 1.5rem 2rem;
        border-radius: 12px;
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        box-shadow: 0 4px 15px rgba(157, 127, 199, 0.3);
    }
    
    .faq-category-title i {
        font-size: 1.8rem;
    }
    
    .faq-item {
        background: white;
        border-radius: 12px;
        margin-bottom: 1rem;
        box-shadow: 0 2px 10px rgba(157, 127, 199, 0.1);
        border: 1px solid rgba(157, 127, 199, 0.2);
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .faq-item:hover {
        box-shadow: 0 4px 20px rgba(157, 127, 199, 0.2);
        transform: translateY(-2px);
    }
    
    .faq-question {
        padding: 1.5rem 2rem;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-weight: 600;
        color: #555;
        transition: all 0.3s ease;
        user-select: none;
    }
    
    .faq-question:hover {
        color: #8B6FC7;
        background: linear-gradient(90deg, rgba(157, 127, 199, 0.05), transparent);
    }
    
    .faq-question.active {
        color: #8B6FC7;
        background: linear-gradient(90deg, rgba(157, 127, 199, 0.1), transparent);
    }
    
    .faq-question i {
        color: #9D7FC7;
        transition: transform 0.3s ease;
        font-size: 1.2rem;
    }
    
    .faq-question.active i {
        transform: rotate(180deg);
    }
    
    .faq-answer {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.4s ease, padding 0.4s ease;
        padding: 0 2rem;
        background: linear-gradient(135deg, #F5F0FA 0%, #E8DFF0 100%);
    }
    
    .faq-answer.active {
        max-height: 1000px;
        padding: 1.5rem 2rem;
    }
    
    .faq-answer p {
        color: #555;
        line-height: 1.8;
        margin: 0;
        font-size: 1.05rem;
    }
    
    .faq-answer ul {
        margin: 1rem 0;
        padding-left: 1.5rem;
    }
    
    .faq-answer ul li {
        color: #555;
        line-height: 1.8;
        margin-bottom: 0.5rem;
    }
    
    .faq-answer strong {
        color: #7A5FB8;
    }
    
    .contact-box {
        background: linear-gradient(135deg, #E8DFF0 0%, #D9CEE8 100%);
        border-radius: 16px;
        padding: 2.5rem;
        text-align: center;
        margin-top: 3rem;
        box-shadow: 0 4px 20px rgba(157, 127, 199, 0.15);
    }
    
    .contact-box h3 {
        color: #8B6FC7;
        font-weight: 700;
        margin-bottom: 1rem;
        font-size: 1.8rem;
    }
    
    .contact-box p {
        color: #555;
        margin-bottom: 1.5rem;
        font-size: 1.1rem;
    }
    
    .contact-box a {
        display: inline-block;
        background: linear-gradient(135deg, #9D7FC7 0%, #8B6FC7 100%);
        color: white;
        padding: 12px 30px;
        border-radius: 50px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(157, 127, 199, 0.3);
    }
    
    .contact-box a:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(157, 127, 199, 0.4);
    }
    
    .search-box {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        margin-bottom: 3rem;
        box-shadow: 0 4px 20px rgba(157, 127, 199, 0.1);
        border: 1px solid rgba(157, 127, 199, 0.2);
    }
    
    .search-box input {
        border: 2px solid rgba(157, 127, 199, 0.3);
        border-radius: 50px;
        padding: 12px 20px;
        width: 100%;
        font-size: 1rem;
        transition: all 0.3s ease;
    }
    
    .search-box input:focus {
        outline: none;
        border-color: #9D7FC7;
        box-shadow: 0 0 0 0.2rem rgba(157, 127, 199, 0.25);
    }
    
    @media (max-width: 768px) {
        .faq-hero h1 {
            font-size: 2rem;
        }
        
        .faq-hero p {
            font-size: 1rem;
        }
        
        .faq-question {
            padding: 1.25rem 1.5rem;
            font-size: 0.95rem;
        }
        
        .faq-answer.active {
            padding: 1.25rem 1.5rem;
        }
        
        .faq-category-title {
            padding: 1.25rem 1.5rem;
            font-size: 1.3rem;
        }
    }
</style>

<!-- Hero Section -->
<div class="faq-hero">
    <div class="container">
        <div class="text-center">
            <h1><i class="fas fa-question-circle me-3"></i>Sıkça Sorulan Sorular</h1>
            <p>Merak ettiğiniz soruların yanıtlarını burada bulabilirsiniz</p>
        </div>
    </div>
</div>

<!-- Content -->
<div class="container faq-container mb-5">
    <!-- Search Box -->
    <div class="search-box">
        <div class="input-group">
            <span class="input-group-text" style="background: linear-gradient(135deg, #9D7FC7 0%, #8B6FC7 100%); color: white; border: none; border-radius: 50px 0 0 50px;">
                <i class="fas fa-search"></i>
            </span>
            <input type="text" id="faq-search" class="form-control" placeholder="Sorularınızı arayın..." style="border-left: none; border-radius: 0 50px 50px 0;">
        </div>
    </div>

    <!-- Sipariş ve Teslimat -->
    <div class="faq-category">
        <div class="faq-category-title">
            <i class="fas fa-shopping-cart"></i>
            <span>Sipariş ve Teslimat</span>
        </div>
        
        <div class="faq-item">
            <div class="faq-question" onclick="toggleFaq(this)">
                <span>Siparişimi nasıl verebilirim?</span>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="faq-answer">
                <p>
                    Sipariş vermek için öncelikle ürünleri sepete eklemeniz gerekmektedir. Sepetinize eklediğiniz 
                    ürünleri gözden geçirdikten sonra "Sepete Git" butonuna tıklayarak ödeme sayfasına yönlendirilirsiniz. 
                    Ödeme sayfasında teslimat adresinizi seçip ödeme yönteminizi belirleyerek siparişinizi tamamlayabilirsiniz.
                </p>
            </div>
        </div>
        
        <div class="faq-item">
            <div class="faq-question" onclick="toggleFaq(this)">
                <span>Siparişim ne zaman teslim edilir?</span>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="faq-answer">
                <p>
                    Siparişleriniz genellikle <strong>1-3 iş günü</strong> içinde kargoya verilir. Kargo süresi 
                    teslimat adresinize göre değişiklik gösterebilir. İstanbul içi teslimatlar genellikle 1-2 iş günü, 
                    diğer şehirlere teslimatlar ise 2-5 iş günü sürmektedir.
                </p>
                <p>
                    Sipariş durumunuzu "Siparişlerim" sayfasından takip edebilirsiniz.
                </p>
            </div>
        </div>
        
        <div class="faq-item">
            <div class="faq-question" onclick="toggleFaq(this)">
                <span>Kargo ücreti ne kadar?</span>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="faq-answer">
                <p>
                    <strong>150 TL ve üzeri</strong> siparişlerde kargo ücretsizdir. 150 TL altındaki siparişlerde 
                    kargo ücreti <strong>25 TL</strong>'dir. Kampanya dönemlerinde kargo ücretsiz tutarı değişiklik 
                    gösterebilir.
                </p>
            </div>
        </div>
        
        <div class="faq-item">
            <div class="faq-question" onclick="toggleFaq(this)">
                <span>Siparişimi nasıl takip edebilirim?</span>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="faq-answer">
                <p>
                    Sipariş takibi için hesabınıza giriş yaparak "Siparişlerim" bölümünden sipariş durumunuzu 
                    görebilirsiniz. Siparişiniz kargoya verildiğinde size SMS ve e-posta ile kargo takip numarası 
                    gönderilir.
                </p>
            </div>
        </div>
    </div>

    <!-- Ödeme -->
    <div class="faq-category">
        <div class="faq-category-title">
            <i class="fas fa-credit-card"></i>
            <span>Ödeme</span>
        </div>
        
        <div class="faq-item">
            <div class="faq-question" onclick="toggleFaq(this)">
                <span>Hangi ödeme yöntemlerini kabul ediyorsunuz?</span>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="faq-answer">
                <p>
                    Aşağıdaki ödeme yöntemlerini kabul ediyoruz:
                </p>
                <ul>
                    <li><strong>Kredi Kartı:</strong> Visa, MasterCard, Troy (3D Secure ile güvenli ödeme)</li>
                    <li><strong>Havale/EFT:</strong> Banka hesaplarımıza havale yapabilirsiniz</li>
                </ul>
                <p>
                    Tüm ödemeler SSL şifreleme ile güvenli bir şekilde işlenmektedir.
                </p>
            </div>
        </div>
        
        <div class="faq-item">
            <div class="faq-question" onclick="toggleFaq(this)">
                <span>Ödeme bilgilerim güvende mi?</span>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="faq-answer">
                <p>
                    Evet, ödeme bilgileriniz tamamen güvendedir. Tüm ödemeler <strong>3D Secure</strong> protokolü 
                    ile işlenmektedir. Kart bilgileriniz sistemimizde saklanmamaktadır. Ödeme işlemleri güvenli 
                    ödeme sistemleri üzerinden gerçekleştirilmektedir.
                </p>
            </div>
        </div>
        
        <div class="faq-item">
            <div class="faq-question" onclick="toggleFaq(this)">
                <span>Havale ile ödeme yaparsam ne zaman onaylanır?</span>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="faq-answer">
                <p>
                    Havale/EFT ile ödeme yaptığınızda, ödemenizin banka tarafından onaylanması genellikle 
                    <strong>1-2 iş günü</strong> sürmektedir. Ödeme onaylandıktan sonra siparişiniz hazırlanmaya 
                    başlanır.
                </p>
            </div>
        </div>
    </div>

    <!-- İade ve Değişim -->
    <div class="faq-category">
        <div class="faq-category-title">
            <i class="fas fa-undo"></i>
            <span>İade ve Değişim</span>
        </div>
        
        <div class="faq-item">
            <div class="faq-question" onclick="toggleFaq(this)">
                <span>Ürünü nasıl iade edebilirim?</span>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="faq-answer">
                <p>
                    Ürün iadesi için aşağıdaki adımları takip edebilirsiniz:
                </p>
                <ul>
                    <li>Hesabınıza giriş yapın ve "Siparişlerim" bölümüne gidin</li>
                    <li>İade etmek istediğiniz siparişi seçin</li>
                    <li>"İade Talep Et" butonuna tıklayın</li>
                    <li>İade nedeninizi belirtin</li>
                    <li>Onaylandıktan sonra ürünü kargo ile gönderin</li>
                </ul>
                <p>
                    <strong>Not:</strong> Ürünler orijinal ambalajında, kullanılmamış ve etiketli olmalıdır.
                </p>
            </div>
        </div>
        
        <div class="faq-item">
            <div class="faq-question" onclick="toggleFaq(this)">
                <span>İade süresi ne kadar?</span>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="faq-answer">
                <p>
                    Ürünlerinizi <strong>14 gün</strong> içinde iade edebilirsiniz. İade süresi, ürünün size 
                    teslim edildiği tarihten itibaren başlar.
                </p>
            </div>
        </div>
        
        <div class="faq-item">
            <div class="faq-question" onclick="toggleFaq(this)">
                <span>İade ücreti kim öder?</span>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="faq-answer">
                <p>
                    Ürün hatası veya yanlış ürün gönderilmesi durumunda iade kargo ücreti tarafımızca karşılanır. 
                    Müşteri kaynaklı iadelerde kargo ücreti müşteriye aittir.
                </p>
            </div>
        </div>
        
        <div class="faq-item">
            <div class="faq-question" onclick="toggleFaq(this)">
                <span>İade parası ne zaman iade edilir?</span>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="faq-answer">
                <p>
                    İade edilen ürün kontrol edildikten sonra, ödemeniz <strong>3-5 iş günü</strong> içinde 
                    aynı ödeme yöntemiyle iade edilir.
                </p>
            </div>
        </div>
    </div>

    <!-- Hesap ve Üyelik -->
    <div class="faq-category">
        <div class="faq-category-title">
            <i class="fas fa-user"></i>
            <span>Hesap ve Üyelik</span>
        </div>
        
        <div class="faq-item">
            <div class="faq-question" onclick="toggleFaq(this)">
                <span>Nasıl üye olabilirim?</span>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="faq-answer">
                <p>
                    Üye olmak için sağ üst köşedeki "Kayıt Ol" butonuna tıklayın. Gerekli bilgileri doldurarak 
                    hesabınızı oluşturabilirsiniz. Üyelik tamamen ücretsizdir.
                </p>
            </div>
        </div>
        
        <div class="faq-item">
            <div class="faq-question" onclick="toggleFaq(this)">
                <span>Şifremi unuttum, ne yapmalıyım?</span>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="faq-answer">
                <p>
                    Giriş sayfasında "Şifremi Unuttum" linkine tıklayarak e-posta adresinize şifre sıfırlama 
                    bağlantısı gönderebilirsiniz. E-postanızı kontrol ederek yeni şifrenizi oluşturabilirsiniz.
                </p>
            </div>
        </div>
        
        <div class="faq-item">
            <div class="faq-question" onclick="toggleFaq(this)">
                <span>Üye olmadan alışveriş yapabilir miyim?</span>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="faq-answer">
                <p>
                    Üye olmadan da alışveriş yapabilirsiniz, ancak üye olmanız durumunda sipariş takibi, 
                    favori ürünler, indirim kuponları ve özel kampanyalardan yararlanabilirsiniz.
                </p>
            </div>
        </div>
    </div>

    <!-- Genel Sorular -->
    <div class="faq-category">
        <div class="faq-category-title">
            <i class="fas fa-info-circle"></i>
            <span>Genel Sorular</span>
        </div>
        
        <div class="faq-item">
            <div class="faq-question" onclick="toggleFaq(this)">
                <span>Kampanya ve indirimlerden nasıl haberdar olabilirim?</span>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="faq-answer">
                <p>
                    Kampanya ve indirimlerden haberdar olmak için:
                </p>
                <ul>
                    <li>E-posta bildirimlerine abone olabilirsiniz</li>
                    <li>Sosyal medya hesaplarımızı takip edebilirsiniz</li>
                    <li>Ana sayfamızdaki kampanya bölümlerini inceleyebilirsiniz</li>
                </ul>
            </div>
        </div>
        
        <div class="faq-item">
            <div class="faq-question" onclick="toggleFaq(this)">
                <span>Ürün stokta yoksa ne olur?</span>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="faq-answer">
                <p>
                    Stokta olmayan ürünler için "Stokta Yok" uyarısı gösterilir. Bu ürünler için "Stokta Olunca 
                    Haber Ver" seçeneğini kullanarak stok geldiğinde bilgilendirilebilirsiniz.
                </p>
            </div>
        </div>
        
        <div class="faq-item">
            <div class="faq-question" onclick="toggleFaq(this)">
                <span>Kargo takip numarasını nereden bulabilirim?</span>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="faq-answer">
                <p>
                    Kargo takip numaranız siparişiniz kargoya verildiğinde e-posta ve SMS ile size gönderilir. 
                    Ayrıca "Siparişlerim" sayfasından sipariş detaylarınıza girerek kargo takip numarasını 
                    görebilirsiniz.
                </p>
            </div>
        </div>
        
        <div class="faq-item">
            <div class="faq-question" onclick="toggleFaq(this)">
                <span>Favori ürünlerimi nasıl ekleyebilirim?</span>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="faq-answer">
                <p>
                    Ürün kartlarında veya ürün detay sayfasında bulunan kalp ikonuna tıklayarak ürünleri 
                    favorilerinize ekleyebilirsiniz. Favori ürünlerinize "Favorilerim" sayfasından ulaşabilirsiniz.
                </p>
            </div>
        </div>
    </div>

    <!-- İletişim Kutusu -->
    <div class="contact-box">
        <h3><i class="fas fa-headset me-2"></i>Cevabını Bulamadınız mı?</h3>
        <p>Size yardımcı olmaktan mutluluk duyarız. Bizimle iletişime geçin!</p>
        <a href="mailto:info@eticaret.com">
            <i class="fas fa-envelope me-2"></i>Bize Yazın
        </a>
    </div>
</div>

<script>
    function toggleFaq(element) {
        const faqItem = element.closest('.faq-item');
        const faqAnswer = faqItem.querySelector('.faq-answer');
        const isActive = faqAnswer.classList.contains('active');
        
        // Close all other FAQs
        document.querySelectorAll('.faq-answer').forEach(answer => {
            answer.classList.remove('active');
        });
        document.querySelectorAll('.faq-question').forEach(question => {
            question.classList.remove('active');
        });
        
        // Toggle current FAQ
        if (!isActive) {
            faqAnswer.classList.add('active');
            element.classList.add('active');
        }
    }
    
    // Search functionality
    document.getElementById('faq-search').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const faqItems = document.querySelectorAll('.faq-item');
        
        faqItems.forEach(item => {
            const question = item.querySelector('.faq-question span').textContent.toLowerCase();
            const answer = item.querySelector('.faq-answer').textContent.toLowerCase();
            
            if (question.includes(searchTerm) || answer.includes(searchTerm)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
        
        // Hide categories with no visible items
        document.querySelectorAll('.faq-category').forEach(category => {
            const visibleItems = category.querySelectorAll('.faq-item[style="display: block;"], .faq-item:not([style*="display: none"])');
            if (visibleItems.length === 0 && searchTerm !== '') {
                category.style.display = 'none';
            } else {
                category.style.display = 'block';
            }
        });
    });
</script>

<?php
include 'footer.php';
?>

