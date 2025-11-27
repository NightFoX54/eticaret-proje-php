    </div><!-- Main container end -->

    <!-- Footer -->
    <footer class="footer-custom text-white mt-5">
        <style>
            .footer-custom {
                background: linear-gradient(135deg, #7A5FB8 0%, #8B6FC7 50%, #9D7FC7 100%);
                position: relative;
                overflow: hidden;
                animation: fadeInUp 0.8s ease-out;
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
            
            .footer-custom::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 1px;
                background: linear-gradient(90deg, transparent, rgba(255,255,255,0.5), transparent);
                animation: shimmer 3s infinite;
            }
            
            @keyframes shimmer {
                0% {
                    transform: translateX(-100%);
                }
                100% {
                    transform: translateX(100%);
                }
            }
            
            .footer-custom .container {
                position: relative;
                z-index: 1;
            }
            
            .footer-section {
                padding: 3rem 0 2rem;
            }
            
            .footer-section h5 {
                font-weight: 700;
                font-size: 1.25rem;
                margin-bottom: 1.5rem;
                position: relative;
                display: inline-block;
                padding-bottom: 0.75rem;
            }
            
            .footer-section h5::after {
                content: '';
                position: absolute;
                bottom: 0;
                left: 0;
                width: 50px;
                height: 3px;
                background: linear-gradient(90deg, rgba(255,255,255,0.8), transparent);
                border-radius: 2px;
                animation: expandLine 0.6s ease-out;
            }
            
            @keyframes expandLine {
                from {
                    width: 0;
                }
                to {
                    width: 50px;
                }
            }
            
            .footer-section ul {
                list-style: none;
                padding: 0;
            }
            
            .footer-section ul li {
                margin-bottom: 0.75rem;
                transition: all 0.3s ease;
            }
            
            .footer-section ul li a {
                color: rgba(255, 255, 255, 0.9);
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                transition: all 0.3s ease;
                position: relative;
                padding-left: 0;
            }
            
            .footer-section ul li a::before {
                content: '→';
                position: absolute;
                left: -20px;
                opacity: 0;
                transition: all 0.3s ease;
                color: rgba(255, 255, 255, 0.6);
            }
            
            .footer-section ul li:hover a {
                color: white;
                padding-left: 20px;
                transform: translateX(5px);
            }
            
            .footer-section ul li:hover a::before {
                opacity: 1;
                left: 0;
            }
            
            .footer-section ul li i {
                width: 20px;
                margin-right: 10px;
                transition: all 0.3s ease;
            }
            
            .footer-section ul li:hover i {
                transform: scale(1.2);
                color: rgba(255, 255, 255, 1);
            }
            
            .social-links {
                display: flex;
                gap: 1rem;
                margin-top: 1.5rem;
            }
            
            .social-links a {
                width: 45px;
                height: 45px;
                display: flex;
                align-items: center;
                justify-content: center;
                background: rgba(255, 255, 255, 0.1);
                border-radius: 50%;
                color: white;
                text-decoration: none;
                transition: all 0.3s ease;
                backdrop-filter: blur(10px);
                border: 2px solid rgba(255, 255, 255, 0.2);
            }
            
            .social-links a:hover {
                background: rgba(255, 255, 255, 0.2);
                transform: translateY(-5px) scale(1.1);
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
                border-color: rgba(255, 255, 255, 0.4);
            }
            
            .social-links a i {
                font-size: 1.2rem;
                transition: transform 0.3s ease;
            }
            
            .social-links a:hover i {
                transform: rotate(360deg);
            }
            
            .footer-about p {
                color: rgba(255, 255, 255, 0.85);
                line-height: 1.8;
                font-size: 0.95rem;
            }
            
            .footer-copyright {
                background: rgba(0, 0, 0, 0.25);
                padding: 1.5rem 0;
                border-top: 1px solid rgba(255, 255, 255, 0.1);
                backdrop-filter: blur(10px);
            }
            
            .footer-copyright p {
                margin: 0;
                color: rgba(255, 255, 255, 0.9);
                font-size: 0.9rem;
            }
            
            .payment-icons {
                display: flex;
                gap: 0.75rem;
                justify-content: center;
                align-items: center;
            }
            
            .payment-icons img {
                height: 30px;
                opacity: 0.9;
                transition: all 0.3s ease;
                background: rgba(255, 255, 255, 0.2);
                padding: 5px 10px;
                border-radius: 4px;
            }
            
            .payment-icons img:hover {
                opacity: 1;
                transform: scale(1.1);
                background: rgba(255, 255, 255, 0.3);
            }
            
            .footer-section .contact-item {
                display: flex;
                align-items: flex-start;
                margin-bottom: 1rem;
                transition: all 0.3s ease;
            }
            
            .footer-section .contact-item:hover {
                transform: translateX(5px);
            }
            
            .footer-section .contact-item i {
                width: 25px;
                margin-right: 12px;
                margin-top: 3px;
                color: rgba(255, 255, 255, 0.9);
                transition: all 0.3s ease;
            }
            
            .footer-section .contact-item:hover i {
                color: white;
                transform: scale(1.2);
            }
            
            .footer-section .contact-item span {
                color: rgba(255, 255, 255, 0.9);
                transition: color 0.3s ease;
            }
            
            .footer-section .contact-item:hover span {
                color: white;
            }
            
            @media (max-width: 768px) {
                .footer-section {
                    padding: 2rem 0 1.5rem;
                    text-align: center;
                }
                
                .footer-section h5::after {
                    left: 50%;
                    transform: translateX(-50%);
                }
                
                .social-links {
                    justify-content: center;
                }
                
                .footer-section ul li a::before {
                    display: none;
                }
                
                .footer-section ul li:hover a {
                    padding-left: 0;
                }
            }
        </style>
        
        <div class="footer-section">
            <div class="container">
                <div class="row">
                    <!-- About Us -->
                    <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                        <h5><i class="fas fa-store me-2"></i>Hakkımızda</h5>
                        <div class="footer-about">
                            <p>
                                E-Ticaret olarak müşterilerimize en kaliteli ürünleri en uygun fiyatlarla sunmak için çalışıyoruz. Güvenilir alışveriş deneyimi için yanınızdayız.
                            </p>
                            <div class="social-links">
                                <a href="https://www.facebook.com" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                                <a href="https://www.twitter.com" target="_blank" rel="noopener noreferrer" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                                <a href="https://www.instagram.com" target="_blank" rel="noopener noreferrer" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                                <a href="https://www.youtube.com" target="_blank" rel="noopener noreferrer" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Links -->
                    <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                        <h5><i class="fas fa-link me-2"></i>Hızlı Erişim</h5>
                        <ul>
                            <li><a href="index.php"><i class="fas fa-home"></i> Anasayfa</a></li>
                            <li><a href="urunler.php"><i class="fas fa-box"></i> Ürünler</a></li>
                            <li><a href="urunler.php"><i class="fas fa-tags"></i> Kampanyalar</a></li>
                            <li><a href="urunler.php"><i class="fas fa-star"></i> Yeni Gelenler</a></li>
                            <li><a href="urunler.php"><i class="fas fa-fire"></i> En Çok Satanlar</a></li>
                        </ul>
                    </div>
                    
                    <!-- Customer Service -->
                    <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                        <h5><i class="fas fa-headset me-2"></i>Müşteri Hizmetleri</h5>
                        <ul>
                            <li><a href="#"><i class="fas fa-question-circle"></i> Yardım Merkezi</a></li>
                            <li><a href="orders.php"><i class="fas fa-shipping-fast"></i> Sipariş Takibi</a></li>
                            <li><a href="#"><i class="fas fa-undo"></i> İade ve Değişim</a></li>
                            <li><a href="gizlilik-politikasi.php"><i class="fas fa-shield-alt"></i> Gizlilik Politikası</a></li>
                            <li><a href="sikca-sorulan-sorular.php"><i class="fas fa-comments"></i> Sıkça Sorulan Sorular</a></li>
                        </ul>
                    </div>
                    
                    <!-- Contact Us -->
                    <div class="col-lg-3 col-md-6">
                        <h5><i class="fas fa-envelope me-2"></i>İletişim</h5>
                        <ul class="list-unstyled">
                            <li class="contact-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>İstanbul, Türkiye</span>
                            </li>
                            <li class="contact-item">
                                <i class="fas fa-envelope"></i>
                                <span>info@eticaret.com</span>
                            </li>
                            <li class="contact-item">
                                <i class="fas fa-phone"></i>
                                <span>+90 555 123 45 67</span>
                            </li>
                            <li class="contact-item">
                                <i class="fas fa-fax"></i>
                                <span>+90 216 123 45 67</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Copyright -->
        <div class="footer-copyright">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                        <p class="mb-0">
                            <i class="fas fa-copyright me-1"></i> 2024 E-Ticaret. Tüm hakları saklıdır.
                        </p>
                    </div>
                    <div class="col-md-6 text-center text-md-end">
                        <div class="payment-icons">
                            <img src="https://dummyimage.com/60x35/ffffff/9D7FC7&text=VISA" alt="Visa" title="Visa" style="background: white; color: #9D7FC7;">
                            <img src="https://dummyimage.com/60x35/ffffff/9D7FC7&text=MC" alt="MasterCard" title="MasterCard" style="background: white; color: #9D7FC7;">
                            <img src="https://dummyimage.com/60x35/ffffff/9D7FC7&text=PP" alt="PayPal" title="PayPal" style="background: white; color: #9D7FC7;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 