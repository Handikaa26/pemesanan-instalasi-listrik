<?php
// views/admin/produk.php
require_admin();
$pageTitle = 'Kelola Produk & Layanan';
require_once __DIR__ . '/../layouts/header.php';
$db = Database::connect();

$categories = $db->query("SELECT * FROM kategori")->fetchAll();

$sql = "SELECT p.*, k.nama_kategori, d.besar_diskon 
        FROM produk p
        LEFT JOIN kategori k ON p.id_kategori = k.id_kategori
        LEFT JOIN diskon d ON p.id_produk = d.id_produk AND d.is_active = 1
        ORDER BY p.id_produk DESC";
$products = $db->query($sql)->fetchAll();
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">Kelola Layanan & Produk Instalasi</h3>
            <p class="text-muted small mb-0">Tambah, ubah spesifikasi daya/ukuran, serta harga paket instalasi listrik</p>
        </div>
        <button class="btn btn-primary-custom btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#modalTambahProduk">
            <i class="bi bi-plus-lg me-1"></i> Tambah Layanan Baru
        </button>
    </div>

    <!-- Search Bar AJAX Admin Produk -->
    <div class="card card-custom p-3 mb-4">
        <div class="row align-items-center g-2">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" id="searchProdukInput" class="form-control border-start-0" placeholder="Filter langsung nama paket, kategori, atau daya..." autocomplete="off">
                    <button class="btn btn-outline-secondary d-none" type="button" id="btnResetProduk"><i class="bi bi-x-lg"></i></button>
                </div>
            </div>
            <div class="col-md-6 text-md-end text-muted small">
                <span id="produkInfoCount"><?= count($products) ?> paket layanan terdaftar</span>
            </div>
        </div>
    </div>

    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'added'): ?>
        <div class="alert alert-success py-2 small"><i class="bi bi-check-circle me-1"></i> Layanan baru berhasil ditambahkan!</div>
    <?php endif; ?>
    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'updated'): ?>
        <div class="alert alert-success py-2 small"><i class="bi bi-check-circle me-1"></i> Layanan berhasil diperbarui!</div>
    <?php endif; ?>
    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
        <div class="alert alert-info py-2 small"><i class="bi bi-info-circle me-1"></i> Layanan berhasil dihapus.</div>
    <?php endif; ?>

    <div class="card card-custom p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light small">
                    <tr>
                        <th>ID</th>
                        <th>Nama Paket Layanan</th>
                        <th>Kategori</th>
                        <th>Fase / Daya</th>
                        <th>Harga Satuan</th>
                        <th>Kuota / Terjual</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="produkTableBody">
                    <?php foreach ($products as $p): ?>
                    <tr class="produk-row" data-search="<?= strtolower(htmlspecialchars($p['nama_produk'] . ' ' . $p['nama_kategori'] . ' ' . $p['daya'] . ' ' . $p['ukuran'] . ' ' . $p['label_produk'])) ?>">
                        <td><?= $p['id_produk'] ?></td>
                        <td>
                            <strong><?= htmlspecialchars($p['nama_produk']) ?></strong>
                            <?php if ($p['label_produk']): ?>
                                <span class="badge bg-warning text-dark ms-1"><?= htmlspecialchars($p['label_produk']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($p['nama_kategori']) ?></td>
                        <td>
                            <small class="text-muted d-block"><?= htmlspecialchars($p['ukuran'] ?: '-') ?></small>
                            <small class="text-primary"><?= htmlspecialchars($p['daya'] ?: '-') ?></small>
                        </td>
                        <td class="fw-bold text-dark"><?= rupiah($p['harga']) ?></td>
                        <td>
                            <small class="text-muted">Stok: <?= $p['stok'] ?> | Terjual: <?= $p['terjual'] ?></small>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEditProduk<?= $p['id_produk'] ?>">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <a href="index.php?page=admin_produk_delete&id=<?= $p['id_produk'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus produk ini?')">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>

                    <!-- Modal Edit Produk -->
                    <div class="modal fade" id="modalEditProduk<?= $p['id_produk'] ?>" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <form action="index.php?page=admin_produk_update" method="POST">
                                    <input type="hidden" name="id_produk" value="<?= $p['id_produk'] ?>">
                                    <div class="modal-header">
                                        <h5 class="modal-title fw-bold">Edit Layanan Instalasi</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row g-3">
                                            <div class="col-md-8">
                                                <label class="form-label small fw-bold">Nama Paket Layanan</label>
                                                <input type="text" name="nama_produk" class="form-control" value="<?= htmlspecialchars($p['nama_produk']) ?>" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label small fw-bold">Label Promo (opsional)</label>
                                                <input type="text" name="label_produk" class="form-control" value="<?= htmlspecialchars($p['label_produk'] ?? '') ?>" placeholder="Misal: Best Seller">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">Kategori Layanan</label>
                                                <select name="id_kategori" class="form-select" required>
                                                    <?php foreach ($categories as $cat): ?>
                                                    <option value="<?= $cat['id_kategori'] ?>" <?= ($p['id_kategori'] == $cat['id_kategori']) ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($cat['nama_kategori']) ?>
                                                    </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">Harga Paket (Rp)</label>
                                                <input type="number" name="harga" class="form-control" value="<?= $p['harga'] ?>" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label small fw-bold">Fase / Ukuran</label>
                                                <input type="text" name="ukuran" class="form-control" value="<?= htmlspecialchars($p['ukuran'] ?? '') ?>" placeholder="Misal: 1 Phase / 3 Phase">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label small fw-bold">Kapasitas Daya</label>
                                                <input type="text" name="daya" class="form-control" value="<?= htmlspecialchars($p['daya'] ?? '') ?>" placeholder="Misal: 900 VA - 2200 VA">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label small fw-bold">Kuota Kuota Pengerjaan</label>
                                                <input type="number" name="stok" class="form-control" value="<?= $p['stok'] ?>" required>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label small fw-bold">Deskripsi & Ruang Lingkup</label>
                                                <textarea name="deskripsi_produk" class="form-control" rows="4" required><?= htmlspecialchars($p['deskripsi_produk'] ?? '') ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                                        <button type="submit" class="btn btn-primary-custom btn-sm">Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Produk -->
<div class="modal fade" id="modalTambahProduk" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="index.php?page=admin_produk_add" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Tambah Layanan Instalasi Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label small fw-bold">Nama Paket Layanan</label>
                            <input type="text" name="nama_produk" class="form-control" placeholder="Contoh: Paket Instalasi Ruko 2 Lantai" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Label Promo</label>
                            <input type="text" name="label_produk" class="form-control" placeholder="Misal: Promo Hemat">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Kategori Layanan</label>
                            <select name="id_kategori" class="form-select" required>
                                <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id_kategori'] ?>"><?= htmlspecialchars($cat['nama_kategori']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Harga Paket (Rp)</label>
                            <input type="number" name="harga" class="form-control" placeholder="Contoh: 2500000" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Fase / Ukuran</label>
                            <input type="text" name="ukuran" class="form-control" placeholder="Contoh: 1 Phase">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Kapasitas Daya</label>
                            <input type="text" name="daya" class="form-control" placeholder="Contoh: 1300 VA - 3500 VA">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Kuota Tim / Kapasitas</label>
                            <input type="number" name="stok" class="form-control" value="10" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Deskripsi & Ruang Lingkup</label>
                            <textarea name="deskripsi_produk" class="form-control" rows="4" placeholder="Jelaskan titik stop kontak, lampu, jenis kabel SNI yang dipakai..." required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-custom btn-sm"><i class="bi bi-save me-1"></i> Tambahkan Layanan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('searchProdukInput');
    const btnReset = document.getElementById('btnResetProduk');
    const rows = document.querySelectorAll('.produk-row');
    const infoCount = document.getElementById('produkInfoCount');

    input.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        let visibleCount = 0;

        if (query.length > 0) {
            btnReset.classList.remove('d-none');
        } else {
            btnReset.classList.add('d-none');
        }

        rows.forEach(function(row) {
            const data = row.getAttribute('data-search') || '';
            if (data.includes(query)) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        if (infoCount) {
            infoCount.textContent = `${visibleCount} paket layanan ditemukan`;
        }
    });

    btnReset.addEventListener('click', function() {
        input.value = '';
        btnReset.classList.add('d-none');
        rows.forEach(r => r.style.display = '');
        if (infoCount) {
            infoCount.textContent = `${rows.length} paket layanan terdaftar`;
        }
        input.focus();
    });
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

