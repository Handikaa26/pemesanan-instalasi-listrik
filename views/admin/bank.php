<?php
// views/admin/bank.php
require_admin();
$pageTitle = 'Kelola Rekening Bank';
require_once __DIR__ . '/../layouts/header.php';
$db = Database::connect();

$banks = $db->query("SELECT * FROM bank ORDER BY id_bank ASC")->fetchAll();
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">Kelola Rekening Bank Pembayaran</h3>
            <p class="text-muted small mb-0">Daftar rekening tujuan transfer pembayaran pesanan konsumen</p>
        </div>
        <button class="btn btn-primary-custom btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#modalTambahBank">
            <i class="bi bi-plus-lg me-1"></i> Tambah Bank Baru
        </button>
    </div>

    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'added'): ?>
        <div class="alert alert-success py-2 small"><i class="bi bi-check-circle me-1"></i> Rekening bank berhasil ditambahkan!</div>
    <?php endif; ?>
    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'updated'): ?>
        <div class="alert alert-success py-2 small"><i class="bi bi-check-circle me-1"></i> Rekening bank berhasil diperbarui!</div>
    <?php endif; ?>
    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
        <div class="alert alert-info py-2 small"><i class="bi bi-info-circle me-1"></i> Rekening bank berhasil dihapus.</div>
    <?php endif; ?>

    <div class="card card-custom p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light small">
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th>Nama Bank</th>
                        <th>Nomor Rekening</th>
                        <th>Atas Nama Rekening</th>
                        <th style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($banks as $b): ?>
                    <tr>
                        <td><?= $b['id_bank'] ?></td>
                        <td class="fw-bold text-dark"><i class="bi bi-bank me-2 text-primary"></i><?= htmlspecialchars($b['nama_bank']) ?></td>
                        <td class="font-monospace fw-semibold text-primary"><?= htmlspecialchars($b['norek']) ?></td>
                        <td><?= htmlspecialchars($b['atas_nama']) ?></td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEditBank<?= $b['id_bank'] ?>">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <a href="index.php?page=admin_bank_delete&id=<?= $b['id_bank'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus rekening bank ini?')">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>

                    <!-- Modal Edit -->
                    <div class="modal fade" id="modalEditBank<?= $b['id_bank'] ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="index.php?page=admin_bank_update" method="POST">
                                    <input type="hidden" name="id_bank" value="<?= $b['id_bank'] ?>">
                                    <div class="modal-header">
                                        <h5 class="modal-title fw-bold">Edit Rekening Bank</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold">Nama Bank</label>
                                            <input type="text" name="nama_bank" class="form-control" value="<?= htmlspecialchars($b['nama_bank']) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold">Nomor Rekening</label>
                                            <input type="text" name="norek" class="form-control" value="<?= htmlspecialchars($b['norek']) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold">Atas Nama</label>
                                            <input type="text" name="atas_nama" class="form-control" value="<?= htmlspecialchars($b['atas_nama']) ?>" required>
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
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Bank -->
<div class="modal fade" id="modalTambahBank" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="index.php?page=admin_bank_add" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Tambah Rekening Bank</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Bank</label>
                        <input type="text" name="nama_bank" class="form-control" placeholder="Contoh: Bank BNI" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nomor Rekening</label>
                        <input type="text" name="norek" class="form-control" placeholder="Contoh: 1234567890" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Atas Nama</label>
                        <input type="text" name="atas_nama" class="form-control" value="PT EKSAVINDO MITRA MANDIRI" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-custom btn-sm"><i class="bi bi-save me-1"></i> Simpan Rekening</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

