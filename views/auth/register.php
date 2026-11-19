<?php
// views/auth/register.php
$pageTitle = 'Daftar Akun Baru';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card card-custom p-4 p-md-5">
                <div class="text-center mb-4">
                    <span class="bg-warning bg-opacity-20 text-warning-emphasis p-3 rounded-circle d-inline-flex mb-2">
                        <i class="bi bi-person-plus-fill fs-2 text-warning"></i>
                    </span>
                    <h3 class="fw-bold">Pendaftaran Konsumen</h3>
                    <p class="text-muted small">Buat akun untuk memesan layanan instalasi listrik dan memantau status pengerjaan</p>
                </div>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger py-2 small"><i class="bi bi-x-circle me-1"></i> <?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form action="index.php?page=register_process" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="username" class="form-control" placeholder="Contoh: Budi Santoso" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold small">Alamat Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" placeholder="nama@email.com" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold small">Nomor Telepon/WA <span class="text-danger">*</span></label>
                            <input type="text" name="nomor_telp" class="form-control" placeholder="08xxxxxxxxxx" required value="<?= htmlspecialchars($_POST['nomor_telp'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold small">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="form-control" value="<?= htmlspecialchars($_POST['tanggal_lahir'] ?? '') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold small">Kata Sandi <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold small">Alamat Lengkap Pemasangan / Domisili <span class="text-danger">*</span></label>
                        <textarea name="alamat" class="form-control" rows="2" placeholder="Nama Jalan, No. Rumah, RT/RW, Kelurahan, Kecamatan, Kota Metro" required><?= htmlspecialchars($_POST['alamat'] ?? '') ?></textarea>
                        <div class="form-text small">Alamat ini akan otomatis disimpan sebagai alamat utama pemesanan jasa listrik Anda.</div>
                    </div>

                    <button type="submit" class="btn btn-primary-custom w-100 py-2 mb-3">
                        <i class="bi bi-person-check me-1"></i> Selesaikan Pendaftaran
                    </button>
                </form>

                <div class="text-center text-muted small">
                    Sudah punya akun? <a href="index.php?page=login" class="fw-bold text-decoration-none">Masuk di sini</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

