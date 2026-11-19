<?php
// views/konsumen/katalog_items.php
// Digunakan baik oleh katalog.php maupun AJAX response
$finalPrice = $item['harga'] - ($item['besar_diskon'] ?? 0);
?>
<div class="col-md-6 col-lg-4 product-card-col">
    <div class="card card-custom h-100 d-flex flex-column">
        <div class="position-relative bg-light rounded-top p-4 text-center border-bottom">
            <?php if (!empty($item['label_produk'])): ?>
            <span class="position-absolute top-0 start-0 m-3 badge bg-primary"><?= htmlspecialchars($item['label_produk']) ?></span>
            <?php endif; ?>
            <?php if (!empty($item['besar_diskon'])): ?>
            <span class="position-absolute top-0 end-0 m-3 badge bg-danger">Diskon <?= rupiah($item['besar_diskon']) ?></span>
            <?php endif; ?>
            <i class="bi bi-lightning-charge text-primary display-4 my-3 d-inline-block"></i>
            <div class="text-muted small fw-semibold"><?= htmlspecialchars($item['nama_kategori']) ?></div>
        </div>

        <div class="card-body d-flex flex-column p-4">
            <h5 class="fw-bold text-dark mb-2"><?= htmlspecialchars($item['nama_produk']) ?></h5>
            <p class="text-muted small flex-grow-1"><?= htmlspecialchars(substr($item['deskripsi_produk'], 0, 110)) ?>...</p>

            <div class="bg-light p-2 rounded-3 mb-3 small d-flex justify-content-between">
                <span><i class="bi bi-cpu me-1 text-primary"></i> <?= htmlspecialchars($item['ukuran'] ?: '-') ?></span>
                <span><i class="bi bi-speedometer2 me-1 text-warning"></i> <?= htmlspecialchars($item['daya'] ?: '-') ?></span>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-2">
                <div>
                    <?php if (!empty($item['besar_diskon'])): ?>
                    <del class="text-muted small d-block"><?= rupiah($item['harga']) ?></del>
                    <?php endif; ?>
                    <span class="fs-5 fw-bold text-primary"><?= rupiah($finalPrice) ?></span>
                </div>
                <div class="d-flex gap-2">
                    <a href="index.php?page=detail_produk&id=<?= $item['id_produk'] ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                        Detail
                    </a>
                    <form action="index.php?page=tambah_keranjang" method="POST" class="d-inline">
                        <input type="hidden" name="id_produk" value="<?= $item['id_produk'] ?>">
                        <input type="hidden" name="jml_beli" value="1">
                        <button type="submit" class="btn btn-primary-custom btn-sm rounded-pill px-3" title="Pesan / Tambah Keranjang">
                            <i class="bi bi-cart-plus me-1"></i> Pesan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

