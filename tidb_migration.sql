-- ==========================================================
-- MIGRATION SCRIPT UNTUK TiDB CLOUD (Serverless / Dedicated)
-- Proyek: Pemesanan Instalasi Listrik PT. Eksavindo Mitra Mandiri
-- Generated: 2026-11-19 06:07:13
-- ==========================================================

CREATE DATABASE IF NOT EXISTS `pemesanan_instalasi_listrik`;
USE `pemesanan_instalasi_listrik`;

SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------
-- Struktur Tabel `role`
-- ---------------------------------------------------------
DROP TABLE IF EXISTS `role`;
CREATE TABLE `role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data untuk Tabel `role` (2 baris)
INSERT INTO `role` (`id`, `role`) VALUES
('1', 'Admin'),
('2', 'Konsumen');

-- ---------------------------------------------------------
-- Struktur Tabel `user`
-- ---------------------------------------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id_user` char(8) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nomor_telp` varchar(20) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `foto_profil` varchar(100) DEFAULT 'default.png',
  `role_id` int(11) NOT NULL DEFAULT 2,
  `is_active` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `email` (`email`),
  KEY `fk_user_role` (`role_id`),
  CONSTRAINT `fk_user_role` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data untuk Tabel `user` (3 baris)
INSERT INTO `user` (`id_user`, `username`, `email`, `password`, `nomor_telp`, `tanggal_lahir`, `foto_profil`, `role_id`, `is_active`, `created_at`) VALUES
('ADM00001', 'Administrator', 'admin@eksavindo.com', '$2y$12$SnlbGUakDesrUa9XnfvUE.j7KfKfwQovSll5IeH8cE6JqM04vOa86', '081234567890', '1995-01-01', 'default.png', '1', '1', '2026-11-12 01:14:35'),
('USR00001', 'Budi Santoso', 'budi@gmail.com', '$2y$12$nKy8rlJshoM0wnnV9nkPxeooQvvPCLmxXxVnyqH8te8ewYN6qfZey', '089876543210', '1998-05-15', 'default.png', '2', '1', '2026-11-12 01:14:35'),
('USR00002', 'Dika', 'darksystem67@gmail.com', '$2y$12$dJkY3rRiv3JmK1XtV3sNDeaivjimNT/kKOCwus8obTFnEsBuvHi6y', '085769738790', '2026-11-12', 'default.png', '2', '1', '2026-11-12 02:49:45');

-- ---------------------------------------------------------
-- Struktur Tabel `kategori`
-- ---------------------------------------------------------
DROP TABLE IF EXISTS `kategori`;
CREATE TABLE `kategori` (
  `id_kategori` int(11) NOT NULL AUTO_INCREMENT,
  `nama_kategori` varchar(100) NOT NULL,
  PRIMARY KEY (`id_kategori`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data untuk Tabel `kategori` (5 baris)
INSERT INTO `kategori` (`id_kategori`, `nama_kategori`) VALUES
('1', 'Instalasi Rumah Tinggal'),
('2', 'Instalasi Gedung & Industri'),
('3', 'Perawatan & Perbaikan Panel'),
('4', 'Pemasangan Grounding & Penangkal Petir'),
('5', 'Paket Tambah Titik Lampu & Stop Kontak');

-- ---------------------------------------------------------
-- Struktur Tabel `produk`
-- ---------------------------------------------------------
DROP TABLE IF EXISTS `produk`;
CREATE TABLE `produk` (
  `id_produk` int(11) NOT NULL AUTO_INCREMENT,
  `nama_produk` varchar(100) NOT NULL,
  `label_produk` varchar(100) DEFAULT NULL,
  `ukuran` varchar(100) DEFAULT NULL,
  `daya` varchar(100) DEFAULT NULL,
  `id_kategori` int(11) NOT NULL,
  `deskripsi_produk` text DEFAULT NULL,
  `harga` int(11) NOT NULL,
  `foto_produk` varchar(100) DEFAULT 'default_product.jpg',
  `stok` int(11) NOT NULL DEFAULT 10,
  `terjual` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_produk`),
  KEY `fk_produk_kategori` (`id_kategori`),
  CONSTRAINT `fk_produk_kategori` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data untuk Tabel `produk` (6 baris)
INSERT INTO `produk` (`id_produk`, `nama_produk`, `label_produk`, `ukuran`, `daya`, `id_kategori`, `deskripsi_produk`, `harga`, `foto_produk`, `stok`, `terjual`, `created_at`) VALUES
('1', 'Paket Instalasi Rumah Tipe 36/45', 'Paket Standar', '1 Phase', '900 VA - 2200 VA', '1', 'Pemasangan instalasi listrik rumah baru/renovasi lengkap dengan MCB box 4 grup, 8 titik lampu, 6 stop kontak, kabel SNI NYM Supreme 3x2.5mm dan 2x1.5mm berstandar PLN/PUIL.', '1750000', 'paket_rumah.jpg', '19', '6', '2026-11-12 01:14:35'),
('2', 'Paket Instalasi Rumah Tipe 54/70', 'Paket Best Seller', '1 Phase', '2200 VA - 4400 VA', '1', 'Paket lengkap 14 titik lampu, 10 stop kontak, grounding tembaga, box panel MCB 6 grup + ELCB anti sengatan listrik sesuai standar PUIL 2020.', '2850000', 'paket_rumah_besar.jpg', '10', '13', '2026-11-12 01:14:35'),
('3', 'Instalasi Listrik 3 Phase Komersil / Ruko', 'Paket Komersil', '3 Phase', '6600 VA - 16500 VA', '2', 'Pemasangan instalasi listrik 3 phase untuk ruko/kantor/kafe, pembagian beban R-S-T seimbang, MCB 3 phase, kabel NYY/NYFGBY berkualitas tinggi.', '5500000', 'paket_3phase.jpg', '10', '3', '2026-11-12 01:14:35'),
('4', 'Pemasangan Grounding Rod & Penangkal Petir', 'Proteksi Ekstra', 'Konvensional', 'Resistansi < 2 Ohm', '4', 'Instalasi sistem proteksi petir splitzen tembaga + kabel BC 50mm + grounding rod 3 meter, pengujian tahanan tanah dengan Earth Tester terkalibrasi.', '2300000', 'penangkal_petir.jpg', '12', '4', '2026-11-12 01:14:35'),
('5', 'Paket Tambah 5 Titik Stop Kontak & Lampu', 'Paket Hemat', '1 Phase', 'Standard', '5', 'Jasa penambahan 5 titik baru (stop kontak/saklar/lampu) termasuk pembobokan rapi, pipa konduit, inbow doos, dan kabel NYM 3x2.5mm.', '650000', 'tambah_titik.jpg', '28', '14', '2026-11-12 01:14:35'),
('6', 'Service & Penataan Ulang Panel Listrik', 'Maintenance', '1 Phase / 3 Phase', 'All Capacity', '3', 'Pengecekan instalasi kabel korsleting, penggantian MCB usang, perapihan wiring panel, dan uji beban termal untuk mencegah bahaya kebakaran.', '850000', 'service_panel.jpg', '25', '7', '2026-11-12 01:14:35');

-- ---------------------------------------------------------
-- Struktur Tabel `diskon`
-- ---------------------------------------------------------
DROP TABLE IF EXISTS `diskon`;
CREATE TABLE `diskon` (
  `id_diskon` int(11) NOT NULL AUTO_INCREMENT,
  `id_produk` int(11) NOT NULL,
  `besar_diskon` int(11) NOT NULL DEFAULT 0,
  `is_active` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_diskon`),
  KEY `fk_diskon_produk` (`id_produk`),
  CONSTRAINT `fk_diskon_produk` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data untuk Tabel `diskon` (2 baris)
INSERT INTO `diskon` (`id_diskon`, `id_produk`, `besar_diskon`, `is_active`) VALUES
('1', '1', '100000', '1'),
('2', '2', '150000', '1');

-- ---------------------------------------------------------
-- Struktur Tabel `bank`
-- ---------------------------------------------------------
DROP TABLE IF EXISTS `bank`;
CREATE TABLE `bank` (
  `id_bank` int(11) NOT NULL AUTO_INCREMENT,
  `nama_bank` varchar(100) NOT NULL,
  `norek` varchar(150) NOT NULL,
  `atas_nama` varchar(100) NOT NULL,
  PRIMARY KEY (`id_bank`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data untuk Tabel `bank` (3 baris)
INSERT INTO `bank` (`id_bank`, `nama_bank`, `norek`, `atas_nama`) VALUES
('1', 'Bank Mandiri', '1140029384751', 'PT EKSAVINDO MITRA MANDIRI'),
('2', 'Bank BCA', '8820194821', 'PT EKSAVINDO MITRA MANDIRI'),
('3', 'Bank BRI', '012901002345501', 'PT EKSAVINDO MITRA MANDIRI');

-- ---------------------------------------------------------
-- Struktur Tabel `alamat`
-- ---------------------------------------------------------
DROP TABLE IF EXISTS `alamat`;
CREATE TABLE `alamat` (
  `id_alamat` int(11) NOT NULL AUTO_INCREMENT,
  `alamat` varchar(255) NOT NULL,
  `penerima` varchar(100) NOT NULL,
  `telepon_penerima` varchar(50) NOT NULL,
  `id_user` char(8) NOT NULL,
  `tipe_alamat` varchar(50) DEFAULT 'Rumah',
  `kota` varchar(100) DEFAULT 'Kota Metro',
  `provinsi` varchar(100) DEFAULT 'Lampung',
  `kode_pos` varchar(20) DEFAULT '34111',
  `catatan_patokan` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_alamat`),
  KEY `fk_alamat_user` (`id_user`),
  CONSTRAINT `fk_alamat_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data untuk Tabel `alamat` (2 baris)
INSERT INTO `alamat` (`id_alamat`, `alamat`, `penerima`, `telepon_penerima`, `id_user`, `tipe_alamat`, `kota`, `provinsi`, `kode_pos`, `catatan_patokan`) VALUES
('1', 'Jl. Ki Hajar Dewantara No. 15, Iringmulyo, Metro Timur, Kota Metro', 'Budi Santoso', '089876543210', 'USR00001', 'Rumah', 'Kota Metro', 'Lampung', '34111', NULL),
('2', 'Gang tawes nomor 69', 'Dika', '085769738790', 'USR00002', 'Rumah', 'Kota Metro', 'Lampung', '34111', NULL);

-- ---------------------------------------------------------
-- Struktur Tabel `keranjang`
-- ---------------------------------------------------------
DROP TABLE IF EXISTS `keranjang`;
CREATE TABLE `keranjang` (
  `id_keranjang` int(11) NOT NULL AUTO_INCREMENT,
  `id_produk` int(11) NOT NULL,
  `ukuran` char(3) DEFAULT NULL,
  `jml_beli` int(11) NOT NULL DEFAULT 1,
  `id_user` char(8) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_keranjang`),
  KEY `fk_keranjang_produk` (`id_produk`),
  KEY `fk_keranjang_user` (`id_user`),
  CONSTRAINT `fk_keranjang_produk` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`) ON DELETE CASCADE,
  CONSTRAINT `fk_keranjang_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ---------------------------------------------------------
-- Struktur Tabel `favorit`
-- ---------------------------------------------------------
DROP TABLE IF EXISTS `favorit`;
CREATE TABLE `favorit` (
  `id_favorit` int(11) NOT NULL AUTO_INCREMENT,
  `id_produk` int(11) NOT NULL,
  `id_user` char(8) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_favorit`),
  KEY `fk_favorit_produk` (`id_produk`),
  KEY `fk_favorit_user` (`id_user`),
  CONSTRAINT `fk_favorit_produk` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`) ON DELETE CASCADE,
  CONSTRAINT `fk_favorit_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ---------------------------------------------------------
-- Struktur Tabel `pesanan`
-- ---------------------------------------------------------
DROP TABLE IF EXISTS `pesanan`;
CREATE TABLE `pesanan` (
  `id_pesanan` varchar(50) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `id_alamat` int(11) NOT NULL,
  `id_bank` int(11) NOT NULL,
  `id_user` char(8) NOT NULL,
  `ukuran_produk` varchar(10) DEFAULT NULL,
  `jml_beli` int(11) NOT NULL DEFAULT 1,
  `total_bayar` int(11) NOT NULL,
  `kurir` varchar(100) DEFAULT 'Teknisi PT. Eksavindo',
  `jadwal_pemasangan` varchar(50) DEFAULT NULL,
  `status_keberangkatan` int(11) DEFAULT 0,
  `metode_bayar` enum('manual_transfer','payment_gateway') NOT NULL DEFAULT 'payment_gateway',
  `bukti_transfer` varchar(255) DEFAULT NULL,
  `status_pembayaran` int(11) DEFAULT 0,
  `status_pemesanan` int(11) DEFAULT 0,
  `snap_token` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_pesanan`),
  KEY `fk_pesanan_produk` (`id_produk`),
  KEY `fk_pesanan_alamat` (`id_alamat`),
  KEY `fk_pesanan_bank` (`id_bank`),
  KEY `fk_pesanan_user` (`id_user`),
  CONSTRAINT `fk_pesanan_alamat` FOREIGN KEY (`id_alamat`) REFERENCES `alamat` (`id_alamat`) ON DELETE CASCADE,
  CONSTRAINT `fk_pesanan_bank` FOREIGN KEY (`id_bank`) REFERENCES `bank` (`id_bank`) ON DELETE CASCADE,
  CONSTRAINT `fk_pesanan_produk` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`) ON DELETE CASCADE,
  CONSTRAINT `fk_pesanan_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data untuk Tabel `pesanan` (8 baris)
INSERT INTO `pesanan` (`id_pesanan`, `id_produk`, `id_alamat`, `id_bank`, `id_user`, `ukuran_produk`, `jml_beli`, `total_bayar`, `kurir`, `jadwal_pemasangan`, `status_keberangkatan`, `metode_bayar`, `bukti_transfer`, `status_pembayaran`, `status_pemesanan`, `snap_token`, `created_at`) VALUES
('ORD-20261111-4CF3D', '5', '1', '1', 'USR00001', NULL, '1', '650000', 'Tim Armada 1 (Teknisi Tersertifikasi PT. Eksavindo)', 'Kamis, 12 November 2026 - Pukul 09.00 WIB', '1', 'payment_gateway', NULL, '1', '2', 'SNAP-111EA568E96F7ACA', '2026-11-12 02:23:12'),
('ORD-20261111-98706', '1', '2', '1', 'USR00002', NULL, '1', '1650000', 'Teknisi PT. Eksavindo', 'Kamis, 12 November 2026 - Pukul 14.00 WIB', '3', 'manual_transfer', 'bukti_1794428155_7775.jpeg', '1', '2', 'SNAP-E2F30C5FA07C09A0', '2026-11-12 03:15:55'),
('ORD-20261111-9ED9C', '2', '2', '1', 'USR00002', NULL, '1', '2700000', 'Tim Armada 1 (Teknisi Tersertifikasi PT. Eksavindo)', 'Kamis, 12 November 2026 - Pukul 09.00 WIB', '1', 'payment_gateway', NULL, '1', '1', 'SNAP-7081364B0B116E1F', '2026-11-12 02:51:31'),
('ORD-20261113-50703', '2', '1', '1', 'USR00001', NULL, '1', '2700000', 'Tim Armada 1 (Teknisi Tersertifikasi PT. Eksavindo)', 'Sabtu, 14 November 2026 - Pukul 09.00 WIB', '1', 'payment_gateway', NULL, '1', '1', 'SNAP-A2F02B2FE54A7B57', '2026-11-13 08:38:43'),
('ORD-20261113-88DDA', '2', '1', '1', 'USR00001', NULL, '1', '2700000', 'Tim Armada 1 (Teknisi Tersertifikasi PT. Eksavindo)', 'Sabtu, 14 November 2026 - Pukul 09.00 WIB', '1', 'payment_gateway', NULL, '1', '1', 'SNAP-27EEF50FD4EFB4AA', '2026-11-13 09:09:03'),
('ORD-20261113-8D784', '2', '1', '1', 'USR00001', NULL, '1', '2700000', 'Teknisi PT. Eksavindo', NULL, '0', 'payment_gateway', NULL, '0', '0', 'SNAP-17D3F8DF12B51774', '2026-11-13 09:00:47'),
('ORD-20261113-9A7AE', '5', '1', '1', 'USR00001', NULL, '1', '650000', 'Teknisi PT. Eksavindo', NULL, '0', 'payment_gateway', NULL, '0', '0', 'SNAP-456630FC0596497A', '2026-11-13 09:03:58'),
('ORD-20261117-8E4E7', '2', '2', '1', 'USR00002', NULL, '1', '2700000', 'Tim Armada 1 (Teknisi Tersertifikasi PT. Eksavindo)', 'Rabu, 18 November 2026 - Pukul 09.00 WIB', '1', 'payment_gateway', NULL, '1', '2', 'SNAP-F10F87EA82031989', '2026-11-17 21:40:28');

-- ---------------------------------------------------------
-- Struktur Tabel `detail_pesanan`
-- ---------------------------------------------------------
DROP TABLE IF EXISTS `detail_pesanan`;
CREATE TABLE `detail_pesanan` (
  `id_detail_pesanan` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_pesanan` varchar(50) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `ukuran_produk` varchar(50) DEFAULT NULL,
  `jml_beli` int(11) NOT NULL DEFAULT 1,
  `harga_satuan` int(11) NOT NULL,
  `subtotal` int(11) NOT NULL,
  PRIMARY KEY (`id_detail_pesanan`),
  KEY `fk_detail_pesanan` (`id_pesanan`),
  KEY `fk_detail_produk` (`id_produk`),
  CONSTRAINT `fk_detail_pesanan` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`) ON DELETE CASCADE,
  CONSTRAINT `fk_detail_produk` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data untuk Tabel `detail_pesanan` (8 baris)
INSERT INTO `detail_pesanan` (`id_detail_pesanan`, `id_pesanan`, `id_produk`, `ukuran_produk`, `jml_beli`, `harga_satuan`, `subtotal`) VALUES
('1', 'ORD-20261111-4CF3D', '5', '1 Phase', '1', '650000', '650000'),
('2', 'ORD-20261111-9ED9C', '2', '1 Phase', '1', '2700000', '2700000'),
('3', 'ORD-20261111-98706', '1', '1 Phase', '1', '1650000', '1650000'),
('4', 'ORD-20261113-50703', '2', '1 Phase', '1', '2700000', '2700000'),
('5', 'ORD-20261113-8D784', '2', '1 Phase', '1', '2700000', '2700000'),
('6', 'ORD-20261113-9A7AE', '5', '1 Phase', '1', '650000', '650000'),
('7', 'ORD-20261113-88DDA', '2', '1 Phase', '1', '2700000', '2700000'),
('8', 'ORD-20261117-8E4E7', '2', '1 Phase', '1', '2700000', '2700000');

-- ---------------------------------------------------------
-- Struktur Tabel `transaksi_gateway`
-- ---------------------------------------------------------
DROP TABLE IF EXISTS `transaksi_gateway`;
CREATE TABLE `transaksi_gateway` (
  `id_transaksi_gateway` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_pesanan` varchar(50) NOT NULL,
  `order_id` varchar(64) NOT NULL,
  `gross_amount` int(11) NOT NULL,
  `snap_token` varchar(255) DEFAULT NULL,
  `payment_type` varchar(50) DEFAULT NULL,
  `transaction_status` varchar(50) NOT NULL DEFAULT 'pending',
  `fraud_status` varchar(50) DEFAULT NULL,
  `settlement_time` datetime DEFAULT NULL,
  `raw_response` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`raw_response`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_transaksi_gateway`),
  UNIQUE KEY `order_id` (`order_id`),
  KEY `fk_gateway_pesanan` (`id_pesanan`),
  CONSTRAINT `fk_gateway_pesanan` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data untuk Tabel `transaksi_gateway` (7 baris)
INSERT INTO `transaksi_gateway` (`id_transaksi_gateway`, `id_pesanan`, `order_id`, `gross_amount`, `snap_token`, `payment_type`, `transaction_status`, `fraud_status`, `settlement_time`, `raw_response`, `created_at`, `updated_at`) VALUES
('1', 'ORD-20261111-4CF3D', 'ORD-20261111-4CF3D', '650000', 'SNAP-111EA568E96F7ACA', 'qris', 'settlement', NULL, '2026-11-12 03:10:24', NULL, '2026-11-12 02:23:12', '2026-11-12 03:10:24'),
('2', 'ORD-20261111-9ED9C', 'ORD-20261111-9ED9C', '2700000', 'SNAP-7081364B0B116E1F', 'qris', 'settlement', NULL, '2026-11-12 02:51:34', NULL, '2026-11-12 02:51:31', '2026-11-12 02:51:34'),
('3', 'ORD-20261113-50703', 'ORD-20261113-50703', '2700000', 'SNAP-A2F02B2FE54A7B57', 'qris', 'settlement', NULL, '2026-11-13 08:38:45', NULL, '2026-11-13 08:38:43', '2026-11-13 08:38:45'),
('4', 'ORD-20261113-8D784', 'ORD-20261113-8D784', '2700000', 'SNAP-17D3F8DF12B51774', 'qris', 'pending', NULL, NULL, NULL, '2026-11-13 09:00:47', '2026-11-13 09:00:47'),
('5', 'ORD-20261113-9A7AE', 'ORD-20261113-9A7AE', '650000', 'SNAP-456630FC0596497A', 'qris', 'pending', NULL, NULL, NULL, '2026-11-13 09:03:58', '2026-11-13 09:03:58'),
('6', 'ORD-20261113-88DDA', 'ORD-20261113-88DDA', '2700000', 'SNAP-27EEF50FD4EFB4AA', 'qris', 'settlement', NULL, '2026-11-13 09:09:28', NULL, '2026-11-13 09:09:03', '2026-11-13 09:09:28'),
('7', 'ORD-20261117-8E4E7', 'ORD-20261117-8E4E7', '2700000', 'SNAP-F10F87EA82031989', 'qris', 'settlement', NULL, '2026-11-17 21:42:06', NULL, '2026-11-17 21:40:28', '2026-11-17 21:42:06');

SET FOREIGN_KEY_CHECKS = 1;
-- MIGRATION COMPLETED SUCCESSFULLY --
