<?php
// views/konsumen/pesanan_saya.php
require_login();
$pageTitle = 'Riwayat Pesanan Saya';
require_once __DIR__ . '/../layouts/header.php';
$db = Database::connect();
$currentUser = get_auth_user();

$sql = "SELECT p.*, pr.nama_produk, pr.daya, b.nama_bank, b.norek, a.alamat, a.penerima, a.telepon_penerima
        FROM pesanan p
        JOIN produk pr ON p.id_produk = pr.id_produk
        LEFT JOIN bank b ON p.id_bank = b.id_bank
        JOIN alamat a ON p.id_alamat = a.id_alamat
        WHERE p.id_user = ?
        ORDER BY p.created_at DESC";
$stmt = $db->prepare($sql);
$stmt->execute([$currentUser['id_user']]);
$pesananList = $stmt->fetchAll();

// Mapping status
$statusBayarLabel = [
    0 => '<span class="badge badge-status-0 border border-warning px-3 py-2 rounded-pill"><i class="bi bi-clock me-1"></i> Menunggu Pembayaran / Verifikasi</span>',
    1 => '<span class="badge badge-status-2 border border-success px-3 py-2 rounded-pill"><i class="bi bi-check-circle me-1"></i> Lunas / Terverifikasi</span>',
    2 => '<span class="badge badge-status-3 border border-danger px-3 py-2 rounded-pill"><i class="bi bi-x-circle me-1"></i> Ditolak / Dibatalkan</span>'
];

$statusPesananLabel = [
    0 => '<span class="badge bg-secondary">Menunggu Pengecekan</span>',
    1 => '<span class="badge bg-primary">Sedang Diproses</span>',
    2 => '<span class="badge bg-success">Pemasangan Selesai</span>',
    3 => '<span class="badge bg-danger">Dibatalkan</span>'
];
?>

<div class="container py-5">
    <div class="mb-4">
        <h3 class="fw-bold mb-1">Riwayat & Status Pesanan Instalasi</h3>
        <p class="text-muted small mb-0">Pantau verifikasi pembayaran otomatis/manual, jadwal kunjungan teknisi, dan cetak kuitansi resmi</p>
    </div>

    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'pesanan_berhasil'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <h5 class="alert-heading fw-bold"><i class="bi bi-check-circle-fill me-2"></i> Pesanan Berhasil Dibuat!</h5>
            <p class="mb-0 small">Pesanan instalasi listrik Anda telah tercatat dalam sistem PT. Eksavindo Mitra Mandiri.</p>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (empty($pesananList)): ?>
        <div class="card card-custom p-5 text-center my-4">
            <i class="bi bi-journal-x text-muted display-4 mb-3"></i>
            <h5 class="fw-bold">Belum Ada Pesanan</h5>
            <p class="text-muted small">Anda belum melakukan pemesanan instalasi listrik apapun.</p>
            <div>
                <a href="index.php?page=katalog" class="btn btn-primary-custom rounded-pill px-4">Pesan Layanan Sekarang</a>
            </div>
        </div>
    <?php else: ?>
        <div class="d-flex flex-column gap-4">
            <?php foreach ($pesananList as $pesan): ?>
            <div class="card card-custom p-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center border-bottom pb-3 mb-3 gap-2">
                    <div>
                        <span class="fw-bold text-dark fs-5 font-monospace"><?= htmlspecialchars($pesan['id_pesanan']) ?></span>
                        <span class="text-muted small ms-2"><i class="bi bi-calendar3 me-1"></i> <?= date('d M Y, H:i', strtotime($pesan['created_at'])) ?></span>
                        <span class="badge bg-light text-dark border ms-2">
                            <?= ($pesan['metode_bayar'] === 'payment_gateway') ? '<i class="bi bi-lightning-charge-fill text-warning me-1"></i>Payment Gateway' : '<i class="bi bi-bank me-1"></i>Transfer Bank Manual' ?>
                        </span>
                    </div>
                    <div>
                        <?= $statusBayarLabel[$pesan['status_pembayaran']] ?? '-' ?>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-5">
                        <h6 class="fw-bold mb-1 text-primary"><?= htmlspecialchars($pesan['nama_produk']) ?></h6>
                        <div class="text-muted small mb-2"><?= $pesan['jml_beli'] ?>x unit | <?= htmlspecialchars($pesan['daya'] ?: '-') ?></div>
                        <div class="fw-bold text-dark fs-5 mb-3"><?= rupiah($pesan['total_bayar']) ?></div>

                        <div class="bg-light p-3 rounded-3 small">
                            <div class="fw-semibold text-muted mb-1"><i class="bi bi-geo-alt me-1 text-danger"></i> Lokasi Pemasangan:</div>
                            <div><strong><?= htmlspecialchars($pesan['penerima']) ?></strong> (<?= htmlspecialchars($pesan['telepon_penerima']) ?>)</div>
                            <div class="text-muted"><?= htmlspecialchars($pesan['alamat']) ?></div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="bg-light p-3 rounded-3 h-100 small d-flex flex-column justify-content-between">
                            <div>
                                <div class="fw-bold mb-2 text-dark"><i class="bi bi-clock-history me-1 text-primary"></i> Jadwal & Tim Teknisi:</div>
                                <div class="mb-2">
                                    <span class="text-muted d-block">Jadwal Pemasangan:</span>
                                    <strong class="text-primary fs-6"><?= $pesan['jadwal_pemasangan'] ? htmlspecialchars($pesan['jadwal_pemasangan']) : '<span class="text-muted fst-italic">Menunggu konfirmasi admin</span>' ?></strong>
                                </div>
                                <div class="mb-2">
                                    <span class="text-muted d-block">Teknisi / Armada:</span>
                                    <span><?= htmlspecialchars($pesan['kurir'] ?: 'Teknisi PT. Eksavindo') ?></span>
                                </div>
                                <div>
                                    <span class="text-muted d-block">Status Pekerjaan:</span>
                                    <?= $statusPesananLabel[$pesan['status_pemesanan']] ?? '-' ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 d-flex flex-column justify-content-between text-end">
                        <div class="text-start text-md-end">
                            <?php if ($pesan['metode_bayar'] === 'payment_gateway'): ?>
                                <span class="badge bg-primary bg-opacity-10 text-primary p-2 w-100 text-center mb-2">
                                    <i class="bi bi-qr-code-scan me-1"></i> QRIS / Virtual Account
                                </span>
                                <?php if ($pesan['status_pembayaran'] == 0): ?>
                                <a href="index.php?page=bayar_gateway&id=<?= $pesan['id_pesanan'] ?>" class="btn btn-warning btn-sm w-100 rounded-pill fw-bold text-dark mb-2">
                                    <i class="bi bi-credit-card-2-front me-1"></i> Selesaikan Bayar
                                </a>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-muted small d-block">Transfer Manual:</span>
                                <strong><?= htmlspecialchars($pesan['nama_bank'] ?: 'Bank PT. Eksavindo') ?></strong>
                                <?php if ($pesan['bukti_transfer']): ?>
                                <div class="mt-2">
                                    <a href="uploads/bukti/<?= htmlspecialchars($pesan['bukti_transfer']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill">
                                        <i class="bi bi-image me-1"></i> Lihat Bukti
                                    </a>
                                </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>

                        <div class="mt-3">
                            <a href="index.php?page=kuitansi&id=<?= $pesan['id_pesanan'] ?>" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill w-100 mb-2">
                                <i class="bi bi-printer me-1"></i> Cetak Kuitansi / Invoice
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
