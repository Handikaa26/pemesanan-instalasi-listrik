<?php
// views/konsumen/keranjang.php
require_login();
$pageTitle = 'Keranjang Pemesanan';
require_once __DIR__ . '/../layouts/header.php';
$db = Database::connect();
$currentUser = get_auth_user();

$sql = "SELECT k.*, p.nama_produk, p.harga, p.ukuran AS ukuran_produk, p.daya, d.besar_diskon,
               (p.harga - COALESCE(d.besar_diskon, 0)) AS harga_final
        FROM keranjang k
        JOIN produk p ON k.id_produk = p.id_produk
        LEFT JOIN diskon d ON p.id_produk = d.id_produk AND d.is_active = 1
        WHERE k.id_user = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$currentUser['id_user']]);
$items = $stmt->fetchAll();

$grandTotal = 0;
foreach ($items as $item) {
    $grandTotal += ($item['harga_final'] * $item['jml_beli']);
}
?>

<div class="container py-5">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h3 class="fw-bold mb-1">Keranjang Pemesanan Anda</h3>
            <p class="text-muted small mb-0">Periksa paket instalasi yang telah Anda pilih sebelum melanjutkan ke checkout</p>
        </div>
        <a href="index.php?page=katalog" class="btn btn-outline-primary btn-sm rounded-pill">
            <i class="bi bi-plus-lg me-1"></i> Tambah Layanan Lain
        </a>
    </div>

    <?php if (empty($items)): ?>
    <div class="card card-custom p-5 text-center my-4">
        <i class="bi bi-cart-x text-muted display-4 mb-3"></i>
        <h5 class="fw-bold">Keranjang Anda Masih Kosong</h5>
        <p class="text-muted small">Anda belum menambahkan paket pemesanan instalasi listrik ke keranjang.</p>
        <div>
            <a href="index.php?page=katalog" class="btn btn-primary-custom rounded-pill px-4">Pilih Paket Layanan</a>
        </div>
    </div>
    <?php else: ?>
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card card-custom p-4">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Paket Instalasi</th>
                                <th>Harga Satuan</th>
                                <th style="width: 130px;">Jumlah</th>
                                <th>Subtotal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): 
                                $subtotal = $item['harga_final'] * $item['jml_beli'];
                            ?>
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($item['nama_produk']) ?></div>
                                    <span class="text-muted small"><?= htmlspecialchars($item['daya'] ?: '-') ?> | <?= htmlspecialchars($item['ukuran_produk'] ?: '-') ?></span>
                                </td>
                                <td>
                                    <?php if (!empty($item['besar_diskon'])): ?>
                                        <del class="text-muted small d-block"><?= rupiah($item['harga']) ?></del>
                                    <?php endif; ?>
                                    <span class="fw-semibold text-primary"><?= rupiah($item['harga_final']) ?></span>
                                </td>
                                <td>
                                    <form action="index.php?page=update_keranjang" method="POST" class="d-flex align-items-center gap-1">
                                        <input type="hidden" name="id_keranjang" value="<?= $item['id_keranjang'] ?>">
                                        <input type="number" name="jml_beli" value="<?= $item['jml_beli'] ?>" min="1" max="50" class="form-control form-control-sm text-center" style="width: 60px;">
                                        <button type="submit" class="btn btn-sm btn-outline-secondary" title="Perbarui"><i class="bi bi-arrow-repeat"></i></button>
                                    </form>
                                </td>
                                <td class="fw-bold text-dark"><?= rupiah($subtotal) ?></td>
                                <td>
                                    <a href="index.php?page=hapus_keranjang&id=<?= $item['id_keranjang'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus paket ini dari keranjang?')" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card card-custom p-4">
                <h5 class="fw-bold mb-3 border-bottom pb-2">Ringkasan Pemesanan</h5>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Total Paket:</span>
                    <span class="fw-semibold"><?= count($items) ?> Layanan</span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">Biaya Pengiriman Teknisi:</span>
                    <span class="text-success fw-semibold">Gratis Area Metro</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between align-items-baseline mb-4">
                    <span class="fw-bold fs-5">Total Pembayaran:</span>
                    <span class="display-6 fw-bold text-primary fs-4"><?= rupiah($grandTotal) ?></span>
                </div>

                <a href="index.php?page=checkout" class="btn btn-primary-custom w-100 py-3 rounded-pill fw-bold shadow-sm text-center">
                    <i class="bi bi-box-arrow-right me-1"></i> Lanjut ke Checkout
                </a>

                <div class="mt-4 p-3 bg-light rounded-3 small text-muted">
                    <i class="bi bi-info-circle-fill text-primary me-1"></i> Pada halaman checkout, Anda dapat memilih alamat pengerjaan, rekening transfer, dan mengunggah bukti pembayaran.
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

