-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1
-- Üretim Zamanı: 26 Kas 2025, 16:32:41
-- Sunucu sürümü: 8.0.40
-- PHP Sürümü: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `eticaret`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `banka_hesaplari`
--

CREATE TABLE `banka_hesaplari` (
  `id` int NOT NULL,
  `banka_adi` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `hesap_sahibi` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `sube_adi` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `sube_kodu` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `hesap_no` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `iban` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `durum` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `bildirimler`
--

CREATE TABLE `bildirimler` (
  `id` int NOT NULL,
  `kullanici_id` int NOT NULL,
  `bildirim_mesaj` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `bildirim_link` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tarih` datetime NOT NULL,
  `okundu` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `faturalar`
--

CREATE TABLE `faturalar` (
  `id` int NOT NULL,
  `fatura_no` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `kullanici_id` int NOT NULL,
  `adres_id` int NOT NULL,
  `ara_toplam` decimal(10,2) NOT NULL,
  `indirim_tutari` decimal(10,2) DEFAULT '0.00',
  `kupon_id` int DEFAULT NULL,
  `toplam` decimal(10,2) NOT NULL,
  `odeme_yontemi` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tarih` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `siparis_durumu` enum('onaylandi','iptal_edildi','beklemede','hazirlaniyor','kargolandi','teslim_edildi') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'beklemede'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `fatura_urunler`
--

CREATE TABLE `fatura_urunler` (
  `id` int NOT NULL,
  `fatura_id` int NOT NULL,
  `secenek_id` int NOT NULL,
  `miktar` int NOT NULL,
  `birim_fiyat` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tetikleyiciler `fatura_urunler`
--
DELIMITER $$
CREATE TRIGGER `trg_update_urun_stok_after_insert` AFTER INSERT ON `fatura_urunler` FOR EACH ROW BEGIN
    UPDATE urun_secenek
    SET stok = stok - NEW.miktar
    WHERE secenek_id = NEW.secenek_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `kategori_ziyaret`
--

CREATE TABLE `kategori_ziyaret` (
  `ziyaret_id` int NOT NULL,
  `ziyaret_ip` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `kategori_id` int NOT NULL,
  `ziyaret_tarih` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `kullanicilar`
--

CREATE TABLE `kullanicilar` (
  `id` int NOT NULL,
  `ad` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `soyad` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `mail` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tel_no` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sifre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `profil_foto_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '/uploads/images/default-avatar.jpg',
  `rol` enum('admin','moderator','kullanici') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `kayit_tarihi` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `kullanici_adres`
--

CREATE TABLE `kullanici_adres` (
  `id` int NOT NULL,
  `kullanici_id` int NOT NULL,
  `adres_adi` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `ad_soyad` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `telefon` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tc_no` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `il` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `ilce` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `adres` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `posta_kodu` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `varsayilan` tinyint(1) DEFAULT '0',
  `tarih` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `kullanici_favori`
--

CREATE TABLE `kullanici_favori` (
  `id` int NOT NULL,
  `kullanici_id` int NOT NULL,
  `urun_id` int NOT NULL,
  `tarih` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `kupon`
--

CREATE TABLE `kupon` (
  `id` int NOT NULL,
  `kupon_kod` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `kupon_tur` enum('yuzde','sabit') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `kupon_indirim` decimal(10,2) NOT NULL,
  `kupon_min_tutar` decimal(10,2) NOT NULL,
  `kupon_limit` int DEFAULT NULL,
  `kupon_baslangic_tarih` date NOT NULL,
  `kupon_bitis_tarih` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `nitelik`
--

CREATE TABLE `nitelik` (
  `nitelik_id` int NOT NULL,
  `ad` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `nitelik_degeri`
--

CREATE TABLE `nitelik_degeri` (
  `deger_id` int NOT NULL,
  `nitelik_id` int DEFAULT NULL,
  `deger` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `secenek_nitelik_degeri`
--

CREATE TABLE `secenek_nitelik_degeri` (
  `secenek_id` int NOT NULL,
  `deger_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `sepet`
--

CREATE TABLE `sepet` (
  `id` int NOT NULL,
  `kullanici_id` int DEFAULT NULL,
  `secenek_id` int DEFAULT NULL,
  `adet` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `site_ziyaret`
--

CREATE TABLE `site_ziyaret` (
  `ziyaret_id` int NOT NULL,
  `ziyaret_ip` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ziyaret_tarayici` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ziyaret_cihaz` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ziyaret_os` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ziyaret_tarih` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `urun`
--

CREATE TABLE `urun` (
  `id` int NOT NULL,
  `urun_isim` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `urun_marka` int NOT NULL,
  `urun_aciklama` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `urun_stok` int NOT NULL,
  `urun_fiyat` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `urun_degerlendirme`
--

CREATE TABLE `urun_degerlendirme` (
  `id` int NOT NULL,
  `kullanici_id` int NOT NULL,
  `urun_id` int NOT NULL,
  `fatura_id` int NOT NULL,
  `puan` int NOT NULL,
  `yorum` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `tarih` datetime NOT NULL,
  `magaza_yanit` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `magaza_yanit_tarih` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `urun_fotolar`
--

CREATE TABLE `urun_fotolar` (
  `id` int NOT NULL,
  `urun_id` int NOT NULL,
  `foto_path` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `urun_kategoriler`
--

CREATE TABLE `urun_kategoriler` (
  `id` int NOT NULL,
  `kategori_isim` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `parent_kategori_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `urun_kategori_baglantisi`
--

CREATE TABLE `urun_kategori_baglantisi` (
  `id` int NOT NULL,
  `urun_id` int NOT NULL,
  `kategori_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `urun_markalar`
--

CREATE TABLE `urun_markalar` (
  `id` int NOT NULL,
  `marka_isim` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `urun_secenek`
--

CREATE TABLE `urun_secenek` (
  `secenek_id` int NOT NULL,
  `urun_id` int DEFAULT NULL,
  `fiyat` decimal(10,2) DEFAULT NULL,
  `stok` int DEFAULT NULL,
  `durum` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `urun_sorular`
--

CREATE TABLE `urun_sorular` (
  `id` int NOT NULL,
  `kullanici_id` int NOT NULL,
  `urun_id` int NOT NULL,
  `soru` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tarih` datetime NOT NULL,
  `cevap` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `cevap_tarih` datetime DEFAULT NULL,
  `herkese_acik` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `urun_ziyaret`
--

CREATE TABLE `urun_ziyaret` (
  `ziyaret_id` int NOT NULL,
  `ziyaret_ip` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `urun_id` int NOT NULL,
  `ziyaret_tarih` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `bildirimler`
--
ALTER TABLE `bildirimler`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bildirim_kullanici_id` (`kullanici_id`);

--
-- Tablo için indeksler `faturalar`
--
ALTER TABLE `faturalar`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `fatura_no` (`fatura_no`),
  ADD KEY `faturalar_ibfk_3` (`adres_id`),
  ADD KEY `faturalar_ibfk_1` (`kullanici_id`),
  ADD KEY `faturalar_ibfk_2` (`kupon_id`);

--
-- Tablo için indeksler `fatura_urunler`
--
ALTER TABLE `fatura_urunler`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fatura_id` (`fatura_id`),
  ADD KEY `fatura_urunler_ibfk_2` (`secenek_id`);

--
-- Tablo için indeksler `kategori_ziyaret`
--
ALTER TABLE `kategori_ziyaret`
  ADD PRIMARY KEY (`ziyaret_id`),
  ADD KEY `kategori_id` (`kategori_id`);

--
-- Tablo için indeksler `kullanicilar`
--
ALTER TABLE `kullanicilar`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `mail` (`mail`);

--
-- Tablo için indeksler `kullanici_adres`
--
ALTER TABLE `kullanici_adres`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kullanici_id` (`kullanici_id`);

--
-- Tablo için indeksler `kullanici_favori`
--
ALTER TABLE `kullanici_favori`
  ADD PRIMARY KEY (`id`),
  ADD KEY `favori_ibfk1` (`kullanici_id`),
  ADD KEY `favori_ibfk2` (`urun_id`);

--
-- Tablo için indeksler `kupon`
--
ALTER TABLE `kupon`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kupon_kod` (`kupon_kod`);

--
-- Tablo için indeksler `nitelik`
--
ALTER TABLE `nitelik`
  ADD PRIMARY KEY (`nitelik_id`);

--
-- Tablo için indeksler `nitelik_degeri`
--
ALTER TABLE `nitelik_degeri`
  ADD PRIMARY KEY (`deger_id`),
  ADD KEY `nitelik_id` (`nitelik_id`);

--
-- Tablo için indeksler `secenek_nitelik_degeri`
--
ALTER TABLE `secenek_nitelik_degeri`
  ADD PRIMARY KEY (`secenek_id`,`deger_id`),
  ADD KEY `deger_id` (`deger_id`);

--
-- Tablo için indeksler `sepet`
--
ALTER TABLE `sepet`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sepet_ibfk_1` (`kullanici_id`),
  ADD KEY `sepet_ibfk_2` (`secenek_id`);

--
-- Tablo için indeksler `site_ziyaret`
--
ALTER TABLE `site_ziyaret`
  ADD PRIMARY KEY (`ziyaret_id`);

--
-- Tablo için indeksler `urun`
--
ALTER TABLE `urun`
  ADD PRIMARY KEY (`id`),
  ADD KEY `urun_marka` (`urun_marka`);

--
-- Tablo için indeksler `urun_degerlendirme`
--
ALTER TABLE `urun_degerlendirme`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kullanici_id` (`kullanici_id`),
  ADD KEY `urun_id` (`urun_id`),
  ADD KEY `fatura_id` (`fatura_id`);

--
-- Tablo için indeksler `urun_fotolar`
--
ALTER TABLE `urun_fotolar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `urun_id` (`urun_id`);

--
-- Tablo için indeksler `urun_kategoriler`
--
ALTER TABLE `urun_kategoriler`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_kategori_id` (`parent_kategori_id`);

--
-- Tablo için indeksler `urun_kategori_baglantisi`
--
ALTER TABLE `urun_kategori_baglantisi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `urun_id` (`urun_id`),
  ADD KEY `kategori_id` (`kategori_id`);

--
-- Tablo için indeksler `urun_markalar`
--
ALTER TABLE `urun_markalar`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `urun_secenek`
--
ALTER TABLE `urun_secenek`
  ADD PRIMARY KEY (`secenek_id`),
  ADD KEY `urun_id` (`urun_id`);

--
-- Tablo için indeksler `urun_sorular`
--
ALTER TABLE `urun_sorular`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kullanici_id` (`kullanici_id`),
  ADD KEY `urun_id` (`urun_id`);

--
-- Tablo için indeksler `urun_ziyaret`
--
ALTER TABLE `urun_ziyaret`
  ADD PRIMARY KEY (`ziyaret_id`),
  ADD KEY `urun_id` (`urun_id`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `bildirimler`
--
ALTER TABLE `bildirimler`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `faturalar`
--
ALTER TABLE `faturalar`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `fatura_urunler`
--
ALTER TABLE `fatura_urunler`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `kategori_ziyaret`
--
ALTER TABLE `kategori_ziyaret`
  MODIFY `ziyaret_id` int NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `kullanicilar`
--
ALTER TABLE `kullanicilar`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `kullanici_adres`
--
ALTER TABLE `kullanici_adres`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `kullanici_favori`
--
ALTER TABLE `kullanici_favori`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `kupon`
--
ALTER TABLE `kupon`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `nitelik`
--
ALTER TABLE `nitelik`
  MODIFY `nitelik_id` int NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `nitelik_degeri`
--
ALTER TABLE `nitelik_degeri`
  MODIFY `deger_id` int NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `sepet`
--
ALTER TABLE `sepet`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `site_ziyaret`
--
ALTER TABLE `site_ziyaret`
  MODIFY `ziyaret_id` int NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `urun`
--
ALTER TABLE `urun`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `urun_degerlendirme`
--
ALTER TABLE `urun_degerlendirme`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `urun_fotolar`
--
ALTER TABLE `urun_fotolar`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `urun_kategoriler`
--
ALTER TABLE `urun_kategoriler`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `urun_kategori_baglantisi`
--
ALTER TABLE `urun_kategori_baglantisi`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `urun_markalar`
--
ALTER TABLE `urun_markalar`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `urun_secenek`
--
ALTER TABLE `urun_secenek`
  MODIFY `secenek_id` int NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `urun_sorular`
--
ALTER TABLE `urun_sorular`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `urun_ziyaret`
--
ALTER TABLE `urun_ziyaret`
  MODIFY `ziyaret_id` int NOT NULL AUTO_INCREMENT;

--
-- Dökümü yapılmış tablolar için kısıtlamalar
--

--
-- Tablo kısıtlamaları `bildirimler`
--
ALTER TABLE `bildirimler`
  ADD CONSTRAINT `bildirim_kullanici_id` FOREIGN KEY (`kullanici_id`) REFERENCES `kullanicilar` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Tablo kısıtlamaları `faturalar`
--
ALTER TABLE `faturalar`
  ADD CONSTRAINT `faturalar_ibfk_1` FOREIGN KEY (`kullanici_id`) REFERENCES `kullanicilar` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `faturalar_ibfk_2` FOREIGN KEY (`kupon_id`) REFERENCES `kupon` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `faturalar_ibfk_3` FOREIGN KEY (`adres_id`) REFERENCES `kullanici_adres` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Tablo kısıtlamaları `fatura_urunler`
--
ALTER TABLE `fatura_urunler`
  ADD CONSTRAINT `fatura_urunler_ibfk_1` FOREIGN KEY (`fatura_id`) REFERENCES `faturalar` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fatura_urunler_ibfk_2` FOREIGN KEY (`secenek_id`) REFERENCES `urun_secenek` (`secenek_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Tablo kısıtlamaları `kategori_ziyaret`
--
ALTER TABLE `kategori_ziyaret`
  ADD CONSTRAINT `kategori_ziyaret_ibfk_1` FOREIGN KEY (`kategori_id`) REFERENCES `urun_kategoriler` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `kullanici_adres`
--
ALTER TABLE `kullanici_adres`
  ADD CONSTRAINT `kullanici_adres_ibfk_1` FOREIGN KEY (`kullanici_id`) REFERENCES `kullanicilar` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `kullanici_favori`
--
ALTER TABLE `kullanici_favori`
  ADD CONSTRAINT `favori_ibfk1` FOREIGN KEY (`kullanici_id`) REFERENCES `kullanicilar` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `favori_ibfk2` FOREIGN KEY (`urun_id`) REFERENCES `urun` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Tablo kısıtlamaları `nitelik_degeri`
--
ALTER TABLE `nitelik_degeri`
  ADD CONSTRAINT `nitelik_degeri_ibfk_1` FOREIGN KEY (`nitelik_id`) REFERENCES `nitelik` (`nitelik_id`);

--
-- Tablo kısıtlamaları `secenek_nitelik_degeri`
--
ALTER TABLE `secenek_nitelik_degeri`
  ADD CONSTRAINT `secenek_nitelik_degeri_ibfk_1` FOREIGN KEY (`secenek_id`) REFERENCES `urun_secenek` (`secenek_id`),
  ADD CONSTRAINT `secenek_nitelik_degeri_ibfk_2` FOREIGN KEY (`deger_id`) REFERENCES `nitelik_degeri` (`deger_id`);

--
-- Tablo kısıtlamaları `sepet`
--
ALTER TABLE `sepet`
  ADD CONSTRAINT `sepet_ibfk_1` FOREIGN KEY (`kullanici_id`) REFERENCES `kullanicilar` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sepet_ibfk_2` FOREIGN KEY (`secenek_id`) REFERENCES `urun_secenek` (`secenek_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Tablo kısıtlamaları `urun`
--
ALTER TABLE `urun`
  ADD CONSTRAINT `urun_marka` FOREIGN KEY (`urun_marka`) REFERENCES `urun_markalar` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Tablo kısıtlamaları `urun_degerlendirme`
--
ALTER TABLE `urun_degerlendirme`
  ADD CONSTRAINT `urun_degerlendirme_ibfk_1` FOREIGN KEY (`kullanici_id`) REFERENCES `kullanicilar` (`id`),
  ADD CONSTRAINT `urun_degerlendirme_ibfk_2` FOREIGN KEY (`urun_id`) REFERENCES `urun` (`id`),
  ADD CONSTRAINT `urun_degerlendirme_ibfk_3` FOREIGN KEY (`fatura_id`) REFERENCES `faturalar` (`id`);

--
-- Tablo kısıtlamaları `urun_fotolar`
--
ALTER TABLE `urun_fotolar`
  ADD CONSTRAINT `urun_fotolar_ibfk_1` FOREIGN KEY (`urun_id`) REFERENCES `urun` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `urun_kategoriler`
--
ALTER TABLE `urun_kategoriler`
  ADD CONSTRAINT `urun_kategoriler_ibfk_1` FOREIGN KEY (`parent_kategori_id`) REFERENCES `urun_kategoriler` (`id`);

--
-- Tablo kısıtlamaları `urun_kategori_baglantisi`
--
ALTER TABLE `urun_kategori_baglantisi`
  ADD CONSTRAINT `urun_kategori_baglantisi_ibfk_1` FOREIGN KEY (`urun_id`) REFERENCES `urun` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `urun_kategori_baglantisi_ibfk_2` FOREIGN KEY (`kategori_id`) REFERENCES `urun_kategoriler` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `urun_secenek`
--
ALTER TABLE `urun_secenek`
  ADD CONSTRAINT `urun_secenek_ibfk_1` FOREIGN KEY (`urun_id`) REFERENCES `urun` (`id`);

--
-- Tablo kısıtlamaları `urun_sorular`
--
ALTER TABLE `urun_sorular`
  ADD CONSTRAINT `urun_sorular_ibfk_1` FOREIGN KEY (`kullanici_id`) REFERENCES `kullanicilar` (`id`),
  ADD CONSTRAINT `urun_sorular_ibfk_2` FOREIGN KEY (`urun_id`) REFERENCES `urun` (`id`);

--
-- Tablo kısıtlamaları `urun_ziyaret`
--
ALTER TABLE `urun_ziyaret`
  ADD CONSTRAINT `urun_ziyaret_ibfk_1` FOREIGN KEY (`urun_id`) REFERENCES `urun` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
