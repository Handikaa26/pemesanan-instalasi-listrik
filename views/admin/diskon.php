<?php
// views/admin/diskon.php
require_admin();
$pageTitle = 'Kelola Diskon Promo';
require_once __DIR__ . '/../layouts/header.php';
$db = Database::connect();

$discounts = $db->query("SELECT d.*, p.nama_produk, p.harga 
                         FROM diskon d 
                         JOIN produk p ON d.id_produk = p.id_produk 
                         ORDER BY d.id_diskon DESC")->fetchAll();

$allProducts = $db->query("SELECT id_produk, nama_produk, harga FROM produk ORDER BY nama_produk ASC")->fetchAll();
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">Kelola Diskon & Promo Layanan</h3>
            <p class="text-muted small mb-0">Atur potongan harga untuk menarik konsumen memesan instalasi listrik</p>
        </div>
        <button class="btn btn-primary-custom btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#modalTambahDiskon">
            <i class="bi bi-plus-lg me-1"></i> Tambah Diskon Baru
        </button>
    </div>

    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'added'): ?>
        <div class="alert alert-success py-2 small"><i class="bi bi-check-circle me-1"></i> Diskon promo berhasil ditambahkan!</div>
    <?php endif; ?>
    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'updated'): ?>
        <div class="alert alert-success py-2 small"><i class="bi bi-check-circle me-1"></i> Diskon berhasil diperbarui!</div>
    <?php endif; ?>
    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
        <div class="alert alert-info py-2 small"><i class="bi bi-info-circle me-1"></i> Diskon promo berhasil dihapus.</div>
    <?php endif; ?>

    <div class="card card-custom p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light small">
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th>Layanan Paket</th>
                        <th>Harga Normal</th>
                        <th>Potongan Diskon</th>
                        <th>Harga Setelah Diskon</th>
                        <th>Status</th>
                        <th style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($discounts)): ?>
                        <tr><td colspan="7" class="text-center py-4 text-muted">Belum ada data diskon promo aktif.</td></tr>
                    <?php else: ?>
                        <?php foreach ($discounts as $d): 
                            $hargaSetelahDiskon = max(0, $d['harga'] - $d['besar_diskon']);
                        ?>
                        <tr>
                            <td><?= $d['id_diskon'] ?></td>
                            <td class="fw-bold text-dark"><?= htmlspecialchars($d['nama_produk']) ?></td>
                            <td><?= rupiah($d['harga']) ?></td>
                            <td class="text-danger fw-bold">- <?= rupiah($d['besar_diskon']) ?></td>
                            <td class="text-success fw-bold"><?= rupiah($hargaSetelahDiskon) ?></td>
                            <td>
                                <?= ($d['is_active'] == 1) ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-secondary">Nonaktif</span>' ?>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEditDiskon<?= $d['id_diskon'] ?>">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <a href="index.php?page=admin_diskon_delete&id=<?= $d['id_diskon'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus promo diskon ini?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>

                        <!-- Modal Edit Diskon -->
                        <div class="modal fade" id="modalEditDiskon<?= $d['id_diskon'] ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="index.php?page=admin_diskon_update" method="POST">
                                        <input type="hidden" name="id_diskon" value="<?= $d['id_diskon'] ?>">
                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold">Edit Diskon Promo</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">Layanan Paket</label>
                                                <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($d['nama_produk']) ?> (<?= rupiah($d['harga']) ?>)" readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">Besar Diskon Potongan (Rp)</label>
                                                <input type="number" name="besar_diskon" class="form-control" value="<?= $d['besar_diskon'] ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">Status Aktif</label>
                                                <select name="is_active" class="form-select">
                                                    <option value="1" <?= ($d['is_active'] == 1) ? 'selected' : '' ?>>Aktif</option>
                                                    <option value="0" <?= ($d['is_active'] == 0) ? 'selected' : '' ?>>Nonaktif</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary-custom btn-sm">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Diskon -->
<div class="modal fade" id="modalTambahDiskon" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="index.php?page=admin_diskon_add" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Tambah Diskon Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Pilih Layanan</label>
                        <select name="id_produk" class="form-select" required>
                            <?php foreach ($allProducts as $prod): ?>
                            <option value="<?= $prod['id_produk'] ?>"><?= htmlspecialchars($prod['nama_produk']) ?> (<?= rupiah($prod['harga']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Besar Potongan Diskon (Rp)</label>
                        <input type="number" name="besar_diskon" class="form-control" placeholder="Contoh: 150000" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Status</label>
                        <select name="is_active" class="form-select">
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-custom btn-sm"><i class="bi bi-save me-1"></i> Simpan Diskon</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

