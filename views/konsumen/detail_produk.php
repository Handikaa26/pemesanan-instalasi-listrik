<?php
// views/konsumen/detail_produk.php
$id_produk = intval($_GET['id'] ?? 0);
$db = Database::connect();

$stmt = $db->prepare("SELECT p.*, k.nama_kategori, d.besar_diskon 
                      FROM produk p
                      LEFT JOIN kategori k ON p.id_kategori = k.id_kategori
                      LEFT JOIN diskon d ON p.id_produk = d.id_produk AND d.is_active = 1
                      WHERE p.id_produk = ?");
$stmt->execute([$id_produk]);
$produk = $stmt->fetch();

if (!$produk) {
    header("Location: index.php?page=katalog");
    exit;
}

$finalPrice = $produk['harga'] - ($produk['besar_diskon'] ?? 0);
$pageTitle = $produk['nama_produk'];
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container py-5">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none">Beranda</a></li>
            <li class="breadcrumb-item"><a href="index.php?page=katalog" class="text-decoration-none">Layanan</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($produk['nama_produk']) ?></li>
        </ol>
    </nav>

    <div class="row g-5">
        <div class="col-lg-5">
            <div class="card card-custom p-4 text-center bg-light">
                <i class="bi bi-hdd-rack text-primary display-1 my-5"></i>
                <div class="d-flex justify-content-center gap-2 mt-3">
                    <span class="badge bg-primary px-3 py-2 rounded-pill"><?= htmlspecialchars($produk['nama_kategori']) ?></span>
                    <?php if (!empty($produk['label_produk'])): ?>
                    <span class="badge bg-warning text-dark px-3 py-2 rounded-pill"><?= htmlspecialchars($produk['label_produk']) ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <h2 class="fw-bold mb-2"><?= htmlspecialchars($produk['nama_produk']) ?></h2>
            <div class="d-flex align-items-center gap-3 mb-4">
                <span class="text-muted small"><i class="bi bi-check-circle-fill text-success me-1"></i> Terjual: <strong><?= $produk['terjual'] ?>x</strong> dipesan</span>
                <span class="text-muted small"><i class="bi bi-box me-1"></i> Kuota Tersedia: <strong><?= $produk['stok'] ?> unit</strong></span>
            </div>

            <div class="p-3 bg-light rounded-4 mb-4">
                <div class="text-muted small">Biaya Paket Instalasi:</div>
                <div class="d-flex align-items-baseline gap-2">
                    <span class="display-6 fw-bold text-primary"><?= rupiah($finalPrice) ?></span>
                    <?php if (!empty($produk['besar_diskon'])): ?>
                        <del class="text-muted fs-5"><?= rupiah($produk['harga']) ?></del>
                        <span class="badge bg-danger">Hemat <?= rupiah($produk['besar_diskon']) ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="mb-4">
                <h6 class="fw-bold mb-2">Spesifikasi Layanan & Teknis:</h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm small">
                        <tr>
                            <th class="bg-light w-25">Fase / Tipe</th>
                            <td><?= htmlspecialchars($produk['ukuran'] ?: '1 Phase / Standar') ?></td>
                        </tr>
                        <tr>
                            <th class="bg-light">Kapasitas Daya</th>
                            <td><?= htmlspecialchars($produk['daya'] ?: 'Menyesuaikan beban PLN') ?></td>
                        </tr>
                        <tr>
                            <th class="bg-light">Standar Kualitas</th>
                            <td>PUIL 2020 & SNI (Standar Nasional Indonesia)</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Garansi Pemasangan</th>
                            <td>Garansi Resmi PT. Eksavindo Mitra Mandiri (30 Hari)</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="mb-4">
                <h6 class="fw-bold mb-2">Deskripsi & Ruang Lingkup Pengerjaan:</h6>
                <p class="text-muted leading-relaxed"><?= nl2br(htmlspecialchars($produk['deskripsi_produk'])) ?></p>
            </div>

            <form action="index.php?page=tambah_keranjang" method="POST" class="d-flex align-items-center gap-3">
                <input type="hidden" name="id_produk" value="<?= $produk['id_produk'] ?>">
                <div style="width: 100px;">
                    <label class="form-label small fw-semibold">Jumlah Unit</label>
                    <input type="number" name="jml_beli" class="form-control" value="1" min="1" max="<?= max(1, $produk['stok']) ?>" required>
                </div>
                <div class="flex-grow-1 align-self-end">
                    <button type="submit" class="btn btn-primary-custom w-100 py-2">
                        <i class="bi bi-cart-plus me-1"></i> Masukkan ke Keranjang Pemesanan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

