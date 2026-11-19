<?php
// views/admin/dashboard.php
require_admin();
$pageTitle = 'Dashboard Admin';
require_once __DIR__ . '/../layouts/header.php';
$db = Database::connect();

// Statistik
$totalPesanan = $db->query("SELECT COUNT(*) FROM pesanan")->fetchColumn();
$menungguVerifikasi = $db->query("SELECT COUNT(*) FROM pesanan WHERE status_pembayaran = 0")->fetchColumn();
$pesananSelesai = $db->query("SELECT COUNT(*) FROM pesanan WHERE status_pemesanan = 2")->fetchColumn();
$totalPendapatan = $db->query("SELECT SUM(total_bayar) FROM pesanan WHERE status_pembayaran = 1")->fetchColumn() ?: 0;

$totalUser = $db->query("SELECT COUNT(*) FROM user WHERE role_id = 2")->fetchColumn();
$totalProduk = $db->query("SELECT COUNT(*) FROM produk")->fetchColumn();

// Pesanan terbaru
$stmtLatest = $db->query("SELECT p.*, pr.nama_produk, u.username, b.nama_bank
                         FROM pesanan p
                         JOIN produk pr ON p.id_produk = pr.id_produk
                         JOIN user u ON p.id_user = u.id_user
                         JOIN bank b ON p.id_bank = b.id_bank
                         ORDER BY p.created_at DESC LIMIT 5");
$latestOrders = $stmtLatest->fetchAll();
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">Dashboard Administrator</h3>
            <p class="text-muted small mb-0">Kelola operasional PT. Eksavindo Mitra Mandiri, verifikasi pesanan & jadwal teknisi</p>
        </div>
        <div>
            <a href="index.php?page=admin_pesanan" class="btn btn-primary-custom btn-sm rounded-pill">
                <i class="bi bi-card-checklist me-1"></i> Lihat Semua Pesanan
            </a>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card card-custom p-4 border-start border-4 border-primary">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-semibold">Total Pesanan</div>
                        <div class="fs-3 fw-bold text-dark"><?= $totalPesanan ?></div>
                    </div>
                    <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle"><i class="bi bi-receipt fs-4"></i></div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card card-custom p-4 border-start border-4 border-warning">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-semibold">Menunggu Verifikasi</div>
                        <div class="fs-3 fw-bold text-warning"><?= $menungguVerifikasi ?></div>
                    </div>
                    <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle"><i class="bi bi-clock-history fs-4"></i></div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card card-custom p-4 border-start border-4 border-success">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-semibold">Pemasangan Selesai</div>
                        <div class="fs-3 fw-bold text-success"><?= $pesananSelesai ?></div>
                    </div>
                    <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle"><i class="bi bi-check-all fs-4"></i></div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card card-custom p-4 border-start border-4 border-info">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-semibold">Total Omset Terverifikasi</div>
                        <div class="fs-4 fw-bold text-info"><?= rupiah($totalPendapatan) ?></div>
                    </div>
                    <div class="bg-info bg-opacity-10 text-info p-3 rounded-circle"><i class="bi bi-cash-stack fs-4"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Access Buttons -->
    <div class="row g-3 mb-5">
        <div class="col-md-3">
            <a href="index.php?page=admin_produk" class="card card-custom p-3 text-decoration-none text-dark hover-border-primary">
                <div class="d-flex align-items-center gap-3">
                    <i class="bi bi-box-seam text-primary fs-3"></i>
                    <div>
                        <div class="fw-bold">Kelola Layanan/Produk</div>
                        <small class="text-muted"><?= $totalProduk ?> paket terdaftar</small>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="index.php?page=admin_kategori" class="card card-custom p-3 text-decoration-none text-dark hover-border-primary">
                <div class="d-flex align-items-center gap-3">
                    <i class="bi bi-tags text-warning fs-3"></i>
                    <div>
                        <div class="fw-bold">Kelola Kategori</div>
                        <small class="text-muted">Kategori instalasi & panel</small>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="index.php?page=admin_bank" class="card card-custom p-3 text-decoration-none text-dark hover-border-primary">
                <div class="d-flex align-items-center gap-3">
                    <i class="bi bi-bank text-info fs-3"></i>
                    <div>
                        <div class="fw-bold">Kelola Bank Pembayaran</div>
                        <small class="text-muted">Rekening resmi transfer</small>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="index.php?page=admin_diskon" class="card card-custom p-3 text-decoration-none text-dark hover-border-primary">
                <div class="d-flex align-items-center gap-3">
                    <i class="bi bi-percent text-danger fs-3"></i>
                    <div>
                        <div class="fw-bold">Kelola Diskon Promo</div>
                        <small class="text-muted">Potongan harga layanan</small>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Pesanan Terkini -->
    <div class="card card-custom p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">Pesanan Masuk Terbaru</h5>
            <a href="index.php?page=admin_pesanan" class="text-primary small text-decoration-none">Lihat Semua</a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID Pesanan</th>
                        <th>Konsumen</th>
                        <th>Layanan</th>
                        <th>Total Biaya</th>
                        <th>Status Bayar</th>
                        <th>Jadwal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($latestOrders)): ?>
                        <tr><td colspan="7" class="text-center py-3 text-muted">Belum ada pesanan masuk.</td></tr>
                    <?php else: ?>
                        <?php foreach ($latestOrders as $ord): ?>
                        <tr>
                            <td class="font-monospace fw-bold small"><?= htmlspecialchars($ord['id_pesanan']) ?></td>
                            <td><?= htmlspecialchars($ord['username']) ?></td>
                            <td><?= htmlspecialchars($ord['nama_produk']) ?></td>
                            <td class="fw-semibold"><?= rupiah($ord['total_bayar']) ?></td>
                            <td>
                                <?php if ($ord['status_pembayaran'] == 1): ?>
                                    <span class="badge bg-success">Lunas</span>
                                <?php elseif ($ord['status_pembayaran'] == 2): ?>
                                    <span class="badge bg-danger">Ditolak</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark">Perlu Dicek</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $ord['jadwal_pemasangan'] ? htmlspecialchars($ord['jadwal_pemasangan']) : '<span class="text-muted small fst-italic">Belum Dijadwalkan</span>' ?></td>
                            <td>
                                <a href="index.php?page=admin_pesanan_detail&id=<?= $ord['id_pesanan'] ?>" class="btn btn-sm btn-outline-primary rounded-pill">
                                    Proses / Detail
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

