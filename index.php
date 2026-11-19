<?php
// index.php - Front Controller / Router
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/app.php';

require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/OrderController.php';
require_once __DIR__ . '/controllers/AdminController.php';

$page = $_GET['page'] ?? 'home';

switch ($page) {
    // --- AUTHENTICATION ---
    case 'login':
        AuthController::showLogin();
        break;
    case 'login_process':
        AuthController::processLogin();
        break;
    case 'register':
        AuthController::showRegister();
        break;
    case 'register_process':
        AuthController::processRegister();
        break;
    case 'logout':
        AuthController::logout();
        break;

    // --- KONSUMEN / PUBLIC ---
    case 'home':
        require_once __DIR__ . '/views/konsumen/home.php';
        break;
    case 'katalog':
        require_once __DIR__ . '/views/konsumen/katalog.php';
        break;
    case 'detail_produk':
        require_once __DIR__ . '/views/konsumen/detail_produk.php';
        break;
    case 'keranjang':
        require_once __DIR__ . '/views/konsumen/keranjang.php';
        break;
    case 'tambah_keranjang':
        OrderController::addToCart();
        break;
    case 'update_keranjang':
        OrderController::updateCart();
        break;
    case 'hapus_keranjang':
        OrderController::removeFromCart();
        break;
    case 'checkout':
        require_once __DIR__ . '/views/konsumen/checkout.php';
        break;
    case 'checkout_process':
        OrderController::processCheckout();
        break;
    case 'bayar_gateway':
        require_once __DIR__ . '/views/konsumen/bayar_gateway.php';
        break;
    case 'simulasi_bayar_sukses':
        OrderController::simulasiBayarSukses();
        break;
    case 'pesanan_saya':
        require_once __DIR__ . '/views/konsumen/pesanan_saya.php';
        break;
    case 'kuitansi':
        require_once __DIR__ . '/views/konsumen/kuitansi.php';
        break;
    case 'profil':
        require_once __DIR__ . '/views/konsumen/profil.php';
        break;
    case 'profil_update':
        require_login();
        $db = Database::connect();
        $user = get_auth_user();
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $nomor_telp = trim($_POST['nomor_telp'] ?? '');
        $tanggal_lahir = !empty($_POST['tanggal_lahir']) ? $_POST['tanggal_lahir'] : null;
        $password = $_POST['password'] ?? '';

        if (!empty($password)) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("UPDATE user SET username=?, email=?, nomor_telp=?, tanggal_lahir=?, password=? WHERE id_user=?");
            $stmt->execute([$username, $email, $nomor_telp, $tanggal_lahir, $hash, $user['id_user']]);
        } else {
            $stmt = $db->prepare("UPDATE user SET username=?, email=?, nomor_telp=?, tanggal_lahir=? WHERE id_user=?");
            $stmt->execute([$username, $email, $nomor_telp, $tanggal_lahir, $user['id_user']]);
        }
        $_SESSION['user']['username'] = $username;
        $_SESSION['user']['email'] = $email;
        $_SESSION['user']['nomor_telp'] = $nomor_telp;
        header("Location: index.php?page=profil&msg=profil_updated");
        exit;
        break;
    case 'alamat_add':
        require_login();
        $db = Database::connect();
        $user = get_auth_user();
        $penerima = trim($_POST['penerima'] ?? '');
        $telepon = trim($_POST['telepon_penerima'] ?? '');
        $alamat = trim($_POST['alamat'] ?? '');
        $tipe = trim($_POST['tipe_alamat'] ?? 'Rumah');
        $kota = trim($_POST['kota'] ?? 'Kota Metro');
        $provinsi = trim($_POST['provinsi'] ?? 'Lampung');
        $kode_pos = trim($_POST['kode_pos'] ?? '34111');
        $catatan = trim($_POST['catatan_patokan'] ?? '');
        if (!empty($alamat) && !empty($penerima)) {
            $stmt = $db->prepare("INSERT INTO alamat (alamat, penerima, telepon_penerima, id_user, tipe_alamat, kota, provinsi, kode_pos, catatan_patokan) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$alamat, $penerima, $telepon, $user['id_user'], $tipe, $kota, $provinsi, $kode_pos, $catatan]);
        }
        $redirect = $_POST['redirect_to'] ?? 'profil';
        if ($redirect === 'checkout') {
            header("Location: index.php?page=checkout&msg=alamat_added");
        } else {
            header("Location: index.php?page=profil&msg=alamat_added");
        }
        exit;
        break;
    case 'alamat_delete':
        require_login();
        $db = Database::connect();
        $user = get_auth_user();
        $id_alamat = intval($_GET['id'] ?? 0);
        $stmt = $db->prepare("DELETE FROM alamat WHERE id_alamat = ? AND id_user = ?");
        $stmt->execute([$id_alamat, $user['id_user']]);
        header("Location: index.php?page=profil&msg=alamat_deleted");
        exit;
        break;

    // --- ADMINISTRATOR ---
    case 'admin_dashboard':
        require_once __DIR__ . '/views/admin/dashboard.php';
        break;
    case 'admin_pesanan':
        require_once __DIR__ . '/views/admin/pesanan.php';
        break;
    case 'admin_pesanan_detail':
        require_once __DIR__ . '/views/admin/pesanan_detail.php';
        break;
    case 'admin_pesanan_update':
        AdminController::updatePesanan();
        break;
    case 'admin_produk':
        require_once __DIR__ . '/views/admin/produk.php';
        break;
    case 'admin_produk_add':
        AdminController::addProduk();
        break;
    case 'admin_produk_update':
        AdminController::updateProduk();
        break;
    case 'admin_produk_delete':
        AdminController::deleteProduk();
        break;
    case 'admin_kategori':
        require_once __DIR__ . '/views/admin/kategori.php';
        break;
    case 'admin_kategori_add':
        AdminController::addKategori();
        break;
    case 'admin_kategori_update':
        AdminController::updateKategori();
        break;
    case 'admin_kategori_delete':
        AdminController::deleteKategori();
        break;
    case 'admin_bank':
        require_once __DIR__ . '/views/admin/bank.php';
        break;
    case 'admin_bank_add':
        AdminController::addBank();
        break;
    case 'admin_bank_update':
        AdminController::updateBank();
        break;
    case 'admin_bank_delete':
        AdminController::deleteBank();
        break;
    case 'admin_diskon':
        require_once __DIR__ . '/views/admin/diskon.php';
        break;
    case 'admin_diskon_add':
        AdminController::addDiskon();
        break;
    case 'admin_diskon_update':
        AdminController::updateDiskon();
        break;
    case 'admin_diskon_delete':
        AdminController::deleteDiskon();
        break;

    default:
        require_once __DIR__ . '/views/konsumen/home.php';
        break;
}
