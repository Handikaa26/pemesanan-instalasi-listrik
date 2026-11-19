<?php
// views/konsumen/bayar_gateway.php
require_login();
$id_pesanan = $_GET['id'] ?? '';
$db = Database::connect();
$currentUser = get_auth_user();

$stmt = $db->prepare("SELECT p.*, pr.nama_produk, a.alamat, a.penerima, a.telepon_penerima, a.tipe_alamat, a.kota, a.catatan_patokan, u.email, u.username
                      FROM pesanan p
                      JOIN produk pr ON p.id_produk = pr.id_produk
                      JOIN alamat a ON p.id_alamat = a.id_alamat
                      JOIN user u ON p.id_user = u.id_user
                      WHERE p.id_pesanan = ? AND p.id_user = ?");
$stmt->execute([$id_pesanan, $currentUser['id_user']]);
$pesanan = $stmt->fetch();

if (!$pesanan) {
    header("Location: index.php?page=pesanan_saya");
    exit;
}

$pageTitle = 'Pembayaran GoPay QRIS - ' . $pesanan['id_pesanan'];
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="card card-custom p-4 text-center shadow-lg border-0">
                
                <!-- Header GoPay QRIS Sesuai Gambar -->
                <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                    <div class="text-start">
                        <h4 class="fw-bold mb-0 text-dark" style="letter-spacing: -0.5px;">GoPay QRIS</h4>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-info text-white px-2 py-1 fw-bold"><i class="bi bi-wallet2 me-1"></i>gopay</span>
                        <span class="badge bg-light text-secondary border px-2 py-1"><i class="bi bi-lightning-charge-fill text-warning"></i> gopaylater</span>
                        <span class="badge bg-dark text-white px-2 py-1 fw-bold">QRIS</span>
                    </div>
                </div>

                <!-- Detail Tagihan Otomatis (Konsumen Tidak Perlu Input Manual) -->
                <div class="p-3 bg-light rounded-3 mb-3 text-start border">
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="text-muted">Nomor Pesanan:</span>
                        <span class="font-monospace fw-bold text-dark"><?= htmlspecialchars($pesanan['id_pesanan']) ?></span>
                    </div>
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="text-muted">Paket Instalasi:</span>
                        <strong class="text-primary"><?= htmlspecialchars($pesanan['nama_produk']) ?></strong>
                    </div>
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="text-muted">Lokasi Pemasangan:</span>
                        <span class="text-dark text-end fw-semibold" style="max-width: 60%;">
                            <i class="bi bi-geo-alt-fill text-danger me-1"></i><?= htmlspecialchars($pesanan['penerima']) ?> (<?= htmlspecialchars($pesanan['tipe_alamat'] ?? 'Rumah') ?>)
                            <br><small class="text-muted fw-normal"><?= htmlspecialchars($pesanan['alamat']) ?></small>
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-baseline mt-2 pt-2 border-top">
                        <span class="fw-bold text-dark">Total Pembayaran Otomatis:</span>
                        <span class="fs-4 fw-bold text-danger"><?= rupiah($pesanan['total_bayar']) ?></span>
                    </div>
                </div>

                <?php if ($pesanan['status_pembayaran'] == 1): ?>
                    <!-- TAMPILAN JIKA SUDAH LUNAS -->
                    <div class="alert alert-success py-4 my-3 rounded-4 shadow-sm">
                        <i class="bi bi-patch-check-fill text-success" style="font-size: 3rem;"></i>
                        <h5 class="fw-bold mt-2 mb-1">Pembayaran Berhasil & LUNAS!</h5>
                        <p class="small text-muted mb-0">Dana telah kami terima. Teknisi PT. Eksavindo telah dijadwalkan secara otomatis ke alamat Anda.</p>
                    </div>

                    <div class="d-flex flex-column gap-2 mt-3">
                        <a href="index.php?page=kuitansi&id=<?= $pesanan['id_pesanan'] ?>" target="_blank" class="btn btn-primary-custom w-100 py-3 rounded-pill fw-bold shadow">
                            <i class="bi bi-printer me-1"></i> Cetak Kuitansi / Invoice Resmi
                        </a>
                        <a href="index.php?page=pesanan_saya" class="btn btn-outline-secondary rounded-pill py-2">
                            <i class="bi bi-receipt me-1"></i> Lihat Daftar Pesanan Saya
                        </a>
                    </div>

                <?php else: ?>

                    <!-- TAMPILAN KARTU STANDAR QRIS NASIONAL (PERSIS SEPERTI GAMBAR) -->
                    <div class="position-relative mx-auto my-2 p-2 bg-white rounded-4 shadow border" style="max-width: 380px;">
                        
                        <!-- Gambar Standar QRIS GPN Uji Coba Instalasi Listrik -->
                        <div class="p-2 text-center">
                            <img src="assets/qris_midtrans_standar.png" alt="QRIS Standar Pembayaran Nasional GPN - Uji Coba Instalasi Listrik" class="img-fluid rounded-3" style="width: 100%; max-width: 340px; display: block; margin: 0 auto;">
                        </div>

                    </div>

                    <!-- Keterangan Scan Otomatis -->
                    <div class="alert alert-primary bg-primary bg-opacity-10 border-0 text-primary py-2 px-3 small my-3 rounded-3 text-start">
                        <i class="bi bi-shield-lock-fill me-1"></i> <strong>Sistem Scan Otomatis Aktif:</strong> Konsumen cukup buka aplikasi e-wallet (GoPay, BCA, OVO, Dana, ShopeePay, Livin Mandiri) lalu scan QRIS di atas.
                    </div>

                    <!-- Tombol Konfirmasi Selesai Bayar -->
                    <form action="index.php?page=simulasi_bayar_sukses" method="POST" class="mt-2">
                        <input type="hidden" name="id_pesanan" value="<?= $pesanan['id_pesanan'] ?>">
                        <input type="hidden" name="payment_type" value="qris">
                        <button type="submit" class="btn btn-success w-100 py-3 rounded-pill fw-bold shadow-sm">
                            <i class="bi bi-check2-circle me-1"></i> Saya Sudah Selesai Scan & Bayar
                        </button>
                    </form>

                    <a href="index.php?page=pesanan_saya" class="btn btn-link text-muted small text-decoration-none mt-2">
                        <i class="bi bi-arrow-left me-1"></i> Bayar Nanti / Kembali ke Riwayat
                    </a>

                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
