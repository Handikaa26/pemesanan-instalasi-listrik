<?php
// views/admin/kategori.php
require_admin();
$pageTitle = 'Kelola Kategori Layanan';
require_once __DIR__ . '/../layouts/header.php';
$db = Database::connect();

$categories = $db->query("SELECT k.*, (SELECT COUNT(*) FROM produk p WHERE p.id_kategori = k.id_kategori) AS total_produk FROM kategori k ORDER BY k.id_kategori ASC")->fetchAll();
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">Kelola Kategori Layanan</h3>
            <p class="text-muted small mb-0">Kelompokkan jenis-jenis pekerjaan instalasi listrik PT. Eksavindo</p>
        </div>
        <button class="btn btn-primary-custom btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#modalTambahKategori">
            <i class="bi bi-plus-lg me-1"></i> Tambah Kategori
        </button>
    </div>

    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'added'): ?>
        <div class="alert alert-success py-2 small"><i class="bi bi-check-circle me-1"></i> Kategori baru berhasil ditambahkan!</div>
    <?php endif; ?>
    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'updated'): ?>
        <div class="alert alert-success py-2 small"><i class="bi bi-check-circle me-1"></i> Kategori berhasil diperbarui!</div>
    <?php endif; ?>
    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
        <div class="alert alert-info py-2 small"><i class="bi bi-info-circle me-1"></i> Kategori berhasil dihapus.</div>
    <?php endif; ?>

    <div class="card card-custom p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light small">
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th>Nama Kategori</th>
                        <th>Jumlah Paket Terdaftar</th>
                        <th style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $k): ?>
                    <tr>
                        <td><?= $k['id_kategori'] ?></td>
                        <td class="fw-bold text-dark"><?= htmlspecialchars($k['nama_kategori']) ?></td>
                        <td><span class="badge bg-secondary"><?= $k['total_produk'] ?> paket</span></td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEditKat<?= $k['id_kategori'] ?>">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <a href="index.php?page=admin_kategori_delete&id=<?= $k['id_kategori'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus kategori ini?')">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>

                    <!-- Modal Edit -->
                    <div class="modal fade" id="modalEditKat<?= $k['id_kategori'] ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="index.php?page=admin_kategori_update" method="POST">
                                    <input type="hidden" name="id_kategori" value="<?= $k['id_kategori'] ?>">
                                    <div class="modal-header">
                                        <h5 class="modal-title fw-bold">Edit Kategori</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <label class="form-label small fw-bold">Nama Kategori</label>
                                        <input type="text" name="nama_kategori" class="form-control" value="<?= htmlspecialchars($k['nama_kategori']) ?>" required>
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
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambahKategori" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="index.php?page=admin_kategori_add" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Tambah Kategori Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label small fw-bold">Nama Kategori</label>
                    <input type="text" name="nama_kategori" class="form-control" placeholder="Contoh: Instalasi Smart Home" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-custom btn-sm"><i class="bi bi-save me-1"></i> Simpan Kategori</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

