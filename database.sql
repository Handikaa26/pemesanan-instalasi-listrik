-- Database schema based on Tugas Akhir:
-- "PERANCANGAN SISTEM INFORMASI PEMESANAN INSTALASI LISTRIK PADA PT. EKSAVINDO MITRA MANDIRI BERBASIS WEBSITE"
-- Penulis: Afrizal Agung Nugraha (NPM: 21410014)

CREATE DATABASE IF NOT EXISTS `pemesanan_instalasi_listrik` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `pemesanan_instalasi_listrik`;

-- 1. Table role
CREATE TABLE IF NOT EXISTS `role` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `role` VARCHAR(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Table user
CREATE TABLE IF NOT EXISTS `user` (
    `id_user` CHAR(8) NOT NULL PRIMARY KEY,
    `username` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `nomor_telp` VARCHAR(20) DEFAULT NULL,
    `tanggal_lahir` DATE DEFAULT NULL,
    `foto_profil` VARCHAR(100) DEFAULT 'default.png',
    `role_id` INT NOT NULL DEFAULT 2,
    `is_active` INT NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_user_role` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Table kategori
CREATE TABLE IF NOT EXISTS `kategori` (
    `id_kategori` INT AUTO_INCREMENT PRIMARY KEY,
    `nama_kategori` VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Table produk (Layanan/Produk Instalasi Listrik)
CREATE TABLE IF NOT EXISTS `produk` (
    `id_produk` INT AUTO_INCREMENT PRIMARY KEY,
    `nama_produk` VARCHAR(100) NOT NULL,
    `label_produk` VARCHAR(100) DEFAULT NULL,
    `ukuran` VARCHAR(100) DEFAULT NULL,
    `daya` VARCHAR(100) DEFAULT NULL,
    `id_kategori` INT NOT NULL,
    `deskripsi_produk` TEXT DEFAULT NULL,
    `harga` INT NOT NULL,
    `foto_produk` VARCHAR(100) DEFAULT 'default_product.jpg',
    `stok` INT NOT NULL DEFAULT 10,
    `terjual` INT NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_produk_kategori` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Table diskon
CREATE TABLE IF NOT EXISTS `diskon` (
    `id_diskon` INT AUTO_INCREMENT PRIMARY KEY,
    `id_produk` INT NOT NULL,
    `besar_diskon` INT NOT NULL DEFAULT 0,
    `is_active` INT NOT NULL DEFAULT 1,
    CONSTRAINT `fk_diskon_produk` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Table bank (Rekening Pembayaran PT. Eksavindo Mitra Mandiri)
CREATE TABLE IF NOT EXISTS `bank` (
    `id_bank` INT AUTO_INCREMENT PRIMARY KEY,
    `nama_bank` VARCHAR(100) NOT NULL,
    `norek` VARCHAR(150) NOT NULL,
    `atas_nama` VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. Table alamat (Alamat Pemasangan Konsumen)
CREATE TABLE IF NOT EXISTS `alamat` (
    `id_alamat` INT AUTO_INCREMENT PRIMARY KEY,
    `alamat` VARCHAR(255) NOT NULL,
    `penerima` VARCHAR(100) NOT NULL,
    `telepon_penerima` VARCHAR(50) NOT NULL,
    `id_user` CHAR(8) NOT NULL,
    CONSTRAINT `fk_alamat_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 8. Table keranjang
CREATE TABLE IF NOT EXISTS `keranjang` (
    `id_keranjang` INT AUTO_INCREMENT PRIMARY KEY,
    `id_produk` INT NOT NULL,
    `ukuran` CHAR(3) DEFAULT NULL,
    `jml_beli` INT NOT NULL DEFAULT 1,
    `id_user` CHAR(8) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_keranjang_produk` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`) ON DELETE CASCADE,
    CONSTRAINT `fk_keranjang_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 9. Table favorit
CREATE TABLE IF NOT EXISTS `favorit` (
    `id_favorit` INT AUTO_INCREMENT PRIMARY KEY,
    `id_produk` INT NOT NULL,
    `id_user` CHAR(8) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_favorit_produk` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`) ON DELETE CASCADE,
    CONSTRAINT `fk_favorit_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 10. Table pesanan
CREATE TABLE IF NOT EXISTS `pesanan` (
    `id_pesanan` VARCHAR(50) NOT NULL PRIMARY KEY,
    `id_produk` INT NOT NULL,
    `id_alamat` INT NOT NULL,
    `id_bank` INT NOT NULL,
    `id_user` CHAR(8) NOT NULL,
    `ukuran_produk` VARCHAR(10) DEFAULT NULL,
    `jml_beli` INT NOT NULL DEFAULT 1,
    `total_bayar` INT NOT NULL,
    `kurir` VARCHAR(100) DEFAULT 'Teknisi PT. Eksavindo',
    `jadwal_pemasangan` VARCHAR(50) DEFAULT NULL,
    `status_keberangkatan` INT DEFAULT 0, -- 0: Menunggu, 1: Teknisi Ditugaskan, 2: Menuju Lokasi, 3: Tiba di Lokasi / Selesai
    `bukti_transfer` VARCHAR(255) DEFAULT NULL,
    `status_pembayaran` INT DEFAULT 0, -- 0: Belum Bayar/Menunggu Verifikasi, 1: Lunas/Terverifikasi, 2: Ditolak
    `status_pemesanan` INT DEFAULT 0, -- 0: Baru, 1: Diproses, 2: Selesai, 3: Dibatalkan
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_pesanan_produk` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`) ON DELETE CASCADE,
    CONSTRAINT `fk_pesanan_alamat` FOREIGN KEY (`id_alamat`) REFERENCES `alamat` (`id_alamat`) ON DELETE CASCADE,
    CONSTRAINT `fk_pesanan_bank` FOREIGN KEY (`id_bank`) REFERENCES `bank` (`id_bank`) ON DELETE CASCADE,
    CONSTRAINT `fk_pesanan_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- SEED DATA AWAL
INSERT INTO `role` (`id`, `role`) VALUES
(1, 'Admin'),
(2, 'Konsumen')
ON DUPLICATE KEY UPDATE `role` = VALUES(`role`);

-- Password default: 'admin123' dan 'user123' (pakai password_hash nanti via script setup)

