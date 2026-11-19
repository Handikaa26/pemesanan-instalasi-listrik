<?php
// views/konsumen/profil.php
require_login();
$pageTitle = 'Profil Pengguna & Alamat';
require_once __DIR__ . '/../layouts/header.php';
$db = Database::connect();
$currentUser = get_auth_user();

// Refresh data user dari DB
$stmtU = $db->prepare("SELECT * FROM user WHERE id_user = ?");
$stmtU->execute([$currentUser['id_user']]);
$user = $stmtU->fetch();

// Daftar Alamat
$stmtA = $db->prepare("SELECT * FROM alamat WHERE id_user = ?");
$stmtA->execute([$currentUser['id_user']]);
$daftarAlamat = $stmtA->fetchAll();
?>

<div class="container py-5">
    <div class="mb-4">
        <h3 class="fw-bold mb-1">Pengaturan Profil & Alamat</h3>
        <p class="text-muted small mb-0">Kelola informasi kontak dan daftar lokasi instalasi listrik Anda</p>
    </div>

    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'profil_updated'): ?>
        <div class="alert alert-success py-2 small"><i class="bi bi-check-circle me-1"></i> Data profil berhasil diperbarui!</div>
    <?php endif; ?>
    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'alamat_added'): ?>
        <div class="alert alert-success py-2 small"><i class="bi bi-check-circle me-1"></i> Alamat baru berhasil ditambahkan!</div>
    <?php endif; ?>
    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'alamat_deleted'): ?>
        <div class="alert alert-info py-2 small"><i class="bi bi-info-circle me-1"></i> Alamat berhasil dihapus.</div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Edit Profil -->
        <div class="col-lg-5">
            <div class="card card-custom p-4">
                <h5 class="fw-bold mb-3 border-bottom pb-2"><i class="bi bi-person me-2 text-primary"></i>Informasi Akun</h5>
                <form action="index.php?page=profil_update" method="POST">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">ID Pengguna</label>
                        <input type="text" class="form-control form-control-sm bg-light" value="<?= htmlspecialchars($user['id_user']) ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nama Lengkap</label>
                        <input type="text" name="username" class="form-control form-control-sm" value="<?= htmlspecialchars($user['username']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Email</label>
                        <input type="email" name="email" class="form-control form-control-sm" value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nomor Telepon / WhatsApp</label>
                        <input type="text" name="nomor_telp" class="form-control form-control-sm" value="<?= htmlspecialchars($user['nomor_telp'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" class="form-control form-control-sm" value="<?= htmlspecialchars($user['tanggal_lahir'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Kata Sandi Baru (Kosongkan jika tidak diubah)</label>
                        <input type="password" name="password" class="form-control form-control-sm" placeholder="••••••••">
                    </div>
                    <button type="submit" class="btn btn-primary-custom btn-sm w-100 py-2">
                        <i class="bi bi-save me-1"></i> Simpan Perubahan Profil
                    </button>
                </form>
            </div>
        </div>

        <!-- Daftar Alamat -->
        <div class="col-lg-7">
            <div class="card card-custom p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                    <h5 class="fw-bold mb-0"><i class="bi bi-geo-alt me-2 text-danger"></i>Daftar Alamat Pemasangan</h5>
                    <button class="btn btn-sm btn-outline-primary rounded-pill" data-bs-toggle="collapse" data-bs-target="#formTambahAlamat">
                        <i class="bi bi-plus-lg me-1"></i> Tambah Alamat
                    </button>
                </div>

                <div class="collapse mb-4" id="formTambahAlamat">
                    <div class="bg-light p-3 rounded-3 border">
                        <h6 class="fw-bold mb-3 text-primary"><i class="bi bi-plus-circle me-1"></i>Tambah Alamat Baru</h6>
                        <form action="index.php?page=alamat_add" method="POST">
                            <div class="row g-2 mb-2">
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold mb-1">Tipe Lokasi</label>
                                    <select name="tipe_alamat" class="form-select form-select-sm">
                                        <option value="Rumah">🏠 Rumah Tinggal</option>
                                        <option value="Komersial / Usaha">🏢 Komersial / Toko / Kantor</option>
                                        <option value="Industri / Gedung">🏭 Industri / Proyek</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold mb-1">Nama Pemilik / Penerima</label>
                                    <input type="text" name="penerima" class="form-control form-control-sm" placeholder="Nama Lengkap" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold mb-1">Nomor WhatsApp / HP</label>
                                    <input type="text" name="telepon_penerima" class="form-control form-control-sm" placeholder="08xxxxxxxxxx" required>
                                </div>
                            </div>
                            <div class="row g-2 mb-2">
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold mb-1">Kode Pos</label>
                                    <input type="text" name="kode_pos" class="form-control form-control-sm" placeholder="34111" value="34111">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold mb-1">Kota / Kabupaten</label>
                                    <input type="text" name="kota" class="form-control form-control-sm" placeholder="Kota Metro" value="Kota Metro">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold mb-1">Provinsi</label>
                                    <input type="text" name="provinsi" class="form-control form-control-sm" placeholder="Lampung" value="Lampung">
                                </div>
                            </div>
                            <div class="mb-2">
                                <label class="form-label small fw-semibold mb-1">Alamat Lengkap</label>
                                <textarea name="alamat" class="form-control form-control-sm" rows="2" placeholder="Nama jalan, nomor rumah/ruko, RT/RW, kelurahan" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold mb-1">Patokan Lokasi (Opsional)</label>
                                <input type="text" name="catatan_patokan" class="form-control form-control-sm" placeholder="Contoh: Sebelah minimarket, pagar hitam">
                            </div>
                            <button type="submit" class="btn btn-success btn-sm rounded-pill px-3"><i class="bi bi-check-lg me-1"></i> Simpan Alamat</button>
                        </form>
                    </div>
                </div>

                <?php if (empty($daftarAlamat)): ?>
                    <p class="text-muted small">Belum ada data alamat tersimpan.</p>
                <?php else: ?>
                    <div class="d-flex flex-column gap-3">
                        <?php foreach ($daftarAlamat as $alm): 
                            $tipe = $alm['tipe_alamat'] ?? 'Rumah';
                            $isHome = (stripos($tipe, 'Rumah') !== false);
                            $kota = $alm['kota'] ?? 'Kota Metro';
                            $kodepos = $alm['kode_pos'] ?? '34111';
                            $catatan = $alm['catatan_patokan'] ?? '';
                        ?>
                        <div class="border rounded-3 p-3 position-relative bg-white shadow-sm">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="fw-bold text-dark"><?= htmlspecialchars($alm['penerima']) ?></span>
                                        <span class="badge <?= $isHome ? 'bg-primary-subtle text-primary border border-primary' : 'bg-warning-subtle text-warning-emphasis border border-warning' ?> small">
                                            <i class="bi <?= $isHome ? 'bi-house-door-fill' : 'bi-building' ?> me-1"></i><?= htmlspecialchars($tipe) ?>
                                        </span>
                                    </div>
                                    <div class="text-muted small mb-1"><i class="bi bi-telephone me-1"></i> <?= htmlspecialchars($alm['telepon_penerima']) ?></div>
                                    <p class="small text-secondary mb-1"><?= htmlspecialchars($alm['alamat']) ?></p>
                                    <div class="small text-muted mb-0"><i class="bi bi-pin-map-fill text-danger me-1"></i><?= htmlspecialchars($kota) ?> <?= !empty($kodepos) ? '- ' . htmlspecialchars($kodepos) : '' ?></div>
                                    <?php if (!empty($catatan)): ?>
                                    <div class="small text-muted mt-1 fst-italic"><i class="bi bi-info-circle me-1"></i>Patokan: <?= htmlspecialchars($catatan) ?></div>
                                    <?php endif; ?>
                                </div>
                                <a href="index.php?page=alamat_delete&id=<?= $alm['id_alamat'] ?>" class="btn btn-sm btn-outline-danger rounded-circle p-2" onclick="return confirm('Hapus alamat ini?')" title="Hapus Alamat">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

