<?php
// views/konsumen/home.php
$pageTitle = 'Beranda Layanan Instalasi Listrik';
require_once __DIR__ . '/../layouts/header.php';
$db = Database::connect();

// Ambil paket terlaris & diskon
$sql = "SELECT p.*, k.nama_kategori, d.besar_diskon 
        FROM produk p
        LEFT JOIN kategori k ON p.id_kategori = k.id_kategori
        LEFT JOIN diskon d ON p.id_produk = d.id_produk AND d.is_active = 1
        ORDER BY p.terjual DESC LIMIT 6";
$stmt = $db->query($sql);
$featuredProducts = $stmt->fetchAll();

// Kategori
$categories = $db->query("SELECT * FROM kategori")->fetchAll();
?>

<!-- Hero Banner -->
<div class="container my-4">
    <div class="hero-banner p-4 p-md-5">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold mb-3">
                    <i class="bi bi-patch-check-fill me-1"></i> RESMI & STANDAR PUIL
                </span>
                <h1 class="display-6 fw-extrabold mb-3">Solusi Aman & Terpercaya Instalasi Listrik Anda</h1>
                <p class="lead opacity-90 mb-4 fs-6">
                    PT. Eksavindo Mitra Mandiri melayani perancangan dan pemasangan instalasi listrik rumah tinggal, gedung komersil, grounding & penangkal petir dengan tenaga ahli bersertifikat.
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="index.php?page=katalog" class="btn btn-warning btn-lg px-4 fw-bold text-dark rounded-pill shadow">
                        <i class="bi bi-lightning-charge me-1"></i> Pesan Instalasi Sekarang
                    </a>
                    <?php if (!is_logged_in()): ?>
                    <a href="index.php?page=register" class="btn btn-outline-light btn-lg px-4 rounded-pill">
                        Daftar Akun Konsumen
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-5 text-center d-none d-lg-block">
                <div class="bg-white bg-opacity-10 p-4 rounded-4 backdrop-blur">
                    <i class="bi bi-shield-fill-check text-warning" style="font-size: 5rem;"></i>
                    <h5 class="fw-bold mt-2">Jaminan Kualitas & Keamanan</h5>
                    <p class="small text-white-50 mb-0">Teknisi profesional, material SNI, pengerjaan cepat dan bergaransi.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Features / Keunggulan -->
<div class="container my-5">
    <div class="row g-4 text-center">
        <div class="col-md-3">
            <div class="card card-custom p-4 h-100">
                <div class="text-primary mb-3"><i class="bi bi-tools fs-1"></i></div>
                <h6 class="fw-bold">Teknisi Berpengalaman</h6>
                <p class="text-muted small mb-0">Dikerjakan langsung oleh teknisi berkeahlian dan bersertifikasi kelistrikan.</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-custom p-4 h-100">
                <div class="text-warning mb-3"><i class="bi bi-patch-check fs-1"></i></div>
                <h6 class="fw-bold">Material Berstandar SNI</h6>
                <p class="text-muted small mb-0">Kabel dan komponen MCB kualitas teruji standar PUIL menjamin keamanan keluarga.</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-custom p-4 h-100">
                <div class="text-success mb-3"><i class="bi bi-calendar2-check fs-1"></i></div>
                <h6 class="fw-bold">Jadwal Tepat Waktu</h6>
                <p class="text-muted small mb-0">Konfirmasi pemesanan cepat dan penjadwalan kunjungan fleksibel sesuai waktu Anda.</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-custom p-4 h-100">
                <div class="text-info mb-3"><i class="bi bi-file-earmark-text fs-1"></i></div>
                <h6 class="fw-bold">Kuitansi & Invoice Resmi</h6>
                <p class="text-muted small mb-0">Sistem terintegrasi dengan bukti pembayaran dan pencetakan kuitansi resmi.</p>
            </div>
        </div>
    </div>
</div>

<!-- Kategori Layanan -->
<div class="container my-5">
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <h4 class="fw-bold mb-1">Kategori Layanan</h4>
            <p class="text-muted small mb-0">Pilih kategori instalasi sesuai kebutuhan properti Anda</p>
        </div>
        <a href="index.php?page=katalog" class="text-primary fw-semibold text-decoration-none small">Lihat Semua <i class="bi bi-arrow-right"></i></a>
    </div>

    <div class="row g-3">
        <?php foreach ($categories as $cat): ?>
        <div class="col-6 col-md-4 col-lg-2">
            <a href="index.php?page=katalog&kategori=<?= $cat['id_kategori'] ?>" class="text-decoration-none text-dark">
                <div class="card card-custom text-center p-3 h-100 border hover-border-primary">
                    <div class="bg-light p-3 rounded-circle d-inline-block mx-auto mb-2 text-primary">
                        <i class="bi bi-lightning-charge fs-4"></i>
                    </div>
                    <div class="small fw-bold"><?= htmlspecialchars($cat['nama_kategori']) ?></div>
                </div>
            </a>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Paket Terlaris / Populer -->
<div class="container my-5">
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <span class="badge bg-danger mb-1">REKOMENDASI</span>
            <h4 class="fw-bold mb-1">Paket Pemesanan Paling Populer</h4>
            <p class="text-muted small mb-0">Layanan instalasi listrik terfavorit dengan rating kepuasan tertinggi</p>
        </div>
        <a href="index.php?page=katalog" class="btn btn-outline-primary rounded-pill btn-sm px-3">Lihat Katalog Lengkap</a>
    </div>

    <div class="row g-4">
        <?php foreach ($featuredProducts as $item): 
            $finalPrice = $item['harga'] - ($item['besar_diskon'] ?? 0);
        ?>
        <div class="col-md-6 col-lg-4">
            <div class="card card-custom h-100 d-flex flex-column">
                <div class="position-relative bg-light rounded-top p-4 text-center border-bottom">
                    <?php if (!empty($item['label_produk'])): ?>
                    <span class="position-absolute top-0 start-0 m-3 badge bg-primary"><?= htmlspecialchars($item['label_produk']) ?></span>
                    <?php endif; ?>
                    <?php if (!empty($item['besar_diskon'])): ?>
                    <span class="position-absolute top-0 end-0 m-3 badge bg-danger">Diskon <?= rupiah($item['besar_diskon']) ?></span>
                    <?php endif; ?>
                    <i class="bi bi-hdd-network text-primary display-4 my-3 d-inline-block"></i>
                    <div class="text-muted small"><?= htmlspecialchars($item['nama_kategori']) ?></div>
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
                                <button type="submit" class="btn btn-primary-custom btn-sm rounded-pill px-3" title="Tambah ke Keranjang">
                                    <i class="bi bi-cart-plus"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

