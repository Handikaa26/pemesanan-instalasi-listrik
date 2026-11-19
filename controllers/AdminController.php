<?php
// controllers/AdminController.php
class AdminController {
    public static function updatePesanan() {
        require_admin();
        $db = Database::connect();

        $id_pesanan = $_POST['id_pesanan'] ?? '';
        $status_pembayaran = intval($_POST['status_pembayaran'] ?? 0);
        $jadwal_pemasangan = trim($_POST['jadwal_pemasangan'] ?? '');
        $kurir = trim($_POST['kurir'] ?? 'Teknisi PT. Eksavindo');
        $status_keberangkatan = intval($_POST['status_keberangkatan'] ?? 0);
        $status_pemesanan = intval($_POST['status_pemesanan'] ?? 0);

        $stmt = $db->prepare("UPDATE pesanan SET 
            status_pembayaran = ?,
            jadwal_pemasangan = ?,
            kurir = ?,
            status_keberangkatan = ?,
            status_pemesanan = ?
            WHERE id_pesanan = ?");
        
        $stmt->execute([
            $status_pembayaran,
            $jadwal_pemasangan,
            $kurir,
            $status_keberangkatan,
            $status_pemesanan,
            $id_pesanan
        ]);

        header("Location: index.php?page=admin_pesanan&msg=updated");
        exit;
    }

    // CRUD Produk
    public static function addProduk() {
        require_admin();
        $db = Database::connect();

        $nama_produk = trim($_POST['nama_produk'] ?? '');
        $label_produk = trim($_POST['label_produk'] ?? '');
        $id_kategori = intval($_POST['id_kategori'] ?? 1);
        $harga = intval($_POST['harga'] ?? 0);
        $ukuran = trim($_POST['ukuran'] ?? '');
        $daya = trim($_POST['daya'] ?? '');
        $stok = intval($_POST['stok'] ?? 10);
        $deskripsi_produk = trim($_POST['deskripsi_produk'] ?? '');

        $stmt = $db->prepare("INSERT INTO produk (nama_produk, label_produk, ukuran, daya, id_kategori, deskripsi_produk, harga, stok, terjual) 
                             VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0)");
        $stmt->execute([$nama_produk, $label_produk, $ukuran, $daya, $id_kategori, $deskripsi_produk, $harga, $stok]);

        header("Location: index.php?page=admin_produk&msg=added");
        exit;
    }

    public static function updateProduk() {
        require_admin();
        $db = Database::connect();

        $id_produk = intval($_POST['id_produk'] ?? 0);
        $nama_produk = trim($_POST['nama_produk'] ?? '');
        $label_produk = trim($_POST['label_produk'] ?? '');
        $id_kategori = intval($_POST['id_kategori'] ?? 1);
        $harga = intval($_POST['harga'] ?? 0);
        $ukuran = trim($_POST['ukuran'] ?? '');
        $daya = trim($_POST['daya'] ?? '');
        $stok = intval($_POST['stok'] ?? 10);
        $deskripsi_produk = trim($_POST['deskripsi_produk'] ?? '');

        $stmt = $db->prepare("UPDATE produk SET nama_produk=?, label_produk=?, ukuran=?, daya=?, id_kategori=?, deskripsi_produk=?, harga=?, stok=? WHERE id_produk=?");
        $stmt->execute([$nama_produk, $label_produk, $ukuran, $daya, $id_kategori, $deskripsi_produk, $harga, $stok, $id_produk]);

        header("Location: index.php?page=admin_produk&msg=updated");
        exit;
    }

    public static function deleteProduk() {
        require_admin();
        $db = Database::connect();
        $id_produk = intval($_GET['id'] ?? 0);

        $stmt = $db->prepare("DELETE FROM produk WHERE id_produk = ?");
        $stmt->execute([$id_produk]);

        header("Location: index.php?page=admin_produk&msg=deleted");
        exit;
    }

    // CRUD Kategori
    public static function addKategori() {
        require_admin();
        $db = Database::connect();
        $nama_kategori = trim($_POST['nama_kategori'] ?? '');

        if (!empty($nama_kategori)) {
            $stmt = $db->prepare("INSERT INTO kategori (nama_kategori) VALUES (?)");
            $stmt->execute([$nama_kategori]);
        }
        header("Location: index.php?page=admin_kategori&msg=added");
        exit;
    }

    public static function updateKategori() {
        require_admin();
        $db = Database::connect();
        $id_kategori = intval($_POST['id_kategori'] ?? 0);
        $nama_kategori = trim($_POST['nama_kategori'] ?? '');

        if (!empty($nama_kategori) && $id_kategori > 0) {
            $stmt = $db->prepare("UPDATE kategori SET nama_kategori = ? WHERE id_kategori = ?");
            $stmt->execute([$nama_kategori, $id_kategori]);
        }
        header("Location: index.php?page=admin_kategori&msg=updated");
        exit;
    }

    public static function deleteKategori() {
        require_admin();
        $db = Database::connect();
        $id_kategori = intval($_GET['id'] ?? 0);

        $stmt = $db->prepare("DELETE FROM kategori WHERE id_kategori = ?");
        $stmt->execute([$id_kategori]);

        header("Location: index.php?page=admin_kategori&msg=deleted");
        exit;
    }

    // CRUD Bank
    public static function addBank() {
        require_admin();
        $db = Database::connect();
        $nama_bank = trim($_POST['nama_bank'] ?? '');
        $norek = trim($_POST['norek'] ?? '');
        $atas_nama = trim($_POST['atas_nama'] ?? '');

        $stmt = $db->prepare("INSERT INTO bank (nama_bank, norek, atas_nama) VALUES (?, ?, ?)");
        $stmt->execute([$nama_bank, $norek, $atas_nama]);

        header("Location: index.php?page=admin_bank&msg=added");
        exit;
    }

    public static function updateBank() {
        require_admin();
        $db = Database::connect();
        $id_bank = intval($_POST['id_bank'] ?? 0);
        $nama_bank = trim($_POST['nama_bank'] ?? '');
        $norek = trim($_POST['norek'] ?? '');
        $atas_nama = trim($_POST['atas_nama'] ?? '');

        $stmt = $db->prepare("UPDATE bank SET nama_bank = ?, norek = ?, atas_nama = ? WHERE id_bank = ?");
        $stmt->execute([$nama_bank, $norek, $atas_nama, $id_bank]);

        header("Location: index.php?page=admin_bank&msg=updated");
        exit;
    }

    public static function deleteBank() {
        require_admin();
        $db = Database::connect();
        $id_bank = intval($_GET['id'] ?? 0);

        $stmt = $db->prepare("DELETE FROM bank WHERE id_bank = ?");
        $stmt->execute([$id_bank]);

        header("Location: index.php?page=admin_bank&msg=deleted");
        exit;
    }

    // CRUD Diskon
    public static function addDiskon() {
        require_admin();
        $db = Database::connect();
        $id_produk = intval($_POST['id_produk'] ?? 0);
        $besar_diskon = intval($_POST['besar_diskon'] ?? 0);
        $is_active = intval($_POST['is_active'] ?? 1);

        $stmt = $db->prepare("INSERT INTO diskon (id_produk, besar_diskon, is_active) VALUES (?, ?, ?)");
        $stmt->execute([$id_produk, $besar_diskon, $is_active]);

        header("Location: index.php?page=admin_diskon&msg=added");
        exit;
    }

    public static function updateDiskon() {
        require_admin();
        $db = Database::connect();
        $id_diskon = intval($_POST['id_diskon'] ?? 0);
        $besar_diskon = intval($_POST['besar_diskon'] ?? 0);
        $is_active = intval($_POST['is_active'] ?? 1);

        $stmt = $db->prepare("UPDATE diskon SET besar_diskon = ?, is_active = ? WHERE id_diskon = ?");
        $stmt->execute([$besar_diskon, $is_active, $id_diskon]);

        header("Location: index.php?page=admin_diskon&msg=updated");
        exit;
    }

    public static function deleteDiskon() {
        require_admin();
        $db = Database::connect();
        $id_diskon = intval($_GET['id'] ?? 0);

        $stmt = $db->prepare("DELETE FROM diskon WHERE id_diskon = ?");
        $stmt->execute([$id_diskon]);

        header("Location: index.php?page=admin_diskon&msg=deleted");
        exit;
    }
}

