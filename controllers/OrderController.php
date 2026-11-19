<?php
// controllers/OrderController.php
class OrderController {
    public static function addToCart() {
        require_login();
        $db = Database::connect();
        $currentUser = get_auth_user();

        $id_produk = intval($_POST['id_produk'] ?? 0);
        $jml_beli = max(1, intval($_POST['jml_beli'] ?? 1));

        $stmt = $db->prepare("SELECT id_keranjang, jml_beli FROM keranjang WHERE id_user = ? AND id_produk = ?");
        $stmt->execute([$currentUser['id_user'], $id_produk]);
        $existing = $stmt->fetch();

        if ($existing) {
            $newJml = $existing['jml_beli'] + $jml_beli;
            $stmtUp = $db->prepare("UPDATE keranjang SET jml_beli = ? WHERE id_keranjang = ?");
            $stmtUp->execute([$newJml, $existing['id_keranjang']]);
        } else {
            $stmtIn = $db->prepare("INSERT INTO keranjang (id_produk, jml_beli, id_user) VALUES (?, ?, ?)");
            $stmtIn->execute([$id_produk, $jml_beli, $currentUser['id_user']]);
        }

        header("Location: index.php?page=keranjang");
        exit;
    }

    public static function updateCart() {
        require_login();
        $db = Database::connect();
        $currentUser = get_auth_user();

        $id_keranjang = intval($_POST['id_keranjang'] ?? 0);
        $jml_beli = max(1, intval($_POST['jml_beli'] ?? 1));

        $stmt = $db->prepare("UPDATE keranjang SET jml_beli = ? WHERE id_keranjang = ? AND id_user = ?");
        $stmt->execute([$jml_beli, $id_keranjang, $currentUser['id_user']]);

        header("Location: index.php?page=keranjang");
        exit;
    }

    public static function removeFromCart() {
        require_login();
        $db = Database::connect();
        $currentUser = get_auth_user();

        $id_keranjang = intval($_GET['id'] ?? 0);
        $stmt = $db->prepare("DELETE FROM keranjang WHERE id_keranjang = ? AND id_user = ?");
        $stmt->execute([$id_keranjang, $currentUser['id_user']]);

        header("Location: index.php?page=keranjang");
        exit;
    }

    public static function processCheckout() {
        require_login();
        $db = Database::connect();
        $currentUser = get_auth_user();

        $metode_bayar = $_POST['metode_bayar'] ?? 'payment_gateway';
        $id_bank = intval($_POST['id_bank'] ?? 1); // default bank pertama jika gateway
        $id_alamat = intval($_POST['id_alamat'] ?? 0);

        // Mode pilihan alamat: 'saved' (alamat tersimpan) atau 'new' (alamat baru)
        $address_mode = $_POST['address_mode'] ?? 'saved';

        // Jika mode tambah alamat baru dipilih atau input new_alamat & new_penerima diisi
        if ($address_mode === 'new' || (!empty($_POST['new_alamat']) && !empty($_POST['new_penerima']))) {
            $newAlamat = trim($_POST['new_alamat'] ?? '');
            $newPenerima = trim($_POST['new_penerima'] ?? '');
            $newTelp = trim($_POST['new_telepon'] ?? $currentUser['nomor_telp']);
            $newTipe = trim($_POST['new_tipe_alamat'] ?? 'Rumah');
            $newKota = trim($_POST['new_kota'] ?? 'Kota Metro');
            $newProvinsi = trim($_POST['new_provinsi'] ?? 'Lampung');
            $newKodePos = trim($_POST['new_kode_pos'] ?? '34111');
            $newCatatan = trim($_POST['new_catatan'] ?? '');

            if (empty($newPenerima) || empty($newAlamat)) {
                $error = "Mohon lengkapi Nama Penerima dan Alamat Lengkap untuk lokasi baru.";
                require_once __DIR__ . '/../views/konsumen/checkout.php';
                return;
            }

            $stmtA = $db->prepare("INSERT INTO alamat (alamat, penerima, telepon_penerima, id_user, tipe_alamat, kota, provinsi, kode_pos, catatan_patokan) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmtA->execute([$newAlamat, $newPenerima, $newTelp, $currentUser['id_user'], $newTipe, $newKota, $newProvinsi, $newKodePos, $newCatatan]);
            $id_alamat = $db->lastInsertId();
        }

        if ($id_alamat <= 0) {
            $error = "Pilih lokasi pemasangan instalasi terlebih dahulu.";
            require_once __DIR__ . '/../views/konsumen/checkout.php';
            return;
        }

        // Upload bukti pembayaran jika metode manual
        $buktiFileName = null;
        if ($metode_bayar === 'manual_transfer') {
            if (isset($_FILES['bukti_transfer']) && $_FILES['bukti_transfer']['error'] === UPLOAD_ERR_OK) {
                $tmpPath = $_FILES['bukti_transfer']['tmp_name'];
                $ext = strtolower(pathinfo($_FILES['bukti_transfer']['name'], PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png'];

                if (!in_array($ext, $allowed)) {
                    $error = "Format file bukti pembayaran harus berupa JPG, JPEG, atau PNG.";
                    require_once __DIR__ . '/../views/konsumen/checkout.php';
                    return;
                }

                $buktiFileName = 'bukti_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
                $uploadDest = __DIR__ . '/../uploads/bukti/' . $buktiFileName;
                move_uploaded_file($tmpPath, $uploadDest);
            } else {
                $error = "Wajib mengunggah bukti transfer untuk metode pembayaran manual.";
                require_once __DIR__ . '/../views/konsumen/checkout.php';
                return;
            }
        }

        // Ambil item keranjang
        $sql = "SELECT k.*, p.harga, p.ukuran AS ukuran_produk, d.besar_diskon,
                       (p.harga - COALESCE(d.besar_diskon, 0)) AS harga_final
                FROM keranjang k
                JOIN produk p ON k.id_produk = p.id_produk
                LEFT JOIN diskon d ON p.id_produk = d.id_produk AND d.is_active = 1
                WHERE k.id_user = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$currentUser['id_user']]);
        $items = $stmt->fetchAll();

        if (empty($items)) {
            header("Location: index.php?page=keranjang");
            exit;
        }

        $orderId = generate_order_id();
        $snapToken = 'SNAP-' . strtoupper(substr(md5($orderId), 0, 16));

        try {
            $db->beginTransaction();

            foreach ($items as $item) {
                $totalBayar = $item['harga_final'] * $item['jml_beli'];

                // 1. Insert header pesanan
                $stmtOrder = $db->prepare("INSERT INTO pesanan (
                    id_pesanan, id_produk, id_alamat, id_bank, id_user, ukuran_produk, jml_beli,
                    total_bayar, kurir, jadwal_pemasangan, status_keberangkatan, metode_bayar,
                    bukti_transfer, status_pembayaran, status_pemesanan, snap_token
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Teknisi PT. Eksavindo', NULL, 0, ?, ?, 0, 0, ?)");

                $stmtOrder->execute([
                    $orderId,
                    $item['id_produk'],
                    $id_alamat,
                    $id_bank,
                    $currentUser['id_user'],
                    $item['ukuran'],
                    $item['jml_beli'],
                    $totalBayar,
                    $metode_bayar,
                    $buktiFileName,
                    $snapToken
                ]);

                // 2. Insert tabel detail_pesanan (Normalisasi 1NF & 2NF)
                $stmtDetail = $db->prepare("INSERT INTO detail_pesanan (
                    id_pesanan, id_produk, ukuran_produk, jml_beli, harga_satuan, subtotal
                ) VALUES (?, ?, ?, ?, ?, ?)");
                $stmtDetail->execute([
                    $orderId,
                    $item['id_produk'],
                    $item['ukuran_produk'],
                    $item['jml_beli'],
                    $item['harga_final'],
                    $totalBayar
                ]);

                // Update stok dan terjual
                $db->prepare("UPDATE produk SET terjual = terjual + ?, stok = GREATEST(0, stok - ?) WHERE id_produk = ?")
                   ->execute([$item['jml_beli'], $item['jml_beli'], $item['id_produk']]);
            }

            // 3. Catat di tabel transaksi_gateway jika metode payment gateway
            if ($metode_bayar === 'payment_gateway') {
                $totalAll = 0;
                foreach ($items as $it) {
                    $totalAll += ($it['harga_final'] * $it['jml_beli']);
                }
                $stmtGw = $db->prepare("INSERT INTO transaksi_gateway (
                    id_pesanan, order_id, gross_amount, snap_token, payment_type, transaction_status
                ) VALUES (?, ?, ?, ?, 'qris', 'pending')");
                $stmtGw->execute([$orderId, $orderId, $totalAll, $snapToken]);
            }

            // Hapus keranjang
            $db->prepare("DELETE FROM keranjang WHERE id_user = ?")->execute([$currentUser['id_user']]);

            $db->commit();

            if ($metode_bayar === 'payment_gateway') {
                header("Location: index.php?page=bayar_gateway&id=" . $orderId);
            } else {
                header("Location: index.php?page=pesanan_saya&msg=pesanan_berhasil");
            }
            exit;
        } catch (Exception $e) {
            $db->rollBack();
            $error = "Terjadi kesalahan saat memproses pesanan: " . $e->getMessage();
            require_once __DIR__ . '/../views/konsumen/checkout.php';
        }
    }

    public static function simulasiBayarSukses() {
        require_login();
        $db = Database::connect();
        $id_pesanan = $_POST['id_pesanan'] ?? '';
        $payment_type = $_POST['payment_type'] ?? 'qris';

        if (!empty($id_pesanan)) {
            // Hitung jadwal otomatis: Besok pagi pukul 09.00 WIB
            $hariIndo = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            $bulanIndo = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            
            $besokTime = strtotime('+1 day');
            $namaHari = $hariIndo[date('w', $besokTime)];
            $tgl = date('j', $besokTime);
            $namaBulan = $bulanIndo[(int)date('n', $besokTime)];
            $tahun = date('Y', $besokTime);
            $jadwalOtomatis = "$namaHari, $tgl $namaBulan $tahun - Pukul 09.00 WIB";
            $timTeknisiOtomatis = "Tim Armada 1 (Teknisi Tersertifikasi PT. Eksavindo)";

            // Update OTOMATIS: Lunas (1), Sedang Diproses (1), Jadwal Terisi, Teknisi Ditugaskan (1)
            $stmt = $db->prepare("UPDATE pesanan SET 
                status_pembayaran = 1,
                status_pemesanan = 1,
                jadwal_pemasangan = ?,
                kurir = ?,
                status_keberangkatan = 1
                WHERE id_pesanan = ?");
            $stmt->execute([$jadwalOtomatis, $timTeknisiOtomatis, $id_pesanan]);

            // Update transaksi gateway
            $stmtGw = $db->prepare("UPDATE transaksi_gateway SET transaction_status = 'settlement', payment_type = ?, settlement_time = NOW() WHERE id_pesanan = ?");
            $stmtGw->execute([$payment_type, $id_pesanan]);
        }

        header("Location: index.php?page=bayar_gateway&id=" . $id_pesanan . "&msg=lunas");
        exit;
    }
}
