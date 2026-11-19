<?php
// setup_db.php
$host = '127.0.0.1';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    echo "Koneksi ke MySQL server berhasil.\n";

    $sql = file_get_contents(__DIR__ . '/database.sql');
    $pdo->exec($sql);
    echo "Database & tabel berhasil dibuat.\n";

    // Reconnect to specific DB
    $pdo->exec("USE `pemesanan_instalasi_listrik`");

    // Insert Default Admin & Konsumen if not exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM `user` WHERE `id_user` = ?");
    $stmt->execute(['ADM00001']);
    if ($stmt->fetchColumn() == 0) {
        $adminPass = password_hash('admin123', PASSWORD_DEFAULT);
        $userPass = password_hash('user123', PASSWORD_DEFAULT);

        $pdo->prepare("INSERT INTO `user` (`id_user`, `username`, `email`, `password`, `nomor_telp`, `tanggal_lahir`, `foto_profil`, `role_id`, `is_active`) VALUES
            (?, ?, ?, ?, ?, ?, ?, ?, ?)")
            ->execute(['ADM00001', 'Administrator', 'admin@eksavindo.com', $adminPass, '081234567890', '1995-01-01', 'default.png', 1, 1]);

        $pdo->prepare("INSERT INTO `user` (`id_user`, `username`, `email`, `password`, `nomor_telp`, `tanggal_lahir`, `foto_profil`, `role_id`, `is_active`) VALUES
            (?, ?, ?, ?, ?, ?, ?, ?, ?)")
            ->execute(['USR00001', 'Budi Santoso', 'budi@gmail.com', $userPass, '089876543210', '1998-05-15', 'default.png', 2, 1]);
        
        echo "User awal (Admin & Konsumen demo) berhasil ditambahkan.\n";
    }

    // Insert Default Kategori
    $stmt = $pdo->query("SELECT COUNT(*) FROM `kategori`");
    if ($stmt->fetchColumn() == 0) {
        $pdo->exec("INSERT INTO `kategori` (`id_kategori`, `nama_kategori`) VALUES
            (1, 'Instalasi Rumah Tinggal'),
            (2, 'Instalasi Gedung & Industri'),
            (3, 'Perawatan & Perbaikan Panel'),
            (4, 'Pemasangan Grounding & Penangkal Petir'),
            (5, 'Paket Tambah Titik Lampu & Stop Kontak')");
        echo "Kategori layanan awal berhasil ditambahkan.\n";
    }

    // Insert Default Bank
    $stmt = $pdo->query("SELECT COUNT(*) FROM `bank`");
    if ($stmt->fetchColumn() == 0) {
        $pdo->exec("INSERT INTO `bank` (`id_bank`, `nama_bank`, `norek`, `atas_nama`) VALUES
            (1, 'Bank Mandiri', '1140029384751', 'PT EKSAVINDO MITRA MANDIRI'),
            (2, 'Bank BCA', '8820194821', 'PT EKSAVINDO MITRA MANDIRI'),
            (3, 'Bank BRI', '012901002345501', 'PT EKSAVINDO MITRA MANDIRI')");
        echo "Data Bank PT. Eksavindo berhasil ditambahkan.\n";
    }

    // Insert Default Produk Layanan
    $stmt = $pdo->query("SELECT COUNT(*) FROM `produk`");
    if ($stmt->fetchColumn() == 0) {
        $pdo->exec("INSERT INTO `produk` (`id_produk`, `nama_produk`, `label_produk`, `ukuran`, `daya`, `id_kategori`, `deskripsi_produk`, `harga`, `foto_produk`, `stok`, `terjual`) VALUES
            (1, 'Paket Instalasi Rumah Tipe 36/45', 'Paket Standar', '1 Phase', '900 VA - 2200 VA', 1, 'Pemasangan instalasi listrik rumah baru/renovasi lengkap dengan MCB box 4 grup, 8 titik lampu, 6 stop kontak, kabel SNI NYM Supreme 3x2.5mm dan 2x1.5mm berstandar PLN/PUIL.', 1750000, 'paket_rumah.jpg', 20, 5),
            (2, 'Paket Instalasi Rumah Tipe 54/70', 'Paket Best Seller', '1 Phase', '2200 VA - 4400 VA', 1, 'Paket lengkap 14 titik lampu, 10 stop kontak, grounding tembaga, box panel MCB 6 grup + ELCB anti sengatan listrik sesuai standar PUIL 2020.', 2850000, 'paket_rumah_besar.jpg', 15, 8),
            (3, 'Instalasi Listrik 3 Phase Komersil / Ruko', 'Paket Komersil', '3 Phase', '6600 VA - 16500 VA', 2, 'Pemasangan instalasi listrik 3 phase untuk ruko/kantor/kafe, pembagian beban R-S-T seimbang, MCB 3 phase, kabel NYY/NYFGBY berkualitas tinggi.', 5500000, 'paket_3phase.jpg', 10, 3),
            (4, 'Pemasangan Grounding Rod & Penangkal Petir', 'Proteksi Ekstra', 'Konvensional', 'Resistansi < 2 Ohm', 4, 'Instalasi sistem proteksi petir splitzen tembaga + kabel BC 50mm + grounding rod 3 meter, pengujian tahanan tanah dengan Earth Tester terkalibrasi.', 2300000, 'penangkal_petir.jpg', 12, 4),
            (5, 'Paket Tambah 5 Titik Stop Kontak & Lampu', 'Paket Hemat', '1 Phase', 'Standard', 5, 'Jasa penambahan 5 titik baru (stop kontak/saklar/lampu) termasuk pembobokan rapi, pipa konduit, inbow doos, dan kabel NYM 3x2.5mm.', 650000, 'tambah_titik.jpg', 30, 12),
            (6, 'Service & Penataan Ulang Panel Listrik', 'Maintenance', '1 Phase / 3 Phase', 'All Capacity', 3, 'Pengecekan instalasi kabel korsleting, penggantian MCB usang, perapihan wiring panel, dan uji beban termal untuk mencegah bahaya kebakaran.', 850000, 'service_panel.jpg', 25, 7)");
        echo "Produk/layanan instalasi berhasil ditambahkan.\n";
    }

    // Insert Default Diskon
    $stmt = $pdo->query("SELECT COUNT(*) FROM `diskon`");
    if ($stmt->fetchColumn() == 0) {
        $pdo->exec("INSERT INTO `diskon` (`id_diskon`, `id_produk`, `besar_diskon`, `is_active`) VALUES
            (1, 1, 100000, 1),
            (2, 2, 150000, 1)");
        echo "Data Diskon berhasil ditambahkan.\n";
    }

    // Insert Default Alamat User Demo
    $stmt = $pdo->query("SELECT COUNT(*) FROM `alamat`");
    if ($stmt->fetchColumn() == 0) {
        $pdo->exec("INSERT INTO `alamat` (`id_alamat`, `alamat`, `penerima`, `telepon_penerima`, `id_user`) VALUES
            (1, 'Jl. Ki Hajar Dewantara No. 15, Iringmulyo, Metro Timur, Kota Metro', 'Budi Santoso', '089876543210', 'USR00001')");
        echo "Alamat demo berhasil ditambahkan.\n";
    }

    echo "SETUP DATABASE SELESAI DENGAN SUKSES!\n";

} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

