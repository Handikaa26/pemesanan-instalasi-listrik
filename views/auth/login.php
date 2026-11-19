<?php
// views/auth/login.php
$pageTitle = 'Masuk Akun';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card card-custom p-4 p-md-5">
                <div class="text-center mb-4">
                    <span class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle d-inline-flex mb-2">
                        <i class="bi bi-box-arrow-in-right fs-2"></i>
                    </span>
                    <h3 class="fw-bold">Selamat Datang</h3>
                    <p class="text-muted small">Silakan masuk ke akun Anda untuk memesan instalasi listrik</p>
                </div>

                <?php if (isset($_GET['msg']) && $_GET['msg'] == 'harus_login'): ?>
                    <div class="alert alert-warning py-2 small"><i class="bi bi-exclamation-triangle me-1"></i> Silakan masuk terlebih dahulu.</div>
                <?php endif; ?>
                <?php if (isset($_GET['msg']) && $_GET['msg'] == 'registered'): ?>
                    <div class="alert alert-success py-2 small"><i class="bi bi-check-circle me-1"></i> Registrasi berhasil! Silakan masuk dengan email & password Anda.</div>
                <?php endif; ?>
                <?php if (isset($_GET['msg']) && $_GET['msg'] == 'logout'): ?>
                    <div class="alert alert-info py-2 small"><i class="bi bi-info-circle me-1"></i> Anda telah keluar dari sistem.</div>
                <?php endif; ?>
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger py-2 small"><i class="bi bi-x-circle me-1"></i> <?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form action="index.php?page=login_process" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                            <input type="email" name="email" class="form-control border-start-0" placeholder="nama@email.com" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold small">Kata Sandi</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-key text-muted"></i></span>
                            <input type="password" name="password" class="form-control border-start-0" placeholder="••••••••" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary-custom w-100 py-2 mb-3">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Masuk Sekarang
                    </button>
                </form>

                <div class="text-center text-muted small mt-2">
                    Belum memiliki akun? <a href="index.php?page=register" class="fw-bold text-decoration-none">Daftar sekarang</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

