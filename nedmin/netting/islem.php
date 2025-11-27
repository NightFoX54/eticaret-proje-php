<?php
ob_start();
session_start();
include 'baglan.php';
require_once '../../vendor/autoload.php';



// Kullanıcı Giriş İşlemi
if (isset($_POST['islem']) && $_POST['islem'] == 'admin_giris') {
    $mail = htmlspecialchars(trim($_POST['mail']));
    $sifre = trim($_POST['sifre']);
    
    // Veritabanından kullanıcıyı sorgula
    $kullanicisor = $db->prepare("SELECT * FROM kullanicilar WHERE mail=:mail");
    $kullanicisor->execute([
        'mail' => $mail
    ]);
    
    $say = $kullanicisor->rowCount();
    $kullanicicek = $kullanicisor->fetch(PDO::FETCH_ASSOC);
    
    // Kullanıcı var mı ve şifre doğru mu kontrol et
    if ($say > 0 && password_verify($sifre, $kullanicicek['sifre'])) {
        $_SESSION['kullanici_id'] = $kullanicicek['id'];
        $_SESSION['kullanici_mail'] = $kullanicicek['mail'];
        $_SESSION['kullanici_ad'] = $kullanicicek['ad'];
        $_SESSION['kullanici_soyad'] = $kullanicicek['soyad'];
        $_SESSION['kullanici_rol'] = $kullanicicek['rol'];
        
        // Sadece admin ve moderator rolüne sahip kullanıcılar giriş yapabilir
        if ($kullanicicek['rol'] == 'admin' || $kullanicicek['rol'] == 'moderator') {
            $response = [
                'durum' => 'success',
                'mesaj' => 'Giriş başarılı. Yönlendiriliyorsunuz...'
            ];
        } else {
            $response = [
                'durum' => 'error',
                'mesaj' => 'Yönetim paneline erişim yetkiniz bulunmamaktadır.'
            ];
            session_destroy();
        }
    } else {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Kullanıcı adı veya şifre hatalı!'
        ];
    }
    
    // AJAX isteği için JSON yanıtı döndür
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Tüm kategorileri getir (AJAX için)
if (isset($_GET['islem']) && $_GET['islem'] == 'kategori_getir') {
    $exclude_id = isset($_GET['exclude_id']) ? intval($_GET['exclude_id']) : null;
    
    try {
        // Tüm kategorileri getir
        $kategori_sorgu = "SELECT id, kategori_isim, parent_kategori_id FROM urun_kategoriler ORDER BY COALESCE(parent_kategori_id, 0), kategori_isim ASC";
        $kategori_sor = $db->prepare($kategori_sorgu);
        $kategori_sor->execute();
        
        $kategoriler = array();
        
        while ($kategori = $kategori_sor->fetch(PDO::FETCH_ASSOC)) {
            // Eğer exclude_id belirtilmişse ve bu kategori exclude_id ise, bu kategoriyi listeden çıkar
            if ($exclude_id && $kategori['id'] == $exclude_id) {
                continue;
            }
            $kategoriler[] = $kategori;
        }
        
        $response = [
            'durum' => 'success',
            'kategoriler' => $kategoriler
        ];
    } catch (PDOException $e) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Kategoriler getirilirken bir hata oluştu: ' . $e->getMessage()
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Kategori detaylarını getir
if (isset($_GET['islem']) && $_GET['islem'] == 'kategori_detay') {
    $kategori_id = intval($_GET['kategori_id']);
    
    try {
        $kategori_sor = $db->prepare("SELECT * FROM urun_kategoriler WHERE id = :id");
        $kategori_sor->execute(['id' => $kategori_id]);
        $kategori = $kategori_sor->fetch(PDO::FETCH_ASSOC);
        
        if ($kategori) {
            $response = [
                'durum' => 'success',
                'kategori' => $kategori
            ];
        } else {
            $response = [
                'durum' => 'error',
                'mesaj' => 'Kategori bulunamadı'
            ];
        }
    } catch (PDOException $e) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Veritabanı hatası: ' . $e->getMessage()
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Kategori Ekleme İşlemi
if (isset($_POST['islem']) && $_POST['islem'] == 'kategori_ekle') {
    $kategori_isim = htmlspecialchars(trim($_POST['kategori_isim']));
    $parent_kategori_id = isset($_POST['parent_kategori_id']) && $_POST['parent_kategori_id'] != "" ? intval($_POST['parent_kategori_id']) : null;
    
    try {
        $kategori_ekle = $db->prepare("INSERT INTO urun_kategoriler SET 
            kategori_isim=:kategori_isim, 
            parent_kategori_id=:parent_kategori_id");
            
        $sonuc = $kategori_ekle->execute([
            'kategori_isim' => $kategori_isim,
            'parent_kategori_id' => $parent_kategori_id
        ]);
        
        if ($sonuc) {
            $response = [
                'durum' => 'success',
                'mesaj' => 'Kategori başarıyla eklendi.',
                'kategori_id' => $db->lastInsertId()
            ];
        } else {
            $response = [
                'durum' => 'error',
                'mesaj' => 'Kategori eklenirken bir hata oluştu.'
            ];
        }
    } catch (PDOException $e) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Veritabanı hatası: ' . $e->getMessage()
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Kategori Güncelleme İşlemi
if (isset($_POST['islem']) && $_POST['islem'] == 'kategori_guncelle') {
    $kategori_id = intval($_POST['kategori_id']);
    $kategori_isim = htmlspecialchars(trim($_POST['kategori_isim']));
    $parent_kategori_id = isset($_POST['parent_kategori_id']) && $_POST['parent_kategori_id'] != "" ? intval($_POST['parent_kategori_id']) : null;
    
    // Kendi kendine parent olma durumunu kontrol et
    if ($kategori_id == $parent_kategori_id) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Bir kategori kendisini üst kategori olarak alamaz!'
        ];
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    // Alt kategori döngüsünü kontrol et
    if ($parent_kategori_id) {
        $current_parent_id = $parent_kategori_id;
        $visited_ids = array($kategori_id);
        
        while ($current_parent_id) {
            if (in_array($current_parent_id, $visited_ids)) {
                $response = [
                    'durum' => 'error',
                    'mesaj' => 'Kategori hiyerarşisinde döngü oluşturulamaz!'
                ];
                
                header('Content-Type: application/json');
                echo json_encode($response);
                exit;
            }
            
            $visited_ids[] = $current_parent_id;
            
            $parent_check = $db->prepare("SELECT parent_kategori_id FROM urun_kategoriler WHERE id = :id");
            $parent_check->execute(['id' => $current_parent_id]);
            $parent_data = $parent_check->fetch(PDO::FETCH_ASSOC);
            
            $current_parent_id = $parent_data ? $parent_data['parent_kategori_id'] : null;
        }
    }
    
    try {
        $kategori_guncelle = $db->prepare("UPDATE urun_kategoriler SET 
            kategori_isim=:kategori_isim, 
            parent_kategori_id=:parent_kategori_id 
            WHERE id=:kategori_id");
            
        $sonuc = $kategori_guncelle->execute([
            'kategori_isim' => $kategori_isim,
            'parent_kategori_id' => $parent_kategori_id,
            'kategori_id' => $kategori_id
        ]);
        
        if ($sonuc) {
            $response = [
                'durum' => 'success',
                'mesaj' => 'Kategori başarıyla güncellendi.'
            ];
        } else {
            $response = [
                'durum' => 'error',
                'mesaj' => 'Kategori güncellenirken bir hata oluştu.'
            ];
        }
    } catch (PDOException $e) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Veritabanı hatası: ' . $e->getMessage()
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Kategori Silme İşlemi
if (isset($_POST['islem']) && $_POST['islem'] == 'kategori_sil') {
    $kategori_id = intval($_POST['kategori_id']);
    
    try {
        // Önce bu kategoriye bağlı alt kategorileri kontrol et
        $alt_kategori_kontrol = $db->prepare("SELECT COUNT(*) as adet FROM urun_kategoriler WHERE parent_kategori_id=:kategori_id");
        $alt_kategori_kontrol->execute(['kategori_id' => $kategori_id]);
        $alt_kategori_say = $alt_kategori_kontrol->fetch(PDO::FETCH_ASSOC)['adet'];
        
        if ($alt_kategori_say > 0) {
            $response = [
                'durum' => 'error',
                'mesaj' => 'Bu kategoriye bağlı alt kategoriler bulunmaktadır. Önce alt kategorileri silmelisiniz.'
            ];
        } else {
            // Kategoriye bağlı ürünleri kontrol et
            $urun_kontrol = $db->prepare("SELECT COUNT(*) as adet FROM urun_kategori_baglantisi WHERE kategori_id=:kategori_id");
            $urun_kontrol->execute(['kategori_id' => $kategori_id]);
            $urun_say = $urun_kontrol->fetch(PDO::FETCH_ASSOC)['adet'];
            
            if ($urun_say > 0) {
                // Kategori-ürün bağlantılarını da sil
                $baglanti_sil = $db->prepare("DELETE FROM urun_kategori_baglantisi WHERE kategori_id=:kategori_id");
                $baglanti_sil->execute(['kategori_id' => $kategori_id]);
            }
            
            // Kategoriyi sil
            $kategori_sil = $db->prepare("DELETE FROM urun_kategoriler WHERE id=:kategori_id");
            $sonuc = $kategori_sil->execute(['kategori_id' => $kategori_id]);
            
            if ($sonuc) {
                $response = [
                    'durum' => 'success',
                    'mesaj' => 'Kategori başarıyla silindi.'
                ];
            } else {
                $response = [
                    'durum' => 'error',
                    'mesaj' => 'Kategori silinirken bir hata oluştu.'
                ];
            }
        }
    } catch (PDOException $e) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Veritabanı hatası: ' . $e->getMessage()
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Kupon Ekleme İşlemi
if (isset($_POST['islem']) && $_POST['islem'] == 'kupon_ekle') {
    $kupon_kod = strtoupper(htmlspecialchars(trim($_POST['kupon_kod'])));
    $kupon_tur = $_POST['kupon_tur'] == 'yuzde' ? 'yuzde' : 'sabit';
    $kupon_indirim = floatval($_POST['kupon_indirim']);
    $kupon_min_tutar = floatval($_POST['kupon_min_tutar']);
    $kupon_limit = !empty($_POST['kupon_limit']) ? intval($_POST['kupon_limit']) : null;
    $kupon_baslangic_tarih = $_POST['kupon_baslangic_tarih'];
    $kupon_bitis_tarih = $_POST['kupon_bitis_tarih'];
    
    // Validasyon kontrolleri
    if (empty($kupon_kod)) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Kupon kodu boş olamaz!'
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    if ($kupon_indirim <= 0) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'İndirim miktarı sıfırdan büyük olmalıdır!'
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    if ($kupon_tur == 'yuzde' && $kupon_indirim > 100) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Yüzde indirim 100\'den büyük olamaz!'
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    // Kod benzersiz mi kontrol et
    try {
        $kontrol = $db->prepare("SELECT COUNT(*) as adet FROM kupon WHERE kupon_kod = :kupon_kod");
        $kontrol->execute(['kupon_kod' => $kupon_kod]);
        $say = $kontrol->fetch(PDO::FETCH_ASSOC)['adet'];
        
        if ($say > 0) {
            $response = [
                'durum' => 'error',
                'mesaj' => 'Bu kupon kodu zaten kullanılıyor!'
            ];
        } else {
            $ekle = $db->prepare("INSERT INTO kupon SET 
                kupon_kod = :kupon_kod,
                kupon_tur = :kupon_tur,
                kupon_indirim = :kupon_indirim,
                kupon_min_tutar = :kupon_min_tutar,
                kupon_limit = :kupon_limit,
                kupon_baslangic_tarih = :kupon_baslangic_tarih,
                kupon_bitis_tarih = :kupon_bitis_tarih");
                
            $sonuc = $ekle->execute([
                'kupon_kod' => $kupon_kod,
                'kupon_tur' => $kupon_tur,
                'kupon_indirim' => $kupon_indirim,
                'kupon_min_tutar' => $kupon_min_tutar,
                'kupon_limit' => $kupon_limit,
                'kupon_baslangic_tarih' => $kupon_baslangic_tarih,
                'kupon_bitis_tarih' => $kupon_bitis_tarih
            ]);
            
            if ($sonuc) {
                $response = [
                    'durum' => 'success',
                    'mesaj' => 'Kupon başarıyla eklendi.',
                    'kupon_id' => $db->lastInsertId()
                ];
            } else {
                $response = [
                    'durum' => 'error',
                    'mesaj' => 'Kupon eklenirken bir hata oluştu.'
                ];
            }
        }
    } catch (PDOException $e) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Veritabanı hatası: ' . $e->getMessage()
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Kupon Bilgilerini Getir
if (isset($_GET['islem']) && $_GET['islem'] == 'kupon_getir') {
    $kupon_id = intval($_GET['kupon_id']);
    
    try {
        $kupon_sor = $db->prepare("SELECT * FROM kupon WHERE id = :id");
        $kupon_sor->execute(['id' => $kupon_id]);
        $kupon = $kupon_sor->fetch(PDO::FETCH_ASSOC);
        
        if ($kupon) {
            $response = [
                'durum' => 'success',
                'kupon' => $kupon
            ];
        } else {
            $response = [
                'durum' => 'error',
                'mesaj' => 'Kupon bulunamadı!'
            ];
        }
    } catch (PDOException $e) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Veritabanı hatası: ' . $e->getMessage()
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Live Search
if (isset($_GET['islem']) && $_GET['islem'] == 'live_search') {
    $query = isset($_GET['query']) ? $_GET['query'] : '';
    
    if (empty($query) || strlen($query) < 2) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Arama sorgusu çok kısa!'
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    try {
        // Prepare search query
        $search_term = "%{$query}%";
        
        // Join with the most important tables to search in product names, descriptions, brand names and categories
        $search_sql = "
            SELECT DISTINCT u.id, u.urun_isim, u.urun_fiyat, 
                   um.marka_isim,
                   (SELECT uk.kategori_isim FROM urun_kategori_baglantisi ukb 
                    JOIN urun_kategoriler uk ON ukb.kategori_id = uk.id 
                    WHERE ukb.urun_id = u.id LIMIT 1) as kategori_isim,
                   (SELECT foto_path FROM urun_fotolar WHERE urun_id = u.id ORDER BY id ASC LIMIT 1) as foto_path,
                   (SELECT COUNT(*) FROM urun_ziyaret WHERE urun_id = u.id) as ziyaret_sayisi
            FROM urun u
            LEFT JOIN urun_markalar um ON u.urun_marka = um.id
            LEFT JOIN urun_kategori_baglantisi ukb ON u.id = ukb.urun_id
            LEFT JOIN urun_kategoriler uk ON ukb.kategori_id = uk.id
            WHERE 
                u.urun_isim LIKE :search_term OR
                u.urun_aciklama LIKE :search_term2 OR
                um.marka_isim LIKE :search_term3 OR
                uk.kategori_isim LIKE :search_term4
            GROUP BY u.id
            ORDER BY ziyaret_sayisi DESC
            LIMIT 5
        ";
        
        $stmt = $db->prepare($search_sql);
        $stmt->bindParam(':search_term', $search_term, PDO::PARAM_STR);
        $stmt->bindParam(':search_term2', $search_term, PDO::PARAM_STR);
        $stmt->bindParam(':search_term3', $search_term, PDO::PARAM_STR);
        $stmt->bindParam(':search_term4', $search_term, PDO::PARAM_STR);
        $stmt->execute();
        
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $response = [
            'durum' => 'success',
            'results' => $results
        ];
    } catch (PDOException $e) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Veritabanı hatası: ' . $e->getMessage()
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Kupon Güncelleme İşlemi
if (isset($_POST['islem']) && $_POST['islem'] == 'kupon_guncelle') {
    $kupon_id = intval($_POST['kupon_id']);
    $kupon_kod = strtoupper(htmlspecialchars(trim($_POST['kupon_kod'])));
    $kupon_tur = $_POST['kupon_tur'] == 'yuzde' ? 'yuzde' : 'sabit';
    $kupon_indirim = floatval($_POST['kupon_indirim']);
    $kupon_min_tutar = floatval($_POST['kupon_min_tutar']);
    $kupon_limit = !empty($_POST['kupon_limit']) ? intval($_POST['kupon_limit']) : null;
    $kupon_baslangic_tarih = $_POST['kupon_baslangic_tarih'];
    $kupon_bitis_tarih = $_POST['kupon_bitis_tarih'];
    
    // Validasyon kontrolleri
    if (empty($kupon_kod)) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Kupon kodu boş olamaz!'
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    if ($kupon_indirim <= 0) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'İndirim miktarı sıfırdan büyük olmalıdır!'
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    if ($kupon_tur == 'yuzde' && $kupon_indirim > 100) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Yüzde indirim 100\'den büyük olamaz!'
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    // Kod benzersiz mi kontrol et (mevcut kupon hariç)
    try {
        $kontrol = $db->prepare("SELECT COUNT(*) as adet FROM kupon WHERE kupon_kod = :kupon_kod AND id != :kupon_id");
        $kontrol->execute([
            'kupon_kod' => $kupon_kod,
            'kupon_id' => $kupon_id
        ]);
        $say = $kontrol->fetch(PDO::FETCH_ASSOC)['adet'];
        
        if ($say > 0) {
            $response = [
                'durum' => 'error',
                'mesaj' => 'Bu kupon kodu zaten kullanılıyor!'
            ];
        } else {
            $guncelle = $db->prepare("UPDATE kupon SET 
                kupon_kod = :kupon_kod,
                kupon_tur = :kupon_tur,
                kupon_indirim = :kupon_indirim,
                kupon_min_tutar = :kupon_min_tutar,
                kupon_limit = :kupon_limit,
                kupon_baslangic_tarih = :kupon_baslangic_tarih,
                kupon_bitis_tarih = :kupon_bitis_tarih
                WHERE id = :kupon_id");
                
            $sonuc = $guncelle->execute([
                'kupon_kod' => $kupon_kod,
                'kupon_tur' => $kupon_tur,
                'kupon_indirim' => $kupon_indirim,
                'kupon_min_tutar' => $kupon_min_tutar,
                'kupon_limit' => $kupon_limit,
                'kupon_baslangic_tarih' => $kupon_baslangic_tarih,
                'kupon_bitis_tarih' => $kupon_bitis_tarih,
                'kupon_id' => $kupon_id
            ]);
            
            if ($sonuc) {
                $response = [
                    'durum' => 'success',
                    'mesaj' => 'Kupon başarıyla güncellendi.'
                ];
            } else {
                $response = [
                    'durum' => 'error',
                    'mesaj' => 'Kupon güncellenirken bir hata oluştu.'
                ];
            }
        }
    } catch (PDOException $e) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Veritabanı hatası: ' . $e->getMessage()
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Kupon Silme İşlemi
if (isset($_POST['islem']) && $_POST['islem'] == 'kupon_sil') {
    $kupon_id = intval($_POST['kupon_id']);
    
    try {
        $sil = $db->prepare("DELETE FROM kupon WHERE id = :kupon_id");
        $sonuc = $sil->execute(['kupon_id' => $kupon_id]);
        
        if ($sonuc) {
            $response = [
                'durum' => 'success',
                'mesaj' => 'Kupon başarıyla silindi.'
            ];
        } else {
            $response = [
                'durum' => 'error',
                'mesaj' => 'Kupon silinirken bir hata oluştu.'
            ];
        }
    } catch (PDOException $e) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Veritabanı hatası: ' . $e->getMessage()
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Sipariş Detayı Getirme İşlemi
if (isset($_GET['islem']) && $_GET['islem'] == 'get_user_siparis_detay') {
    $siparis_id = intval($_GET['siparis_id']);
    
    try {
        // Sipariş bilgilerini getir
        $siparis_sql = "SELECT f.*, k.ad as musteri_ad, k.soyad as musteri_soyad, k.mail as musteri_mail, kp.kupon_kod, 
                            ka.adres_adi, ka.ad_soyad as teslimat_ad_soyad, ka.telefon as teslimat_telefon, 
                            ka.tc_no, ka.il, ka.ilce, ka.adres as teslimat_adres, ka.posta_kodu
                        FROM faturalar f
                        LEFT JOIN kullanicilar k ON f.kullanici_id = k.id
                        LEFT JOIN kupon kp ON f.kupon_id = kp.id
                        LEFT JOIN kullanici_adres ka ON f.adres_id = ka.id
                        WHERE f.id = :siparis_id";
        
        $siparis_stmt = $db->prepare($siparis_sql);
        $siparis_stmt->execute(['siparis_id' => $siparis_id]);
        $siparis = $siparis_stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$siparis) {
            $response = [
                'durum' => 'error',
                'mesaj' => 'Sipariş bulunamadı!'
            ];
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
        
        // Siparişteki ürünleri getir (secenek_id kullanarak) ve değerlendirme bilgilerini ekle
        $urunler_sql = "SELECT fu.*, u.urun_isim, u.id as urun_id,
                        GROUP_CONCAT(CONCAT(n.ad, ': ', nd.deger) SEPARATOR ', ') as varyasyon_bilgisi,
                        ud.id as degerlendirme_id, ud.puan, ud.yorum, ud.tarih as degerlendirme_tarih
                        FROM fatura_urunler fu
                        LEFT JOIN urun_secenek s ON fu.secenek_id = s.secenek_id
                        LEFT JOIN urun u ON s.urun_id = u.id
                        LEFT JOIN secenek_nitelik_degeri snd ON s.secenek_id = snd.secenek_id
                        LEFT JOIN nitelik_degeri nd ON snd.deger_id = nd.deger_id
                        LEFT JOIN nitelik n ON nd.nitelik_id = n.nitelik_id
                        LEFT JOIN urun_degerlendirme ud ON (ud.urun_id = u.id AND ud.fatura_id = fu.fatura_id)
                        WHERE fu.fatura_id = :fatura_id
                        GROUP BY fu.id, fu.fatura_id, fu.secenek_id, fu.miktar, u.urun_isim, s.fiyat, 
                                ud.id, ud.puan, ud.yorum, ud.tarih";
        
        $urunler_stmt = $db->prepare($urunler_sql);
        $urunler_stmt->execute(['fatura_id' => $siparis_id]);
        $urunler = $urunler_stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $response = [
            'durum' => 'success',
            'siparis' => $siparis,
            'urunler' => $urunler
        ];
    } catch (PDOException $e) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Veritabanı hatası: ' . $e->getMessage()
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}


// Add Review
if (isset($_POST['islem']) && $_POST['islem'] == 'add_review') {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['kullanici_id'])) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Kullanıcı giriş yapmamış.'
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    $kullanici_id = $_SESSION['kullanici_id'];
    $urun_id = intval($_POST['urun_id']);
    $fatura_id = intval($_POST['fatura_id']);
    $puan = intval($_POST['puan']);
    $yorum = trim($_POST['yorum']);
    
    // Validate rating
    if ($puan < 1 || $puan > 5) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Geçersiz puan değeri.'
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    try {
        // Check if user has already reviewed this product in this order
        $check_stmt = $db->prepare("SELECT id FROM urun_degerlendirme WHERE kullanici_id = ? AND urun_id = ? AND fatura_id = ?");
        $check_stmt->execute([$kullanici_id, $urun_id, $fatura_id]);
        
        if ($check_stmt->rowCount() > 0) {
            $response = [
                'durum' => 'error',
                'mesaj' => 'Bu ürün için zaten bir değerlendirme yapmışsınız.'
            ];
        } else {
            $insert_stmt = $db->prepare("INSERT INTO urun_degerlendirme (kullanici_id, urun_id, fatura_id, puan, yorum, tarih) 
                                       VALUES (?, ?, ?, ?, ?, NOW())");
            $result = $insert_stmt->execute([$kullanici_id, $urun_id, $fatura_id, $puan, $yorum]);
            
            if ($result) {
                $response = [
                    'durum' => 'success',
                    'mesaj' => 'Değerlendirmeniz başarıyla kaydedildi.'
                ];
            } else {
                $response = [
                    'durum' => 'error',
                    'mesaj' => 'Değerlendirme kaydedilirken bir hata oluştu.'
                ];
            }
        }
    } catch (PDOException $e) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Veritabanı hatası: ' . $e->getMessage()
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Ürün Ekleme İşlemi
if (isset($_POST['islem']) && $_POST['islem'] == 'urun_ekle') {
    $urun_isim = htmlspecialchars(trim($_POST['urun_isim']));
    $urun_kod = isset($_POST['urun_kod']) ? htmlspecialchars(trim($_POST['urun_kod'])) : null;
    $urun_aciklama = htmlspecialchars($_POST['urun_aciklama']);
    $urun_fiyat = floatval($_POST['urun_fiyat']);
    $urun_stok = intval($_POST['urun_stok']);
    $urun_durum = isset($_POST['urun_durum']) && $_POST['urun_durum'] == 1 ? 1 : 0;
    $urun_marka = isset($_POST['urun_marka']) ? intval($_POST['urun_marka']) : null;
    $kategori_ids = isset($_POST['kategori_ids']) ? $_POST['kategori_ids'] : [];
    
    // Varyasyon verileri
    $nitelikler = isset($_POST['nitelikler']) ? json_decode($_POST['nitelikler'], true) : [];
    $varyasyonlar = isset($_POST['varyasyonlar']) ? json_decode($_POST['varyasyonlar'], true) : [];
    
    // Validasyon
    if (empty($urun_isim)) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Ürün adı boş olamaz!'
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    if ($urun_fiyat <= 0) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Ürün fiyatı sıfırdan büyük olmalıdır!'
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    try {
        // Ürünü ekle
        $db->beginTransaction();
        
        $ekle = $db->prepare("INSERT INTO urun SET 
            urun_isim = :urun_isim,
            urun_aciklama = :urun_aciklama,
            urun_fiyat = :urun_fiyat,
            urun_stok = :urun_stok,
            urun_marka = :urun_marka");
            
        $urun_sonuc = $ekle->execute([
            'urun_isim' => $urun_isim,
            'urun_aciklama' => $urun_aciklama,
            'urun_fiyat' => $urun_fiyat,
            'urun_stok' => $urun_stok,
            'urun_marka' => $urun_marka
        ]);
        
        $urun_id = $db->lastInsertId();
        
        // Nitelikleri ve değerlerini kaydet
        $nitelik_map = []; // Geçici ID'leri gerçek ID'lerle eşleştirmek için
        $deger_map = []; // Geçici ID'leri gerçek ID'lerle eşleştirmek için
        
        if (!empty($nitelikler)) {
            foreach ($nitelikler as $nitelik) {
                // Niteliği kaydet
                $nitelik_var = $db->prepare("SELECT COUNT(*) as adet FROM nitelik WHERE ad = :ad");
                $nitelik_var->execute(['ad' => $nitelik['ad']]);
                $nitelik_var = $nitelik_var->fetch(PDO::FETCH_ASSOC)['adet'];
                if ($nitelik_var == 0) {
                    $nitelik_ekle = $db->prepare("INSERT INTO nitelik SET ad = :ad");
                    $nitelik_ekle->execute(['ad' => $nitelik['ad']]);
                    $nitelik_id = $db->lastInsertId();
                    $nitelik_map[$nitelik['id']] = $nitelik_id;
                } else {
                    $nitelik_sor = $db->prepare("SELECT nitelik_id FROM nitelik WHERE ad = :ad");
                    $nitelik_sor->execute(['ad' => $nitelik['ad']]);
                    $nitelik_id = $nitelik_sor->fetch(PDO::FETCH_ASSOC)['nitelik_id'];
                    $nitelik_map[$nitelik['id']] = $nitelik_id;
                }
                
                // Niteliğin değerlerini kaydet
                if (!empty($nitelik['degerler'])) {
                    foreach ($nitelik['degerler'] as $deger) {
                        $deger_var = $db->prepare("SELECT COUNT(*) as adet FROM nitelik_degeri WHERE deger = :deger AND nitelik_id = :nitelik_id");
                        $deger_var->execute(['deger' => $deger['deger'], 'nitelik_id' => $nitelik_id]);
                        $deger_var = $deger_var->fetch(PDO::FETCH_ASSOC)['adet'];
                        if ($deger_var == 0) {
                            $deger_ekle = $db->prepare("INSERT INTO nitelik_degeri SET 
                                nitelik_id = :nitelik_id,
                                deger = :deger");
                            $deger_ekle->execute([
                                'nitelik_id' => $nitelik_id,
                                'deger' => $deger['deger']
                            ]);
                            $deger_id = $db->lastInsertId();
                        } else {
                            $deger_sor = $db->prepare("SELECT deger_id FROM nitelik_degeri WHERE deger = :deger AND nitelik_id = :nitelik_id");
                            $deger_sor->execute(['deger' => $deger['deger'], 'nitelik_id' => $nitelik_id]);
                            $deger_id = $deger_sor->fetch(PDO::FETCH_ASSOC)['deger_id'];
                        }
                        $deger_map[$deger['id']] = $deger_id;
                    }
                }
            }
        }
        
        // Varyasyonları kaydet
        if (!empty($varyasyonlar)) {
            foreach ($varyasyonlar as $varyasyon) {
                $secenek_ekle = $db->prepare("INSERT INTO urun_secenek SET 
                    urun_id = :urun_id,
                    fiyat = :fiyat,
                    stok = :stok");
                $secenek_ekle->execute([
                    'urun_id' => $urun_id,
                    'fiyat' => floatval($varyasyon['fiyat']),
                    'stok' => intval($varyasyon['stok'])
                ]);
                $secenek_id = $db->lastInsertId();
                
                // Seçenek-nitelik değeri bağlantılarını kaydet
                if (!empty($varyasyon['nitelik_degerleri'])) {
                    foreach ($varyasyon['nitelik_degerleri'] as $nitelik_deger) {
                        // Geçici ID'leri gerçek ID'lere dönüştür
                        $real_nitelik_id = $nitelik_map[$nitelik_deger['nitelik_id']];
                        $real_deger_id = $deger_map[$nitelik_deger['deger_id']];
                        
                        $baglanti_ekle = $db->prepare("INSERT INTO secenek_nitelik_degeri SET 
                            secenek_id = :secenek_id,
                            deger_id = :deger_id");
                        $baglanti_ekle->execute([
                            'secenek_id' => $secenek_id,
                            'deger_id' => $real_deger_id
                        ]);
                    }
                }
            }
        }else{
            $tek_secenek_ekle = $db->prepare("INSERT INTO urun_secenek SET 
                urun_id = :urun_id,
                fiyat = :fiyat,
                stok = :stok");
            $tek_secenek_ekle->execute([
                'urun_id' => $urun_id,
                'fiyat' => floatval($urun_fiyat),
                'stok' => intval($urun_stok)
            ]);
        }
        
        // Ürün-kategori ilişkilerini ekle
        if (!empty($kategori_ids)) {
            foreach ($kategori_ids as $kategori_id) {
                $kategori_ekle = $db->prepare("INSERT INTO urun_kategori_baglantisi SET 
                    urun_id = :urun_id,
                    kategori_id = :kategori_id");
                
                $kategori_ekle->execute([
                    'urun_id' => $urun_id,
                    'kategori_id' => intval($kategori_id)
                ]);
            }
        }
        
        // Ürün resimleri için işlem
        if (isset($_FILES['urun_resimler']) && !empty($_FILES['urun_resimler']['name'][0])) {
            $dosya_sayisi = count($_FILES['urun_resimler']['name']);
            $izin_verilen_uzantilar = ['jpg', 'jpeg', 'png', 'webp'];
            $upload_dir = '../../uploads/urunler/';
            
            // Upload klasörü yoksa oluştur
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            for ($i = 0; $i < $dosya_sayisi; $i++) {
                if ($_FILES['urun_resimler']['error'][$i] === 0) {
                    $dosya_adi = $_FILES['urun_resimler']['name'][$i];
                    $dosya_tmp = $_FILES['urun_resimler']['tmp_name'][$i];
                    $dosya_boyut = $_FILES['urun_resimler']['size'][$i];
                    
                    // Dosya uzantısını kontrol et
                    $uzanti = strtolower(pathinfo($dosya_adi, PATHINFO_EXTENSION));
                    if (!in_array($uzanti, $izin_verilen_uzantilar)) {
                        continue; // Geçersiz uzantı, sonraki dosyaya geç
                    }
                    
                    // Dosya boyutunu kontrol et (5MB max)
                    if ($dosya_boyut > 5 * 1024 * 1024) {
                        continue; // Dosya çok büyük, sonraki dosyaya geç
                    }
                    
                    // Benzersiz bir dosya adı oluştur
                    $yeni_dosya_adi = 'urun_' . $urun_id . '_' . uniqid() . '.' . $uzanti;
                    $hedef_yol = $upload_dir . $yeni_dosya_adi;
                    
                    // Dosyayı yükle
                    if (move_uploaded_file($dosya_tmp, $hedef_yol)) {
                        // Veritabanına resim kaydını ekle
                        $resim_ekle = $db->prepare("INSERT INTO urun_fotolar SET 
                            urun_id = :urun_id,
                            foto_path = :foto_path");
                        
                        $resim_ekle->execute([
                            'urun_id' => $urun_id,
                            'foto_path' => 'uploads/urunler/' . $yeni_dosya_adi
                        ]);
                    }
                }
            }
        }
        
        $db->commit();
        
        $response = [
            'durum' => 'success',
            'mesaj' => 'Ürün başarıyla eklendi.',
            'urun_id' => $urun_id
        ];
    } catch (PDOException $e) {
        $db->rollBack();
        $response = [
            'durum' => 'error',
            'mesaj' => 'Veritabanı hatası: ' . $e->getMessage()
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Ürün Bilgilerini Getir
if (isset($_GET['islem']) && $_GET['islem'] == 'urun_getir') {
    $urun_id = intval($_GET['urun_id']);
    
    try {
        // Ürün bilgilerini getir
        $urun_sor = $db->prepare("SELECT u.*, um.marka_isim 
                                FROM urun u 
                                LEFT JOIN urun_markalar um ON u.urun_marka = um.id
                                WHERE u.id = :id");
        $urun_sor->execute(['id' => $urun_id]);
        $urun = $urun_sor->fetch(PDO::FETCH_ASSOC);
        
        if (!$urun) {
            $response = [
                'durum' => 'error',
                'mesaj' => 'Ürün bulunamadı!'
            ];
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
        
        // Ürünün kategorilerini getir
        $kategori_sor = $db->prepare("SELECT kategori_id FROM urun_kategori_baglantisi WHERE urun_id = :urun_id");
        $kategori_sor->execute(['urun_id' => $urun_id]);
        $kategoriler = $kategori_sor->fetchAll(PDO::FETCH_COLUMN);
        
        // Ürünün resimlerini getir
        $resim_sor = $db->prepare("SELECT * FROM urun_fotolar WHERE urun_id = :urun_id ORDER BY id ASC");
        $resim_sor->execute(['urun_id' => $urun_id]);
        $resimler = $resim_sor->fetchAll(PDO::FETCH_ASSOC);
        
        // Ürün niteliklerini getir (sadece durum=1 olan seçeneklerden)
        $nitelikler = [];
        $nitelik_sor = $db->prepare("
            SELECT DISTINCT n.nitelik_id, n.ad
            FROM urun_secenek us
            JOIN secenek_nitelik_degeri snd ON us.secenek_id = snd.secenek_id
            JOIN nitelik_degeri nd ON snd.deger_id = nd.deger_id
            JOIN nitelik n ON nd.nitelik_id = n.nitelik_id
            WHERE us.urun_id = :urun_id AND us.durum = 1
        ");
        $nitelik_sor->execute(['urun_id' => $urun_id]);
        
        while ($nitelik = $nitelik_sor->fetch(PDO::FETCH_ASSOC)) {
            // Her nitelik için değerleri getir (sadece durum=1 olan seçeneklerden)
            $deger_sor = $db->prepare("
                SELECT DISTINCT nd.deger_id, nd.deger
                FROM urun_secenek us
                JOIN secenek_nitelik_degeri snd ON us.secenek_id = snd.secenek_id
                JOIN nitelik_degeri nd ON snd.deger_id = nd.deger_id
                WHERE us.urun_id = :urun_id AND nd.nitelik_id = :nitelik_id AND us.durum = 1
            ");
            $deger_sor->execute([
                'urun_id' => $urun_id,
                'nitelik_id' => $nitelik['nitelik_id']
            ]);
            
            $degerler = [];
            while ($deger = $deger_sor->fetch(PDO::FETCH_ASSOC)) {
                $degerler[] = [
                    'id' => $deger['deger_id'],
                    'deger' => $deger['deger']
                ];
            }
            
            // Sadece değerleri olan nitelikleri ekle
            if (!empty($degerler)) {
                $nitelikler[] = [
                    'id' => $nitelik['nitelik_id'],
                    'ad' => $nitelik['ad'],
                    'degerler' => $degerler
                ];
            }
        }
        
        // Ürün varyasyonlarını getir (sadece durum=1 olan seçenekler)
        $varyasyonlar = [];
        $secenek_sor = $db->prepare("
            SELECT us.secenek_id, us.fiyat, us.stok
            FROM urun_secenek us
            WHERE us.urun_id = :urun_id AND us.durum = 1
        ");
        $secenek_sor->execute(['urun_id' => $urun_id]);
        
        while ($secenek = $secenek_sor->fetch(PDO::FETCH_ASSOC)) {
            // Her seçenek için nitelik değerlerini getir (sadece durum=1 olan seçeneklerden)
            $deger_sor = $db->prepare("
                SELECT nd.deger_id, nd.deger, n.nitelik_id, n.ad as nitelik_ad
                FROM secenek_nitelik_degeri snd
                JOIN nitelik_degeri nd ON snd.deger_id = nd.deger_id
                JOIN nitelik n ON nd.nitelik_id = n.nitelik_id
                JOIN urun_secenek us ON snd.secenek_id = us.secenek_id
                WHERE snd.secenek_id = :secenek_id AND us.durum = 1
            ");
            $deger_sor->execute(['secenek_id' => $secenek['secenek_id']]);
            
            $nitelik_degerleri = [];
            $kombinasyon = '';
            $i = 0;
            while ($deger = $deger_sor->fetch(PDO::FETCH_ASSOC)) {
                $nitelik_degerleri[] = [
                    'nitelik_id' => $deger['nitelik_id'],
                    'deger_id' => $deger['deger_id']
                ];
                
                $kombinasyon .= $deger['nitelik_ad'] . ': ' . $deger['deger'] . ', ';
                $i++;
            }
            
            // Son virgülü kaldır
            $kombinasyon = rtrim($kombinasyon, ', ');
            if($i > 0){
                $varyasyonlar[] = [
                    'secenek_id' => $secenek['secenek_id'],
                    'fiyat' => $secenek['fiyat'],
                    'stok' => $secenek['stok'],
                    'kombinasyon' => $kombinasyon,
                    'nitelik_degerleri' => $nitelik_degerleri
                ];
            }
        }
        
        $response = [
            'durum' => 'success',
            'urun' => $urun,
            'kategoriler' => $kategoriler,
            'resimler' => $resimler,
            'nitelikler' => $nitelikler,
            'varyasyonlar' => $varyasyonlar
        ];
    } catch (PDOException $e) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Veritabanı hatası: ' . $e->getMessage()
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Ürün Güncelleme İşlemi
if (isset($_POST['islem']) && $_POST['islem'] == 'urun_guncelle') {
    $urun_id = intval($_POST['urun_id']);
    $urun_isim = htmlspecialchars(trim($_POST['urun_isim']));
    $urun_aciklama = htmlspecialchars($_POST['urun_aciklama']);
    $urun_fiyat = floatval($_POST['urun_fiyat']);
    $urun_stok = intval($_POST['urun_stok']);
    $urun_durum = isset($_POST['urun_durum']) && $_POST['urun_durum'] == 1 ? 1 : 0;
    $urun_marka = isset($_POST['urun_marka']) ? intval($_POST['urun_marka']) : null;
    $kategori_ids = isset($_POST['kategori_ids']) ? $_POST['kategori_ids'] : [];
    $silinecek_resimler = isset($_POST['silinecek_resimler']) ? $_POST['silinecek_resimler'] : [];
    
    // Varyasyon verileri
    $nitelikler = isset($_POST['nitelikler']) ? json_decode($_POST['nitelikler'], true) : [];
    $varyasyonlar = isset($_POST['varyasyonlar']) ? json_decode($_POST['varyasyonlar'], true) : [];
    
    // Validasyon
    if (empty($urun_isim)) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Ürün adı boş olamaz!'
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    if ($urun_fiyat <= 0) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Ürün fiyatı sıfırdan büyük olmalıdır!'
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    try {
        $db->beginTransaction();
        
        // Ürünü güncelle
        $guncelle = $db->prepare("UPDATE urun SET 
            urun_isim = :urun_isim,
            urun_aciklama = :urun_aciklama,
            urun_fiyat = :urun_fiyat,
            urun_stok = :urun_stok,
            urun_marka = :urun_marka
            WHERE id = :urun_id");
        
        $urun_sonuc = $guncelle->execute([
            'urun_isim' => $urun_isim,
            'urun_aciklama' => $urun_aciklama,
            'urun_fiyat' => $urun_fiyat,
            'urun_stok' => $urun_stok,
            'urun_marka' => $urun_marka,
            'urun_id' => $urun_id
        ]);
        
        // Mevcut nitelikleri ve değerleri sil (eğer varsa)
        // Önce mevcut değer-seçenek bağlantılarını al
        $baglanti_sor = $db->prepare("
            SELECT DISTINCT snd.deger_id, nd.nitelik_id
            FROM urun_secenek us
            JOIN secenek_nitelik_degeri snd ON us.secenek_id = snd.secenek_id
            JOIN nitelik_degeri nd ON snd.deger_id = nd.deger_id
            WHERE us.urun_id = :urun_id
        ");
        $baglanti_sor->execute(['urun_id' => $urun_id]);
        $mevcut_baglantilar = $baglanti_sor->fetchAll(PDO::FETCH_ASSOC);
        
        // Mevcut seçenekleri getir
        $secenek_sor = $db->prepare("SELECT secenek_id FROM urun_secenek WHERE urun_id = :urun_id");
        $secenek_sor->execute(['urun_id' => $urun_id]);
        $mevcut_secenekler = $secenek_sor->fetchAll(PDO::FETCH_COLUMN);
        
        
        // Seçenekleri sil
        $secenek_sil = $db->prepare("UPDATE urun_secenek SET durum = 0 WHERE urun_id = :urun_id");
        $secenek_sil->execute(['urun_id' => $urun_id]);
        
        // Mevcut nitelik ve değerleri kullanmak için haritala
        $nitelik_map = []; // Geçici ID'leri gerçek ID'lerle eşleştirmek için
        $deger_map = []; // Geçici ID'leri gerçek ID'lerle eşleştirmek için
        $mevcut_nitelikler = [];
        
        // Yeni nitelikleri ve değerleri kaydet
        if (!empty($nitelikler)) {
            foreach ($nitelikler as $nitelik) {
                // Niteliği kaydet veya mevcut niteliği kullan
                $nitelik_var = $db->prepare("SELECT COUNT(*) as adet FROM nitelik WHERE ad = :ad");
                $nitelik_var->execute(['ad' => $nitelik['ad']]);
                $nitelik_var = $nitelik_var->fetch(PDO::FETCH_ASSOC)['adet'];
                if ($nitelik_var == 0) {
                    $nitelik_ekle = $db->prepare("INSERT INTO nitelik SET ad = :ad");
                    $nitelik_ekle->execute(['ad' => $nitelik['ad']]);
                    $nitelik_id = $db->lastInsertId();
                } else {
                    $nitelik_sor = $db->prepare("SELECT nitelik_id FROM nitelik WHERE ad = :ad");
                    $nitelik_sor->execute(['ad' => $nitelik['ad']]);
                    $nitelik_id = $nitelik_sor->fetch(PDO::FETCH_ASSOC)['nitelik_id'];
                }
                
                $nitelik_map[$nitelik['id']] = $nitelik_id;
                $mevcut_nitelikler[] = $nitelik_id;
                
                // Niteliğin değerlerini kaydet
                if (!empty($nitelik['degerler'])) {
                    foreach ($nitelik['degerler'] as $deger) {
                        $deger_var = $db->prepare("SELECT COUNT(*) as adet FROM nitelik_degeri WHERE deger = :deger AND nitelik_id = :nitelik_id");
                        $deger_var->execute(['deger' => $deger['deger'], 'nitelik_id' => $nitelik_id]);
                        $deger_var = $deger_var->fetch(PDO::FETCH_ASSOC)['adet'];
                        if ($deger_var == 0) {
                            $deger_ekle = $db->prepare("INSERT INTO nitelik_degeri SET 
                                nitelik_id = :nitelik_id,
                                deger = :deger");
                            $deger_ekle->execute([
                                'nitelik_id' => $nitelik_id,
                                'deger' => $deger['deger']
                            ]);
                            $deger_id = $db->lastInsertId();
                        }else{
                            $deger_sor = $db->prepare("SELECT deger_id FROM nitelik_degeri WHERE deger = :deger AND nitelik_id = :nitelik_id");
                            $deger_sor->execute(['deger' => $deger['deger'], 'nitelik_id' => $nitelik_id]);
                            $deger_id = $deger_sor->fetch(PDO::FETCH_ASSOC)['deger_id'];
                        }
                        $deger_map[$deger['id']] = $deger_id;
                    }
                }
            }
        }
        
        // Varyasyonları kaydet
        if (!empty($varyasyonlar)) {
            foreach ($varyasyonlar as $varyasyon) {
                $secenek_prompt = "";
                $secenek_prompt_count = 0;
                if(!empty($varyasyon['nitelik_degerleri'])){
                    foreach($varyasyon['nitelik_degerleri'] as $nitelik_deger){
                        if($secenek_prompt_count > 0){
                            $secenek_prompt .= ",";
                        }
                        $secenek_prompt .= "?";
                        $secenek_prompt_count++;
                    }
                }
                if($secenek_prompt_count > 0){
                    
                    $sql = "SELECT s.secenek_id
                                                FROM urun_secenek u
                                                JOIN secenek_nitelik_degeri s ON u.secenek_id = s.secenek_id
                                                WHERE u.urun_id = ?
                                                GROUP BY s.secenek_id
                                                HAVING 
                                                SUM(s.deger_id IN (".$secenek_prompt.")) = ? AND
                                                COUNT(*) = ?";
                    $secenek_var = $db->prepare($sql);
                    $secenek_var->bindParam(1, $urun_id);
                    $i = 2;
                    foreach($varyasyon['nitelik_degerleri'] as $nitelik_deger){
                        $secenek_var->bindParam($i, $nitelik_deger['deger_id']);
                        $i++;
                    }
                    $secenek_var->bindParam($i, $secenek_prompt_count);
                    $secenek_var->bindParam($i+1, $secenek_prompt_count);
                    $secenek_var->execute();
                    $secenek_var = $secenek_var->fetchAll(PDO::FETCH_ASSOC);
                }
                if(empty($secenek_var)){
                // Seçeneği kaydet
                    $secenek_ekle = $db->prepare("INSERT INTO urun_secenek SET 
                        urun_id = :urun_id,
                        fiyat = :fiyat,
                        stok = :stok");
                    $secenek_ekle->execute([
                        'urun_id' => $urun_id,
                        'fiyat' => floatval($varyasyon['fiyat']),
                        'stok' => intval($varyasyon['stok'])
                    ]);
                    $secenek_id = $db->lastInsertId();
                    
                    // Seçenek-nitelik değeri bağlantılarını kaydet
                    if (!empty($varyasyon['nitelik_degerleri'])) {
                        foreach ($varyasyon['nitelik_degerleri'] as $nitelik_deger) {
                            // Geçici ID'leri gerçek ID'lere dönüştür
                            $real_nitelik_id = isset($nitelik_map[$nitelik_deger['nitelik_id']]) ? 
                                                $nitelik_map[$nitelik_deger['nitelik_id']] : 
                                                $nitelik_deger['nitelik_id'];
                                                
                            $real_deger_id = isset($deger_map[$nitelik_deger['deger_id']]) ? 
                                            $deger_map[$nitelik_deger['deger_id']] : 
                                            $nitelik_deger['deger_id'];
                            
                            $baglanti_ekle = $db->prepare("INSERT INTO secenek_nitelik_degeri SET 
                                secenek_id = :secenek_id,
                                deger_id = :deger_id");
                            $baglanti_ekle->execute([
                                'secenek_id' => $secenek_id,
                                'deger_id' => $real_deger_id
                            ]);
                        }
                    }
                }else{
                    $secenek_aktif = $db->prepare("UPDATE urun_secenek SET durum = 1 WHERE secenek_id = ?");
                    $secenek_aktif->execute([$secenek_var[0]['secenek_id']]);
                }
            }
        }else{
            $secenek_var = $db->prepare("SELECT us.secenek_id, COUNT(snd.deger_id) AS deger_sayisi
                                            FROM urun_secenek us
                                            JOIN urun u ON u.id = us.urun_id
                                            LEFT JOIN secenek_nitelik_degeri snd ON snd.secenek_id = us.secenek_id
                                            WHERE u.id = ?
                                            GROUP BY us.secenek_id;
                                            ");
            $secenek_var->bindParam(1, $urun_id);
            $secenek_var->execute();
            $secenek_var = $secenek_var->fetchAll(PDO::FETCH_ASSOC);
            if(!empty($secenek_var)){
                $secenek_id = $secenek_var[0]['secenek_id'];
                $secenek_aktif = $db->prepare("UPDATE urun_secenek SET durum = 1 WHERE secenek_id = ?");
                $secenek_aktif->execute([$secenek_id]);
            }else{
                $tek_secenek_ekle = $db->prepare("INSERT INTO urun_secenek SET 
                    urun_id = :urun_id,
                    fiyat = :fiyat,
                    stok = :stok");
            $tek_secenek_ekle->execute([
                'urun_id' => $urun_id,
                'fiyat' => floatval($urun_fiyat),
                    'stok' => intval($urun_stok)
                ]);
            }
        }
        
        // Kullanılmayan nitelik ve değerleri temizle - İsteğe bağlı, dikkatli olun!
        // Bu işlem sadece bu ürün için özel oluşturulan ve artık kullanılmayan nitelikleri siler
        
        // Ürün-kategori ilişkilerini güncelle
        // Önce mevcut ilişkileri sil
        $kategori_sil = $db->prepare("DELETE FROM urun_kategori_baglantisi WHERE urun_id = :urun_id");
        $kategori_sil->execute(['urun_id' => $urun_id]);
        
        // Yeni ilişkileri ekle
        if (!empty($kategori_ids)) {
            foreach ($kategori_ids as $kategori_id) {
                $kategori_ekle = $db->prepare("INSERT INTO urun_kategori_baglantisi SET 
                    urun_id = :urun_id,
                    kategori_id = :kategori_id");
                
                $kategori_ekle->execute([
                    'urun_id' => $urun_id,
                    'kategori_id' => intval($kategori_id)
                ]);
            }
        }
        
        // Silinecek resimleri işle
        if (!empty($silinecek_resimler)) {
            foreach ($silinecek_resimler as $resim_id) {
                // Önce resim dosyasının yolunu al
                $resim_sorgu = $db->prepare("SELECT foto_path FROM urun_fotolar WHERE id = :resim_id AND urun_id = :urun_id");
                $resim_sorgu->execute([
                    'resim_id' => intval($resim_id),
                    'urun_id' => $urun_id
                ]);
                $resim = $resim_sorgu->fetch(PDO::FETCH_ASSOC);
                
                if ($resim) {
                    // Dosyayı sil
                    $dosya_yolu = "../../" . $resim['foto_path'];
                    if (file_exists($dosya_yolu)) {
                        unlink($dosya_yolu);
                    }
                    
                    // Veritabanından resim kaydını sil
                    $resim_sil = $db->prepare("DELETE FROM urun_fotolar WHERE id = :resim_id");
                    $resim_sil->execute(['resim_id' => intval($resim_id)]);
                }
            }
        }
        
        // Yeni resimleri ekle
        if (isset($_FILES['urun_resimler']) && !empty($_FILES['urun_resimler']['name'][0])) {
            $dosya_sayisi = count($_FILES['urun_resimler']['name']);
            $izin_verilen_uzantilar = ['jpg', 'jpeg', 'png', 'webp'];
            $upload_dir = '../../uploads/urunler/';
            
            // Upload klasörü yoksa oluştur
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            for ($i = 0; $i < $dosya_sayisi; $i++) {
                if ($_FILES['urun_resimler']['error'][$i] === 0) {
                    $dosya_adi = $_FILES['urun_resimler']['name'][$i];
                    $dosya_tmp = $_FILES['urun_resimler']['tmp_name'][$i];
                    $dosya_boyut = $_FILES['urun_resimler']['size'][$i];
                    
                    // Dosya uzantısını kontrol et
                    $uzanti = strtolower(pathinfo($dosya_adi, PATHINFO_EXTENSION));
                    if (!in_array($uzanti, $izin_verilen_uzantilar)) {
                        continue; // Geçersiz uzantı, sonraki dosyaya geç
                    }
                    
                    // Dosya boyutunu kontrol et (5MB max)
                    if ($dosya_boyut > 5 * 1024 * 1024) {
                        continue; // Dosya çok büyük, sonraki dosyaya geç
                    }
                    
                    // Benzersiz bir dosya adı oluştur
                    $yeni_dosya_adi = 'urun_' . $urun_id . '_' . uniqid() . '.' . $uzanti;
                    $hedef_yol = $upload_dir . $yeni_dosya_adi;
                    
                    // Dosyayı yükle
                    if (move_uploaded_file($dosya_tmp, $hedef_yol)) {
                        // Veritabanına resim kaydını ekle
                        $resim_ekle = $db->prepare("INSERT INTO urun_fotolar SET 
                            urun_id = :urun_id,
                            foto_path = :foto_path");
                        
                        $resim_ekle->execute([
                            'urun_id' => $urun_id,
                            'foto_path' => 'uploads/urunler/' . $yeni_dosya_adi
                        ]);
                    }
                }
            }
        }
        
        $db->commit();
        
        $response = [
            'durum' => 'success',
            'mesaj' => 'Ürün başarıyla güncellendi.'
        ];

    } catch (PDOException $e) {
        $db->rollBack();
        $response = [
            'durum' => 'error',
            'mesaj' => 'Veritabanı hatası: ' . $e->getMessage()
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Ürün Silme İşlemi
if (isset($_POST['islem']) && $_POST['islem'] == 'urun_sil') {
    $urun_id = intval($_POST['urun_id']);
    
    try {
        $db->beginTransaction();
        
        // Önce ürün resimlerini veritabanından ve dosya sisteminden sil
        $resim_sorgu = $db->prepare("SELECT foto_path FROM urun_fotolar WHERE urun_id = :urun_id");
        $resim_sorgu->execute(['urun_id' => $urun_id]);
        $resimler = $resim_sorgu->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($resimler as $resim) {
            $dosya_yolu = "../../" . $resim['foto_path'];
            if (file_exists($dosya_yolu)) {
                unlink($dosya_yolu);
            }
        }
        
        // Veritabanından resimleri sil
        $resim_sil = $db->prepare("DELETE FROM urun_fotolar WHERE urun_id = :urun_id");
        $resim_sil->execute(['urun_id' => $urun_id]);
        
        // Ürün varyasyon bağlantılarını sil
        // Önce seçenek ID'lerini al
        $secenek_sorgu = $db->prepare("SELECT secenek_id FROM urun_secenek WHERE urun_id = :urun_id");
        $secenek_sorgu->execute(['urun_id' => $urun_id]);
        $secenekler = $secenek_sorgu->fetchAll(PDO::FETCH_COLUMN);
        
        if (!empty($secenekler)) {
            $secenek_ids = implode(',', $secenekler);
            
            // Seçenek-nitelik değeri bağlantılarını sil
            $db->exec("DELETE FROM secenek_nitelik_degeri WHERE secenek_id IN ($secenek_ids)");
            
            // Seçenekleri sil
            $secenek_sil = $db->prepare("DELETE FROM urun_secenek WHERE urun_id = :urun_id");
            $secenek_sil->execute(['urun_id' => $urun_id]);
        }
        
        // Ürün-kategori ilişkilerini sil
        $kategori_sil = $db->prepare("DELETE FROM urun_kategori_baglantisi WHERE urun_id = :urun_id");
        $kategori_sil->execute(['urun_id' => $urun_id]);
        
        // Son olarak ürünü sil
        $urun_sil = $db->prepare("DELETE FROM urun WHERE id = :urun_id");
        $sonuc = $urun_sil->execute(['urun_id' => $urun_id]);
        
        $db->commit();
        
        if ($sonuc) {
            $response = [
                'durum' => 'success',
                'mesaj' => 'Ürün başarıyla silindi.'
            ];
        } else {
            $response = [
                'durum' => 'error',
                'mesaj' => 'Ürün silinirken bir hata oluştu.'
            ];
        }
    } catch (PDOException $e) {
        $db->rollBack();
        $response = [
            'durum' => 'error',
            'mesaj' => 'Veritabanı hatası: ' . $e->getMessage()
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Sipariş Listesi Getirme İşlemi
if (isset($_GET['islem']) && $_GET['islem'] == 'siparis_listele') {
    // Filtre parametrelerini al
    $durum = isset($_GET['durum']) ? $_GET['durum'] : '';
    $baslangic_tarih = isset($_GET['baslangic_tarih']) ? $_GET['baslangic_tarih'] : '';
    $bitis_tarih = isset($_GET['bitis_tarih']) ? $_GET['bitis_tarih'] : '';
    $sayfa = isset($_GET['sayfa']) ? intval($_GET['sayfa']) : 1;
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 15;
    $offset = ($sayfa - 1) * $limit;
    
    // Sıralama seçenekleri
    $sort = isset($_GET['sort']) ? $_GET['sort'] : 'id';
    $order = isset($_GET['order']) ? $_GET['order'] : 'DESC';
    
    // Geçerli sıralama alanları
    $valid_sort_columns = ['id', 'fatura_no', 'toplam', 'tarih', 'odeme_yontemi', 'siparis_durumu'];
    if (!in_array($sort, $valid_sort_columns)) {
        $sort = 'id';
    }
    
    // Geçerli sıralama yönleri
    $valid_orders = ['ASC', 'DESC'];
    if (!in_array($order, $valid_orders)) {
        $order = 'DESC';
    }
    
    try {
        // Sorgu oluştur
        $where_conditions = [];
        $params = [];
        
        if (!empty($durum)) {
            $where_conditions[] = "f.siparis_durumu = :durum";
            $params['durum'] = $durum;
        }
        
        if (!empty($baslangic_tarih)) {
            $where_conditions[] = "DATE(f.tarih) >= :baslangic_tarih";
            $params['baslangic_tarih'] = $baslangic_tarih;
        }
        
        if (!empty($bitis_tarih)) {
            $where_conditions[] = "DATE(f.tarih) <= :bitis_tarih";
            $params['bitis_tarih'] = $bitis_tarih;
        }
        
        $where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";
        
        // Toplam kayıt sayısını al
        $count_sql = "SELECT COUNT(*) as toplam FROM faturalar f $where_clause";
        $count_stmt = $db->prepare($count_sql);
        $count_stmt->execute($params);
        $toplam_kayit = $count_stmt->fetch(PDO::FETCH_ASSOC)['toplam'];
        
        // Siparişleri getir
        $sql = "SELECT f.*, k.ad as musteri_ad, k.soyad as musteri_soyad, k.mail as musteri_mail,
                    ka.il, ka.ilce, ka.adres as teslimat_adres
                FROM faturalar f
                LEFT JOIN kullanicilar k ON f.kullanici_id = k.id
                LEFT JOIN kullanici_adres ka ON f.adres_id = ka.id
                $where_clause
                ORDER BY f.$sort $order
                LIMIT :limit OFFSET :offset";
                
        $params['limit'] = $limit;
        $params['offset'] = $offset;
        
        $stmt = $db->prepare($sql);
        
        // limit ve offset değerlerini int olarak bind etmek gerekiyor
        foreach ($params as $key => $val) {
            if ($key === 'limit' || $key === 'offset') {
                $stmt->bindValue(":$key", $val, PDO::PARAM_INT);
            } else {
                $stmt->bindValue(":$key", $val);
            }
        }
        
        $stmt->execute();
        $siparisler = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $response = [
            'durum' => 'success',
            'siparisler' => $siparisler,
            'toplam_kayit' => $toplam_kayit
        ];
    } catch (PDOException $e) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Veritabanı hatası: ' . $e->getMessage()
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Sipariş Detayı Getirme İşlemi
if (isset($_GET['islem']) && $_GET['islem'] == 'siparis_detay') {
    $siparis_id = intval($_GET['siparis_id']);
    
    try {
        // Sipariş bilgilerini getir
        $siparis_sql = "SELECT f.*, k.ad as musteri_ad, k.soyad as musteri_soyad, k.mail as musteri_mail, kp.kupon_kod, 
                            ka.adres_adi, ka.ad_soyad as teslimat_ad_soyad, ka.telefon as teslimat_telefon, 
                            ka.tc_no, ka.il, ka.ilce, ka.adres as teslimat_adres, ka.posta_kodu
                        FROM faturalar f
                        LEFT JOIN kullanicilar k ON f.kullanici_id = k.id
                        LEFT JOIN kupon kp ON f.kupon_id = kp.id
                        LEFT JOIN kullanici_adres ka ON f.adres_id = ka.id
                        WHERE f.id = :siparis_id";
        
        $siparis_stmt = $db->prepare($siparis_sql);
        $siparis_stmt->execute(['siparis_id' => $siparis_id]);
        $siparis = $siparis_stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$siparis) {
            $response = [
                'durum' => 'error',
                'mesaj' => 'Sipariş bulunamadı!'
            ];
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
        
        // Siparişteki ürünleri getir (secenek_id kullanarak)
        $urunler_sql = "SELECT fu.*, u.urun_isim,
                        GROUP_CONCAT(CONCAT(n.ad, ': ', nd.deger) SEPARATOR ', ') as varyasyon_bilgisi
                        FROM fatura_urunler fu
                        LEFT JOIN urun_secenek s ON fu.secenek_id = s.secenek_id
                        LEFT JOIN urun u ON s.urun_id = u.id
                        LEFT JOIN secenek_nitelik_degeri snd ON s.secenek_id = snd.secenek_id
                        LEFT JOIN nitelik_degeri nd ON snd.deger_id = nd.deger_id
                        LEFT JOIN nitelik n ON nd.nitelik_id = n.nitelik_id
                        WHERE fu.fatura_id = :fatura_id
                        GROUP BY fu.id, fu.fatura_id, fu.secenek_id, fu.miktar, u.urun_isim, s.fiyat";
        
        $urunler_stmt = $db->prepare($urunler_sql);
        $urunler_stmt->execute(['fatura_id' => $siparis_id]);
        $urunler = $urunler_stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $response = [
            'durum' => 'success',
            'siparis' => $siparis,
            'urunler' => $urunler
        ];
    } catch (PDOException $e) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Veritabanı hatası: ' . $e->getMessage()
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Sipariş Durumu Değiştirme İşlemi
if (isset($_POST['islem']) && $_POST['islem'] == 'siparis_durum_degistir') {
    $siparis_id = intval($_POST['siparis_id']);
    $yeni_durum = $_POST['yeni_durum'];
    
    // Durum değerini kontrol et
    $gecerli_durumlar = ['onaylandi', 'iptal_edildi', 'beklemede', 'hazirlaniyor', 'kargolandi', 'teslim_edildi'];
    if (!in_array($yeni_durum, $gecerli_durumlar)) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Geçersiz sipariş durumu!'
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    try {
        // Önce siparişin mevcut durumunu kontrol et
        $durum_kontrol = $db->prepare("SELECT * FROM faturalar WHERE id = :siparis_id");
        $durum_kontrol->execute(['siparis_id' => $siparis_id]);
        $mevcut_durum = $durum_kontrol->fetch(PDO::FETCH_ASSOC);

        if (!$mevcut_durum) {
            $response = [
                'durum' => 'error',
                'mesaj' => 'Sipariş bulunamadı!'
            ];
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
        
        // Durum değişikliği mantık kontrolü
        // İptal edilmiş sipariş durumu değiştirilemez
        if ($mevcut_durum['siparis_durumu'] === 'iptal_edildi' && $yeni_durum !== 'iptal_edildi') {
            $response = [
                'durum' => 'error',
                'mesaj' => 'İptal edilmiş siparişin durumu değiştirilemez!'
            ];
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
        
        // Teslim edilmiş sipariş durumu değiştirilemez
        if ($mevcut_durum['siparis_durumu'] === 'teslim_edildi' && $yeni_durum !== 'teslim_edildi') {
            $response = [
                'durum' => 'error',
                'mesaj' => 'Teslim edilmiş siparişin durumu değiştirilemez!'
            ];
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
        
        // Durumu güncelle
        $guncelle = $db->prepare("UPDATE faturalar SET siparis_durumu = :yeni_durum WHERE id = :siparis_id");
        $sonuc = $guncelle->execute([
            'yeni_durum' => $yeni_durum,
            'siparis_id' => $siparis_id
        ]);
        
        if ($sonuc) {
            // İptal edilen siparişlerde ürün stoklarını geri ekle
            if ($yeni_durum == 'iptal_edildi') {
                stokGeriEkle($db, $siparis_id);
            }
            
            // Durum mesajını oluştur
            switch($yeni_durum) {
                case 'onaylandi':
                    $durum_text = 'onaylandı';
                    break;
                case 'iptal_edildi':
                    $durum_text = 'iptal edildi';
                    break;
                case 'beklemede':
                    $durum_text = 'beklemede olarak güncellendi';
                    break;
                case 'hazirlaniyor':
                    $durum_text = 'hazırlanıyor olarak güncellendi';
                    break;
                case 'kargolandi':
                    $durum_text = 'kargolandı olarak güncellendi';
                    break;
                case 'teslim_edildi':
                    $durum_text = 'teslim edildi olarak güncellendi';
                    break;
                default:
                    $durum_text = 'güncellendi';
            }
            
            $bildirim_sql = "INSERT INTO bildirimler (kullanici_id, bildirim_mesaj, bildirim_link, tarih) VALUES (:kullanici_id, :bildirim_mesaj, :bildirim_link, NOW())";
            $bildirim_stmt = $db->prepare($bildirim_sql);
            $bildirim_stmt->execute([
                'kullanici_id' => $mevcut_durum['kullanici_id'],
                'bildirim_mesaj' => "". $mevcut_durum['fatura_no'] ." siparişiniz ". $durum_text. ".",
                'bildirim_link' => 'orders.php'
            ]);

            $response = [
                'durum' => 'success',
                'mesaj' => 'Sipariş başarıyla ' . $durum_text
            ];
        } else {
            $response = [
                'durum' => 'error',
                'mesaj' => 'Sipariş durumu güncellenirken bir hata oluştu.'
            ];
        }
    } catch (PDOException $e) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Veritabanı hatası: ' . $e->getMessage()
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// İptal edilen siparişlerde ürün stoklarını geri eklemek için yardımcı fonksiyon
function stokGeriEkle($db, $siparis_id) {
    try {
        // Siparişteki ürünleri al (secenek_id ile)
        $urunler_sor = $db->prepare("SELECT secenek_id, miktar FROM fatura_urunler WHERE fatura_id = :siparis_id");
        $urunler_sor->execute(['siparis_id' => $siparis_id]);
        $urunler = $urunler_sor->fetchAll(PDO::FETCH_ASSOC);
        
        // Her ürün seçeneğinin stok miktarını güncelle
        foreach ($urunler as $urun) {
            $stok_guncelle = $db->prepare("UPDATE urun_secenek SET stok = stok + :miktar WHERE secenek_id = :secenek_id");
            $stok_guncelle->execute([
                'miktar' => $urun['miktar'],
                'secenek_id' => $urun['secenek_id']
            ]);
        }
        
        return true;
    } catch (PDOException $e) {
        // Hata durumunda false döndür
        return false;
    }
}

// Kullanıcı Listesi Getirme İşlemi
if (isset($_GET['islem']) && $_GET['islem'] == 'kullanici_listele') {
    // Filtre parametrelerini al
    $rol = isset($_GET['rol']) ? $_GET['rol'] : '';
    $arama = isset($_GET['arama']) ? $_GET['arama'] : '';
    $sayfa = isset($_GET['sayfa']) ? intval($_GET['sayfa']) : 1;
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 15;
    $offset = ($sayfa - 1) * $limit;
    
    // Sıralama seçenekleri
    $sort = isset($_GET['sort']) ? $_GET['sort'] : 'id';
    $order = isset($_GET['order']) ? $_GET['order'] : 'DESC';
    
    // Geçerli sıralama alanları
    $valid_sort_columns = ['id', 'ad', 'soyad', 'mail', 'tel_no', 'rol', 'kayit_tarihi'];
    if (!in_array($sort, $valid_sort_columns)) {
        $sort = 'id';
    }
    
    // Geçerli sıralama yönleri
    $valid_orders = ['ASC', 'DESC'];
    if (!in_array($order, $valid_orders)) {
        $order = 'DESC';
    }
    
    try {
        // Sorgu oluştur
        $where_conditions = [];
        $params = [];
        
        if (!empty($rol)) {
            $where_conditions[] = "rol = :rol";
            $params['rol'] = $rol;
        }
        
        if (!empty($arama)) {
            $where_conditions[] = "(ad LIKE :arama OR soyad LIKE :arama OR mail LIKE :arama)";
            $params['arama'] = "%" . $arama . "%";
        }
        
        $where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";
        
        // Toplam kayıt sayısını al
        $count_sql = "SELECT COUNT(*) as toplam FROM kullanicilar $where_clause";
        $count_stmt = $db->prepare($count_sql);
        $count_stmt->execute($params);
        $toplam_kayit = $count_stmt->fetch(PDO::FETCH_ASSOC)['toplam'];
        
        // Kullanıcıları getir
        $sql = "SELECT id, ad, soyad, mail, tel_no, rol, kayit_tarihi 
                FROM kullanicilar
                $where_clause
                ORDER BY $sort $order
                LIMIT :limit OFFSET :offset";
                
        $params['limit'] = $limit;
        $params['offset'] = $offset;
        
        $stmt = $db->prepare($sql);
        
        // limit ve offset değerlerini int olarak bind etmek gerekiyor
        foreach ($params as $key => $val) {
            if ($key === 'limit' || $key === 'offset') {
                $stmt->bindValue(":$key", $val, PDO::PARAM_INT);
            } else {
                $stmt->bindValue(":$key", $val);
            }
        }
        
        $stmt->execute();
        $kullanicilar = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $response = [
            'durum' => 'success',
            'kullanicilar' => $kullanicilar,
            'toplam_kayit' => $toplam_kayit
        ];
    } catch (PDOException $e) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Veritabanı hatası: ' . $e->getMessage()
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Kullanıcı Bilgisi Getirme İşlemi
if (isset($_GET['islem']) && $_GET['islem'] == 'kullanici_getir') {
    $kullanici_id = intval($_GET['kullanici_id']);
    
    try {
        // Kullanıcı bilgilerini getir (şifre hariç)
        $sql = "SELECT id, ad, soyad, mail, tel_no, rol, kayit_tarihi 
                FROM kullanicilar 
                WHERE id = :kullanici_id";
        
        $stmt = $db->prepare($sql);
        $stmt->execute(['kullanici_id' => $kullanici_id]);
        $kullanici = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($kullanici) {
            $response = [
                'durum' => 'success',
                'kullanici' => $kullanici
            ];
        } else {
            $response = [
                'durum' => 'error',
                'mesaj' => 'Kullanıcı bulunamadı!'
            ];
        }
    } catch (PDOException $e) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Veritabanı hatası: ' . $e->getMessage()
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Kullanıcı Ekleme İşlemi
if (isset($_POST['islem']) && $_POST['islem'] == 'kullanici_ekle') {
    $ad = htmlspecialchars(trim($_POST['ad']));
    $soyad = htmlspecialchars(trim($_POST['soyad']));
    $mail = htmlspecialchars(trim($_POST['mail']));
    $telefon = isset($_POST['telefon']) ? htmlspecialchars(trim($_POST['telefon'])) : null;
    $sifre = $_POST['sifre'];
    $rol = $_POST['rol'];
    
    // Validasyon kontrolleri
    if (empty($ad) || empty($soyad) || empty($mail) || empty($sifre) || empty($rol)) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Lütfen zorunlu alanları doldurun!'
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    // E-posta formatı kontrolü
    if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Geçerli bir e-posta adresi girin!'
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    // Geçerli rol kontrolü
    $gecerli_roller = ['admin', 'moderator', 'user'];
    if (!in_array($rol, $gecerli_roller)) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Geçerli bir kullanıcı rolü seçin!'
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    try {
        // E-posta adresi benzersiz mi kontrol et
        $mail_kontrol = $db->prepare("SELECT COUNT(*) as adet FROM kullanicilar WHERE mail = :mail");
        $mail_kontrol->execute(['mail' => $mail]);
        $mail_say = $mail_kontrol->fetch(PDO::FETCH_ASSOC)['adet'];
        
        if ($mail_say > 0) {
            $response = [
                'durum' => 'error',
                'mesaj' => 'Bu e-posta adresi zaten kullanılıyor!'
            ];
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
        
        // Şifreyi hashle
        $sifre_hash = password_hash($sifre, PASSWORD_DEFAULT);
        
        // Kullanıcıyı ekle
        $ekle = $db->prepare("INSERT INTO kullanicilar SET 
            ad = :ad,
            soyad = :soyad,
            mail = :mail,
            tel_no = :telefon,
            sifre = :sifre,
            rol = :rol,
            kayit_tarihi = NOW()");
            
        $sonuc = $ekle->execute([
            'ad' => $ad,
            'soyad' => $soyad,
            'mail' => $mail,
            'telefon' => $telefon,
            'sifre' => $sifre_hash,
            'rol' => $rol
        ]);
        
        if ($sonuc) {
            $response = [
                'durum' => 'success',
                'mesaj' => 'Kullanıcı başarıyla eklendi.',
                'kullanici_id' => $db->lastInsertId()
            ];
        } else {
            $response = [
                'durum' => 'error',
                'mesaj' => 'Kullanıcı eklenirken bir hata oluştu.'
            ];
        }
    } catch (PDOException $e) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Veritabanı hatası: ' . $e->getMessage()
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Kullanıcı Güncelleme İşlemi
if (isset($_POST['islem']) && $_POST['islem'] == 'kullanici_guncelle') {
    $kullanici_id = intval($_POST['kullanici_id']);
    $ad = htmlspecialchars(trim($_POST['ad']));
    $soyad = htmlspecialchars(trim($_POST['soyad']));
    $mail = htmlspecialchars(trim($_POST['mail']));
    $telefon = isset($_POST['telefon']) ? htmlspecialchars(trim($_POST['telefon'])) : null;
    $sifre = isset($_POST['sifre']) ? $_POST['sifre'] : null;
    $rol = $_POST['rol'];
    
    // Validasyon kontrolleri
    if (empty($ad) || empty($soyad) || empty($mail) || empty($rol)) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Lütfen zorunlu alanları doldurun!'
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    // E-posta formatı kontrolü
    if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Geçerli bir e-posta adresi girin!'
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    // Geçerli rol kontrolü
    $gecerli_roller = ['admin', 'moderator', 'user'];
    if (!in_array($rol, $gecerli_roller)) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Geçerli bir kullanıcı rolü seçin!'
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    try {
        // Kullanıcı var mı kontrol et
        $kullanici_kontrol = $db->prepare("SELECT COUNT(*) as adet FROM kullanicilar WHERE id = :kullanici_id");
        $kullanici_kontrol->execute(['kullanici_id' => $kullanici_id]);
        $kullanici_say = $kullanici_kontrol->fetch(PDO::FETCH_ASSOC)['adet'];
        
        if ($kullanici_say == 0) {
            $response = [
                'durum' => 'error',
                'mesaj' => 'Kullanıcı bulunamadı!'
            ];
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
        
        // E-posta adresi benzersiz mi kontrol et (kendi e-postası hariç)
        $mail_kontrol = $db->prepare("SELECT COUNT(*) as adet FROM kullanicilar WHERE mail = :mail AND id != :kullanici_id");
        $mail_kontrol->execute([
            'mail' => $mail,
            'kullanici_id' => $kullanici_id
        ]);
        $mail_say = $mail_kontrol->fetch(PDO::FETCH_ASSOC)['adet'];
        
        if ($mail_say > 0) {
            $response = [
                'durum' => 'error',
                'mesaj' => 'Bu e-posta adresi zaten başka bir kullanıcı tarafından kullanılıyor!'
            ];
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
        
        // SQL sorgusu hazırla
        $sql = "UPDATE kullanicilar SET 
                ad = :ad,
                soyad = :soyad,
                mail = :mail,
                tel_no = :telefon,
                rol = :rol";
        
        $params = [
            'ad' => $ad,
            'soyad' => $soyad,
            'mail' => $mail,
            'telefon' => $telefon,
            'rol' => $rol,
            'kullanici_id' => $kullanici_id
        ];
        
        // Şifre girilmişse SQL sorgusuna ekle
        if (!empty($sifre)) {
            $sifre_hash = password_hash($sifre, PASSWORD_DEFAULT);
            $sql .= ", sifre = :sifre";
            $params['sifre'] = $sifre_hash;
        }
        
        $sql .= " WHERE id = :kullanici_id";
        
        // Güncelleme işlemini gerçekleştir
        $guncelle = $db->prepare($sql);
        $sonuc = $guncelle->execute($params);
        
        if ($sonuc) {
            $response = [
                'durum' => 'success',
                'mesaj' => 'Kullanıcı bilgileri başarıyla güncellendi.'
            ];
        } else {
            $response = [
                'durum' => 'error',
                'mesaj' => 'Kullanıcı güncellenirken bir hata oluştu.'
            ];
        }
    } catch (PDOException $e) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Veritabanı hatası: ' . $e->getMessage()
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Kullanıcı Silme İşlemi
if (isset($_POST['islem']) && $_POST['islem'] == 'kullanici_sil') {
    $kullanici_id = intval($_POST['kullanici_id']);
    
    // Mevcut kullanıcının kendisini silmesini engelle
    if (isset($_SESSION['kullanici_id']) && $_SESSION['kullanici_id'] == $kullanici_id) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Kendinizi silemezsiniz!'
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    try {
        $db->beginTransaction();
        
        // İlişkili kayıtları kontrol et
        $siparis_kontrol = $db->prepare("SELECT COUNT(*) as adet FROM faturalar WHERE kullanici_id = :kullanici_id");
        $siparis_kontrol->execute(['kullanici_id' => $kullanici_id]);
        $siparis_say = $siparis_kontrol->fetch(PDO::FETCH_ASSOC)['adet'];
        
        // İlişkili siparişlerin durumunu güncelle veya sil
        if ($siparis_say > 0) {
            // Burada siparişleri silmek yerine, onları "iptal edildi" olarak işaretleyebiliriz
            // veya tamamen silebiliriz. Bu örnekte siparişleri siliyoruz.
            $siparis_sil = $db->prepare("DELETE FROM faturalar WHERE kullanici_id = :kullanici_id");
            $siparis_sil->execute(['kullanici_id' => $kullanici_id]);
        }
        
        // Kullanıcıyı sil
        $kullanici_sil = $db->prepare("DELETE FROM kullanicilar WHERE id = :kullanici_id");
        $sonuc = $kullanici_sil->execute(['kullanici_id' => $kullanici_id]);
        
        if ($sonuc) {
            $db->commit();
            $response = [
                'durum' => 'success',
                'mesaj' => 'Kullanıcı ve ilişkili tüm verileri başarıyla silindi.'
            ];
        } else {
            $db->rollBack();
            $response = [
                'durum' => 'error',
                'mesaj' => 'Kullanıcı silinirken bir hata oluştu.'
            ];
        }
    } catch (PDOException $e) {
        $db->rollBack();
        $response = [
            'durum' => 'error',
            'mesaj' => 'Veritabanı hatası: ' . $e->getMessage()
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Site Ziyaretleri İstatistik Verileri
if (isset($_GET['islem']) && $_GET['islem'] == 'site_ziyaret_istatistik') {
    try {
        $period = isset($_GET['period']) ? $_GET['period'] : 'week';
        
        switch ($period) {
            case 'day':
                $sql = "SELECT 
                          DATE_FORMAT(ziyaret_tarih, '%H:00') as zaman, 
                          COUNT(*) as ziyaret_sayisi,
                          COUNT(DISTINCT ziyaret_ip) as tekil_ziyaretci
                        FROM 
                          site_ziyaret 
                        WHERE 
                          DATE(ziyaret_tarih) = CURDATE()
                        GROUP BY 
                          DATE_FORMAT(ziyaret_tarih, '%H:00')
                        ORDER BY 
                          zaman";
                $periyot_aciklama = "Bugün";
                break;
                
            case 'week':
                $sql = "SELECT 
                          DATE(ziyaret_tarih) as zaman, 
                          COUNT(*) as ziyaret_sayisi,
                          COUNT(DISTINCT ziyaret_ip) as tekil_ziyaretci
                        FROM 
                          site_ziyaret 
                        WHERE 
                          ziyaret_tarih >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                        GROUP BY 
                          DATE(ziyaret_tarih)
                        ORDER BY 
                          zaman";
                $periyot_aciklama = "Son 7 Gün";
                break;
                
            case 'month':
                $sql = "SELECT 
                          DATE(ziyaret_tarih) as zaman, 
                          COUNT(*) as ziyaret_sayisi,
                          COUNT(DISTINCT ziyaret_ip) as tekil_ziyaretci
                        FROM 
                          site_ziyaret 
                        WHERE 
                          ziyaret_tarih >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                        GROUP BY 
                          DATE(ziyaret_tarih)
                        ORDER BY 
                          zaman";
                $periyot_aciklama = "Son 30 Gün";
                break;
                
            case 'year':
                $sql = "SELECT 
                          DATE_FORMAT(ziyaret_tarih, '%Y-%m') as zaman, 
                          COUNT(*) as ziyaret_sayisi,
                          COUNT(DISTINCT ziyaret_ip) as tekil_ziyaretci
                        FROM 
                          site_ziyaret 
                        WHERE 
                          ziyaret_tarih >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                        GROUP BY 
                          DATE_FORMAT(ziyaret_tarih, '%Y-%m')
                        ORDER BY 
                          zaman";
                $periyot_aciklama = "Son 12 Ay";
                break;
        }
        
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $ziyaret_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Tarayıcı dağılımı
        $tarayici_sql = "SELECT 
                           ziyaret_tarayici, 
                           COUNT(*) as toplam 
                         FROM 
                           site_ziyaret 
                         GROUP BY 
                           ziyaret_tarayici 
                         ORDER BY 
                           toplam DESC 
                         LIMIT 5";
        $tarayici_stmt = $db->prepare($tarayici_sql);
        $tarayici_stmt->execute();
        $tarayici_data = $tarayici_stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // İşletim sistemi dağılımı
        $os_sql = "SELECT 
                     ziyaret_os, 
                     COUNT(*) as toplam 
                   FROM 
                     site_ziyaret 
                   GROUP BY 
                     ziyaret_os 
                   ORDER BY 
                     toplam DESC 
                   LIMIT 5";
        $os_stmt = $db->prepare($os_sql);
        $os_stmt->execute();
        $os_data = $os_stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Cihaz dağılımı
        $cihaz_sql = "SELECT 
                        ziyaret_cihaz, 
                        COUNT(*) as toplam 
                      FROM 
                        site_ziyaret 
                      GROUP BY 
                        ziyaret_cihaz 
                      ORDER BY 
                        toplam DESC 
                      LIMIT 5";
        $cihaz_stmt = $db->prepare($cihaz_sql);
        $cihaz_stmt->execute();
        $cihaz_data = $cihaz_stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Toplam ve tekil ziyaretçi sayıları
        $ozet_sql = "SELECT 
                       COUNT(*) as toplam_ziyaret, 
                       COUNT(DISTINCT ziyaret_ip) as tekil_ziyaretci,
                       COUNT(DISTINCT DATE(ziyaret_tarih)) as ziyaret_gun
                     FROM 
                       site_ziyaret";
        $ozet_stmt = $db->prepare($ozet_sql);
        $ozet_stmt->execute();
        $ozet_data = $ozet_stmt->fetch(PDO::FETCH_ASSOC);
        
        $response = [
            'durum' => 'success',
            'periyot' => $period,
            'periyot_aciklama' => $periyot_aciklama,
            'ziyaret_data' => $ziyaret_data,
            'tarayici_data' => $tarayici_data,
            'os_data' => $os_data,
            'cihaz_data' => $cihaz_data,
            'ozet' => $ozet_data
        ];
    } catch (PDOException $e) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Veritabanı hatası: ' . $e->getMessage()
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Kategori Ziyaretleri İstatistik Verileri
if (isset($_GET['islem']) && $_GET['islem'] == 'kategori_ziyaret_istatistik') {
    try {
        $period = isset($_GET['period']) ? $_GET['period'] : 'month';
        
        // En çok ziyaret edilen kategoriler
        $populer_sql = "SELECT 
                           k.id,
                           k.kategori_isim, 
                           COUNT(*) as ziyaret_sayisi 
                        FROM 
                           kategori_ziyaret kz
                        JOIN 
                           urun_kategoriler k ON kz.kategori_id = k.id
                        GROUP BY 
                           k.id, k.kategori_isim 
                        ORDER BY 
                           ziyaret_sayisi DESC 
                        LIMIT 10";
        $populer_stmt = $db->prepare($populer_sql);
        $populer_stmt->execute();
        $populer_data = $populer_stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Zaman bazlı kategori ziyaret verileri
        switch ($period) {
            case 'week':
                $sql = "SELECT 
                           DATE(kz.ziyaret_tarih) as zaman,
                           k.kategori_isim,
                           COUNT(*) as ziyaret_sayisi
                        FROM 
                           kategori_ziyaret kz
                        JOIN 
                           urun_kategoriler k ON kz.kategori_id = k.id
                        WHERE 
                           kz.ziyaret_tarih >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                        GROUP BY 
                           DATE(kz.ziyaret_tarih), k.kategori_isim
                        ORDER BY 
                           zaman, ziyaret_sayisi DESC";
                $periyot_aciklama = "Son 7 Gün";
                break;
                
            case 'month':
                $sql = "SELECT 
                           DATE(kz.ziyaret_tarih) as zaman,
                           k.kategori_isim,
                           COUNT(*) as ziyaret_sayisi
                        FROM 
                           kategori_ziyaret kz
                        JOIN 
                           urun_kategoriler k ON kz.kategori_id = k.id
                        WHERE 
                           kz.ziyaret_tarih >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                        GROUP BY 
                           DATE(kz.ziyaret_tarih), k.kategori_isim
                        ORDER BY 
                           zaman, ziyaret_sayisi DESC";
                $periyot_aciklama = "Son 30 Gün";
                break;
                
            case 'year':
                $sql = "SELECT 
                           DATE_FORMAT(kz.ziyaret_tarih, '%Y-%m') as zaman,
                           k.kategori_isim,
                           COUNT(*) as ziyaret_sayisi
                        FROM 
                           kategori_ziyaret kz
                        JOIN 
                           urun_kategoriler k ON kz.kategori_id = k.id
                        WHERE 
                           kz.ziyaret_tarih >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                        GROUP BY 
                           DATE_FORMAT(kz.ziyaret_tarih, '%Y-%m'), k.kategori_isim
                        ORDER BY 
                           zaman, ziyaret_sayisi DESC";
                $periyot_aciklama = "Son 12 Ay";
                break;
        }
        
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $ziyaret_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Ağaç yapısındaki kategorilere göre ziyaret verileri
        $kategori_sql = "SELECT 
                           k.id, 
                           k.kategori_isim, 
                           k.parent_kategori_id,
                           COUNT(kz.ziyaret_id) as ziyaret_sayisi
                        FROM 
                           urun_kategoriler k
                        LEFT JOIN 
                           kategori_ziyaret kz ON k.id = kz.kategori_id
                        GROUP BY 
                           k.id, k.kategori_isim, k.parent_kategori_id
                        ORDER BY 
                           k.parent_kategori_id, k.kategori_isim";
        $kategori_stmt = $db->prepare($kategori_sql);
        $kategori_stmt->execute();
        $kategori_data = $kategori_stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $response = [
            'durum' => 'success',
            'periyot' => $period,
            'periyot_aciklama' => $periyot_aciklama,
            'populer_kategoriler' => $populer_data,
            'ziyaret_data' => $ziyaret_data,
            'kategori_data' => $kategori_data
        ];
    } catch (PDOException $e) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Veritabanı hatası: ' . $e->getMessage()
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Ürün Ziyaretleri İstatistik Verileri
if (isset($_GET['islem']) && $_GET['islem'] == 'urun_ziyaret_istatistik') {
    try {
        $period = isset($_GET['period']) ? $_GET['period'] : 'month';
        
        // En çok ziyaret edilen ürünler
        $populer_sql = "SELECT 
                           u.id,
                           u.urun_isim, 
                           u.urun_fiyat,
                           u.urun_marka,
                           um.marka_isim,
                           COUNT(*) as ziyaret_sayisi,
                           COUNT(DISTINCT uz.ziyaret_ip) as tekil_ziyaretci
                        FROM 
                           urun_ziyaret uz
                        JOIN 
                           urun u ON uz.urun_id = u.id
                        LEFT JOIN
                           urun_markalar um ON u.urun_marka = um.id
                        GROUP BY 
                           u.id, u.urun_isim, u.urun_fiyat, u.urun_marka, um.marka_isim
                        ORDER BY 
                           ziyaret_sayisi DESC 
                        LIMIT 10";
        $populer_stmt = $db->prepare($populer_sql);
        $populer_stmt->execute();
        $populer_data = $populer_stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Zaman bazlı ürün ziyaret verileri
        switch ($period) {
            case 'week':
                $sql = "SELECT 
                           DATE(uz.ziyaret_tarih) as zaman,
                           COUNT(*) as ziyaret_sayisi,
                           COUNT(DISTINCT uz.ziyaret_ip) as tekil_ziyaretci
                        FROM 
                           urun_ziyaret uz
                        WHERE 
                           uz.ziyaret_tarih >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                        GROUP BY 
                           DATE(uz.ziyaret_tarih)
                        ORDER BY 
                           zaman";
                $periyot_aciklama = "Son 7 Gün";
                break;
                
            case 'month':
                $sql = "SELECT 
                           DATE(uz.ziyaret_tarih) as zaman,
                           COUNT(*) as ziyaret_sayisi,
                           COUNT(DISTINCT uz.ziyaret_ip) as tekil_ziyaretci
                        FROM 
                           urun_ziyaret uz
                        WHERE 
                           uz.ziyaret_tarih >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                        GROUP BY 
                           DATE(uz.ziyaret_tarih)
                        ORDER BY 
                           zaman";
                $periyot_aciklama = "Son 30 Gün";
                break;
                
            case 'year':
                $sql = "SELECT 
                           DATE_FORMAT(uz.ziyaret_tarih, '%Y-%m') as zaman,
                           COUNT(*) as ziyaret_sayisi,
                           COUNT(DISTINCT uz.ziyaret_ip) as tekil_ziyaretci
                        FROM 
                           urun_ziyaret uz
                        WHERE 
                           uz.ziyaret_tarih >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                        GROUP BY 
                           DATE_FORMAT(uz.ziyaret_tarih, '%Y-%m')
                        ORDER BY 
                           zaman";
                $periyot_aciklama = "Son 12 Ay";
                break;
        }
        
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $ziyaret_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Kategorilere göre ürün ziyaret dağılımı
        $kategori_sql = "SELECT 
                            k.kategori_isim,
                            COUNT(uz.ziyaret_id) as ziyaret_sayisi
                         FROM 
                            urun_kategoriler k
                         JOIN 
                            urun_kategori_baglantisi ukb ON k.id = ukb.kategori_id
                         JOIN 
                            urun_ziyaret uz ON ukb.urun_id = uz.urun_id
                         GROUP BY 
                            k.kategori_isim
                         ORDER BY 
                            ziyaret_sayisi DESC
                         LIMIT 10";
        $kategori_stmt = $db->prepare($kategori_sql);
        $kategori_stmt->execute();
        $kategori_data = $kategori_stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Markalara göre ürün ziyaret dağılımı
        $marka_sql = "SELECT 
                          u.urun_isim,
                          u.urun_marka,
                          um.marka_isim,
                          COUNT(uz.ziyaret_id) as ziyaret_sayisi,
                          COUNT(DISTINCT uz.ziyaret_ip) as tekil_ziyaretci
                       FROM 
                          urun_ziyaret uz
                       JOIN 
                          urun u ON uz.urun_id = u.id
                       LEFT JOIN
                          urun_markalar um ON u.urun_marka = um.id
                       GROUP BY 
                          u.urun_isim, u.urun_marka, um.marka_isim
                       ORDER BY 
                          ziyaret_sayisi DESC";
        $marka_stmt = $db->prepare($marka_sql);
        $marka_stmt->execute();
        $marka_data = $marka_stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $response = [
            'durum' => 'success',
            'periyot' => $period,
            'periyot_aciklama' => $periyot_aciklama,
            'populer_urunler' => $populer_data,
            'ziyaret_data' => $ziyaret_data,
            'kategori_data' => $kategori_data,
            'marka_data' => $marka_data
        ];
    } catch (PDOException $e) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Veritabanı hatası: ' . $e->getMessage()
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Marka Listesi Getirme İşlemi
if (isset($_GET['islem']) && $_GET['islem'] == 'marka_listele') {
    try {
        $marka_sor = $db->prepare("SELECT * FROM urun_markalar ORDER BY marka_isim ASC");
        $marka_sor->execute();
        $markalar = $marka_sor->fetchAll(PDO::FETCH_ASSOC);
        
        $response = [
            'durum' => 'success',
            'markalar' => $markalar
        ];
    } catch (PDOException $e) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Veritabanı hatası: ' . $e->getMessage()
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Marka Ekleme İşlemi
if (isset($_POST['islem']) && $_POST['islem'] == 'marka_ekle') {
    $marka_isim = htmlspecialchars(trim($_POST['marka_isim']));
    
    // Validasyon
    if (empty($marka_isim)) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Marka adı boş olamaz!'
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    try {
        // Aynı isimde marka var mı kontrol et
        $kontrol = $db->prepare("SELECT COUNT(*) as adet FROM urun_markalar WHERE marka_isim = :marka_isim");
        $kontrol->execute(['marka_isim' => $marka_isim]);
        $adet = $kontrol->fetch(PDO::FETCH_ASSOC)['adet'];
        
        if ($adet > 0) {
            $response = [
                'durum' => 'error',
                'mesaj' => 'Bu isimde bir marka zaten mevcut!'
            ];
        } else {
            // Markayı ekle
            $ekle = $db->prepare("INSERT INTO urun_markalar SET marka_isim = :marka_isim");
            $sonuc = $ekle->execute(['marka_isim' => $marka_isim]);
            
            if ($sonuc) {
                $marka_id = $db->lastInsertId();
                $response = [
                    'durum' => 'success',
                    'mesaj' => 'Marka başarıyla eklendi.',
                    'marka_id' => $marka_id
                ];
            } else {
                $response = [
                    'durum' => 'error',
                    'mesaj' => 'Marka eklenirken bir hata oluştu.'
                ];
            }
        }
    } catch (PDOException $e) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Veritabanı hatası: ' . $e->getMessage()
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Anasayfa Kategorileri Getirme İşlemi
if (isset($_GET['islem']) && $_GET['islem'] == 'anasayfa_kategoriler') {
    try {
        // Ana kategorileri al (parent_kategori_id = null olanlar)
        $kategori_sorgu = "SELECT id, kategori_isim, parent_kategori_id FROM urun_kategoriler 
                           WHERE parent_kategori_id IS NULL 
                           ORDER BY kategori_isim ASC";
        $kategori_sor = $db->prepare($kategori_sorgu);
        $kategori_sor->execute();
        
        $anasayfa_kategoriler = [];
        
        // Ana kategoriler ve ürün sayılarını hesapla
        while ($kategori = $kategori_sor->fetch(PDO::FETCH_ASSOC)) {
            // Alt kategorileri recursive olarak bul
            $alt_kategori_idleri = [];
            getSubCategoryIds($db, $kategori['id'], $alt_kategori_idleri);
            
            // Ana kategori ID'sini de ekle
            $tum_kategori_idleri = array_merge([$kategori['id']], $alt_kategori_idleri);
            
            // Bu kategoriye ve alt kategorilerine bağlı toplam ürün sayısını bul
            $params = [];
            $placeholders = [];
            
            foreach ($tum_kategori_idleri as $index => $id) {
                $key = ":kategori_id_$index";
                $params[$key] = $id;
                $placeholders[] = $key;
            }
            
            $placeholder_string = implode(',', $placeholders);
            
            $urun_sayisi_sorgu = "SELECT COUNT(DISTINCT urun_id) as adet FROM urun_kategori_baglantisi 
                                  WHERE kategori_id IN ($placeholder_string)";
            
            $urun_sayisi_sor = $db->prepare($urun_sayisi_sorgu);
            $urun_sayisi_sor->execute($params);
            $urun_sayisi = $urun_sayisi_sor->fetch(PDO::FETCH_ASSOC)['adet'];
            
            // Kategori bilgilerini diziye ekle
            $kategori['urun_sayisi'] = $urun_sayisi;
            $anasayfa_kategoriler[] = $kategori;
        }
        
        // Ürün sayısına göre sırala (çoktan aza)
        usort($anasayfa_kategoriler, function($a, $b) {
            return $b['urun_sayisi'] - $a['urun_sayisi'];
        });
        
        // En fazla 6 kategori göster
        $anasayfa_kategoriler = array_slice($anasayfa_kategoriler, 0, 6);
        
        $response = [
            'durum' => 'success',
            'kategoriler' => $anasayfa_kategoriler
        ];
    } catch (PDOException $e) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Veritabanı hatası: ' . $e->getMessage()
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Alt kategori ID'lerini recursive olarak bulan yardımcı fonksiyon
function getSubCategoryIds($db, $parent_id, &$ids_array) {
    $stmt = $db->prepare("SELECT id FROM urun_kategoriler WHERE parent_kategori_id = :parent_id");
    $stmt->execute(['parent_id' => $parent_id]);
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $ids_array[] = $row['id'];
        getSubCategoryIds($db, $row['id'], $ids_array);
    }
}

// Popüler Ürünleri Getirme İşlemi
if (isset($_GET['islem']) && $_GET['islem'] == 'populer_urunler') {
    try {
        // Ürün ziyaretlerine göre popüler ürünleri al (son 30 günde en çok ziyaret edilen 10 ürün)
        $populer_sorgu = "SELECT u.id, u.urun_isim, u.urun_fiyat, u.urun_stok, u.urun_marka, AVG(ud.puan) as ortalama_puan, COUNT(Distinct ud.id) as degerlendirme_sayisi, 
                          COUNT(Distinct uz.ziyaret_id) AS ziyaret_sayisi, 
                          um.marka_isim,
                          (SELECT foto_path FROM urun_fotolar WHERE urun_id = u.id ORDER BY id ASC LIMIT 1) AS foto_path
                          FROM urun u
                          LEFT JOIN urun_ziyaret uz ON u.id = uz.urun_id
                          LEFT JOIN urun_markalar um ON u.urun_marka = um.id
                          LEFT JOIN urun_degerlendirme ud ON u.id = ud.urun_id
                          WHERE uz.ziyaret_tarih >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                          GROUP BY u.id, u.urun_isim, u.urun_fiyat, u.urun_stok, u.urun_marka, um.marka_isim
                          ORDER BY ziyaret_sayisi DESC
                          LIMIT 10";
                          
        $populer_sor = $db->prepare($populer_sorgu);
        $populer_sor->execute();
        $populer_urunler = $populer_sor->fetchAll(PDO::FETCH_ASSOC);
        
        $response = [
            'durum' => 'success',
            'urunler' => $populer_urunler
        ];
    } catch (PDOException $e) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Veritabanı hatası: ' . $e->getMessage()
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Yeni Ürünleri Getirme İşlemi
if (isset($_GET['islem']) && $_GET['islem'] == 'yeni_urunler') {
    try {
        // En son eklenen 10 ürünü al
        $yeni_sorgu = "SELECT u.id, u.urun_isim, u.urun_fiyat, u.urun_stok, u.urun_marka, AVG(ud.puan) as ortalama_puan, COUNT(Distinct ud.puan) as degerlendirme_sayisi, 
                       um.marka_isim,
                       (SELECT foto_path FROM urun_fotolar WHERE urun_id = u.id ORDER BY id ASC LIMIT 1) AS foto_path
                       FROM urun u
                       LEFT JOIN urun_markalar um ON u.urun_marka = um.id
                       LEFT JOIN urun_degerlendirme ud ON u.id = ud.urun_id
                       GROUP BY u.id, u.urun_isim, u.urun_fiyat, u.urun_stok, u.urun_marka, um.marka_isim
                       ORDER BY u.id DESC
                       LIMIT 10";
                       
        $yeni_sor = $db->prepare($yeni_sorgu);
        $yeni_sor->execute();
        $yeni_urunler = $yeni_sor->fetchAll(PDO::FETCH_ASSOC);
        
        $response = [
            'durum' => 'success',
            'urunler' => $yeni_urunler
        ];
    } catch (PDOException $e) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Veritabanı hatası: ' . $e->getMessage()
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Ürünü Sepete Ekleme İşlemi
if (isset($_POST['islem']) && $_POST['islem'] == 'sepete_ekle') {
    $secenek_id = intval($_POST['secenek_id']);
    $adet = isset($_POST['adet']) ? intval($_POST['adet']) : 1;
    
    // Sepet oturumu kontrolü
    if (!isset($_SESSION['sepet'])) {
        $_SESSION['sepet'] = [];
    }
    
    // Seçenek ve ürün bilgilerini al
    try {
        $secenek_sor = $db->prepare("SELECT 
            us.secenek_id, 
            us.urun_id, 
            us.fiyat, 
            us.stok, 
            u.urun_isim 
            FROM urun_secenek us 
            JOIN urun u ON us.urun_id = u.id 
            WHERE us.secenek_id = :secenek_id");
        $secenek_sor->execute(['secenek_id' => $secenek_id]);
        $secenek = $secenek_sor->fetch(PDO::FETCH_ASSOC);
        
        if (!$secenek) {
            $response = [
                'durum' => 'error',
                'mesaj' => 'Ürün varyasyonu bulunamadı!'
            ];
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
        
        
        // Stok kontrolü
        if ($secenek['stok'] < $adet) {
            $response = [
                'durum' => 'error',
                'mesaj' => 'Yeterli stok bulunmamaktadır. Mevcut stok: ' . $secenek['stok']
            ];
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
        
        if(!isset($_SESSION['kullanici_id'])) {
            
            // Ürünü sepete ekle veya adedini güncelle
            if (isset($_SESSION['sepet'][$secenek_id])) {
                $yeni_adet = $_SESSION['sepet'][$secenek_id]['adet'] + $adet;
                
                // Yeni adet için stok kontrolü
                if ($secenek['stok'] < $yeni_adet) {
                    $response = [
                        'durum' => 'error',
                        'mesaj' => 'Yeterli stok bulunmamaktadır. Mevcut stok: ' . $secenek['stok']
                    ];
                    header('Content-Type: application/json');
                    echo json_encode($response);
                    exit;
                }
                
                $_SESSION['sepet'][$secenek_id]['adet'] = $yeni_adet;
            } else {
                $_SESSION['sepet'][$secenek_id] = [
                    'secenek_id' => $secenek['secenek_id'],
                    'urun_id' => $secenek['urun_id'],
                    'adet' => $adet
                ];
            }
            
            // Sepetteki toplam ürün adedi
            $sepet_adet = 0;
            foreach ($_SESSION['sepet'] as $item) {
                $sepet_adet += $item['adet'];
            }
        } else {
            
            // Kullanıcı oturum açmışsa, veritabanı sepetine ekle
            $sepette = $db->prepare("SELECT * FROM sepet WHERE kullanici_id = :kullanici_id AND secenek_id = :secenek_id");
            $sepette->execute(['kullanici_id' => $_SESSION['kullanici_id'], 'secenek_id' => $secenek_id]);
            $sepette_urun = $sepette->fetch(PDO::FETCH_ASSOC);
            
            if($sepette_urun) {
                $yeni_adet = $sepette_urun['adet'] + $adet;
                if($secenek['stok'] < $yeni_adet) {
                    $response = [
                        'durum' => 'error',
                        'mesaj' => 'Yeterli stok bulunmamaktadır. Mevcut stok: ' . $secenek['stok']
                    ];
                    header('Content-Type: application/json');
                    echo json_encode($response);
                    exit;
                } else {
                    $ekle = $db->prepare("UPDATE sepet SET adet = :adet WHERE kullanici_id = :kullanici_id AND secenek_id = :secenek_id");
                    $ekle->execute(['adet' => $yeni_adet, 'kullanici_id' => $_SESSION['kullanici_id'], 'secenek_id' => $secenek_id]);
                }
                
            } else {
                $ekle = $db->prepare("INSERT INTO sepet SET kullanici_id = :kullanici_id, secenek_id = :secenek_id, adet = :adet");
                $ekle->execute([
                    'kullanici_id' => $_SESSION['kullanici_id'], 
                    'secenek_id' => $secenek_id,
                    'adet' => $adet
                ]);
            }
            
            $sepet_adet = $db->prepare("SELECT SUM(adet) as adet FROM sepet WHERE kullanici_id = :kullanici_id");
            $sepet_adet->execute(['kullanici_id' => $_SESSION['kullanici_id']]);
            $sepet_adet = $sepet_adet->fetch(PDO::FETCH_ASSOC)['adet'] ?? 0;
        }
        
        $response = [
            'durum' => 'success',
            'mesaj' => 'Ürün sepete eklendi.',
            'sepet_adet' => $sepet_adet
        ];
    } catch (PDOException $e) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Veritabanı hatası: ' . $e->getMessage()
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Ürün Detay Sayfası İçin Ürün Bilgilerini Getir
if (isset($_GET['islem']) && $_GET['islem'] == 'urun_detay') {
    $urun_id = intval($_GET['urun_id']);
    
    try {
        // Ürün bilgilerini getir
        $urun_sorgu = "SELECT u.*, um.marka_isim 
                      FROM urun u 
                      LEFT JOIN urun_markalar um ON u.urun_marka = um.id 
                      WHERE u.id = :urun_id";
        $urun_sor = $db->prepare($urun_sorgu);
        $urun_sor->execute(['urun_id' => $urun_id]);
        $urun = $urun_sor->fetch(PDO::FETCH_ASSOC);
        
        if (!$urun) {
            $response = [
                'durum' => 'error',
                'mesaj' => 'Ürün bulunamadı!'
            ];
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
        
        // Ürün resimlerini getir
        $resim_sorgu = "SELECT * FROM urun_fotolar WHERE urun_id = :urun_id";
        $resim_sor = $db->prepare($resim_sorgu);
        $resim_sor->execute(['urun_id' => $urun_id]);
        $resimler = $resim_sor->fetchAll(PDO::FETCH_ASSOC);
        
        // Ürün kategorilerini getir
        $kategori_sorgu = "SELECT uk.id, uk.kategori_isim 
                          FROM urun_kategori_baglantisi ukb
                          JOIN urun_kategoriler uk ON ukb.kategori_id = uk.id
                          WHERE ukb.urun_id = :urun_id";
        $kategori_sor = $db->prepare($kategori_sorgu);
        $kategori_sor->execute(['urun_id' => $urun_id]);
        $kategoriler = $kategori_sor->fetchAll(PDO::FETCH_ASSOC);
        
        // Ürün niteliklerini getir
        $nitelikler = [];
        $nitelik_sor = $db->prepare("
            SELECT DISTINCT n.nitelik_id, n.ad
            FROM urun_secenek us
            JOIN secenek_nitelik_degeri snd ON us.secenek_id = snd.secenek_id
            JOIN nitelik_degeri nd ON snd.deger_id = nd.deger_id
            JOIN nitelik n ON nd.nitelik_id = n.nitelik_id
            WHERE us.urun_id = :urun_id AND us.durum = 1
        ");
        $nitelik_sor->execute(['urun_id' => $urun_id]);
        
        while ($nitelik = $nitelik_sor->fetch(PDO::FETCH_ASSOC)) {
            // Her nitelik için değerleri getir
            $deger_sor = $db->prepare("
                SELECT DISTINCT nd.deger_id, nd.deger
                FROM urun_secenek us
                JOIN secenek_nitelik_degeri snd ON us.secenek_id = snd.secenek_id
                JOIN nitelik_degeri nd ON snd.deger_id = nd.deger_id
                WHERE us.urun_id = :urun_id AND nd.nitelik_id = :nitelik_id AND us.durum = 1
            ");
            $deger_sor->execute([
                'urun_id' => $urun_id,
                'nitelik_id' => $nitelik['nitelik_id']
            ]);
            
            $degerler = [];
            while ($deger = $deger_sor->fetch(PDO::FETCH_ASSOC)) {
                $degerler[] = [
                    'id' => $deger['deger_id'],
                    'deger' => $deger['deger']
                ];
            }
            
            $nitelikler[] = [
                'id' => $nitelik['nitelik_id'],
                'ad' => $nitelik['ad'],
                'degerler' => $degerler
            ];
        }
        
        // Ürün varyasyonlarını getir
        $varyasyonlar = [];
        $secenek_sor = $db->prepare("
            SELECT us.secenek_id, us.fiyat, us.stok
            FROM urun_secenek us
            WHERE us.urun_id = :urun_id
        ");
        $secenek_sor->execute(['urun_id' => $urun_id]);
        
        while ($secenek = $secenek_sor->fetch(PDO::FETCH_ASSOC)) {
            // Her seçenek için nitelik değerlerini getir
            $deger_sor = $db->prepare("
                SELECT nd.deger_id, nd.deger, n.nitelik_id, n.ad as nitelik_ad
                FROM secenek_nitelik_degeri snd
                JOIN nitelik_degeri nd ON snd.deger_id = nd.deger_id
                JOIN nitelik n ON nd.nitelik_id = n.nitelik_id
                WHERE snd.secenek_id = :secenek_id
            ");
            $deger_sor->execute(['secenek_id' => $secenek['secenek_id']]);
            
            $nitelik_degerleri = [];
            $kombinasyon = '';
            
            while ($deger = $deger_sor->fetch(PDO::FETCH_ASSOC)) {
                $nitelik_degerleri[] = [
                    'nitelik_id' => $deger['nitelik_id'],
                    'deger_id' => $deger['deger_id']
                ];
                
                $kombinasyon .= $deger['nitelik_ad'] . ': ' . $deger['deger'] . ', ';
            }
            
            // Son virgülü kaldır
            $kombinasyon = rtrim($kombinasyon, ', ');
            
            $varyasyonlar[] = [
                'secenek_id' => $secenek['secenek_id'],
                'fiyat' => $secenek['fiyat'],
                'stok' => $secenek['stok'],
                'kombinasyon' => $kombinasyon,
                'nitelik_degerleri' => $nitelik_degerleri
            ];
        }
        
        // Eğer varyasyon yoksa, varsayılan seçenek bilgilerini ekle
        if (empty($varyasyonlar)) {
            $varsayilan_secenek_sor = $db->prepare("
                SELECT secenek_id, fiyat, stok
                FROM urun_secenek
                WHERE urun_id = :urun_id
                LIMIT 1
            ");
            $varsayilan_secenek_sor->execute(['urun_id' => $urun_id]);
            $varsayilan_secenek = $varsayilan_secenek_sor->fetch(PDO::FETCH_ASSOC);
            
            if ($varsayilan_secenek) {
                $varyasyonlar[] = [
                    'secenek_id' => $varsayilan_secenek['secenek_id'],
                    'fiyat' => $varsayilan_secenek['fiyat'],
                    'stok' => $varsayilan_secenek['stok'],
                    'kombinasyon' => '',
                    'nitelik_degerleri' => []
                ];
            }
        }
        
        $response = [
            'durum' => 'success',
            'urun' => $urun,
            'kategoriler' => $kategoriler,
            'resimler' => $resimler,
            'nitelikler' => $nitelikler,
            'varyasyonlar' => $varyasyonlar
        ];
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    } catch (PDOException $e) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Veritabanı hatası: ' . $e->getMessage()
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
}

// Benzer Ürünleri Getir
if (isset($_GET['islem']) && $_GET['islem'] == 'benzer_urunler') {
    $urun_id = intval($_GET['urun_id']);
    
    try {
        // Önce ürünün kategorilerini bulalım
        $kategori_sorgu = "SELECT kategori_id FROM urun_kategori_baglantisi WHERE urun_id = :urun_id";
        $kategori_sor = $db->prepare($kategori_sorgu);
        $kategori_sor->execute(['urun_id' => $urun_id]);
        $kategori_idleri = $kategori_sor->fetchAll(PDO::FETCH_COLUMN);
        
        // Eğer kategori yoksa boş döndür
        if (empty($kategori_idleri)) {
            $response = [
                'durum' => 'error',
                'mesaj' => 'Benzer ürün bulunamadı.'
            ];
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
        
        // Kategori ID'lerini ? ile değiştirip parametreleri hazırla
        $placeholders = rtrim(str_repeat('?,', count($kategori_idleri)), ',');
        

        // Aynı kategorideki diğer ürünleri getir, mevcut ürünü hariç tut
        $urun_sorgu = "SELECT DISTINCT u.id, u.urun_isim, u.urun_fiyat, u.urun_stok, u.urun_marka, 
                      um.marka_isim,
                      (SELECT foto_path FROM urun_fotolar WHERE urun_id = u.id ORDER BY id ASC LIMIT 1) AS foto_path
                      FROM urun u
                      JOIN urun_kategori_baglantisi ukb ON u.id = ukb.urun_id
                      LEFT JOIN urun_markalar um ON u.urun_marka = um.id
                      WHERE ukb.kategori_id IN ($placeholders)
                      AND u.id <> ?
                      GROUP BY u.id
                      ORDER BY RAND()
                      LIMIT 10";
        
        $urun_sor = $db->prepare($urun_sorgu);
        
        // Parametreleri bind et
        $i = 1;
        foreach ($kategori_idleri as $kategori_id) {
            $urun_sor->bindValue($i++, $kategori_id);
        }
        $urun_sor->bindValue($i, $urun_id);
        
        $urun_sor->execute();
        $benzer_urunler = $urun_sor->fetchAll(PDO::FETCH_ASSOC);
        
        // Eğer benzer ürün bulunamazsa, en son eklenen ürünleri göster
        if (empty($benzer_urunler)) {
            $yeni_sorgu = "SELECT u.id, u.urun_isim, u.urun_fiyat, u.urun_stok, u.urun_marka, 
                          um.marka_isim,
                          (SELECT foto_path FROM urun_fotolar WHERE urun_id = u.id ORDER BY id ASC LIMIT 1) AS foto_path
                          FROM urun u
                          LEFT JOIN urun_markalar um ON u.urun_marka = um.id
                          WHERE u.id <> :urun_id
                          ORDER BY u.id DESC
                          LIMIT 10";
            $yeni_sor = $db->prepare($yeni_sorgu);
            $yeni_sor->execute(['urun_id' => $urun_id]);
            $benzer_urunler = $yeni_sor->fetchAll(PDO::FETCH_ASSOC);
        }
        
        $response = [
            'durum' => 'success',
            'urunler' => $benzer_urunler
        ];
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    } catch (PDOException $e) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Veritabanı hatası: ' . $e->getMessage()
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
}

// Ürün Ziyaret Kaydı Ekle (IP başına günde en fazla 3 kez)
if (isset($_POST['islem']) && $_POST['islem'] == 'urun_ziyaret_kaydet') {
    $urun_id = intval($_POST['urun_id']);
    $ziyaret_ip = $_SERVER['REMOTE_ADDR'];
    $ziyaret_tarih = date('Y-m-d H:i:s');
    
    try {
        // Bugün bu IP'den bu ürüne kaç kez ziyaret edilmiş kontrol et
        $kontrol_sorgu = "SELECT COUNT(*) as adet FROM urun_ziyaret 
                         WHERE urun_id = :urun_id 
                         AND ziyaret_ip = :ziyaret_ip 
                         AND DATE(ziyaret_tarih) = CURDATE()";
        $kontrol_sor = $db->prepare($kontrol_sorgu);
        $kontrol_sor->execute([
            'urun_id' => $urun_id,
            'ziyaret_ip' => $ziyaret_ip
        ]);
        $ziyaret_sayisi = $kontrol_sor->fetch(PDO::FETCH_ASSOC)['adet'];
        
        // Günlük 3 ziyaret limitini aşmamışsa kaydet
        if ($ziyaret_sayisi < 3) {
            $ekle_sorgu = "INSERT INTO urun_ziyaret SET 
                         urun_id = :urun_id,
                         ziyaret_ip = :ziyaret_ip,
                         ziyaret_tarih = :ziyaret_tarih";
            
            // Tarayıcı bilgisini al
            $ziyaret_tarayici = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
            
            $ekle = $db->prepare($ekle_sorgu);
            $ekle->execute([
                'urun_id' => $urun_id,
                'ziyaret_ip' => $ziyaret_ip,
                'ziyaret_tarih' => $ziyaret_tarih
            ]);
            
            $response = [
                'durum' => 'success',
                'mesaj' => 'Ziyaret kaydı eklendi.'
            ];
        } else {
            $response = [
                'durum' => 'info',
                'mesaj' => 'Ziyaret kaydı günlük limiti aşıldı.'
            ];
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    } catch (PDOException $e) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Veritabanı hatası: ' . $e->getMessage()
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
}

// Kullanıcı Giriş İşlemi (Site Kullanıcıları İçin)
if (isset($_POST['islem']) && $_POST['islem'] == 'kullanici_giris') {
    $mail = htmlspecialchars(trim($_POST['mail']));
    $sifre = trim($_POST['sifre']);
    
    // Veritabanından kullanıcıyı sorgula
    $kullanicisor = $db->prepare("SELECT * FROM kullanicilar WHERE mail=:mail");
    $kullanicisor->execute([
        'mail' => $mail
    ]);
    
    $say = $kullanicisor->rowCount();
    $kullanicicek = $kullanicisor->fetch(PDO::FETCH_ASSOC);
    
    // Kullanıcı var mı ve şifre doğru mu kontrol et
    if ($say > 0 && password_verify($sifre, $kullanicicek['sifre'])) {
        // Oturum değişkenlerini ata
        $_SESSION['kullanici_id'] = $kullanicicek['id'];
        $_SESSION['kullanici_mail'] = $kullanicicek['mail'];
        $_SESSION['kullanici_ad'] = $kullanicicek['ad'];
        $_SESSION['kullanici_soyad'] = $kullanicicek['soyad'];
        $_SESSION['kullanici_rol'] = $kullanicicek['rol'];
        
        $response = [
            'durum' => 'success',
            'mesaj' => 'Giriş başarılı. Yönlendiriliyorsunuz...'
        ];
    } else {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Kullanıcı adı veya şifre hatalı!'
        ];
    }
    
    // Session sepetindeki ürünleri veritabanı sepetine aktar
    if(isset($_SESSION['sepet']) && isset($_SESSION['kullanici_id'])) {
        $sepet_ekle = $db->prepare("INSERT INTO sepet SET kullanici_id = :kullanici_id, secenek_id = :secenek_id, adet = :adet");
        $sepet_guncelle = $db->prepare("UPDATE sepet SET adet = :adet WHERE kullanici_id = :kullanici_id AND secenek_id = :secenek_id");
        
        foreach($_SESSION['sepet'] as $secenek_id => $item) {
            // Önce seçeneğin ürün ID'sini bul
            $secenek_sor = $db->prepare("SELECT urun_id FROM urun_secenek WHERE secenek_id = :secenek_id");
            $secenek_sor->execute(['secenek_id' => $secenek_id]);
            $urun_id = $secenek_sor->fetch(PDO::FETCH_COLUMN);
            
            if (!$urun_id) continue; // Geçersiz seçenek, atla
            
            // Kullanıcının sepetinde bu seçenek var mı kontrol et
            $sepette = $db->prepare("SELECT * FROM sepet WHERE kullanici_id = :kullanici_id AND secenek_id = :secenek_id");
            $sepette->execute(['kullanici_id' => $_SESSION['kullanici_id'], 'secenek_id' => $secenek_id]);
            $sepette_urun = $sepette->fetch(PDO::FETCH_ASSOC);
            
            if($sepette_urun){
                $yeni_adet = $sepette_urun['adet'] + $item['adet'];
                $sepet_guncelle->execute([
                    'kullanici_id' => $_SESSION['kullanici_id'],
                    'secenek_id' => $secenek_id,
                    'adet' => $yeni_adet
                ]);
            } else {
                $sepet_ekle->execute([
                    'kullanici_id' => $_SESSION['kullanici_id'],
                    'secenek_id' => $secenek_id,
                    'adet' => $item['adet']
                ]);
            }
        }
        
        // Session sepeti temizle
        unset($_SESSION['sepet']);
    }
    
    // AJAX isteği için JSON yanıtı döndür
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Kullanıcı Kayıt İşlemi
if (isset($_POST['islem']) && $_POST['islem'] == 'kullanici_kayit') {
    $ad = htmlspecialchars(trim($_POST['ad']));
    $soyad = htmlspecialchars(trim($_POST['soyad']));
    $mail = htmlspecialchars(trim($_POST['mail']));
    $tel_no = isset($_POST['tel_no']) ? htmlspecialchars(trim($_POST['tel_no'])) : null;
    $sifre = $_POST['sifre'];
    
    // Validasyonlar
    if (empty($ad) || empty($soyad) || empty($mail) || empty($sifre)) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Lütfen zorunlu alanları doldurun!'
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    if (strlen($sifre) < 8) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Şifre en az 8 karakter uzunluğunda olmalıdır!'
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    // E-posta formatı kontrolü
    if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Geçerli bir e-posta adresi girin!'
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    try {
        // E-posta adresi benzersiz mi kontrol et
        $mail_kontrol = $db->prepare("SELECT COUNT(*) as adet FROM kullanicilar WHERE mail = :mail");
        $mail_kontrol->execute(['mail' => $mail]);
        $mail_say = $mail_kontrol->fetch(PDO::FETCH_ASSOC)['adet'];
        
        if ($mail_say > 0) {
            $response = [
                'durum' => 'error',
                'mesaj' => 'Bu e-posta adresi zaten kullanılıyor!'
            ];
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
        
        // Şifreyi hashle
        $sifre_hash = password_hash($sifre, PASSWORD_DEFAULT);
        
        // Kullanıcıyı ekle (rol 'user' olarak ayarla)
        $ekle = $db->prepare("INSERT INTO kullanicilar SET 
            ad = :ad,
            soyad = :soyad,
            mail = :mail,
            tel_no = :tel_no,
            sifre = :sifre,
            rol = :rol,
            kayit_tarihi = NOW()");
            
        $sonuc = $ekle->execute([
            'ad' => $ad,
            'soyad' => $soyad,
            'mail' => $mail,
            'tel_no' => $tel_no,
            'sifre' => $sifre_hash,
            'rol' => 'kullanici'  // Varsayılan rol: kullanıcı
        ]);
        
        if ($sonuc) {
            $kullanici_id = $db->lastInsertId();
            
            $response = [
                'durum' => 'success',
                'mesaj' => 'Kaydınız başarıyla tamamlandı. Giriş sayfasına yönlendiriliyorsunuz.',
                'kullanici_id' => $kullanici_id
            ];
        } else {
            $response = [
                'durum' => 'error',
                'mesaj' => 'Kayıt işlemi sırasında bir hata oluştu.'
            ];
        }
    } catch (PDOException $e) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Veritabanı hatası: ' . $e->getMessage()
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Sepet İçeriğini Getir
if (isset($_GET['islem']) && $_GET['islem'] == 'sepet_getir') {
    // Kullanıcı giriş yapmış mı kontrol et
    $user_logged_in = isset($_SESSION['kullanici_id']);
    $sepet_items = [];
    $toplam_tutar = 0;
    $indirim = 0;
    $kupon = null;
    
    try {
        $stok_aktif_kontrol = $db->prepare("DELETE s
                                            FROM sepet s
                                            JOIN urun_secenek us ON s.secenek_id = us.secenek_id
                                            WHERE us.stok < s.adet OR us.durum = 0;
                                            ");
                $stok_aktif_kontrol->execute();
        if ($user_logged_in) {
            // Veritabanından kullanıcının sepetini getir
            $sepet_sorgu = $db->prepare("
                SELECT s.secenek_id, us.urun_id, s.adet, u.urun_isim, us.fiyat as birim_fiyat, 
                       um.marka_isim, 
                       (SELECT foto_path FROM urun_fotolar WHERE urun_id = us.urun_id ORDER BY id ASC LIMIT 1) as foto_path,
                       (
                         SELECT GROUP_CONCAT(CONCAT(n.ad, ': ', nd.deger) SEPARATOR ', ')
                         FROM secenek_nitelik_degeri snd
                         JOIN nitelik_degeri nd ON snd.deger_id = nd.deger_id
                         JOIN nitelik n ON nd.nitelik_id = n.nitelik_id
                         WHERE snd.secenek_id = s.secenek_id
                       ) as varyasyon_bilgisi
                FROM sepet s
                INNER JOIN urun_secenek us ON s.secenek_id = us.secenek_id
                INNER JOIN urun u ON us.urun_id = u.id
                LEFT JOIN urun_markalar um ON u.urun_marka = um.id
                WHERE s.kullanici_id = :kullanici_id
            ");
            $sepet_sorgu->execute(['kullanici_id' => $_SESSION['kullanici_id']]);
            $sepet_items = $sepet_sorgu->fetchAll(PDO::FETCH_ASSOC);
            
            // Aktif kupon var mı kontrol et
            if (isset($_SESSION['aktif_kupon'])) {
                $kupon_sorgu = $db->prepare("SELECT * FROM kupon WHERE id = :kupon_id");
                $kupon_sorgu->execute(['kupon_id' => $_SESSION['aktif_kupon']]);
                $kupon = $kupon_sorgu->fetch(PDO::FETCH_ASSOC);
            }
        } else {
            // Session'dan sepet bilgilerini al
            if (isset($_SESSION['sepet']) && !empty($_SESSION['sepet'])) {
                // Sepetteki her bir seçenek için bilgileri getir
                foreach ($_SESSION['sepet'] as $secenek_id => $sepet_item) {
                    $secenek_sorgu = $db->prepare("
                        SELECT us.secenek_id, us.urun_id, us.fiyat as birim_fiyat, u.urun_isim, 
                               um.marka_isim, us.stok, 
                               (SELECT foto_path FROM urun_fotolar WHERE urun_id = us.urun_id ORDER BY id ASC LIMIT 1) as foto_path,
                               (
                                 SELECT GROUP_CONCAT(CONCAT(n.ad, ': ', nd.deger) SEPARATOR ', ')
                                 FROM secenek_nitelik_degeri snd
                                 JOIN nitelik_degeri nd ON snd.deger_id = nd.deger_id
                                 JOIN nitelik n ON nd.nitelik_id = n.nitelik_id
                                 WHERE snd.secenek_id = us.secenek_id
                               ) as varyasyon_bilgisi
                        FROM urun_secenek us
                        JOIN urun u ON us.urun_id = u.id
                        LEFT JOIN urun_markalar um ON u.urun_marka = um.id
                        WHERE us.secenek_id = :secenek_id AND us.durum = 1
                    ");
                    $secenek_sorgu->execute(['secenek_id' => $secenek_id]);
                    $secenek = $secenek_sorgu->fetch(PDO::FETCH_ASSOC);
                    
                    if ($secenek) {
                        if($secenek['stok'] < $sepet_item['adet']){
                            $secenek['adet'] = $secenek['stok'];
                            $_SESSION['sepet'][$secenek_id]['adet'] = $secenek['stok'];
                        }else{
                            $secenek['adet'] = $sepet_item['adet'];
                        }
                        $sepet_items[] = $secenek;
                    }
                    else{
                        unset($_SESSION['sepet'][$secenek_id]);
                    }
                }
                
                // Aktif kupon var mı kontrol et
                if (isset($_SESSION['aktif_kupon'])) {
                    $kupon_sorgu = $db->prepare("SELECT * FROM kupon WHERE id = :kupon_id");
                    $kupon_sorgu->execute(['kupon_id' => $_SESSION['aktif_kupon']]);
                    $kupon = $kupon_sorgu->fetch(PDO::FETCH_ASSOC);
                }
            }
        }
        
        // Toplam tutarı hesapla
        foreach ($sepet_items as $item) {
            $toplam_tutar += $item['birim_fiyat'] * $item['adet'];
        }
        
        // Kupon indirimini hesapla
        if ($kupon) {
            if ($toplam_tutar >= $kupon['kupon_min_tutar']) {
                if ($kupon['kupon_tur'] == 'yuzde') {
                    $indirim = $toplam_tutar * ($kupon['kupon_indirim'] / 100);
                } else { // sabit indirim
                    $indirim = $kupon['kupon_indirim'];
                    if ($indirim > $toplam_tutar) {
                        $indirim = $toplam_tutar; // İndirim tutardan fazla olamaz
                    }
                }
            }
        }
        
        $response = [
            'durum' => 'success',
            'sepet' => $sepet_items,
            'toplam' => $toplam_tutar,
            'indirim' => $indirim,
            'kupon' => $kupon
        ];
    } catch (PDOException $e) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Veritabanı hatası: ' . $e->getMessage()
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Sepet Ürün Miktarını Güncelle
if (isset($_POST['islem']) && $_POST['islem'] == 'sepet_guncelle') {
    $secenek_id = intval($_POST['secenek_id']);
    $adet_degisim = intval($_POST['adet_degisim']); // +1 veya -1 gibi değerler
    
    // Kullanıcı giriş yapmış mı kontrol et
    $user_logged_in = isset($_SESSION['kullanici_id']);
    
    try {
        // Seçenek ve ürün bilgisini kontrol et
        $secenek_sor = $db->prepare("SELECT us.secenek_id, us.urun_id, us.stok 
                                    FROM urun_secenek us 
                                    WHERE us.secenek_id = :secenek_id");
        $secenek_sor->execute(['secenek_id' => $secenek_id]);
        $secenek = $secenek_sor->fetch(PDO::FETCH_ASSOC);
        
        if (!$secenek) {
            $response = [
                'durum' => 'error',
                'mesaj' => 'Ürün varyasyonu bulunamadı!'
            ];
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
        
        if ($user_logged_in) {
            // Veritabanında sepetteki ürünü kontrol et
            $sepet_sor = $db->prepare("SELECT * FROM sepet WHERE kullanici_id = :kullanici_id AND secenek_id = :secenek_id");
            $sepet_sor->execute([
                'kullanici_id' => $_SESSION['kullanici_id'],
                'secenek_id' => $secenek_id
            ]);
            $sepet_item = $sepet_sor->fetch(PDO::FETCH_ASSOC);
            
            if (!$sepet_item) {
                $response = [
                    'durum' => 'error',
                    'mesaj' => 'Ürün sepetinizde bulunamadı!'
                ];
                header('Content-Type: application/json');
                echo json_encode($response);
                exit;
            }
            
            // Yeni adet
            $yeni_adet = $sepet_item['adet'] + $adet_degisim;
            
            // 0 veya negatif adette ürün olamaz, en az 1 olmalı
            if ($yeni_adet < 1) {
                $response = [
                    'durum' => 'error',
                    'mesaj' => 'Ürün adedi en az 1 olmalıdır!'
                ];
                header('Content-Type: application/json');
                echo json_encode($response);
                exit;
            }
            
            // Stok kontrolü
            if ($yeni_adet > $secenek['stok']) {
                $response = [
                    'durum' => 'error',
                    'mesaj' => 'Yeterli stok bulunmamaktadır. Mevcut stok: ' . $secenek['stok']
                ];
                header('Content-Type: application/json');
                echo json_encode($response);
                exit;
            }
            
            // Adet güncelle
            $guncelle = $db->prepare("UPDATE sepet SET adet = :adet WHERE kullanici_id = :kullanici_id AND secenek_id = :secenek_id");
            $guncelle->execute([
                'adet' => $yeni_adet,
                'kullanici_id' => $_SESSION['kullanici_id'],
                'secenek_id' => $secenek_id
            ]);
            
            // Toplam ürün sayısını getir
            $sepet_adet_sor = $db->prepare("SELECT SUM(adet) as toplam_adet FROM sepet WHERE kullanici_id = :kullanici_id");
            $sepet_adet_sor->execute(['kullanici_id' => $_SESSION['kullanici_id']]);
            $sepet_adet = $sepet_adet_sor->fetch(PDO::FETCH_ASSOC)['toplam_adet'] ?? 0;
            
            $response = [
                'durum' => 'success',
                'mesaj' => 'Ürün adedi güncellendi.',
                'sepet_adet' => $sepet_adet
            ];
        } else {
            // Session'da sepeti kontrol et
            if (!isset($_SESSION['sepet'][$secenek_id])) {
                $response = [
                    'durum' => 'error',
                    'mesaj' => 'Ürün sepetinizde bulunamadı!'
                ];
                header('Content-Type: application/json');
                echo json_encode($response);
                exit;
            }
            
            // Yeni adet
            $yeni_adet = $_SESSION['sepet'][$secenek_id]['adet'] + $adet_degisim;
            
            // 0 veya negatif adette ürün olamaz, en az 1 olmalı
            if ($yeni_adet < 1) {
                $response = [
                    'durum' => 'error',
                    'mesaj' => 'Ürün adedi en az 1 olmalıdır!'
                ];
                header('Content-Type: application/json');
                echo json_encode($response);
                exit;
            }
            
            // Stok kontrolü
            if ($yeni_adet > $secenek['stok']) {
                $response = [
                    'durum' => 'error',
                    'mesaj' => 'Yeterli stok bulunmamaktadır. Mevcut stok: ' . $secenek['stok']
                ];
                header('Content-Type: application/json');
                echo json_encode($response);
                exit;
            }
            
            // Sepeti güncelle
            $_SESSION['sepet'][$secenek_id]['adet'] = $yeni_adet;
            
            // Toplam ürün sayısını hesapla
            $sepet_adet = 0;
            foreach ($_SESSION['sepet'] as $item) {
                $sepet_adet += $item['adet'];
            }
            
            $response = [
                'durum' => 'success',
                'mesaj' => 'Ürün adedi güncellendi.',
                'sepet_adet' => $sepet_adet
            ];
        }
    } catch (PDOException $e) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Veritabanı hatası: ' . $e->getMessage()
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Sepetten Ürün Silme
if (isset($_POST['islem']) && $_POST['islem'] == 'sepet_urun_sil') {
    $secenek_id = intval($_POST['secenek_id']);
    
    // Kullanıcı giriş yapmış mı kontrol et
    $user_logged_in = isset($_SESSION['kullanici_id']);
    
    try {
        if ($user_logged_in) {
            // Veritabanından ürünü sil
            $sil = $db->prepare("DELETE FROM sepet WHERE kullanici_id = :kullanici_id AND secenek_id = :secenek_id");
            $sil->execute([
                'kullanici_id' => $_SESSION['kullanici_id'],
                'secenek_id' => $secenek_id
            ]);
            
            // Toplam ürün sayısını getir
            $sepet_adet_sor = $db->prepare("SELECT SUM(adet) as toplam_adet FROM sepet WHERE kullanici_id = :kullanici_id");
            $sepet_adet_sor->execute(['kullanici_id' => $_SESSION['kullanici_id']]);
            $sepet_adet = $sepet_adet_sor->fetch(PDO::FETCH_ASSOC)['toplam_adet'] ?? 0;
        } else {
            // Session'dan ürünü sil
            if (isset($_SESSION['sepet'][$secenek_id])) {
                unset($_SESSION['sepet'][$secenek_id]);
            }
            
            // Toplam ürün sayısını hesapla
            $sepet_adet = 0;
            if (isset($_SESSION['sepet'])) {
                foreach ($_SESSION['sepet'] as $item) {
                    $sepet_adet += $item['adet'];
                }
            }
        }
        
        $response = [
            'durum' => 'success',
            'mesaj' => 'Ürün sepetten kaldırıldı.',
            'sepet_adet' => $sepet_adet
        ];
    } catch (PDOException $e) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Veritabanı hatası: ' . $e->getMessage()
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Kupon Uygulama
if (isset($_POST['islem']) && $_POST['islem'] == 'kupon_uygula') {
    $kupon_kod = strtoupper(htmlspecialchars(trim($_POST['kupon_kod'])));
    
    try {
        // Kuponu veritabanından kontrol et
        $kupon_sor = $db->prepare("SELECT * FROM kupon WHERE kupon_kod = :kupon_kod");
        $kupon_sor->execute(['kupon_kod' => $kupon_kod]);
        $kupon = $kupon_sor->fetch(PDO::FETCH_ASSOC);
        
        if (!$kupon) {
            $response = [
                'durum' => 'error',
                'mesaj' => 'Geçersiz kupon kodu!'
            ];
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
        
        // Kupon geçerlilik tarihi kontrolü
        $bugun = date('Y-m-d');
        if ($bugun < $kupon['kupon_baslangic_tarih'] || $bugun > $kupon['kupon_bitis_tarih']) {
            $response = [
                'durum' => 'error',
                'mesaj' => 'Bu kupon süresi dolmuş veya henüz aktif değil!'
            ];
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
        
        // Kupon kullanım limiti kontrolü
        if ($kupon['kupon_limit'] !== null) {
            // Kupon kullanım sayısını kontrol et
            $kullanim_sor = $db->prepare("SELECT COUNT(*) as kullanim_sayisi FROM faturalar WHERE kupon_id = :kupon_id");
            $kullanim_sor->execute(['kupon_id' => $kupon['id']]);
            $kullanim_sayisi = $kullanim_sor->fetch(PDO::FETCH_ASSOC)['kullanim_sayisi'];
            
            if ($kullanim_sayisi >= $kupon['kupon_limit']) {
                $response = [
                    'durum' => 'error',
                    'mesaj' => 'Bu kupon maksimum kullanım limitine ulaşmış!'
                ];
                header('Content-Type: application/json');
                echo json_encode($response);
                exit;
            }
        }
        
        // Sepet toplam tutarını hesapla
        $sepet_toplam = 0;
        if (isset($_SESSION['kullanici_id'])) {
            // Veritabanından kullanıcının sepet toplamını al
            $toplam_sor = $db->prepare("
                SELECT SUM(s.adet * u.fiyat) as toplam
                FROM sepet s
                INNER JOIN urun_secenek u ON s.secenek_id = u.secenek_id
                WHERE s.kullanici_id = :kullanici_id
            ");
            $toplam_sor->execute(['kullanici_id' => $_SESSION['kullanici_id']]);
            $sepet_toplam = $toplam_sor->fetch(PDO::FETCH_ASSOC)['toplam'] ?? 0;
        } else {
            // Session sepetinden toplam hesapla
            if (isset($_SESSION['sepet']) && !empty($_SESSION['sepet'])) {
                foreach ($_SESSION['sepet'] as $item) {
                    // Ürün fiyatını getir
                    $fiyat_sor = $db->prepare("SELECT fiyat FROM urun_secenek WHERE secenek_id = :secenek_id");
                    $fiyat_sor->execute(['secenek_id' => $item['secenek_id']]);
                    $fiyat = $fiyat_sor->fetch(PDO::FETCH_ASSOC)['fiyat'] ?? 0;
                    
                    $sepet_toplam += $fiyat * $item['adet'];
                }
            }
        }
        
        // Minimum tutar kontrolü
        if ($sepet_toplam < $kupon['kupon_min_tutar']) {
            $response = [
                'durum' => 'error',
                'mesaj' => 'Bu kuponu kullanabilmek için minimum sepet tutarı ' . number_format($kupon['kupon_min_tutar'], 2) . ' ₺ olmalıdır!'
            ];
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
        
        // Kuponu aktif et
        $_SESSION['aktif_kupon'] = $kupon['id'];
        
        // İndirim miktarını hesapla
        $indirim = 0;
        if ($kupon['kupon_tur'] == 'yuzde') {
            $indirim = $sepet_toplam * ($kupon['kupon_indirim'] / 100);
            $mesaj = '%' . $kupon['kupon_indirim'] . ' indirim kuponu uygulandı.';
        } else { // sabit indirim
            $indirim = $kupon['kupon_indirim'];
            if ($indirim > $sepet_toplam) {
                $indirim = $sepet_toplam; // İndirim tutardan fazla olamaz
            }
            $mesaj = $kupon['kupon_indirim'] . ' ₺ indirim kuponu uygulandı.';
        }
        
        $response = [
            'durum' => 'success',
            'mesaj' => $mesaj,
            'kupon' => $kupon,
            'indirim' => $indirim
        ];
    } catch (PDOException $e) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Veritabanı hatası: ' . $e->getMessage()
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Kupon Kaldırma
if (isset($_POST['islem']) && $_POST['islem'] == 'kupon_kaldir') {
    // Session'dan kuponu kaldır
    if (isset($_SESSION['aktif_kupon'])) {
        unset($_SESSION['aktif_kupon']);
    }
    
    $response = [
        'durum' => 'success',
        'mesaj' => 'Kupon kaldırıldı.'
    ];
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Tüm Nitelikleri ve Değerlerini Getir
if (isset($_GET['islem']) && $_GET['islem'] == 'nitelik_listele') {
    try {
        // Tüm nitelikleri getir
        $nitelik_sor = $db->prepare("SELECT * FROM nitelik ORDER BY ad ASC");
        $nitelik_sor->execute();
        $nitelikler = [];
        
        while ($nitelik = $nitelik_sor->fetch(PDO::FETCH_ASSOC)) {
            // Her nitelik için değerleri getir
            $deger_sor = $db->prepare("SELECT * FROM nitelik_degeri WHERE nitelik_id = :nitelik_id ORDER BY deger ASC");
            $deger_sor->execute(['nitelik_id' => $nitelik['nitelik_id']]);
            $degerler = $deger_sor->fetchAll(PDO::FETCH_ASSOC);
            
            $nitelikler[] = [
                'id' => $nitelik['nitelik_id'],
                'ad' => $nitelik['ad'],
                'degerler' => $degerler
            ];
        }
        
        $response = [
            'durum' => 'success',
            'nitelikler' => $nitelikler
        ];
    } catch (PDOException $e) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Veritabanı hatası: ' . $e->getMessage()
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Yeni Nitelik Değeri Ekle
if (isset($_POST['islem']) && $_POST['islem'] == 'nitelik_deger_ekle') {
    $nitelik_id = intval($_POST['nitelik_id']);
    $deger = htmlspecialchars(trim($_POST['deger']));
    
    // Validasyon
    if (empty($deger)) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Değer boş olamaz!'
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    try {
        // Aynı değer var mı kontrol et
        $kontrol = $db->prepare("SELECT COUNT(*) as adet FROM nitelik_degeri WHERE nitelik_id = :nitelik_id AND deger = :deger");
        $kontrol->execute([
            'nitelik_id' => $nitelik_id,
            'deger' => $deger
        ]);
        
        if ($kontrol->fetch(PDO::FETCH_ASSOC)['adet'] > 0) {
            $response = [
                'durum' => 'error',
                'mesaj' => 'Bu değer zaten mevcut!'
            ];
        } else {
            // Yeni değeri ekle
            $ekle = $db->prepare("INSERT INTO nitelik_degeri SET nitelik_id = :nitelik_id, deger = :deger");
            $sonuc = $ekle->execute([
                'nitelik_id' => $nitelik_id,
                'deger' => $deger
            ]);
            
            if ($sonuc) {
                $deger_id = $db->lastInsertId();
                $response = [
                    'durum' => 'success',
                    'mesaj' => 'Değer başarıyla eklendi',
                    'deger' => [
                        'deger_id' => $deger_id,
                        'nitelik_id' => $nitelik_id,
                        'deger' => $deger
                    ]
                ];
            } else {
                $response = [
                    'durum' => 'error',
                    'mesaj' => 'Değer eklenirken bir hata oluştu'
                ];
            }
        }
    } catch (PDOException $e) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Veritabanı hatası: ' . $e->getMessage()
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Check Authentication
if (isset($_GET['islem']) && $_GET['islem'] == 'check_auth') {
    // Check if session is started
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    if (isset($_SESSION['kullanici_id'])) {
        $response = [
            'durum' => 'success',
            'mesaj' => 'Kullanıcı giriş yapmış.'
        ];
    } else {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Kullanıcı giriş yapmamış.'
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Get User Profile
if (isset($_GET['islem']) && $_GET['islem'] == 'get_profile') {
    // Check if session is started
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['kullanici_id'])) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Kullanıcı giriş yapmamış.'
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    $kullanici_id = $_SESSION['kullanici_id'];
    
    try {
        $kullanici_sor = $db->prepare("SELECT id, ad, soyad, mail, tel_no FROM kullanicilar WHERE id = :kullanici_id");
        $kullanici_sor->execute(['kullanici_id' => $kullanici_id]);
        $kullanici = $kullanici_sor->fetch(PDO::FETCH_ASSOC);
        
        if ($kullanici) {
            $response = [
                'durum' => 'success',
                'kullanici' => $kullanici
            ];
        } else {
            $response = [
                'durum' => 'error',
                'mesaj' => 'Kullanıcı bulunamadı.'
            ];
        }
    } catch (PDOException $e) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Veritabanı hatası: ' . $e->getMessage()
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Update User Profile
if (isset($_POST['islem']) && $_POST['islem'] == 'profile_guncelle') {
    // Check if session is started
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['kullanici_id'])) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Kullanıcı giriş yapmamış.'
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    $kullanici_id = $_SESSION['kullanici_id'];
    $ad = htmlspecialchars(trim($_POST['ad']));
    $soyad = htmlspecialchars(trim($_POST['soyad']));
    $tel_no = htmlspecialchars(trim($_POST['tel_no']));
    
    // Validasyon kontrolleri
    if (empty($ad) || empty($soyad)) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Ad ve soyad zorunludur!'
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    try {
        $guncelle = $db->prepare("UPDATE kullanicilar SET 
                                ad = :ad,
                                soyad = :soyad,
                                tel_no = :tel_no
                                WHERE id = :kullanici_id");
        
        $sonuc = $guncelle->execute([
            'ad' => $ad,
            'soyad' => $soyad,
            'tel_no' => $tel_no,
            'kullanici_id' => $kullanici_id
        ]);
        
        if ($sonuc) {
            // Session bilgilerini güncelle
            $_SESSION['kullanici_ad'] = $ad;
            $_SESSION['kullanici_soyad'] = $soyad;
            
            $response = [
                'durum' => 'success',
                'mesaj' => 'Profil bilgileriniz başarıyla güncellendi.'
            ];
        } else {
            $response = [
                'durum' => 'error',
                'mesaj' => 'Profil güncellenirken bir hata oluştu.'
            ];
        }
    } catch (PDOException $e) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Veritabanı hatası: ' . $e->getMessage()
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Update User Password
if (isset($_POST['islem']) && $_POST['islem'] == 'sifre_guncelle') {
    // Check if session is started
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['kullanici_id'])) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Kullanıcı giriş yapmamış.'
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    $kullanici_id = $_SESSION['kullanici_id'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validasyon kontrolleri
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Tüm şifre alanları zorunludur!'
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    // Yeni şifre kontrolü
    if ($new_password !== $confirm_password) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Yeni şifreler eşleşmiyor!'
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    try {
        // Mevcut şifreyi kontrol et
        $sifre_sor = $db->prepare("SELECT sifre FROM kullanicilar WHERE id = :kullanici_id");
        $sifre_sor->execute(['kullanici_id' => $kullanici_id]);
        $kullanici = $sifre_sor->fetch(PDO::FETCH_ASSOC);
        
        if (!$kullanici || !password_verify($current_password, $kullanici['sifre'])) {
            $response = [
                'durum' => 'error',
                'mesaj' => 'Mevcut şifre yanlış!'
            ];
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
        
        // Yeni şifreyi hashle
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        // Şifreyi güncelle
        $guncelle = $db->prepare("UPDATE kullanicilar SET sifre = :sifre WHERE id = :kullanici_id");
        $sonuc = $guncelle->execute([
            'sifre' => $hashed_password,
            'kullanici_id' => $kullanici_id
        ]);
        
        if ($sonuc) {
            $response = [
                'durum' => 'success',
                'mesaj' => 'Şifreniz başarıyla güncellendi.'
            ];
        } else {
            $response = [
                'durum' => 'error',
                'mesaj' => 'Şifre güncellenirken bir hata oluştu.'
            ];
        }
    } catch (PDOException $e) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Veritabanı hatası: ' . $e->getMessage()
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Get User Orders
if (isset($_GET['islem']) && $_GET['islem'] == 'get_user_orders') {
    // Check if session is started
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['kullanici_id'])) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Kullanıcı giriş yapmamış.'
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    $kullanici_id = $_SESSION['kullanici_id'];
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
    $offset = ($page - 1) * $limit;
    
    try {
        // Toplam sipariş sayısını al
        $count_stmt = $db->prepare("SELECT COUNT(*) as toplam FROM faturalar WHERE kullanici_id = :kullanici_id");
        $count_stmt->execute(['kullanici_id' => $kullanici_id]);
        $toplam_kayit = $count_stmt->fetch(PDO::FETCH_ASSOC)['toplam'];
        
        // Siparişleri getir
        $siparisler_stmt = $db->prepare("SELECT f.*
                                       FROM faturalar f
                                       WHERE f.kullanici_id = :kullanici_id
                                       ORDER BY f.tarih DESC
                                       LIMIT :limit OFFSET :offset");
        
        $siparisler_stmt->bindValue(':kullanici_id', $kullanici_id);
        $siparisler_stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $siparisler_stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $siparisler_stmt->execute();
        
        $siparisler = $siparisler_stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $response = [
            'durum' => 'success',
            'siparisler' => $siparisler,
            'toplam_kayit' => $toplam_kayit
        ];
    } catch (PDOException $e) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Veritabanı hatası: ' . $e->getMessage()
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}





// Get User Addresses
if (isset($_GET['islem']) && $_GET['islem'] == 'get_user_addresses') {
    // Check if session is started
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['kullanici_id'])) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Kullanıcı giriş yapmamış.'
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    $kullanici_id = $_SESSION['kullanici_id'];
    
    try {
        $adresler_stmt = $db->prepare("SELECT * FROM kullanici_adres 
                                      WHERE kullanici_id = :kullanici_id 
                                      ORDER BY varsayilan DESC, id DESC");
        $adresler_stmt->execute(['kullanici_id' => $kullanici_id]);
        $adresler = $adresler_stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $response = [
            'durum' => 'success',
            'adresler' => $adresler
        ];
    } catch (PDOException $e) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Veritabanı hatası: ' . $e->getMessage()
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Get Single Address
if (isset($_GET['islem']) && $_GET['islem'] == 'get_address') {
    // Check if session is started
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['kullanici_id'])) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Kullanıcı giriş yapmamış.'
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    $kullanici_id = $_SESSION['kullanici_id'];
    $adres_id = intval($_GET['adres_id']);
    
    try {
        $adres_stmt = $db->prepare("SELECT * FROM kullanici_adres 
                                   WHERE id = :adres_id AND kullanici_id = :kullanici_id");
        $adres_stmt->execute([
            'adres_id' => $adres_id,
            'kullanici_id' => $kullanici_id
        ]);
        $adres = $adres_stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($adres) {
            $response = [
                'durum' => 'success',
                'adres' => $adres
            ];
        } else {
            $response = [
                'durum' => 'error',
                'mesaj' => 'Adres bulunamadı.'
            ];
        }
    } catch (PDOException $e) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Veritabanı hatası: ' . $e->getMessage()
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Add New Address
if (isset($_POST['islem']) && $_POST['islem'] == 'add_address') {
    // Check if session is started
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['kullanici_id'])) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Kullanıcı giriş yapmamış.'
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    $kullanici_id = $_SESSION['kullanici_id'];
    $adres_adi = htmlspecialchars(trim($_POST['adres_adi']));
    $ad_soyad = htmlspecialchars(trim($_POST['ad_soyad']));
    $telefon = htmlspecialchars(trim($_POST['telefon']));
    $tc_no = htmlspecialchars(trim($_POST['tc_no']));
    $il = htmlspecialchars(trim($_POST['il']));
    $ilce = htmlspecialchars(trim($_POST['ilce']));
    $adres = htmlspecialchars(trim($_POST['adres']));
    $posta_kodu = htmlspecialchars(trim($_POST['posta_kodu']));
    $varsayilan = isset($_POST['varsayilan']) ? intval($_POST['varsayilan']) : 0;
    
    // Validasyon kontrolleri
    if (empty($adres_adi) || empty($ad_soyad) || empty($telefon) || empty($tc_no) || empty($il) || empty($ilce) || empty($adres)) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Tüm zorunlu alanları doldurun!'
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    try {
        // Eğer varsayılan adres seçildiyse, diğerlerini varsayılan olmaktan çıkar
        if ($varsayilan == 1) {
            $reset_stmt = $db->prepare("UPDATE kullanici_adres SET varsayilan = 0 WHERE kullanici_id = :kullanici_id");
            $reset_stmt->execute(['kullanici_id' => $kullanici_id]);
        }
        
        // Yeni adres ekle
        $insert_stmt = $db->prepare("INSERT INTO kullanici_adres 
                                    (kullanici_id, adres_adi, ad_soyad, telefon, tc_no, il, ilce, adres, posta_kodu, varsayilan)
                                    VALUES 
                                    (:kullanici_id, :adres_adi, :ad_soyad, :telefon, :tc_no, :il, :ilce, :adres, :posta_kodu, :varsayilan)");
        
        $sonuc = $insert_stmt->execute([
            'kullanici_id' => $kullanici_id,
            'adres_adi' => $adres_adi,
            'ad_soyad' => $ad_soyad,
            'telefon' => $telefon,
            'tc_no' => $tc_no,
            'il' => $il,
            'ilce' => $ilce,
            'adres' => $adres,
            'posta_kodu' => $posta_kodu,
            'varsayilan' => $varsayilan
        ]);
        
        if ($sonuc) {
            $response = [
                'durum' => 'success',
                'mesaj' => 'Adres başarıyla eklendi.'
            ];
        } else {
            $response = [
                'durum' => 'error',
                'mesaj' => 'Adres eklenirken bir hata oluştu.'
            ];
        }
    } catch (PDOException $e) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Veritabanı hatası: ' . $e->getMessage()
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Update Address
if (isset($_POST['islem']) && $_POST['islem'] == 'update_address') {
    // Check if session is started
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['kullanici_id'])) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Kullanıcı giriş yapmamış.'
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    $kullanici_id = $_SESSION['kullanici_id'];
    $adres_id = intval($_POST['adres_id']);
    $adres_adi = htmlspecialchars(trim($_POST['adres_adi']));
    $ad_soyad = htmlspecialchars(trim($_POST['ad_soyad']));
    $telefon = htmlspecialchars(trim($_POST['telefon']));
    $tc_no = htmlspecialchars(trim($_POST['tc_no']));
    $il = htmlspecialchars(trim($_POST['il']));
    $ilce = htmlspecialchars(trim($_POST['ilce']));
    $adres = htmlspecialchars(trim($_POST['adres']));
    $posta_kodu = htmlspecialchars(trim($_POST['posta_kodu']));
    $varsayilan = isset($_POST['varsayilan']) ? intval($_POST['varsayilan']) : 0;
    
    // Validasyon kontrolleri
    if (empty($adres_adi) || empty($ad_soyad) || empty($telefon) || empty($tc_no) || empty($il) || empty($ilce) || empty($adres)) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Tüm zorunlu alanları doldurun!'
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    try {
        // Kullanıcının adrese sahip olup olmadığını kontrol et
        $check_stmt = $db->prepare("SELECT id FROM kullanici_adres WHERE id = :adres_id AND kullanici_id = :kullanici_id");
        $check_stmt->execute([
            'adres_id' => $adres_id,
            'kullanici_id' => $kullanici_id
        ]);
        
        if (!$check_stmt->fetch()) {
            $response = [
                'durum' => 'error',
                'mesaj' => 'Bu adrese erişim izniniz yok.'
            ];
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
        
        // Eğer varsayılan adres seçildiyse, diğerlerini varsayılan olmaktan çıkar
        if ($varsayilan == 1) {
            $reset_stmt = $db->prepare("UPDATE kullanici_adres SET varsayilan = 0 WHERE kullanici_id = :kullanici_id");
            $reset_stmt->execute(['kullanici_id' => $kullanici_id]);
        }
        
        // Adresi güncelle
        $update_stmt = $db->prepare("UPDATE kullanici_adres SET 
                                     adres_adi = :adres_adi,
                                     ad_soyad = :ad_soyad,
                                     telefon = :telefon,
                                     tc_no = :tc_no,
                                     il = :il,
                                     ilce = :ilce,
                                     adres = :adres,
                                     posta_kodu = :posta_kodu,
                                     varsayilan = :varsayilan
                                     WHERE id = :adres_id AND kullanici_id = :kullanici_id");
        
        $sonuc = $update_stmt->execute([
            'adres_adi' => $adres_adi,
            'ad_soyad' => $ad_soyad,
            'telefon' => $telefon,
            'tc_no' => $tc_no,
            'il' => $il,
            'ilce' => $ilce,
            'adres' => $adres,
            'posta_kodu' => $posta_kodu,
            'varsayilan' => $varsayilan,
            'adres_id' => $adres_id,
            'kullanici_id' => $kullanici_id
        ]);
        
        if ($sonuc) {
            $response = [
                'durum' => 'success',
                'mesaj' => 'Adres başarıyla güncellendi.'
            ];
        } else {
            $response = [
                'durum' => 'error',
                'mesaj' => 'Adres güncellenirken bir hata oluştu.'
            ];
        }
    } catch (PDOException $e) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Veritabanı hatası: ' . $e->getMessage()
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Set Default Address
if (isset($_POST['islem']) && $_POST['islem'] == 'set_default_address') {
    // Check if session is started
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['kullanici_id'])) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Kullanıcı giriş yapmamış.'
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    $kullanici_id = $_SESSION['kullanici_id'];
    $adres_id = intval($_POST['adres_id']);
    
    try {
        // Kullanıcının adrese sahip olup olmadığını kontrol et
        $check_stmt = $db->prepare("SELECT id FROM kullanici_adres WHERE id = :adres_id AND kullanici_id = :kullanici_id");
        $check_stmt->execute([
            'adres_id' => $adres_id,
            'kullanici_id' => $kullanici_id
        ]);
        
        if (!$check_stmt->fetch()) {
            $response = [
                'durum' => 'error',
                'mesaj' => 'Bu adrese erişim izniniz yok.'
            ];
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
        
        // Tüm adreslerin varsayılan değerini sıfırla
        $reset_stmt = $db->prepare("UPDATE kullanici_adres SET varsayilan = 0 WHERE kullanici_id = :kullanici_id");
        $reset_stmt->execute(['kullanici_id' => $kullanici_id]);
        
        // Seçilen adresi varsayılan yap
        $default_stmt = $db->prepare("UPDATE kullanici_adres SET varsayilan = 1 WHERE id = :adres_id AND kullanici_id = :kullanici_id");
        $sonuc = $default_stmt->execute([
            'adres_id' => $adres_id,
            'kullanici_id' => $kullanici_id
        ]);
        
        if ($sonuc) {
            $response = [
                'durum' => 'success',
                'mesaj' => 'Varsayılan adres başarıyla güncellendi.'
            ];
        } else {
            $response = [
                'durum' => 'error',
                'mesaj' => 'Varsayılan adres güncellenirken bir hata oluştu.'
            ];
        }
    } catch (PDOException $e) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Veritabanı hatası: ' . $e->getMessage()
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Delete Address
if (isset($_POST['islem']) && $_POST['islem'] == 'delete_address') {
    // Check if session is started
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['kullanici_id'])) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Kullanıcı giriş yapmamış.'
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    $kullanici_id = $_SESSION['kullanici_id'];
    $adres_id = intval($_POST['adres_id']);
    
    try {
        // Kullanıcının adrese sahip olup olmadığını ve varsayılan adres olup olmadığını kontrol et
        $check_stmt = $db->prepare("SELECT varsayilan FROM kullanici_adres WHERE id = :adres_id AND kullanici_id = :kullanici_id");
        $check_stmt->execute([
            'adres_id' => $adres_id,
            'kullanici_id' => $kullanici_id
        ]);
        
        $adres = $check_stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$adres) {
            $response = [
                'durum' => 'error',
                'mesaj' => 'Bu adrese erişim izniniz yok.'
            ];
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
        
        // Varsayılan adres silinemez kontrolü
        if ($adres['varsayilan'] == 1) {
            $response = [
                'durum' => 'error',
                'mesaj' => 'Varsayılan adres silinemez. Önce başka bir adresi varsayılan olarak belirleyin.'
            ];
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
        
        // Adresi sil
        $delete_stmt = $db->prepare("DELETE FROM kullanici_adres WHERE id = :adres_id AND kullanici_id = :kullanici_id");
        $sonuc = $delete_stmt->execute([
            'adres_id' => $adres_id,
            'kullanici_id' => $kullanici_id
        ]);
        
        if ($sonuc) {
            $response = [
                'durum' => 'success',
                'mesaj' => 'Adres başarıyla silindi.'
            ];
        } else {
            $response = [
                'durum' => 'error',
                'mesaj' => 'Adres silinirken bir hata oluştu.'
            ];
        }
    } catch (PDOException $e) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Veritabanı hatası: ' . $e->getMessage()
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Tüm markaları getir (AJAX için)
if (isset($_GET['islem']) && $_GET['islem'] == 'marka_getir') {
    // Filtreleme parametreleri
    $kategori_id = isset($_GET['kategori']) ? intval($_GET['kategori']) : null;
    $fiyat_min = isset($_GET['fiyat_min']) && $_GET['fiyat_min'] != '' ? floatval($_GET['fiyat_min']) : null;
    $fiyat_max = isset($_GET['fiyat_max']) && $_GET['fiyat_max'] != '' ? floatval($_GET['fiyat_max']) : null;
    $nitelikler = isset($_GET['nitelikler']) ? json_decode($_GET['nitelikler'], true) : [];
    $search = isset($_GET['search']) ? trim($_GET['search']) : null; // Add search parameter
    
    try {
        // Temel sorgu
        $marka_sorgu = "SELECT DISTINCT m.id, m.marka_isim 
                      FROM urun_markalar m 
                      INNER JOIN urun u ON m.id = u.urun_marka
                      LEFT JOIN urun_secenek us ON u.id = us.urun_id";
        
        // Kategori filtreleme için join
        if ($kategori_id) {
            $marka_sorgu .= " LEFT JOIN urun_kategori_baglantisi ukb ON u.id = ukb.urun_id";
        }
        
        // Kategori isim join (arama için)
        if ($search) {
            if (!$kategori_id) { // Eğer kategori filtresi zaten yoksa join ekleyelim
                $marka_sorgu .= " LEFT JOIN urun_kategori_baglantisi ukb ON u.id = ukb.urun_id";
            }
            $marka_sorgu .= " LEFT JOIN urun_kategoriler uk ON ukb.kategori_id = uk.id";
        }
        
        // Nitelik filtreleme için join
        if (!empty($nitelikler)) {
            $marka_sorgu .= " LEFT JOIN secenek_nitelik_degeri snd ON us.secenek_id = snd.secenek_id";
        }
        
        // WHERE koşulları
        $where_conditions = [];
        $params = [];
        
        // Stok kontrolü
        $where_conditions[] = "u.urun_stok > 0";
        
        // Arama filtresi
        if ($search) {
            $search_term = "%{$search}%";
            $where_conditions[] = "(u.urun_isim LIKE :search_term OR u.urun_aciklama LIKE :search_term2 OR m.marka_isim LIKE :search_term3 OR uk.kategori_isim LIKE :search_term4)";
            $params['search_term'] = $search_term;
            $params['search_term2'] = $search_term;
            $params['search_term3'] = $search_term;
            $params['search_term4'] = $search_term;
        }
        
        // Kategori filtreleme
        if ($kategori_id) {
            // Alt kategorileri dahil etmek için
            $alt_kategoriler = [];
            getSubCategoryIds($db, $kategori_id, $alt_kategoriler);
            $alt_kategoriler[] = $kategori_id;
            
            $kategoriler_str = implode(',', $alt_kategoriler);
            $where_conditions[] = "ukb.kategori_id IN ($kategoriler_str)";
        }
        
        // Fiyat aralığı filtreleme
        if ($fiyat_min !== null) {
            $where_conditions[] = "u.urun_fiyat >= :fiyat_min";
            $params['fiyat_min'] = $fiyat_min;
        }
        
        if ($fiyat_max !== null) {
            $where_conditions[] = "u.urun_fiyat <= :fiyat_max";
            $params['fiyat_max'] = $fiyat_max;
        }
        
        // Nitelik filtreleme
        if (!empty($nitelikler)) {
            foreach ($nitelikler as $nitelik_id => $deger_ids) {
                if (!empty($deger_ids)) {
                    $deger_ids_str = implode(',', $deger_ids);
                    $where_conditions[] = "EXISTS (
                        SELECT 1 FROM secenek_nitelik_degeri snd2 
                        WHERE snd2.secenek_id = us.secenek_id 
                        AND snd2.deger_id IN ($deger_ids_str)
                    )";
                }
            }
        }
        
        // WHERE koşullarını sorguya ekle
        if (!empty($where_conditions)) {
            $marka_sorgu .= " WHERE " . implode(' AND ', $where_conditions);
        }
        
        $marka_sorgu .= " ORDER BY m.marka_isim ASC";
        
        $marka_sor = $db->prepare($marka_sorgu);
        
        // Parametreleri bind et
        foreach ($params as $key => $value) {
            if (is_int($value)) {
                $marka_sor->bindValue(":$key", $value, PDO::PARAM_INT);
            } else {
                $marka_sor->bindValue(":$key", $value);
            }
        }
        
        $marka_sor->execute();
        
        $markalar = array();
        
        while ($marka = $marka_sor->fetch(PDO::FETCH_ASSOC)) {
            $markalar[] = $marka;
        }
        
        $response = [
            'durum' => 'success',
            'markalar' => $markalar
        ];
    } catch (PDOException $e) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Markalar getirilirken bir hata oluştu: ' . $e->getMessage()
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Tüm nitelikleri ve değerlerini getir (AJAX için)
if (isset($_GET['islem']) && $_GET['islem'] == 'nitelik_getir') {
    // Filtreleme parametreleri
    $kategori_id = isset($_GET['kategori']) ? intval($_GET['kategori']) : null;
    $marka_id = isset($_GET['marka']) ? intval($_GET['marka']) : null;
    $fiyat_min = isset($_GET['fiyat_min']) && $_GET['fiyat_min'] != '' ? floatval($_GET['fiyat_min']) : null;
    $fiyat_max = isset($_GET['fiyat_max']) && $_GET['fiyat_max'] != '' ? floatval($_GET['fiyat_max']) : null;
    $nitelikler = isset($_GET['nitelikler']) ? json_decode($_GET['nitelikler'], true) : [];
    $search = isset($_GET['search']) ? trim($_GET['search']) : null; // Add search parameter
    
    // Seçili nitelik ID'sini almak için
    $selected_nitelik_id = isset($_GET['selected_nitelik_id']) ? intval($_GET['selected_nitelik_id']) : null;
    
    try {
        // Tüm nitelikleri getir
        $nitelik_sorgu = "SELECT DISTINCT n.nitelik_id, n.ad 
                       FROM nitelik n
                       INNER JOIN nitelik_degeri nd ON n.nitelik_id = nd.nitelik_id
                       INNER JOIN secenek_nitelik_degeri snd ON nd.deger_id = snd.deger_id
                       INNER JOIN urun_secenek us ON snd.secenek_id = us.secenek_id
                       INNER JOIN urun u ON us.urun_id = u.id";
        
        // Kategori filtreleme için join
        if ($kategori_id) {
            $nitelik_sorgu .= " LEFT JOIN urun_kategori_baglantisi ukb ON u.id = ukb.urun_id";
        }
        
        // Kategori isim join (arama için)
        if ($search) {
            if (!$kategori_id) { // Eğer kategori filtresi zaten yoksa join ekleyelim
                $nitelik_sorgu .= " LEFT JOIN urun_kategori_baglantisi ukb ON u.id = ukb.urun_id";
            }
            $nitelik_sorgu .= " LEFT JOIN urun_kategoriler uk ON ukb.kategori_id = uk.id
                               LEFT JOIN urun_markalar m ON u.urun_marka = m.id";
        } else {
            $nitelik_sorgu .= " LEFT JOIN urun_markalar m ON u.urun_marka = m.id";
        }
        
        // WHERE koşulları
        $where_conditions = [];
        $params = [];
        
        // Stok kontrolü
        $where_conditions[] = "u.urun_stok > 0";
        $where_conditions[] = "us.durum = 1";
        
        // Arama filtresi
        if ($search) {
            $search_term = "%{$search}%";
            $where_conditions[] = "(u.urun_isim LIKE :search_term OR u.urun_aciklama LIKE :search_term2 OR m.marka_isim LIKE :search_term3 OR uk.kategori_isim LIKE :search_term4)";
            $params['search_term'] = $search_term;
            $params['search_term2'] = $search_term;
            $params['search_term3'] = $search_term;
            $params['search_term4'] = $search_term;
        }
        
        // Marka filtreleme
        if ($marka_id) {
            $where_conditions[] = "u.urun_marka = :marka_id";
            $params['marka_id'] = $marka_id;
        }
        
        // Kategori filtreleme
        if ($kategori_id) {
            // Alt kategorileri dahil etmek için
            $alt_kategoriler = [];
            getSubCategoryIds($db, $kategori_id, $alt_kategoriler);
            $alt_kategoriler[] = $kategori_id;
            
            $kategoriler_str = implode(',', $alt_kategoriler);
            $where_conditions[] = "ukb.kategori_id IN ($kategoriler_str)";
        }
        
        // Fiyat aralığı filtreleme
        if ($fiyat_min !== null) {
            $where_conditions[] = "u.urun_fiyat >= :fiyat_min";
            $params['fiyat_min'] = $fiyat_min;
        }
        
        if ($fiyat_max !== null) {
            $where_conditions[] = "u.urun_fiyat <= :fiyat_max";
            $params['fiyat_max'] = $fiyat_max;
        }
        
        // Nitelik filtreleme - seçilen nitelik hariç diğer nitelik filtrelerine bakılır
        if (!empty($nitelikler)) {
            foreach ($nitelikler as $nitelik_id => $deger_ids) {
                // Eğer şu anda seçili olan nitelik değilse filtre uygula
                if ($selected_nitelik_id != $nitelik_id && !empty($deger_ids)) {
                    $deger_ids_str = implode(',', $deger_ids);
                    $where_conditions[] = "EXISTS (
                        SELECT 1 FROM secenek_nitelik_degeri snd2 
                        INNER JOIN urun_secenek us2 ON snd2.secenek_id = us2.secenek_id
                        WHERE us2.urun_id = u.id AND snd2.deger_id IN ($deger_ids_str)
                    )";
                }
            }
        }
        
        // WHERE koşullarını sorguya ekle
        if (!empty($where_conditions)) {
            $nitelik_sorgu .= " WHERE " . implode(' AND ', $where_conditions);
        }
        
        $nitelik_sorgu .= " ORDER BY n.ad ASC";
        
        $nitelik_sor = $db->prepare($nitelik_sorgu);
        
        // Parametreleri bind et
        foreach ($params as $key => $value) {
            if (is_int($value)) {
                $nitelik_sor->bindValue(":$key", $value, PDO::PARAM_INT);
            } else {
                $nitelik_sor->bindValue(":$key", $value);
            }
        }
        
        $nitelik_sor->execute();
        
        $nitelikler_array = array();
        
        while ($nitelik = $nitelik_sor->fetch(PDO::FETCH_ASSOC)) {
            // Her nitelik için değerlerini getir
            $deger_sorgu = "SELECT DISTINCT nd.deger_id, nd.deger 
                         FROM nitelik_degeri nd
                         INNER JOIN secenek_nitelik_degeri snd ON nd.deger_id = snd.deger_id
                         INNER JOIN urun_secenek us ON snd.secenek_id = us.secenek_id
                         INNER JOIN urun u ON us.urun_id = u.id";
            
            // Kategori filtreleme için join
            if ($kategori_id) {
                $deger_sorgu .= " LEFT JOIN urun_kategori_baglantisi ukb ON u.id = ukb.urun_id";
            }
            
            // Kategori isim join (arama için)
            if ($search) {
                if (!$kategori_id) { // Eğer kategori filtresi zaten yoksa join ekleyelim
                    $deger_sorgu .= " LEFT JOIN urun_kategori_baglantisi ukb ON u.id = ukb.urun_id";
                }
                $deger_sorgu .= " LEFT JOIN urun_kategoriler uk ON ukb.kategori_id = uk.id
                                LEFT JOIN urun_markalar m ON u.urun_marka = m.id";
            } else {
                $deger_sorgu .= " LEFT JOIN urun_markalar m ON u.urun_marka = m.id";
            }
            
            $deger_where = ["nd.nitelik_id = :nitelik_id", "u.urun_stok > 0", "us.durum = 1"];
            $deger_params = ['nitelik_id' => $nitelik['nitelik_id']];
            
            // Arama filtresi
            if ($search) {
                $search_term = "%{$search}%";
                $deger_where[] = "(u.urun_isim LIKE :search_term OR u.urun_aciklama LIKE :search_term2 OR m.marka_isim LIKE :search_term3 OR uk.kategori_isim LIKE :search_term4)";
                $deger_params['search_term'] = $search_term;
                $deger_params['search_term2'] = $search_term;
                $deger_params['search_term3'] = $search_term;
                $deger_params['search_term4'] = $search_term;
            }
            
            // Marka filtreleme
            if ($marka_id) {
                $deger_where[] = "u.urun_marka = :marka_id";
                $deger_params['marka_id'] = $marka_id;
            }
            
            // Kategori filtreleme
            if ($kategori_id) {
                $deger_where[] = "ukb.kategori_id IN ($kategoriler_str)";
            }
            
            // Fiyat aralığı filtreleme
            if ($fiyat_min !== null) {
                $deger_where[] = "u.urun_fiyat >= :fiyat_min";
                $deger_params['fiyat_min'] = $fiyat_min;
            }
            
            if ($fiyat_max !== null) {
                $deger_where[] = "u.urun_fiyat <= :fiyat_max";
                $deger_params['fiyat_max'] = $fiyat_max;
            }
            
            // Diğer nitelik filtreleri
            if (!empty($nitelikler)) {
                foreach ($nitelikler as $n_id => $deger_ids) {
                    if ($n_id != $nitelik['nitelik_id'] && !empty($deger_ids)) {
                        $deger_ids_str = implode(',', $deger_ids);
                        $deger_where[] = "EXISTS (
                            SELECT 1 FROM secenek_nitelik_degeri snd2 
                            INNER JOIN urun_secenek us2 ON snd2.secenek_id = us2.secenek_id
                            WHERE us2.urun_id = u.id AND snd2.deger_id IN ($deger_ids_str)
                        )";
                    }
                }
            }
            
            $deger_sorgu .= " WHERE " . implode(' AND ', $deger_where);
            $deger_sorgu .= " ORDER BY nd.deger ASC";
            
            $deger_sor = $db->prepare($deger_sorgu);
            
            // Parametreleri bind et
            foreach ($deger_params as $key => $value) {
                if (is_int($value)) {
                    $deger_sor->bindValue(":$key", $value, PDO::PARAM_INT);
                } else {
                    $deger_sor->bindValue(":$key", $value);
                }
            }
            
            $deger_sor->execute();
            
            $degerler = array();
            while ($deger = $deger_sor->fetch(PDO::FETCH_ASSOC)) {
                $degerler[] = $deger;
            }
            
            // Sadece değeri olan nitelikleri ekle
            if (count($degerler) > 0) {
                $nitelik['degerler'] = $degerler;
                $nitelikler_array[] = $nitelik;
            }
        }
        
        $response = [
            'durum' => 'success',
            'nitelikler' => $nitelikler_array
        ];
    } catch (PDOException $e) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Nitelikler getirilirken bir hata oluştu: ' . $e->getMessage()
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Ürünleri filtrele ve getir (AJAX için)
if (isset($_GET['islem']) && $_GET['islem'] == 'urun_filtrele') {
    // Sayfalama için parametreler
    $sayfa = isset($_GET['sayfa']) ? intval($_GET['sayfa']) : 1;
    $sayfa_basina = 12; // Her sayfada 12 ürün gösterilecek
    $baslangic = ($sayfa - 1) * $sayfa_basina;
    
    // Filtreleme parametreleri
    $kategori_id = isset($_GET['kategori']) ? intval($_GET['kategori']) : null;
    $marka_id = isset($_GET['marka']) ? intval($_GET['marka']) : null;
    $fiyat_min = isset($_GET['fiyat_min']) && $_GET['fiyat_min'] != '' ? floatval($_GET['fiyat_min']) : null;
    $fiyat_max = isset($_GET['fiyat_max']) && $_GET['fiyat_max'] != '' ? floatval($_GET['fiyat_max']) : null;
    $siralama = isset($_GET['siralama']) ? $_GET['siralama'] : 'default';
    $search = isset($_GET['search']) ? trim($_GET['search']) : null; // Search parameter
    

    if($kategori_id != null){
        $kategori_ziyaret_kontrol = $db->prepare("SELECT COUNT(*) as ziyaret_sayisi FROM kategori_ziyaret WHERE kategori_id = :kategori_id and ziyaret_ip = :ziyaret_ip and ziyaret_tarih >= :tarih");
        $kategori_ziyaret_kontrol->execute([
            'kategori_id' => $kategori_id,
            'ziyaret_ip' => $_SERVER['REMOTE_ADDR'],
            'tarih' => date('Y-m-d H:i:s', strtotime('-1 day'))
        ]);
        $kategori_ziyaret_sayisi = $kategori_ziyaret_kontrol->fetch(PDO::FETCH_ASSOC)['ziyaret_sayisi'];
        if($kategori_ziyaret_sayisi == null || $kategori_ziyaret_sayisi < 3){
            $kategori_ziyaret_ekle = $db->prepare("INSERT INTO kategori_ziyaret (kategori_id,ziyaret_ip,ziyaret_tarih) values (:kategori_id,:ziyaret_ip,:ziyaret_tarih)");
            $kategori_ziyaret_ekle->execute([
                'kategori_id' => $kategori_id,
                'ziyaret_ip' => $_SERVER['REMOTE_ADDR'],
                'ziyaret_tarih' => date('Y-m-d H:i:s')
            ]);
        }
    }

    // Nitelik filtreleri
    $nitelikler = isset($_GET['nitelikler']) ? json_decode($_GET['nitelikler'], true) : [];
    
    try {
        // Temel sorgu
        $urun_sorgu = "SELECT DISTINCT u.id, u.urun_isim, u.urun_fiyat, u.urun_stok, m.marka_isim, AVG(ud.puan) as ortalama_puan, COUNT(Distinct ud.puan) as degerlendirme_sayisi, 
                      (SELECT foto_path FROM urun_fotolar WHERE urun_id = u.id ORDER BY id ASC LIMIT 1) as foto_path 
                      FROM urun u 
                      LEFT JOIN urun_markalar m ON u.urun_marka = m.id
                      LEFT JOIN urun_secenek us ON us.urun_id = u.id
                      LEFT JOIN urun_degerlendirme ud ON ud.urun_id = u.id";
        
        // Kategori filtreleme için join
        if ($kategori_id) {
            $urun_sorgu .= " LEFT JOIN urun_kategori_baglantisi ukb ON u.id = ukb.urun_id";
        }
        
        // Kategori isim join (arama için)
        if ($search) {
            if (!$kategori_id) { // Eğer kategori filtresi zaten yoksa join ekleyelim
                $urun_sorgu .= " LEFT JOIN urun_kategori_baglantisi ukb ON u.id = ukb.urun_id";
            }
            $urun_sorgu .= " LEFT JOIN urun_kategoriler uk ON ukb.kategori_id = uk.id";
        }
        
        // Nitelik filtreleme için join
        if (!empty($nitelikler)) {
            $urun_sorgu .= " LEFT JOIN secenek_nitelik_degeri snd ON us.secenek_id = snd.secenek_id";
        }
        
        // WHERE koşulları
        $where_conditions = [];
        $params = [];
        
        // Stok kontrolü
        $where_conditions[] = "u.urun_stok > 0";
        
        // Arama filtresi
        if ($search) {
            $search_term = "%{$search}%";
            $where_conditions[] = "(u.urun_isim LIKE :search_term OR u.urun_aciklama LIKE :search_term2 OR m.marka_isim LIKE :search_term3 OR uk.kategori_isim LIKE :search_term4)";
            $params['search_term'] = $search_term;
            $params['search_term2'] = $search_term;
            $params['search_term3'] = $search_term;
            $params['search_term4'] = $search_term;
        }
        
        // Kategori filtreleme
        if ($kategori_id) {
            // Alt kategorileri dahil etmek için
            $alt_kategoriler = [];
            getSubCategoryIds($db, $kategori_id, $alt_kategoriler);
            $alt_kategoriler[] = $kategori_id;
            
            $kategoriler_str = implode(',', $alt_kategoriler);
            $where_conditions[] = "ukb.kategori_id IN ($kategoriler_str)";
            
            // Kategori bilgisini al
            $kategori_sor = $db->prepare("SELECT kategori_isim FROM urun_kategoriler WHERE id = :kategori_id");
            $kategori_sor->execute(['kategori_id' => $kategori_id]);
            $kategori_bilgisi = $kategori_sor->fetch(PDO::FETCH_ASSOC);
        }
        
        // Marka filtreleme
        if ($marka_id) {
            $where_conditions[] = "u.urun_marka = :marka_id";
            $params['marka_id'] = $marka_id;
        }
        
        // Fiyat aralığı filtreleme
        if ($fiyat_min !== null) {
            $where_conditions[] = "u.urun_fiyat >= :fiyat_min";
            $params['fiyat_min'] = $fiyat_min;
        }
        
        if ($fiyat_max !== null) {
            $where_conditions[] = "u.urun_fiyat <= :fiyat_max";
            $params['fiyat_max'] = $fiyat_max;
        }
        
        // Nitelik filtreleme
        if (!empty($nitelikler)) {
            foreach ($nitelikler as $nitelik_id => $deger_ids) {
                if (!empty($deger_ids)) {
                    $deger_ids_str = implode(',', $deger_ids);
                    $where_conditions[] = "EXISTS (
                        SELECT 1 FROM secenek_nitelik_degeri snd2 
                        WHERE snd2.secenek_id = us.secenek_id 
                        AND snd2.deger_id IN ($deger_ids_str)
                    )";
                }
            }
        }
        
        // WHERE koşullarını sorguya ekle
        if (!empty($where_conditions)) {
            $urun_sorgu .= " WHERE " . implode(' AND ', $where_conditions);
        }
        
        // GROUP BY ekle
        $urun_sorgu .= " GROUP BY u.id";
        
        // Sıralama
        switch ($siralama) {
            case 'newest':
                $urun_sorgu .= " ORDER BY u.id DESC";
                break;
            case 'price_asc':
                $urun_sorgu .= " ORDER BY u.urun_fiyat ASC";
                break;
            case 'price_desc':
                $urun_sorgu .= " ORDER BY u.urun_fiyat DESC";
                break;
            case 'popular':
                $urun_sorgu .= " ORDER BY (SELECT COUNT(*) FROM urun_ziyaret WHERE urun_id = u.id) DESC";
                break;
            default:
                // If search is active and no specific sort is selected, prioritize relevance
                if ($search) {
                    // Use a subquery for ordering by search relevance to avoid GROUP BY issues
                    $urun_sorgu .= " ORDER BY 
                        (CASE 
                            WHEN u.urun_isim LIKE :search_exact THEN 1
                            WHEN m.marka_isim LIKE :search_exact2 THEN 2
                            WHEN (SELECT COUNT(*) FROM urun_kategori_baglantisi ukb2 
                                  JOIN urun_kategoriler uk2 ON ukb2.kategori_id = uk2.id 
                                  WHERE ukb2.urun_id = u.id AND uk2.kategori_isim LIKE :search_exact3) > 0 THEN 3
                            ELSE 4
                        END),
                        (SELECT COUNT(*) FROM urun_ziyaret WHERE urun_id = u.id) DESC";
                    $params['search_exact'] = $search_term;
                    $params['search_exact2'] = $search_term;
                    $params['search_exact3'] = $search_term;
                } else {
                    $urun_sorgu .= " ORDER BY u.id DESC";
                }
                break;
        }
        
        // Toplam ürün sayısını hesapla
        $toplam_sorgu = "SELECT COUNT(DISTINCT u.id) as toplam FROM urun u";
        
        // Join tables in the total query that are used in the WHERE conditions
        $toplam_sorgu .= " LEFT JOIN urun_markalar m ON u.urun_marka = m.id";
        
        // Kategori filtreleme için join
        if ($kategori_id) {
            $toplam_sorgu .= " LEFT JOIN urun_kategori_baglantisi ukb ON u.id = ukb.urun_id";
        }
        
        // Kategori isim join (arama için)
        if ($search) {
            if (!$kategori_id) { // Eğer kategori filtresi zaten yoksa join ekleyelim
                $toplam_sorgu .= " LEFT JOIN urun_kategori_baglantisi ukb ON u.id = ukb.urun_id";
            }
            $toplam_sorgu .= " LEFT JOIN urun_kategoriler uk ON ukb.kategori_id = uk.id";
        }
        
        // Nitelik filtreleme için join
        if (!empty($nitelikler)) {
            $toplam_sorgu .= " LEFT JOIN urun_secenek us ON us.urun_id = u.id";
            $toplam_sorgu .= " LEFT JOIN secenek_nitelik_degeri snd ON us.secenek_id = snd.secenek_id";
        }
        
        // WHERE koşullarını toplam sorgusuna ekle
        if (!empty($where_conditions)) {
            $toplam_sorgu .= " WHERE " . implode(' AND ', $where_conditions);
        }
        
        // Execute total query with the parameters
        $toplam_sor = $db->prepare($toplam_sorgu);
        foreach ($params as $key => $value) {
            // Only bind parameters that are actually used in the total query
            // search_exact parameters are only used in the main query for ordering
            if (!in_array($key, ['search_exact', 'search_exact2', 'search_exact3', 'baslangic', 'sayfa_basina'])) {
                if (is_int($value)) {
                    $toplam_sor->bindValue(":$key", $value, PDO::PARAM_INT);
                } else {
                    $toplam_sor->bindValue(":$key", $value);
                }
            }
        }
        $toplam_sor->execute();
        $toplam_urun = $toplam_sor->fetch(PDO::FETCH_ASSOC)['toplam'];
        
        // Toplam sayfa sayısını hesapla
        $toplam_sayfa = ceil($toplam_urun / $sayfa_basina);
        
        // Sayfalama için LIMIT ekle
        $urun_sorgu .= " LIMIT :baslangic, :sayfa_basina";
        $params['baslangic'] = $baslangic;
        $params['sayfa_basina'] = $sayfa_basina;
        
        // Sorguyu çalıştır
        $urun_sor = $db->prepare($urun_sorgu);
        
        // Parametreleri bind et
        foreach ($params as $key => $value) {
            if (is_int($value)) {
                $urun_sor->bindValue(":$key", $value, PDO::PARAM_INT);
            } else {
                $urun_sor->bindValue(":$key", $value);
            }
        }
        
        $urun_sor->execute();
        $urunler = $urun_sor->fetchAll(PDO::FETCH_ASSOC);
        
        $response = [
            'durum' => 'success',
            'urunler' => $urunler,
            'toplam_urun' => $toplam_urun,
            'toplam_sayfa' => $toplam_sayfa,
            'aktif_sayfa' => $sayfa
        ];
        
        // Kategori bilgisini ekle
        if (isset($kategori_bilgisi)) {
            $response['kategori_bilgisi'] = $kategori_bilgisi;
        }
    } catch (PDOException $e) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Ürünler getirilirken bir hata oluştu: ' . $e->getMessage()
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Ürün favorilere ekleme/çıkarma
if (isset($_POST['islem']) && $_POST['islem'] == 'favori_ekle') {
    if (!isset($_SESSION['kullanici_id'])) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'giris_gerekli'
        ];
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    $urun_id = intval($_POST['urun_id']);
    $kullanici_id = $_SESSION['kullanici_id'];
    
    try {
        // Ürün favorilerde var mı kontrol et
        $favori_kontrol = $db->prepare("SELECT id FROM kullanici_favori WHERE kullanici_id = :kullanici_id AND urun_id = :urun_id");
        $favori_kontrol->execute([
            'kullanici_id' => $kullanici_id,
            'urun_id' => $urun_id
        ]);
        
        if ($favori_kontrol->rowCount() > 0) {
            // Favorilerden çıkar
            $favori_id = $favori_kontrol->fetch(PDO::FETCH_ASSOC)['id'];
            $favori_sil = $db->prepare("DELETE FROM kullanici_favori WHERE id = :id");
            $favori_sil->execute(['id' => $favori_id]);
            
            $response = [
                'durum' => 'success',
                'islem' => 'cikarildi',
                'mesaj' => 'Ürün favorilerden çıkarıldı.'
            ];
        } else {
            // Favorilere ekle
            $favori_ekle = $db->prepare("INSERT INTO kullanici_favori SET kullanici_id = :kullanici_id, urun_id = :urun_id");
            $favori_ekle->execute([
                'kullanici_id' => $kullanici_id,
                'urun_id' => $urun_id
            ]);
            
            $response = [
                'durum' => 'success',
                'islem' => 'eklendi',
                'mesaj' => 'Ürün favorilere eklendi.'
            ];
        }
    } catch (PDOException $e) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'İşlem sırasında bir hata oluştu: ' . $e->getMessage()
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Favori ürünleri getirme işlemi
if (isset($_POST['islem']) && $_POST['islem'] == 'favorileri_getir') {
    // Kullanıcı giriş kontrolü
    if(!isset($_SESSION['kullanici_id'])) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Favori ürünleri görebilmek için giriş yapmalısınız.'
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    $kullanici_id = $_SESSION['kullanici_id'];
    
    try {
        // Kullanıcının favori ürünlerini getir
        $favori_sorgu = "SELECT kf.id as favori_id, u.id as urun_id, u.urun_isim as urun_adi, 
                        u.urun_aciklama, u.urun_fiyat, 
                        (SELECT foto_path FROM urun_fotolar WHERE urun_id = u.id LIMIT 1) as urun_resim
                        FROM kullanici_favori kf
                        JOIN urun u ON kf.urun_id = u.id
                        WHERE kf.kullanici_id = :kullanici_id
                        ORDER BY kf.tarih DESC";
        
        $favori_sor = $db->prepare($favori_sorgu);
        $favori_sor->execute(['kullanici_id' => $kullanici_id]);
        $favoriler = $favori_sor->fetchAll(PDO::FETCH_ASSOC);
        
        if ($favoriler) {
            $response = [
                'durum' => 'success',
                'urunler' => $favoriler
            ];
        } else {
            $response = [
                'durum' => 'error',
                'mesaj' => 'Favori ürün bulunamadı.'
            ];
        }
    } catch (PDOException $e) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Veritabanı hatası: ' . $e->getMessage()
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Favorilerden ürün silme işlemi
if (isset($_POST['islem']) && $_POST['islem'] == 'favori_sil') {
    // Kullanıcı giriş kontrolü
    if(!isset($_SESSION['kullanici_id'])) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Bu işlem için giriş yapmalısınız.'
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    $kullanici_id = $_SESSION['kullanici_id'];
    $urun_id = isset($_POST['urun_id']) ? intval($_POST['urun_id']) : 0;
    
    if ($urun_id <= 0) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Geçersiz ürün ID.'
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    try {
        // Favori kaydını sil
        $favori_sil = $db->prepare("DELETE FROM kullanici_favori 
                                  WHERE kullanici_id = :kullanici_id AND urun_id = :urun_id");
        $sonuc = $favori_sil->execute([
            'kullanici_id' => $kullanici_id,
            'urun_id' => $urun_id
        ]);
        
        if ($sonuc) {
            $response = [
                'durum' => 'success',
                'mesaj' => 'Ürün favorilerinizden kaldırıldı.'
            ];
        } else {
            $response = [
                'durum' => 'error',
                'mesaj' => 'Ürün favorilerinizden kaldırılırken bir hata oluştu.'
            ];
        }
    } catch (PDOException $e) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Veritabanı hatası: ' . $e->getMessage()
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Favori kontrolü işlemi
if (isset($_POST['islem']) && $_POST['islem'] == 'favorileri_kontrol') {
    // Kullanıcı giriş kontrolü
    if(!isset($_SESSION['kullanici_id'])) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Favori ürünleri kontrol etmek için giriş yapmalısınız.'
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    $kullanici_id = $_SESSION['kullanici_id'];
    $urun_id = isset($_POST['urun_id']) ? intval($_POST['urun_id']) : null;
    
    try {
        if ($urun_id) {
            // Tek bir ürün için kontrol
            $favori_kontrol = $db->prepare("SELECT COUNT(*) as adet FROM kullanici_favori 
                                         WHERE kullanici_id = :kullanici_id AND urun_id = :urun_id");
            $favori_kontrol->execute([
                'kullanici_id' => $kullanici_id,
                'urun_id' => $urun_id
            ]);
            $adet = $favori_kontrol->fetch(PDO::FETCH_ASSOC)['adet'];
            
            $response = [
                'durum' => 'success',
                'favori_durumu' => $adet > 0
            ];
        } else {
            // Tüm favori ürün ID'lerini getir
            $favori_sorgu = $db->prepare("SELECT urun_id FROM kullanici_favori WHERE kullanici_id = :kullanici_id");
            $favori_sorgu->execute(['kullanici_id' => $kullanici_id]);
            $favori_urunler = $favori_sorgu->fetchAll(PDO::FETCH_COLUMN);
            
            $response = [
                'durum' => 'success',
                'favori_urunler' => $favori_urunler
            ];
        }
    } catch (PDOException $e) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Veritabanı hatası: ' . $e->getMessage()
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Favorilere ürün ekleme işlemi

if (isset($_POST['islem']) && $_POST['islem'] == 'favori_ekle') {

    // Kullanıcı giriş kontrolü

    if(!isset($_SESSION['kullanici_id'])) {

        $response = [

            'durum' => 'error',

            'mesaj' => 'Bu işlem için giriş yapmalısınız.',

            'kod' => 'giris_gerekli'

        ];

        header('Content-Type: application/json');

        echo json_encode($response);

        exit;

    }

    

    $kullanici_id = $_SESSION['kullanici_id'];

    $urun_id = isset($_POST['urun_id']) ? intval($_POST['urun_id']) : 0;

    

    if ($urun_id <= 0) {

        $response = [

            'durum' => 'error',

            'mesaj' => 'Geçersiz ürün ID.'

        ];

        header('Content-Type: application/json');

        echo json_encode($response);

        exit;

    }

    

    try {

        // Ürün zaten favorilerde mi kontrol et

        $kontrol = $db->prepare("SELECT COUNT(*) as adet FROM kullanici_favori 

                              WHERE kullanici_id = :kullanici_id AND urun_id = :urun_id");

        $kontrol->execute([

            'kullanici_id' => $kullanici_id,

            'urun_id' => $urun_id

        ]);

        $adet = $kontrol->fetch(PDO::FETCH_ASSOC)['adet'];

        

        if ($adet > 0) {

            // Ürün zaten favorilerde, kaldır

            $favori_sil = $db->prepare("DELETE FROM kullanici_favori WHERE kullanici_id = :kullanici_id AND urun_id = :urun_id");

            $sonuc = $favori_sil->execute([

                'kullanici_id' => $kullanici_id,

                'urun_id' => $urun_id

            ]);

            

            if ($sonuc) {

                $response = [

                    'durum' => 'success',

                    'mesaj' => 'Ürün favorilerinizden kaldırıldı.',

                    'islem' => 'kaldirildi'

                ];

            } else {

                $response = [

                    'durum' => 'error',

                    'mesaj' => 'Ürün favorilerinizden kaldırılırken bir hata oluştu.'

                ];

            }

        } else {

            // Favorilere ekle

            $favori_ekle = $db->prepare("INSERT INTO kullanici_favori SET 

                                      kullanici_id = :kullanici_id, 

                                      urun_id = :urun_id, 

                                      eklenme_tarihi = NOW()");

            $sonuc = $favori_ekle->execute([

                'kullanici_id' => $kullanici_id,

                'urun_id' => $urun_id

            ]);

            

            if ($sonuc) {

                $response = [

                    'durum' => 'success',

                    'mesaj' => 'Ürün favorilerinize eklendi.',

                    'islem' => 'eklendi'

                ];

            } else {

                $response = [

                    'durum' => 'error',

                    'mesaj' => 'Ürün favorilerinize eklenirken bir hata oluştu.'

                ];

            }

        }

    } catch (PDOException $e) {

        $response = [

            'durum' => 'error',

            'mesaj' => 'Veritabanı hatası: ' . $e->getMessage()

        ];

    }

    

    header('Content-Type: application/json');

    echo json_encode($response);

    exit;

}



// Check if user is logged in

if (isset($_POST['islem']) && $_POST['islem'] == 'kullanici_kontrol') {

    if (isset($_SESSION['kullanici_mail'])) {

        // User is logged in

        echo json_encode([

            'durum' => 'success'

        ]);

    } else {

        // User is not logged in

        echo json_encode([

            'durum' => 'error',

            'mesaj' => 'Lütfen önce giriş yapın.'

        ]);

    }

    exit;

}



// Add question to product

if (isset($_POST['islem']) && $_POST['islem'] == 'soru_ekle') {

    // Check if user is logged in

    if (!isset($_SESSION['kullanici_mail'])) {

        echo json_encode([

            'durum' => 'error',

            'kod' => 'giris_gerekli',

            'mesaj' => 'Bu işlemi gerçekleştirmek için giriş yapmalısınız.'

        ]);

        exit;

    }

    

    // Get data

    $urun_id = intval($_POST['urun_id']);

    $kullanici_id = intval($_SESSION['kullanici_id']);

    $soru = htmlspecialchars(trim($_POST['soru']));

    $herkese_acik = isset($_POST['herkese_acik']) ? intval($_POST['herkese_acik']) : 0;

    

    // Validate data

    if (empty($soru)) {

        echo json_encode([

            'durum' => 'error',

            'mesaj' => 'Lütfen sorunuzu yazın.'

        ]);

        exit;

    }

    

    // Check if product exists

    $urunKontrol = $db->prepare("SELECT * FROM urun WHERE id = :id");

    $urunKontrol->execute(['id' => $urun_id]);

    

    if ($urunKontrol->rowCount() == 0) {

        echo json_encode([

            'durum' => 'error',

            'mesaj' => 'Ürün bulunamadı.'

        ]);

        exit;

    }

    

    try {

        // Insert question

        $soruEkle = $db->prepare("INSERT INTO urun_sorular SET 

            kullanici_id = :kullanici_id,

            urun_id = :urun_id,

            soru = :soru,

            tarih = NOW(),

            herkese_acik = :herkese_acik

        ");

        

        $insert = $soruEkle->execute([

            'kullanici_id' => $kullanici_id,

            'urun_id' => $urun_id,

            'soru' => $soru,

            'herkese_acik' => $herkese_acik

        ]);

        

        if ($insert) {

            echo json_encode([

                'durum' => 'success',

                'mesaj' => 'Sorunuz başarıyla kaydedildi.'

            ]);

        } else {

            echo json_encode([

                'durum' => 'error',

                'mesaj' => 'Soru kaydedilirken bir hata oluştu.'

            ]);

        }

    } catch(PDOException $e) {

        echo json_encode([

            'durum' => 'error',

            'mesaj' => 'Veritabanı hatası: ' . $e->getMessage()

        ]);

    }

    

    exit;

}



// Get product questions

if (isset($_GET['islem']) && $_GET['islem'] == 'urun_sorular') {

    $urun_id = intval($_GET['urun_id']);

    

    // Pagination parameters

    $sayfa = isset($_GET['sayfa']) ? intval($_GET['sayfa']) : 1;

    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 5;

    $offset = ($sayfa - 1) * $limit;

    

    try {

        // Get questions for the product with pagination

        // If user is logged in, get their private questions too

        // Otherwise only get public questions

        

        if (isset($_SESSION['kullanici_id'])) {

            $kullanici_id = intval($_SESSION['kullanici_id']);

            

            // Get total count

            $toplam_sorgu = $db->prepare("SELECT COUNT(*) as toplam 

                FROM urun_sorular 

                WHERE urun_id = :urun_id AND (herkese_acik = 1 OR kullanici_id = :kullanici_id)");

            $toplam_sorgu->execute([

                'urun_id' => $urun_id,

                'kullanici_id' => $kullanici_id

            ]);

            $toplam = $toplam_sorgu->fetch(PDO::FETCH_ASSOC);

            $toplam_kayit = $toplam['toplam'];

            $toplam_sayfa = ceil($toplam_kayit / $limit);

            

            // Get paginated questions

            $sorular = $db->prepare("SELECT us.*, k.ad as kullanici_ad

                FROM urun_sorular us

                LEFT JOIN kullanicilar k ON us.kullanici_id = k.id

                WHERE us.urun_id = :urun_id 

                AND (us.herkese_acik = 1 OR us.kullanici_id = :kullanici_id)

                ORDER BY us.cevap IS NOT NULL DESC, us.tarih DESC

                LIMIT :limit OFFSET :offset");

            

            $sorular->bindValue(':urun_id', $urun_id, PDO::PARAM_INT);

            $sorular->bindValue(':kullanici_id', $kullanici_id, PDO::PARAM_INT);

            $sorular->bindValue(':limit', $limit, PDO::PARAM_INT);

            $sorular->bindValue(':offset', $offset, PDO::PARAM_INT);

            $sorular->execute();

        } else {

            // Not logged in, only get public questions

            // Get total count

            $toplam_sorgu = $db->prepare("SELECT COUNT(*) as toplam 

                FROM urun_sorular 

                WHERE urun_id = :urun_id AND herkese_acik = 1");

            $toplam_sorgu->execute(['urun_id' => $urun_id]);

            $toplam = $toplam_sorgu->fetch(PDO::FETCH_ASSOC);

            $toplam_kayit = $toplam['toplam'];

            $toplam_sayfa = ceil($toplam_kayit / $limit);

            

            // Get paginated questions

            $sorular = $db->prepare("SELECT us.*, k.ad as kullanici_ad

                FROM urun_sorular us

                LEFT JOIN kullanicilar k ON us.kullanici_id = k.id

                WHERE us.urun_id = :urun_id AND us.herkese_acik = 1

                ORDER BY us.cevap IS NOT NULL DESC, us.tarih DESC

                LIMIT :limit OFFSET :offset");

            

            $sorular->bindValue(':urun_id', $urun_id, PDO::PARAM_INT);

            $sorular->bindValue(':limit', $limit, PDO::PARAM_INT);

            $sorular->bindValue(':offset', $offset, PDO::PARAM_INT);

            $sorular->execute();

        }

        

        $sorularArray = $sorular->fetchAll(PDO::FETCH_ASSOC);

        

        echo json_encode([

            'durum' => 'success',

            'sorular' => $sorularArray,

            'pagination' => [

                'toplam_kayit' => $toplam_kayit,

                'toplam_sayfa' => $toplam_sayfa,

                'aktif_sayfa' => $sayfa,

                'limit' => $limit

            ]

        ]);

    } catch(PDOException $e) {

        echo json_encode([

            'durum' => 'error',

            'mesaj' => 'Veritabanı hatası: ' . $e->getMessage()

        ]);

    }

    

    exit;

}



// Get product reviews

if (isset($_GET['islem']) && $_GET['islem'] == 'urun_degerlendirme') {

    $urun_id = intval($_GET['urun_id']);

    

    // Pagination parameters

    $sayfa = isset($_GET['sayfa']) ? intval($_GET['sayfa']) : 1;

    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 5;

    $offset = ($sayfa - 1) * $limit;

    

    // Filter by rating

    $filter = '';

    $params = ['urun_id' => $urun_id];

    

    if (isset($_GET['puan_filtre']) && !empty($_GET['puan_filtre'])) {

        $puan_filtre = $_GET['puan_filtre'];

        // Validate puan_filtre is a comma-separated list of integers between 1-5

        $puan_array = explode(',', $puan_filtre);

        $valid_puan = array_filter($puan_array, function($puan) {

            return is_numeric($puan) && intval($puan) >= 1 && intval($puan) <= 5;

        });

        

        if (!empty($valid_puan)) {

            // Use named parameters instead of ? placeholders

            $placeholders = [];

            $puan_params = [];

            

            foreach ($valid_puan as $index => $puan) {

                $param_name = "puan_" . $index;

                $placeholders[] = ":$param_name";

                $puan_params[$param_name] = intval($puan);

            }

            

            $filter = " AND puan IN (" . implode(',', $placeholders) . ")";

            $params = array_merge($params, $puan_params);

        }

    }

    

    try {

        // Get total count for pagination

        $toplam_sorgu = $db->prepare("SELECT COUNT(*) as toplam FROM urun_degerlendirme WHERE urun_id = :urun_id" . $filter);

        $toplam_sorgu->execute($params);

        $toplam = $toplam_sorgu->fetch(PDO::FETCH_ASSOC);

        $toplam_kayit = $toplam['toplam'];

        $toplam_sayfa = ceil($toplam_kayit / $limit);

        

        // Get reviews for the product with pagination and filtering

        $query = "SELECT d.*, k.ad as kullanici_ad

            FROM urun_degerlendirme d

            LEFT JOIN kullanicilar k ON d.kullanici_id = k.id

            WHERE d.urun_id = :urun_id" . $filter . "

            ORDER BY d.tarih DESC

            LIMIT :limit OFFSET :offset";

            

        $degerlendirmeler = $db->prepare($query);

        

        // Bind parameters

        $degerlendirmeler->bindValue(':urun_id', $urun_id, PDO::PARAM_INT);

        $degerlendirmeler->bindValue(':limit', $limit, PDO::PARAM_INT);

        $degerlendirmeler->bindValue(':offset', $offset, PDO::PARAM_INT);

        

        // Bind any rating filter parameters

        foreach ($params as $key => $value) {

            if ($key !== 'urun_id' && strpos($key, 'puan_') === 0) {

                $degerlendirmeler->bindValue(':' . $key, $value, PDO::PARAM_INT);

            }

        }

        

        $degerlendirmeler->execute();

        $degerlendirmelerArray = $degerlendirmeler->fetchAll(PDO::FETCH_ASSOC);

        

        // Calculate average rating

        $ortalamaPuan = $db->prepare("SELECT AVG(puan) as ortalama FROM urun_degerlendirme WHERE urun_id = :urun_id");

        $ortalamaPuan->execute(['urun_id' => $urun_id]);

        $ortalama = $ortalamaPuan->fetch(PDO::FETCH_ASSOC);

        

        // Count ratings by score

        $puanlar = [];

        for ($i = 1; $i <= 5; $i++) {

            $puanSorgu = $db->prepare("SELECT COUNT(*) as adet FROM urun_degerlendirme WHERE urun_id = :urun_id AND puan = :puan");

            $puanSorgu->execute([

                'urun_id' => $urun_id,

                'puan' => $i

            ]);

            $puanAdet = $puanSorgu->fetch(PDO::FETCH_ASSOC);

            $puanlar[$i] = intval($puanAdet['adet']);

        }

        

        echo json_encode([

            'durum' => 'success',

            'degerlendirmeler' => $degerlendirmelerArray,

            'ortalama_puan' => $ortalama['ortalama'] ? $ortalama['ortalama'] : 0,

            'puanlar' => $puanlar,

            'pagination' => [

                'toplam_kayit' => $toplam_kayit,

                'toplam_sayfa' => $toplam_sayfa,

                'aktif_sayfa' => $sayfa,

                'limit' => $limit

            ]

        ]);

    } catch(PDOException $e) {

        echo json_encode([

            'durum' => 'error',

            'mesaj' => 'Veritabanı hatası: ' . $e->getMessage()

        ]);

    }

    

    exit;

}



// Get All Reviews

if (isset($_GET['islem']) && $_GET['islem'] == 'get_all_reviews') {

    try {

        $reviews_query = "SELECT ud.*, u.urun_isim, CONCAT(k.ad, ' ', k.soyad) as musteri_ad 

                         FROM urun_degerlendirme ud

                         JOIN urun u ON ud.urun_id = u.id

                         JOIN kullanicilar k ON ud.kullanici_id = k.id

                         ORDER BY ud.tarih DESC";

        $reviews = $db->query($reviews_query)->fetchAll(PDO::FETCH_ASSOC);

        

        $response = [

            'durum' => 'success',

            'data' => $reviews

        ];

    } catch (PDOException $e) {

        $response = [

            'durum' => 'error',

            'mesaj' => 'Veritabanı hatası: ' . $e->getMessage()

        ];

    }

    

    header('Content-Type: application/json');

    echo json_encode($response);

    exit;

}



// Reply to Review

if (isset($_POST['islem']) && $_POST['islem'] == 'reply_to_review') {

    if (!isset($_SESSION['kullanici_rol']) || $_SESSION['kullanici_rol'] != 'admin') {

        $response = [

            'durum' => 'error',

            'mesaj' => 'Bu işlem için yetkiniz yok.'

        ];

        echo json_encode($response);

        exit;

    }



    $review_id = intval($_POST['review_id']);

    $reply = trim($_POST['reply']);

    

    try {

        $update = $db->prepare("UPDATE urun_degerlendirme SET 

                               magaza_yanit = :yanit,

                               magaza_yanit_tarih = NOW()

                               WHERE id = :id");

        $update->execute([

            'yanit' => $reply,

            'id' => $review_id

        ]);

        

        $response = [

            'durum' => 'success',

            'mesaj' => 'Yanıt başarıyla kaydedildi.'

        ];

    } catch (PDOException $e) {

        $response = [

            'durum' => 'error',

            'mesaj' => 'Veritabanı hatası: ' . $e->getMessage()

        ];

    }

    

    echo json_encode($response);

    exit;

}



// Get All Questions

if (isset($_GET['islem']) && $_GET['islem'] == 'get_all_questions') {

    try {

        $questions_query = "SELECT us.*, u.urun_isim, CONCAT(k.ad, ' ', k.soyad) as musteri_ad 

                          FROM urun_sorular us

                          JOIN urun u ON us.urun_id = u.id

                          JOIN kullanicilar k ON us.kullanici_id = k.id

                          ORDER BY us.tarih DESC";

        $questions = $db->query($questions_query)->fetchAll(PDO::FETCH_ASSOC);

        

        $response = [

            'durum' => 'success',

            'data' => $questions

        ];

    } catch (PDOException $e) {

        $response = [

            'durum' => 'error',

            'mesaj' => 'Veritabanı hatası: ' . $e->getMessage()

        ];

    }

    

    header('Content-Type: application/json');

    echo json_encode($response);

    exit;

}



// Answer Question

if (isset($_POST['islem']) && $_POST['islem'] == 'answer_question') {

    if (!isset($_SESSION['kullanici_rol']) || $_SESSION['kullanici_rol'] != 'admin') {

        $response = [

            'durum' => 'error',

            'mesaj' => 'Bu işlem için yetkiniz yok.'

        ];

        echo json_encode($response);

        exit;

    }



    $question_id = intval($_POST['question_id']);

    $answer = trim($_POST['answer']);

    $is_public = intval($_POST['is_public']);

    

    try {

        $update = $db->prepare("UPDATE urun_sorular SET 

                               cevap = :cevap,

                               cevap_tarih = NOW(),

                               herkese_acik = :herkese_acik

                               WHERE id = :id");

        $update->execute([

            'cevap' => $answer,

            'herkese_acik' => $is_public,

            'id' => $question_id

        ]);

        

        $response = [

            'durum' => 'success',

            'mesaj' => 'Yanıt başarıyla kaydedildi.'

        ];

    } catch (PDOException $e) {

        $response = [

            'durum' => 'error',

            'mesaj' => 'Veritabanı hatası: ' . $e->getMessage()

        ];

    }

    

    echo json_encode($response);

    exit;

}





// Bildirim Sayısı Getirme İşlemi

if (isset($_POST['islem']) && $_POST['islem'] == 'bildirim_sayisi') {

    // Kullanıcı oturum kontrolü

    if (!isset($_SESSION['kullanici_id'])) {

        $response = [

            'durum' => 'error',

            'mesaj' => 'Oturum açmanız gerekmektedir.'

        ];

        

        header('Content-Type: application/json');

        echo json_encode($response);

        exit;

    }

    

    $kullanici_id = $_SESSION['kullanici_id'];

    

    try {

        // Okunmamış bildirim sayısını getir

        $bildirim_sorgu = $db->prepare("SELECT COUNT(*) as sayi FROM bildirimler WHERE kullanici_id = :kullanici_id AND okundu = 0");

        $bildirim_sorgu->execute(['kullanici_id' => $kullanici_id]);

        $bildirim_sayisi = $bildirim_sorgu->fetch(PDO::FETCH_ASSOC)['sayi'];

        

        $response = [

            'durum' => 'success',

            'bildirim_sayisi' => $bildirim_sayisi

        ];

    } catch (PDOException $e) {

        $response = [

            'durum' => 'error',

            'mesaj' => 'Veritabanı hatası: ' . $e->getMessage()

        ];

    }

    

    header('Content-Type: application/json');

    echo json_encode($response);

    exit;

}



// Bildirimleri Getirme İşlemi

if (isset($_POST['islem']) && $_POST['islem'] == 'bildirimleri_getir') {

    // Kullanıcı oturum kontrolü

    if (!isset($_SESSION['kullanici_id'])) {

        $response = [

            'durum' => 'error',

            'mesaj' => 'Oturum açmanız gerekmektedir.'

        ];

        

        header('Content-Type: application/json');

        echo json_encode($response);

        exit;

    }

    

    $kullanici_id = $_SESSION['kullanici_id'];

    

    try {

        // Bildirimleri getir (en yeniden en eskiye)

        $bildirimler_sorgu = $db->prepare("SELECT * FROM bildirimler WHERE kullanici_id = :kullanici_id ORDER BY tarih DESC");

        $bildirimler_sorgu->execute(['kullanici_id' => $kullanici_id]);

        $bildirimler = $bildirimler_sorgu->fetchAll(PDO::FETCH_ASSOC);

        

        if (count($bildirimler) > 0) {

            $response = [

                'durum' => 'success',

                'bildirimler' => $bildirimler

            ];

        } else {

            $response = [

                'durum' => 'error',

                'mesaj' => 'Bildirim bulunamadı'

            ];

        }

    } catch (PDOException $e) {

        $response = [

            'durum' => 'error',

            'mesaj' => 'Veritabanı hatası: ' . $e->getMessage()

        ];

    }

    

    header('Content-Type: application/json');

    echo json_encode($response);

    exit;

}



// Bildirimi Okundu Olarak İşaretleme

if (isset($_POST['islem']) && $_POST['islem'] == 'bildirim_okundu') {

    // Kullanıcı oturum kontrolü

    if (!isset($_SESSION['kullanici_id'])) {

        $response = [

            'durum' => 'error',

            'mesaj' => 'Oturum açmanız gerekmektedir.'

        ];

        

        header('Content-Type: application/json');

        echo json_encode($response);

        exit;

    }

    

    $bildirim_id = isset($_POST['bildirim_id']) ? intval($_POST['bildirim_id']) : 0;

    $kullanici_id = $_SESSION['kullanici_id'];

    

    if (!$bildirim_id) {

        $response = [

            'durum' => 'error',

            'mesaj' => 'Geçersiz bildirim ID'

        ];

        

        header('Content-Type: application/json');

        echo json_encode($response);

        exit;

    }

    

    try {

        // Bildirimin kullanıcıya ait olduğunu kontrol et

        $kontrol = $db->prepare("SELECT id FROM bildirimler WHERE id = :id AND kullanici_id = :kullanici_id");

        $kontrol->execute([

            'id' => $bildirim_id,

            'kullanici_id' => $kullanici_id

        ]);

        

        if ($kontrol->rowCount() == 0) {

            $response = [

                'durum' => 'error',

                'mesaj' => 'Bu bildirimi işaretleme yetkiniz yok'

            ];

            

            header('Content-Type: application/json');

            echo json_encode($response);

            exit;

        }

        

        // Bildirimi okundu olarak işaretle

        $guncelle = $db->prepare("UPDATE bildirimler SET okundu = 1 WHERE id = :id");

        $guncelle->execute(['id' => $bildirim_id]);

        

        $response = [

            'durum' => 'success',

            'mesaj' => 'Bildirim okundu olarak işaretlendi'

        ];

    } catch (PDOException $e) {

        $response = [

            'durum' => 'error',

            'mesaj' => 'Veritabanı hatası: ' . $e->getMessage()

        ];

    }

    

    header('Content-Type: application/json');

    echo json_encode($response);

    exit;

}



// Bildirimi Silme İşlemi

if (isset($_POST['islem']) && $_POST['islem'] == 'bildirim_sil') {

    // Kullanıcı oturum kontrolü

    if (!isset($_SESSION['kullanici_id'])) {

        $response = [

            'durum' => 'error',

            'mesaj' => 'Oturum açmanız gerekmektedir.'

        ];

        

        header('Content-Type: application/json');

        echo json_encode($response);

        exit;

    }

    

    $bildirim_id = isset($_POST['bildirim_id']) ? intval($_POST['bildirim_id']) : 0;

    $kullanici_id = $_SESSION['kullanici_id'];

    

    if (!$bildirim_id) {

        $response = [

            'durum' => 'error',

            'mesaj' => 'Geçersiz bildirim ID'

        ];

        

        header('Content-Type: application/json');

        echo json_encode($response);

        exit;

    }

    

    try {

        // Bildirimin kullanıcıya ait olduğunu kontrol et

        $kontrol = $db->prepare("SELECT id FROM bildirimler WHERE id = :id AND kullanici_id = :kullanici_id");

        $kontrol->execute([

            'id' => $bildirim_id,

            'kullanici_id' => $kullanici_id

        ]);

        

        if ($kontrol->rowCount() == 0) {

            $response = [

                'durum' => 'error',

                'mesaj' => 'Bu bildirimi silme yetkiniz yok'

            ];

            

            header('Content-Type: application/json');

            echo json_encode($response);

            exit;

        }

        

        // Bildirimi sil

        $sil = $db->prepare("DELETE FROM bildirimler WHERE id = :id");

        $sil->execute(['id' => $bildirim_id]);

        

        $response = [

            'durum' => 'success',

            'mesaj' => 'Bildirim başarıyla silindi'

        ];

    } catch (PDOException $e) {

        $response = [

            'durum' => 'error',

            'mesaj' => 'Veritabanı hatası: ' . $e->getMessage()

        ];

    }

    

    header('Content-Type: application/json');

    echo json_encode($response);

    exit;

}



// Bildirim Ekleme Yardımcı Fonksiyonu

function bildirimEkle($db, $kullanici_id, $mesaj, $link) {

    try {

        $ekle = $db->prepare("INSERT INTO bildirimler SET 

                             kullanici_id = :kullanici_id,

                             bildirim_mesaj = :mesaj,

                             bildirim_link = :link,

                             tarih = NOW(),

                             okundu = 0");

        

        $ekle->execute([

            'kullanici_id' => $kullanici_id,

            'mesaj' => $mesaj,

            'link' => $link

        ]);

        

        return true;

    } catch (PDOException $e) {

        return false;

    }

}



if (isset($_POST['islem']) && $_POST['islem'] == 'ziyaret_kayit') {

    $site_ziyaret_kontrol = $db->prepare("SELECT COUNT(*) as ziyaret_sayisi FROM site_ziyaret WHERE ziyaret_ip = :ziyaret_ip and ziyaret_tarih >= :tarih");

    $site_ziyaret_kontrol->execute([

        'ziyaret_ip' => $_SERVER['REMOTE_ADDR'],

        'tarih' => date('Y-m-d H:i:s', strtotime('-1 day'))

    ]);

    $site_ziyaret_sayisi = $site_ziyaret_kontrol->fetch(PDO::FETCH_ASSOC)['ziyaret_sayisi'];

    if($site_ziyaret_sayisi == null || $site_ziyaret_sayisi < 3){

        $info = new WhichBrowser\Parser($_SERVER['HTTP_USER_AGENT']);



        $tarayici = $info->browser->toString();   // Chrome, Firefox, etc.

        $os = $info->os->toString();              // Windows, Android, etc.

        $cihaz = $info->device->type ?: 'Bilinmiyor';

        $site_ziyaret_ekle = $db->prepare("INSERT INTO site_ziyaret (ziyaret_ip, ziyaret_tarayici, ziyaret_cihaz, ziyaret_os, ziyaret_tarih) values (:ziyaret_ip, :ziyaret_tarayici, :ziyaret_cihaz, :ziyaret_os, :ziyaret_tarih)");

        $site_ziyaret_ekle->execute([

            'ziyaret_ip' => $_SERVER['REMOTE_ADDR'],

            'ziyaret_tarayici' => $tarayici,

            'ziyaret_cihaz' => $cihaz,

            'ziyaret_os' => $os,

            'ziyaret_tarih' => date('Y-m-d H:i:s')

        ]);

    }

}


// Banka Hesapları Yönetimi
if (isset($_POST['islem']) && $_POST['islem'] == 'banka_hesaplari_getir') {
    // Clear any output buffers
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    try {
        $hesaplar_sor = $db->prepare("SELECT * FROM banka_hesaplari ORDER BY id DESC");
        $hesaplar_sor->execute();
        $hesaplar = $hesaplar_sor->fetchAll(PDO::FETCH_ASSOC);
        
        $response = [
            'durum' => 'success',
            'data' => $hesaplar
        ];
    } catch (PDOException $e) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Banka hesapları getirilirken hata oluştu: ' . $e->getMessage()
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

if (isset($_POST['islem']) && $_POST['islem'] == 'banka_hesap_detay') {
    // Clear any output buffers
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    $hesap_id = intval($_POST['hesap_id']);
    
    try {
        $hesap_sor = $db->prepare("SELECT * FROM banka_hesaplari WHERE id = :id");
        $hesap_sor->execute(['id' => $hesap_id]);
        $hesap = $hesap_sor->fetch(PDO::FETCH_ASSOC);
        
        if ($hesap) {
            $response = [
                'durum' => 'success',
                'data' => $hesap
            ];
        } else {
            $response = [
                'durum' => 'error',
                'mesaj' => 'Banka hesabı bulunamadı'
            ];
        }
    } catch (PDOException $e) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Banka hesabı getirilirken hata oluştu: ' . $e->getMessage()
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

if (isset($_POST['islem']) && $_POST['islem'] == 'banka_hesap_kaydet') {
    // Clear any output buffers
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    $hesap_id = isset($_POST['hesap_id']) ? intval($_POST['hesap_id']) : 0;
    $banka_adi = trim($_POST['banka_adi']);
    $hesap_sahibi = trim($_POST['hesap_sahibi']);
    $sube_adi = trim($_POST['sube_adi']);
    $sube_kodu = trim($_POST['sube_kodu']);
    $hesap_no = trim($_POST['hesap_no']);
    $iban = trim($_POST['iban']);
    $durum = intval($_POST['durum']);
    
    // Validation
    if (empty($banka_adi) || empty($hesap_sahibi) || empty($iban)) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Banka adı, hesap sahibi ve IBAN alanları zorunludur'
        ];
    } else {
        try {
            if ($hesap_id > 0) {
                // Update existing account
                $guncelle = $db->prepare("UPDATE banka_hesaplari SET 
                    banka_adi = :banka_adi,
                    hesap_sahibi = :hesap_sahibi,
                    sube_adi = :sube_adi,
                    sube_kodu = :sube_kodu,
                    hesap_no = :hesap_no,
                    iban = :iban,
                    durum = :durum
                    WHERE id = :id");
                
                $guncelle->execute([
                    'banka_adi' => $banka_adi,
                    'hesap_sahibi' => $hesap_sahibi,
                    'sube_adi' => $sube_adi,
                    'sube_kodu' => $sube_kodu,
                    'hesap_no' => $hesap_no,
                    'iban' => $iban,
                    'durum' => $durum,
                    'id' => $hesap_id
                ]);
                
                $response = [
                    'durum' => 'success',
                    'mesaj' => 'Banka hesabı başarıyla güncellendi'
                ];
            } else {
                // Insert new account
                $ekle = $db->prepare("INSERT INTO banka_hesaplari SET 
                    banka_adi = :banka_adi,
                    hesap_sahibi = :hesap_sahibi,
                    sube_adi = :sube_adi,
                    sube_kodu = :sube_kodu,
                    hesap_no = :hesap_no,
                    iban = :iban,
                    durum = :durum,
                    created_at = NOW()");
                
                $ekle->execute([
                    'banka_adi' => $banka_adi,
                    'hesap_sahibi' => $hesap_sahibi,
                    'sube_adi' => $sube_adi,
                    'sube_kodu' => $sube_kodu,
                    'hesap_no' => $hesap_no,
                    'iban' => $iban,
                    'durum' => $durum
                ]);
                
                $response = [
                    'durum' => 'success',
                    'mesaj' => 'Banka hesabı başarıyla eklendi'
                ];
            }
        } catch (PDOException $e) {
            $response = [
                'durum' => 'error',
                'mesaj' => 'Banka hesabı kaydedilirken hata oluştu: ' . $e->getMessage()
            ];
        }
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

if (isset($_POST['islem']) && $_POST['islem'] == 'banka_hesap_sil') {
    // Clear any output buffers
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    $hesap_id = intval($_POST['hesap_id']);
    
    try {
        $sil = $db->prepare("DELETE FROM banka_hesaplari WHERE id = :id");
        $sil->execute(['id' => $hesap_id]);
        
        if ($sil->rowCount() > 0) {
            $response = [
                'durum' => 'success',
                'mesaj' => 'Banka hesabı başarıyla silindi'
            ];
        } else {
            $response = [
                'durum' => 'error',
                'mesaj' => 'Banka hesabı bulunamadı'
            ];
        }
    } catch (PDOException $e) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Banka hesabı silinirken hata oluştu: ' . $e->getMessage()
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Checkout ve Sipariş Yönetimi
if (isset($_POST['islem']) && $_POST['islem'] == 'sepet_ozeti_getir') {
    // Clear any output buffers
    
    $kullanici_id = $_SESSION['kullanici_id'];
    
    try {
        // Sepetteki ürünleri getir
        $sepet_sor = $db->prepare("
            SELECT s.id, s.adet, u.urun_isim, us.fiyat, uf.foto_path, s.secenek_id
                FROM sepet s
                LEFT JOIN urun_secenek us ON s.secenek_id = us.secenek_id
                LEFT JOIN urun u ON us.urun_id = u.id
                LEFT JOIN urun_fotolar uf ON uf.id = (
                                SELECT MIN(id) 
                                FROM urun_fotolar 
                                WHERE urun_fotolar.urun_id = u.id
                            )
                WHERE s.kullanici_id = :kullanici_id
        ");
        $sepet_sor->execute(['kullanici_id' => $kullanici_id]);
        $sepet_urunler = $sepet_sor->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($sepet_urunler)) {
            $response = [
                'durum' => 'error',
                'mesaj' => 'Sepetiniz boş'
            ];
        } else {
            $toplam = 0;
            $urunler = [];
            
            foreach ($sepet_urunler as $urun) {
                $urun_toplam = $urun['fiyat'] * $urun['adet'];
                $toplam += $urun_toplam;
                
                $secenek_aciklama = $db->prepare("SELECT * FROM secenek_nitelik_degeri snd
                Left Join nitelik_degeri nd ON snd.deger_id = nd.deger_id
                WHERE snd.secenek_id = :secenek_id");
                $secenek_aciklama->execute(['secenek_id' => $urun['secenek_id']]);
                $secenek_aciklama = $secenek_aciklama->fetchAll(PDO::FETCH_ASSOC);

                $secenek_aciklama_full = "";
                $i = 0;
                foreach ($secenek_aciklama as $aciklama) {
                    if($i != 0){
                        $secenek_aciklama_full .= " - ";
                    }
                    $secenek_aciklama_full .= $aciklama['deger'];
                    $i++;
                }

                $urunler[] = [
                    'id' => $urun['id'],
                    'urun_isim' => $urun['urun_isim'],
                    'secenek_aciklama' => $secenek_aciklama_full,
                    'adet' => $urun['adet'],
                    'birim_fiyat' => $urun['fiyat'],
                    'toplam_fiyat' => number_format($urun_toplam, 2)
                ];
            }
            
            $indirim = 0;
            if(isset($_SESSION['aktif_kupon'])){
                $kupon_sor = $db->prepare("SELECT * FROM kupon WHERE id = :id");
                $kupon_sor->execute(['id' => $_SESSION['aktif_kupon']]);
                $kupon = $kupon_sor->fetch(PDO::FETCH_ASSOC);
                
                if($kupon['kupon_tur'] == "sabit"){
                    $indirim = $kupon['kupon_indirim'];
                }else{
                    $indirim = $toplam * ($kupon['kupon_indirim'] / 100);
                }
            }

            $response = [
                'durum' => 'success',
                'urunler' => $urunler,
                'toplam' => number_format($toplam, 2),
                'indirim' => number_format($indirim, 2),
                'final' => number_format($toplam - $indirim, 2)
            ];
        }
    } catch (PDOException $e) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Sepet bilgileri getirilirken hata oluştu: ' . $e->getMessage()
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

if (isset($_POST['islem']) && $_POST['islem'] == 'adresler_getir') {
    // Clear any output buffers
    
    $kullanici_id = $_SESSION['kullanici_id'];
    
    try {
        $adresler_sor = $db->prepare("SELECT * FROM kullanici_adres WHERE kullanici_id = :kullanici_id ORDER BY varsayilan DESC, id DESC");
        $adresler_sor->execute(['kullanici_id' => $kullanici_id]);
        $adresler = $adresler_sor->fetchAll(PDO::FETCH_ASSOC);
        
        $response = [
            'durum' => 'success',
            'adresler' => $adresler
        ];
    } catch (PDOException $e) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Adresler getirilirken hata oluştu: ' . $e->getMessage()
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

if (isset($_POST['islem']) && $_POST['islem'] == 'siparis_ozeti_getir') {
    // Clear any output buffers

    
    $kullanici_id = $_SESSION['kullanici_id'];
    $adres_id = intval($_POST['adres_id']);
    $odeme_yontemi = $_POST['odeme_yontemi'];
    
    try {
        // Adres bilgilerini getir
        $adres_sor = $db->prepare("SELECT * FROM kullanici_adres WHERE id = :id AND kullanici_id = :kullanici_id");
        $adres_sor->execute(['id' => $adres_id, 'kullanici_id' => $kullanici_id]);
        $adres = $adres_sor->fetch(PDO::FETCH_ASSOC);
        
        if (!$adres) {
            $response = [
                'durum' => 'error',
                'mesaj' => 'Geçersiz adres'
            ];
        } else {
            // Sepetteki ürünleri getir
            $sepet_sor = $db->prepare("
                SELECT s.id, s.adet, u.urun_isim, us.fiyat, uf.foto_path, s.secenek_id
                FROM sepet s
                LEFT JOIN urun_secenek us ON s.secenek_id = us.secenek_id
                LEFT JOIN urun u ON us.urun_id = u.id
                LEFT JOIN urun_fotolar uf ON uf.id = (
                                SELECT MIN(id) 
                                FROM urun_fotolar 
                                WHERE urun_fotolar.urun_id = u.id
                            )
                WHERE s.kullanici_id = :kullanici_id
            ");
            $sepet_sor->execute(['kullanici_id' => $kullanici_id]);
            $sepet_urunler = $sepet_sor->fetchAll(PDO::FETCH_ASSOC);
            
            $ara_toplam = 0;
            $urunler = [];
            
            foreach ($sepet_urunler as $urun) {
                $urun_toplam = $urun['fiyat'] * $urun['adet'];
                $ara_toplam += $urun_toplam;
                
                $secenek_aciklama = $db->prepare("SELECT * FROM secenek_nitelik_degeri snd
                Left Join nitelik_degeri nd ON snd.deger_id = nd.deger_id
                WHERE snd.secenek_id = :secenek_id");
                $secenek_aciklama->execute(['secenek_id' => $urun['secenek_id']]);
                $secenek_aciklama = $secenek_aciklama->fetchAll(PDO::FETCH_ASSOC);

                $secenek_aciklama_full = "";
                $i = 0;
                foreach ($secenek_aciklama as $aciklama) {
                    if($i != 0){
                        $secenek_aciklama_full .= " - ";
                    }
                    $secenek_aciklama_full .= $aciklama['deger'];
                    $i++;
                }

                $urunler[] = [
                    'id' => $urun['id'],
                    'urun_isim' => $urun['urun_isim'],
                    'secenek_aciklama' => $secenek_aciklama_full,
                    'adet' => $urun['adet'],
                    'birim_fiyat' => $urun['fiyat'],
                    'toplam_fiyat' => number_format($urun_toplam, 2)
                ];
            }
            
            $indirim = 0;
            if(isset($_SESSION['aktif_kupon'])){
                $kupon_sor = $db->prepare("SELECT * FROM kupon WHERE id = :id");
                $kupon_sor->execute(['id' => $_SESSION['aktif_kupon']]);
                $kupon = $kupon_sor->fetch(PDO::FETCH_ASSOC);
                if($kupon['kupon_tur'] == "sabit"){
                    $indirim = $kupon['kupon_indirim'];
                }else{
                    $indirim = $ara_toplam * ($kupon['kupon_indirim'] / 100);
                }
            }


            $response = [
                'durum' => 'success',
                'data' => [
                    'adres' => $adres,
                    'urunler' => $urunler,
                    'ara_toplam' => number_format($ara_toplam, 2),
                    'indirim' => number_format($indirim, 2),
                    'toplam' => number_format($ara_toplam - $indirim, 2) // Kargo ücretsiz
                ]
            ];
        }
    } catch (PDOException $e) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Sipariş özeti getirilirken hata oluştu: ' . $e->getMessage()
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

if (isset($_POST['islem']) && $_POST['islem'] == 'siparis_olustur') {
    // Clear any output buffers

    
    $kullanici_id = $_SESSION['kullanici_id'];
    $adres_id = intval($_POST['adres_id']);
    $odeme_yontemi = $_POST['odeme_yontemi'];
    
    try {
        // Adres kontrolü
        $adres_sor = $db->prepare("SELECT * FROM kullanici_adres WHERE id = :id AND kullanici_id = :kullanici_id");
        $adres_sor->execute(['id' => $adres_id, 'kullanici_id' => $kullanici_id]);
        $adres = $adres_sor->fetch(PDO::FETCH_ASSOC);
        
        if (!$adres) {
            $response = [
                'durum' => 'error',
                'mesaj' => 'Geçersiz adres'
            ];
        } else {
            // Sepetteki ürünleri getir
            $sepet_sor = $db->prepare("
                SELECT s.id, s.adet, u.urun_isim, us.fiyat, uf.foto_path, s.secenek_id
                FROM sepet s
                LEFT JOIN urun_secenek us ON s.secenek_id = us.secenek_id
                LEFT JOIN urun u ON us.urun_id = u.id
                LEFT JOIN urun_fotolar uf ON uf.id = (
                                SELECT MIN(id) 
                                FROM urun_fotolar 
                                WHERE urun_fotolar.urun_id = u.id
                            )
                WHERE s.kullanici_id = :kullanici_id
            ");
            $sepet_sor->execute(['kullanici_id' => $kullanici_id]);
            $sepet_urunler = $sepet_sor->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($sepet_urunler)) {
                $response = [
                    'durum' => 'error',
                    'mesaj' => 'Sepetiniz boş'
                ];
            } else {
                $db->beginTransaction();
                
                try {
                    // Toplam hesapla
                    $ara_toplam = 0;
                    foreach ($sepet_urunler as $urun) {
                        $ara_toplam += $urun['fiyat'] * $urun['adet'];
                    }

                    $indirim = 0;
                    if(isset($_SESSION['aktif_kupon'])){
                        $kupon_sor = $db->prepare("SELECT * FROM kupon WHERE id = :id");
                        $kupon_sor->execute(['id' => $_SESSION['aktif_kupon']]);
                        $kupon = $kupon_sor->fetch(PDO::FETCH_ASSOC);
                        if($kupon['kupon_tur'] == "sabit"){
                            $indirim = $kupon['kupon_indirim'];
                        }else{
                            $indirim = $ara_toplam * ($kupon['kupon_indirim'] / 100);
                        }
                    }

                    // Fatura numarası oluştur
                    $fatura_no = 'INV-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
                    
                    $kupon_id = null;
                    if(isset($_SESSION['aktif_kupon'])){
                        $kupon_id = $_SESSION['aktif_kupon'];
                    }

                    // Sipariş oluştur
                    $siparis_ekle = $db->prepare("INSERT INTO faturalar SET 
                        fatura_no = :fatura_no,
                        kullanici_id = :kullanici_id,
                        adres_id = :adres_id,
                        ara_toplam = :ara_toplam,
                        indirim_tutari = :indirim_tutari,
                        kupon_id = :kupon_id,
                        toplam = :toplam,
                        odeme_yontemi = :odeme_yontemi,
                        tarih = NOW(),
                        siparis_durumu = 'beklemede'");
                    
                    $siparis_ekle->execute([
                        'fatura_no' => $fatura_no,
                        'kullanici_id' => $kullanici_id,
                        'adres_id' => $adres_id,
                        'ara_toplam' => $ara_toplam,
                        'toplam' => $ara_toplam - $indirim,
                        'odeme_yontemi' => $odeme_yontemi,
                        'kupon_id' => $kupon_id,
                        'indirim_tutari' => $indirim,
                    ]);
                    
                    $siparis_id = $db->lastInsertId();
                    
                    // Sipariş ürünlerini ekle
                    foreach ($sepet_urunler as $urun) {
                        $urun_ekle = $db->prepare("INSERT INTO fatura_urunler SET 
                            fatura_id = :fatura_id,
                            secenek_id = :secenek_id,
                            miktar = :miktar,
                            birim_fiyat = :birim_fiyat");
                        
                        $urun_ekle->execute([
                            'fatura_id' => $siparis_id,
                            'secenek_id' => $urun['secenek_id'],
                            'miktar' => $urun['adet'],
                            'birim_fiyat' => $urun['fiyat']
                        ]);
                    }
                    
                    // Sepeti temizle
                    $sepet_temizle = $db->prepare("DELETE FROM sepet WHERE kullanici_id = :kullanici_id");
                    $sepet_temizle->execute(['kullanici_id' => $kullanici_id]);
                    
                    $db->commit();
                    if(isset($_SESSION['aktif_kupon'])){
                        unset($_SESSION['aktif_kupon']);
                    }
                    $response = [
                        'durum' => 'success',
                        'mesaj' => 'Sipariş başarıyla oluşturuldu',
                        'siparis_id' => $siparis_id
                    ];
                } catch (Exception $e) {
                    $db->rollBack();
                    throw $e;
                }
            }
        }
    } catch (PDOException $e) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Sipariş oluşturulurken hata oluştu: ' . $e->getMessage()
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

if (isset($_POST['islem']) && $_POST['islem'] == 'siparis_detay_getir') {

    // Clear any output buffers



    

    $kullanici_id = $_SESSION['kullanici_id'];

    $siparis_id = intval($_POST['siparis_id']);

    

    try {

        // Sipariş bilgilerini getir

        $siparis_sor = $db->prepare("

            SELECT f.*, ka.*

            FROM faturalar f

            LEFT JOIN kullanici_adres ka ON f.adres_id = ka.id

            WHERE f.id = :siparis_id AND f.kullanici_id = :kullanici_id

        ");

        $siparis_sor->execute(['siparis_id' => $siparis_id, 'kullanici_id' => $kullanici_id]);

        $siparis = $siparis_sor->fetch(PDO::FETCH_ASSOC);

        

        if (!$siparis) {

            $response = [

                'durum' => 'error',

                'mesaj' => 'Sipariş bulunamadı'

            ];

        } else {

            // Sipariş ürünlerini getir

            $urunler_sor = $db->prepare("

                SELECT fu.*, u.urun_isim

                FROM fatura_urunler fu

                LEFT JOIN urun_secenek us ON fu.secenek_id = us.secenek_id

                LEFT JOIN urun u ON us.urun_id = u.id

                WHERE fu.fatura_id = :fatura_id

            ");

            $urunler_sor->execute(['fatura_id' => $siparis_id]);

            $urunler = $urunler_sor->fetchAll(PDO::FETCH_ASSOC);

            

            $siparis['urunler'] = $urunler;

            

            $siparis_adres = $db->prepare("SELECT * FROM kullanici_adres WHERE id = :id");

            $siparis_adres->execute(['id' => $siparis['adres_id']]);

            $siparis_adres = $siparis_adres->fetch(PDO::FETCH_ASSOC);

            $siparis['adres'] = $siparis_adres;

            

            $siparis['adres'] = $siparis_adres;



            $response = [

                'durum' => 'success',

                'data' => $siparis

            ];

        }

    } catch (PDOException $e) {

        $response = [

            'durum' => 'error',

            'mesaj' => 'Sipariş detayı getirilirken hata oluştu: ' . $e->getMessage()

        ];

    }

    

    header('Content-Type: application/json');

    echo json_encode($response);

    exit;

}

if (isset($_POST['islem']) && $_POST['islem'] == 'siparis_odeme_onayla') {
    $siparis_id = intval($_POST['siparis_id']);
    $odeme_yontemi = $_POST['odeme_yontemi'];
    
    try {
        // Sipariş durumunu güncelle
        $guncelle = $db->prepare("UPDATE faturalar SET siparis_durumu = 'onaylandi' WHERE id = :siparis_id");
        $guncelle->execute(['siparis_id' => $siparis_id]);
        $response = [
            'durum' => 'success',
            'mesaj' => 'Sipariş ödeme onaylandı'
        ];
    } catch (PDOException $e) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Sipariş ödeme onaylanırken hata oluştu: ' . $e->getMessage()
        ];
    }
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

if (isset($_GET['islem']) && $_GET['islem'] == 'clear_unconfirmed_orders') {
    try {
        $sil = $db->prepare("DELETE FROM faturalar WHERE siparis_durumu = 'beklemede' AND odeme_yontemi = 'kart' AND tarih < NOW() - INTERVAL 10 MINUTE");
        $sil->execute();
        $response = [
            'durum' => 'success',
            'mesaj' => 'Beklemede olan siparişler silindi'
        ];
    } catch (PDOException $e) {
        $response = [
            'durum' => 'error',
            'mesaj' => 'Beklemede olan siparişler silinirken hata oluştu: ' . $e->getMessage()
        ];
    }
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>
