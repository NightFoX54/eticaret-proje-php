-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1
-- Üretim Zamanı: 26 Kas 2025, 16:32:04
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

--
-- Tablo döküm verisi `banka_hesaplari`
--

INSERT INTO `banka_hesaplari` (`id`, `banka_adi`, `hesap_sahibi`, `sube_adi`, `sube_kodu`, `hesap_no`, `iban`, `durum`, `created_at`) VALUES
(1, 'deneme', 'deneme', 'deneme', '123', '1111222233334444', '1111222233334444', 1, '2025-08-04 11:15:12'),
(2, 'deneme', 'deneme', 'deneme', '123', '1111222233334444', '1111222233334444', 1, '2025-08-04 11:15:18');

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

--
-- Tablo döküm verisi `bildirimler`
--

INSERT INTO `bildirimler` (`id`, `kullanici_id`, `bildirim_mesaj`, `bildirim_link`, `tarih`, `okundu`) VALUES
(1, 1, 'asdf siparişiniz hazırlanıyor olarak güncellendi.', 'orders.php', '2025-05-24 23:38:57', 1),
(2, 1, 'deneme1231233 ürününe sorduğunuz soruya cevap verildi. Cevap: deneme1231', 'urun.php?id=15', '2025-05-25 22:08:00', 1),
(4, 1, 'deneme ürününe yaptığınız değerlendirmeye yanıt verildi. Yanıt: deneme12345', 'urun.php?id=3', '2025-05-25 22:09:57', 1),
(5, 1, 'asdf siparişiniz kargolandı olarak güncellendi.', 'orders.php', '2025-11-25 17:52:48', 0);

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

--
-- Tablo döküm verisi `faturalar`
--

INSERT INTO `faturalar` (`id`, `fatura_no`, `kullanici_id`, `adres_id`, `ara_toplam`, `indirim_tutari`, `kupon_id`, `toplam`, `odeme_yontemi`, `tarih`, `siparis_durumu`) VALUES
(3, 'asdf', 1, 2, 100.00, 0.00, NULL, 100.00, 'kart', '2025-05-19 23:07:42', 'kargolandi'),
(4, 'INV-20251125-9098', 1, 2, 1117.00, 249.99, 1, 867.01, 'havale', '2025-11-25 21:37:56', 'beklemede'),
(5, 'INV-20251125-8441', 1, 2, 123.00, 0.00, NULL, 123.00, 'kart', '2025-11-25 21:57:33', 'onaylandi'),
(6, 'INV-20251125-4431', 1, 2, 123.00, 0.00, NULL, 123.00, 'kart', '2025-11-25 21:57:56', 'onaylandi'),
(7, 'INV-20251125-8700', 1, 2, 123.00, 0.00, NULL, 123.00, 'havale', '2025-11-25 22:09:37', 'beklemede'),
(8, 'INV-20251125-8737', 1, 2, 123.00, 0.00, NULL, 123.00, 'havale', '2025-11-25 22:14:51', 'beklemede'),
(9, 'INV-20251125-4516', 1, 2, 123.00, 0.00, NULL, 123.00, 'kart', '2025-11-25 22:34:25', 'onaylandi'),
(10, 'INV-20251125-0818', 1, 2, 123.00, 0.00, NULL, 123.00, 'kart', '2025-11-25 22:36:40', 'onaylandi'),
(11, 'INV-20251125-2374', 1, 2, 123.00, 0.00, NULL, 123.00, 'kart', '2025-11-25 22:38:03', 'onaylandi'),
(12, 'INV-20251125-6903', 1, 2, 123.00, 0.00, NULL, 123.00, 'kart', '2025-11-25 22:39:25', 'onaylandi');

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
-- Tablo döküm verisi `fatura_urunler`
--

INSERT INTO `fatura_urunler` (`id`, `fatura_id`, `secenek_id`, `miktar`, `birim_fiyat`) VALUES
(7, 3, 46, 2, 200),
(8, 4, 46, 2, 128),
(9, 4, 47, 5, 123),
(10, 4, 62, 2, 123),
(11, 5, 68, 1, 123),
(12, 6, 68, 1, 123),
(13, 7, 68, 1, 123),
(14, 8, 68, 1, 123),
(15, 9, 68, 1, 123),
(16, 10, 68, 1, 123),
(17, 11, 68, 1, 123),
(18, 12, 62, 1, 123);

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

--
-- Tablo döküm verisi `kategori_ziyaret`
--

INSERT INTO `kategori_ziyaret` (`ziyaret_id`, `ziyaret_ip`, `kategori_id`, `ziyaret_tarih`) VALUES
(3, '::1', 1, '2025-05-25 18:48:04'),
(4, '::1', 1, '2025-05-25 18:48:08'),
(5, '::1', 1, '2025-05-25 18:48:08'),
(12, '::1', 10, '2025-05-25 19:28:35'),
(13, '::1', 10, '2025-05-25 19:28:40'),
(14, '::1', 10, '2025-05-25 19:28:48'),
(15, '::1', 1, '2025-05-27 10:53:50'),
(16, '::1', 10, '2025-05-27 10:53:51'),
(17, '::1', 1, '2025-05-27 10:53:52'),
(18, '::1', 10, '2025-05-27 10:53:53'),
(19, '::1', 1, '2025-05-27 10:53:53'),
(20, '::1', 10, '2025-05-27 10:53:54'),
(21, '::1', 10, '2025-11-25 15:45:48'),
(22, '::1', 10, '2025-11-25 15:46:25'),
(23, '::1', 10, '2025-11-25 15:46:36'),
(24, '::1', 1, '2025-11-25 19:57:21'),
(25, '::1', 1, '2025-11-25 19:57:46'),
(26, '::1', 1, '2025-11-25 20:10:20');

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

--
-- Tablo döküm verisi `kullanicilar`
--

INSERT INTO `kullanicilar` (`id`, `ad`, `soyad`, `mail`, `tel_no`, `sifre`, `profil_foto_path`, `rol`, `kayit_tarihi`) VALUES
(1, 'berkay', 'arıkan', 'berkayyy5445@gmail.com', '05397837419', '$2y$10$aTm9pcVQZjc9wtJl6osXyeNXHIJMwG3EwStcgUMX9gMjmZN29YMh2', '/uploads/images/default-avatar.jpg	', 'admin', '2025-05-17 20:40:04'),
(3, 'berkay mustafa', 'arıkan', 'berkay123@gmail.com', '05397837419', '$2y$10$aTm9pcVQZjc9wtJl6osXyeNXHIJMwG3EwStcgUMX9gMjmZN29YMh2', '/uploads/images/default-avatar.jpg	', 'kullanici', '2025-05-20 17:18:15');

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

--
-- Tablo döküm verisi `kullanici_adres`
--

INSERT INTO `kullanici_adres` (`id`, `kullanici_id`, `adres_adi`, `ad_soyad`, `telefon`, `tc_no`, `il`, `ilce`, `adres`, `posta_kodu`, `varsayilan`, `tarih`) VALUES
(2, 1, 'asdf', 'asdf', 'asdf', 'asdf', 'asdf', 'asdf', 'asdf', 'asdf', 1, '2025-05-19 23:07:23');

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

--
-- Tablo döküm verisi `kullanici_favori`
--

INSERT INTO `kullanici_favori` (`id`, `kullanici_id`, `urun_id`, `tarih`) VALUES
(19, 1, 15, '2025-05-22 23:37:01'),
(33, 1, 4, '2025-05-23 00:07:04'),
(57, 1, 16, '2025-11-25 17:40:29');

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

--
-- Tablo döküm verisi `kupon`
--

INSERT INTO `kupon` (`id`, `kupon_kod`, `kupon_tur`, `kupon_indirim`, `kupon_min_tutar`, `kupon_limit`, `kupon_baslangic_tarih`, `kupon_bitis_tarih`) VALUES
(1, 'KUPON10', 'sabit', 249.99, 1000.00, 500, '2025-05-18', '2026-02-05'),
(2, 'KUPON11', 'sabit', 249.99, 1000.00, 500, '2025-05-18', '2025-07-18');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `nitelik`
--

CREATE TABLE `nitelik` (
  `nitelik_id` int NOT NULL,
  `ad` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `nitelik`
--

INSERT INTO `nitelik` (`nitelik_id`, `ad`) VALUES
(2, 'Ağırlık'),
(3, 'Renk'),
(8, 'uzunluk');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `nitelik_degeri`
--

CREATE TABLE `nitelik_degeri` (
  `deger_id` int NOT NULL,
  `nitelik_id` int DEFAULT NULL,
  `deger` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `nitelik_degeri`
--

INSERT INTO `nitelik_degeri` (`deger_id`, `nitelik_id`, `deger`) VALUES
(2, 2, '5kg'),
(3, 2, '10kg'),
(4, 3, 'yeşil'),
(5, 3, 'mavi'),
(6, 2, '2kg'),
(7, 2, '8kg'),
(16, 3, 'kırmızı'),
(19, 8, '2cm');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `secenek_nitelik_degeri`
--

CREATE TABLE `secenek_nitelik_degeri` (
  `secenek_id` int NOT NULL,
  `deger_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `secenek_nitelik_degeri`
--

INSERT INTO `secenek_nitelik_degeri` (`secenek_id`, `deger_id`) VALUES
(47, 2),
(49, 2),
(64, 2),
(46, 3),
(48, 3),
(62, 3),
(63, 3),
(67, 3),
(68, 3),
(46, 4),
(47, 4),
(63, 4),
(64, 4),
(48, 5),
(49, 5),
(62, 5),
(66, 5),
(68, 5),
(69, 5),
(69, 7),
(65, 16),
(67, 16),
(65, 19),
(66, 19),
(67, 19),
(68, 19),
(69, 19);

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

--
-- Tablo döküm verisi `site_ziyaret`
--

INSERT INTO `site_ziyaret` (`ziyaret_id`, `ziyaret_ip`, `ziyaret_tarayici`, `ziyaret_cihaz`, `ziyaret_os`, `ziyaret_tarih`) VALUES
(5, '::1', 'Chrome 136', 'desktop', 'Windows 10', '2025-05-25 19:09:42'),
(6, '::1', 'Chrome 136', 'desktop', 'Windows 10', '2025-05-25 19:28:06'),
(7, '::1', 'Chrome 136', 'desktop', 'Windows 10', '2025-05-25 19:30:04'),
(8, '::1', 'Chrome 136', 'desktop', 'Windows 10', '2025-05-27 10:51:30'),
(9, '::1', 'Chrome 136', 'desktop', 'Windows 10', '2025-05-27 10:53:50'),
(10, '::1', 'Chrome 136', 'desktop', 'Windows 10', '2025-05-27 11:01:48'),
(11, '::1', 'Chrome 142', 'desktop', 'Windows 10', '2025-11-18 20:41:48'),
(12, '::1', 'Chrome 142', 'desktop', 'Windows 10', '2025-11-18 20:42:25'),
(13, '::1', 'Chrome 142', 'desktop', 'Windows 10', '2025-11-18 20:42:39'),
(14, '::1', 'Chrome 142', 'desktop', 'Windows 10', '2025-11-25 15:37:39'),
(15, '::1', 'Chrome 142', 'desktop', 'Windows 10', '2025-11-25 15:39:39'),
(16, '::1', 'Chrome 142', 'desktop', 'Windows 10', '2025-11-25 15:39:41');

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

--
-- Tablo döküm verisi `urun`
--

INSERT INTO `urun` (`id`, `urun_isim`, `urun_marka`, `urun_aciklama`, `urun_stok`, `urun_fiyat`) VALUES
(1, 'deneme', 1, 'deneme', 94, 300.00),
(2, 'deneme2', 3, 'deneme', 12, 123.00),
(3, 'deneme', 3, 'asdf', 12, 123.00),
(4, 'deneme', 3, 'asd', 12, 123.00),
(15, 'deneme1231233', 3, '123', 123, 123.00),
(16, 'deneme1231231', 1, 'asdf', 123, 123.00);

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

--
-- Tablo döküm verisi `urun_degerlendirme`
--

INSERT INTO `urun_degerlendirme` (`id`, `kullanici_id`, `urun_id`, `fatura_id`, `puan`, `yorum`, `tarih`, `magaza_yanit`, `magaza_yanit_tarih`) VALUES
(1, 3, 15, 3, 4, 'asdfg', '2025-05-23 18:13:17', NULL, NULL),
(6, 1, 3, 3, 3, 'asdfg', '2025-05-23 21:59:20', 'deneme12345', '2025-05-25 23:09:57');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `urun_fotolar`
--

CREATE TABLE `urun_fotolar` (
  `id` int NOT NULL,
  `urun_id` int NOT NULL,
  `foto_path` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `urun_fotolar`
--

INSERT INTO `urun_fotolar` (`id`, `urun_id`, `foto_path`) VALUES
(3, 1, 'uploads/urunler/urun_1_682a5e16d39b8.png'),
(4, 2, 'uploads/urunler/urun_2_682cc38463280.png'),
(5, 3, 'uploads/urunler/urun_3_682ce75e5a421.png'),
(6, 4, 'uploads/urunler/urun_4_682ce79034028.png'),
(10, 15, 'uploads/urunler/urun_15_682ee9da7f03e.png'),
(11, 16, 'uploads/urunler/urun_16_68335b6187d79.png');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `urun_kategoriler`
--

CREATE TABLE `urun_kategoriler` (
  `id` int NOT NULL,
  `kategori_isim` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `parent_kategori_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `urun_kategoriler`
--

INSERT INTO `urun_kategoriler` (`id`, `kategori_isim`, `parent_kategori_id`) VALUES
(1, 'deneme', NULL),
(10, 'deneme2', 1);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `urun_kategori_baglantisi`
--

CREATE TABLE `urun_kategori_baglantisi` (
  `id` int NOT NULL,
  `urun_id` int NOT NULL,
  `kategori_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `urun_kategori_baglantisi`
--

INSERT INTO `urun_kategori_baglantisi` (`id`, `urun_id`, `kategori_id`) VALUES
(7, 1, 1),
(8, 1, 10),
(24, 2, 10),
(25, 4, 1),
(39, 3, 1),
(41, 15, 1),
(43, 16, 10);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `urun_markalar`
--

CREATE TABLE `urun_markalar` (
  `id` int NOT NULL,
  `marka_isim` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `urun_markalar`
--

INSERT INTO `urun_markalar` (`id`, `marka_isim`) VALUES
(1, 'deneme'),
(2, 'deneme2'),
(3, 'apple');

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

--
-- Tablo döküm verisi `urun_secenek`
--

INSERT INTO `urun_secenek` (`secenek_id`, `urun_id`, `fiyat`, `stok`, `durum`) VALUES
(44, 2, 123.00, 12, 1),
(45, 4, 123.00, 12, 0),
(46, 3, 128.00, 8, 1),
(47, 3, 123.00, 7, 1),
(48, 3, 128.00, 12, 1),
(49, 3, 123.00, 12, 1),
(62, 15, 123.00, 120, 1),
(63, 15, 123.00, 123, 1),
(64, 15, 123.00, 123, 1),
(65, 16, 123.00, 123, 0),
(66, 16, 123.00, 123, 0),
(67, 16, 123.00, 123, 1),
(68, 16, 123.00, 115, 1),
(69, 16, 123.00, 123, 1);

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

--
-- Tablo döküm verisi `urun_sorular`
--

INSERT INTO `urun_sorular` (`id`, `kullanici_id`, `urun_id`, `soru`, `tarih`, `cevap`, `cevap_tarih`, `herkese_acik`) VALUES
(1, 1, 15, 'asdfg', '2025-05-23 19:11:50', 'deneme1231', '2025-05-25 23:08:00', 1);

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
-- Tablo döküm verisi `urun_ziyaret`
--

INSERT INTO `urun_ziyaret` (`ziyaret_id`, `ziyaret_ip`, `urun_id`, `ziyaret_tarih`) VALUES
(3, 'asdfg', 1, '2025-05-20 13:23:33'),
(4, '::1', 1, '2025-05-20 14:42:38'),
(5, '::1', 1, '2025-05-20 14:42:42'),
(6, '::1', 1, '2025-05-20 14:42:44'),
(7, '::1', 2, '2025-05-20 20:02:19'),
(8, '::1', 2, '2025-05-20 20:02:26'),
(9, '::1', 1, '2025-05-21 21:19:49'),
(10, '::1', 2, '2025-05-21 21:19:54'),
(11, '::1', 2, '2025-05-21 21:20:04'),
(12, '::1', 1, '2025-05-21 21:20:10'),
(13, '::1', 3, '2025-05-21 21:21:01'),
(14, '::1', 3, '2025-05-21 21:21:41'),
(15, '::1', 3, '2025-05-21 21:21:43'),
(16, '::1', 1, '2025-05-21 21:28:53'),
(17, '::1', 2, '2025-05-21 21:29:03'),
(18, '::1', 4, '2025-05-21 21:33:35'),
(19, '::1', 4, '2025-05-21 21:33:47'),
(20, '::1', 4, '2025-05-21 21:36:01'),
(21, '::1', 3, '2025-05-21 23:43:01'),
(22, '::1', 3, '2025-05-22 11:19:53'),
(23, '::1', 3, '2025-05-22 11:43:08'),
(24, '::1', 4, '2025-05-22 11:43:12'),
(25, '::1', 4, '2025-05-22 11:43:26'),
(26, '::1', 15, '2025-05-22 20:10:25'),
(27, '::1', 3, '2025-05-22 20:10:30'),
(28, '::1', 4, '2025-05-22 20:13:46'),
(29, '::1', 2, '2025-05-22 20:39:59'),
(30, '::1', 1, '2025-05-22 20:40:03'),
(31, '::1', 15, '2025-05-22 20:42:53'),
(32, '::1', 1, '2025-05-22 21:46:37'),
(33, '::1', 4, '2025-05-22 23:06:58'),
(34, '::1', 4, '2025-05-22 23:07:00'),
(35, '::1', 4, '2025-05-22 23:07:02'),
(36, '::1', 4, '2025-05-22 23:07:05'),
(37, '::1', 15, '2025-05-23 18:11:40'),
(38, '::1', 15, '2025-05-23 18:11:52'),
(39, '::1', 1, '2025-05-23 18:12:28'),
(40, '::1', 15, '2025-05-23 18:12:56'),
(41, '::1', 4, '2025-05-23 20:31:09'),
(42, '::1', 3, '2025-05-23 20:31:13'),
(43, '::1', 3, '2025-05-24 19:45:01'),
(44, '::1', 3, '2025-05-24 19:45:04'),
(45, '::1', 3, '2025-05-24 19:45:05'),
(46, '::1', 1, '2025-05-24 19:47:00'),
(47, '::1', 15, '2025-05-24 20:28:41'),
(48, '::1', 4, '2025-05-24 20:32:05'),
(49, '::1', 3, '2025-05-25 18:34:19'),
(50, '::1', 3, '2025-05-25 19:37:26'),
(51, '::1', 16, '2025-05-25 20:23:32'),
(52, '::1', 4, '2025-05-25 22:05:36'),
(53, '::1', 4, '2025-05-25 22:08:10'),
(54, '::1', 16, '2025-11-18 20:42:55'),
(55, '::1', 16, '2025-11-18 20:43:14'),
(56, '::1', 3, '2025-11-25 15:39:44'),
(57, '::1', 15, '2025-11-25 15:40:36'),
(58, '::1', 16, '2025-11-25 15:47:23'),
(59, '::1', 3, '2025-11-25 15:49:36'),
(60, '::1', 16, '2025-11-25 15:51:53'),
(61, '::1', 16, '2025-11-25 15:52:15'),
(62, '::1', 15, '2025-11-25 20:39:19'),
(63, '::1', 16, '2025-11-26 10:38:23');

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Tablo için AUTO_INCREMENT değeri `faturalar`
--
ALTER TABLE `faturalar`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Tablo için AUTO_INCREMENT değeri `fatura_urunler`
--
ALTER TABLE `fatura_urunler`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Tablo için AUTO_INCREMENT değeri `kategori_ziyaret`
--
ALTER TABLE `kategori_ziyaret`
  MODIFY `ziyaret_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Tablo için AUTO_INCREMENT değeri `kullanicilar`
--
ALTER TABLE `kullanicilar`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Tablo için AUTO_INCREMENT değeri `kullanici_adres`
--
ALTER TABLE `kullanici_adres`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Tablo için AUTO_INCREMENT değeri `kullanici_favori`
--
ALTER TABLE `kullanici_favori`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- Tablo için AUTO_INCREMENT değeri `kupon`
--
ALTER TABLE `kupon`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Tablo için AUTO_INCREMENT değeri `nitelik`
--
ALTER TABLE `nitelik`
  MODIFY `nitelik_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Tablo için AUTO_INCREMENT değeri `nitelik_degeri`
--
ALTER TABLE `nitelik_degeri`
  MODIFY `deger_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Tablo için AUTO_INCREMENT değeri `sepet`
--
ALTER TABLE `sepet`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Tablo için AUTO_INCREMENT değeri `site_ziyaret`
--
ALTER TABLE `site_ziyaret`
  MODIFY `ziyaret_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Tablo için AUTO_INCREMENT değeri `urun`
--
ALTER TABLE `urun`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Tablo için AUTO_INCREMENT değeri `urun_degerlendirme`
--
ALTER TABLE `urun_degerlendirme`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Tablo için AUTO_INCREMENT değeri `urun_fotolar`
--
ALTER TABLE `urun_fotolar`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Tablo için AUTO_INCREMENT değeri `urun_kategoriler`
--
ALTER TABLE `urun_kategoriler`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Tablo için AUTO_INCREMENT değeri `urun_kategori_baglantisi`
--
ALTER TABLE `urun_kategori_baglantisi`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- Tablo için AUTO_INCREMENT değeri `urun_markalar`
--
ALTER TABLE `urun_markalar`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Tablo için AUTO_INCREMENT değeri `urun_secenek`
--
ALTER TABLE `urun_secenek`
  MODIFY `secenek_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- Tablo için AUTO_INCREMENT değeri `urun_sorular`
--
ALTER TABLE `urun_sorular`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Tablo için AUTO_INCREMENT değeri `urun_ziyaret`
--
ALTER TABLE `urun_ziyaret`
  MODIFY `ziyaret_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

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
