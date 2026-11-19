<?php
// views/admin/pesanan_detail.php
require_admin();
$id_pesanan = $_GET['id'] ?? '';
$db = Database::connect();

$sql = "SELECT p.*, pr.nama_produk, pr.daya, pr.ukuran, pr.harga AS harga_asli,
               b.nama_bank, b.norek, b.atas_nama,
               a.alamat, a.penerima, a.telepon_penerima, a.tipe_alamat, a.kota, a.provinsi, a.kode_pos, a.catatan_patokan,
               u.username, u.email, u.nomor_telp
        FROM pesanan p
        JOIN produk pr ON p.id_produk = pr.id_produk
        JOIN bank b ON p.id_bank = b.id_bank
        JOIN alamat a ON p.id_alamat = a.id_alamat
        JOIN user u ON p.id_user = u.id_user
        WHERE p.id_pesanan = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$id_pesanan]);
$pesanan = $stmt->fetch();

if (!$pesanan) {
    header("Location: index.php?page=admin_pesanan");
    exit;
}

$pageTitle = 'Proses Pesanan ' . $pesanan['id_pesanan'];
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="index.php?page=admin_pesanan" class="text-decoration-none small text-muted"><i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Pesanan</a>
            <h3 class="fw-bold mb-0 mt-1">Pemrosesan Pesanan: <span class="text-primary font-monospace"><?= htmlspecialchars($pesanan['id_pesanan']) ?></span></h3>
        </div>
        <div>
            <a href="index.php?page=kuitansi&id=<?= $pesanan['id_pesanan'] ?>" target="_blank" class="btn btn-outline-primary rounded-pill btn-sm">
                <i class="bi bi-printer me-1"></i> Cetak Kuitansi / Invoice
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Rincian Pesanan & Konsumen -->
        <div class="col-lg-7">
            <div class="card card-custom p-4 mb-4">
                <h5 class="fw-bold mb-3 border-bottom pb-2">Informasi Konsumen & Lokasi</h5>
                <div class="row g-3 small">
                    <div class="col-sm-6">
                        <span class="text-muted d-block">Nama Pemesan:</span>
                        <strong><?= htmlspecialchars($pesanan['username']) ?></strong>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-muted d-block">Kontak / Email:</span>
                        <div><?= htmlspecialchars($pesanan['nomor_telp'] ?: '-') ?></div>
                        <div><?= htmlspecialchars($pesanan['email']) ?></div>
                    </div>
                    <div class="col-12">
                        <span class="text-muted d-block">Penerima & Alamat Pemasangan:</span>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <strong class="fs-6"><?= htmlspecialchars($pesanan['penerima']) ?> (<?= htmlspecialchars($pesanan['telepon_penerima']) ?>)</strong>
                            <span class="badge bg-primary-subtle text-primary border border-primary small">
                                <i class="bi bi-geo-alt-fill me-1"></i><?= htmlspecialchars($pesanan['tipe_alamat'] ?? 'Rumah') ?>
                            </span>
                        </div>
                        <p class="mb-1 text-secondary"><?= htmlspecialchars($pesanan['alamat']) ?></p>
                        <div class="small text-muted mb-1">
                            <i class="bi bi-pin-map text-danger me-1"></i><?= htmlspecialchars($pesanan['kota'] ?? 'Kota Metro') ?>, <?= htmlspecialchars($pesanan['provinsi'] ?? 'Lampung') ?> <?= !empty($pesanan['kode_pos']) ? '- ' . htmlspecialchars($pesanan['kode_pos']) : '' ?>
                        </div>
                        <?php if (!empty($pesanan['catatan_patokan'])): ?>
                        <div class="p-2 bg-light border-start border-warning border-3 rounded small text-dark mt-1">
                            <strong>Patokan/Catatan Teknisi:</strong> <?= htmlspecialchars($pesanan['catatan_patokan']) ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="card card-custom p-4 mb-4">
                <h5 class="fw-bold mb-3 border-bottom pb-2">Rincian Paket Yang Dipesan</h5>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <h6 class="fw-bold text-primary mb-1"><?= htmlspecialchars($pesanan['nama_produk']) ?></h6>
                        <span class="text-muted small"><?= htmlspecialchars($pesanan['daya'] ?: '-') ?> | <?= htmlspecialchars($pesanan['ukuran'] ?: '-') ?></span>
                    </div>
                    <div class="text-end">
                        <div class="text-muted small"><?= $pesanan['jml_beli'] ?> paket</div>
                        <div class="fw-bold fs-5 text-dark"><?= rupiah($pesanan['total_bayar']) ?></div>
                    </div>
                </div>
            </div>

            <div class="card card-custom p-4">
                <h5 class="fw-bold mb-3 border-bottom pb-2">Bukti Pembayaran Konsumen</h5>
                <div class="mb-2 small">
                    Transfer ke: <strong><?= htmlspecialchars($pesanan['nama_bank']) ?></strong> - <?= htmlspecialchars($pesanan['norek']) ?> (a/n <?= htmlspecialchars($pesanan['atas_nama']) ?>)
                </div>
                <?php if ($pesanan['bukti_transfer']): ?>
                    <div class="text-center p-3 bg-light rounded-3 border">
                        <a href="uploads/bukti/<?= htmlspecialchars($pesanan['bukti_transfer']) ?>" target="_blank">
                            <img src="uploads/bukti/<?= htmlspecialchars($pesanan['bukti_transfer']) ?>" alt="Bukti Transfer" class="img-fluid rounded" style="max-height: 380px;">
                        </a>
                        <div class="mt-2">
                            <a href="uploads/bukti/<?= htmlspecialchars($pesanan['bukti_transfer']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-box-arrow-up-right me-1"></i> Buka Ukuran Penuh
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning small mb-0">Belum ada file bukti transfer yang diunggah.</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Form Update Jadwal & Status (Sesuai Flowchart Admin) -->
        <div class="col-lg-5">
            <div class="card card-custom p-4 sticky-top" style="top: 100px;">
                <h5 class="fw-bold mb-3 border-bottom pb-2 text-primary">
                    <i class="bi bi-calendar-check me-2"></i>Tindakan Admin & Penjadwalan
                </h5>

                <form action="index.php?page=admin_pesanan_update" method="POST">
                    <input type="hidden" name="id_pesanan" value="<?= $pesanan['id_pesanan'] ?>">

                    <div class="mb-3">
                        <label class="form-label small fw-bold">1. Verifikasi Status Pembayaran</label>
                        <select name="status_pembayaran" class="form-select">
                            <option value="0" <?= ($pesanan['status_pembayaran'] == 0) ? 'selected' : '' ?>>0 - Menunggu Verifikasi</option>
                            <option value="1" <?= ($pesanan['status_pembayaran'] == 1) ? 'selected' : '' ?>>1 - Lunas / Pembayaran Valid</option>
                            <option value="2" <?= ($pesanan['status_pembayaran'] == 2) ? 'selected' : '' ?>>2 - Ditolak / Tidak Valid</option>
                        </select>
                        <div class="form-text small">Pilih Lunas jika dana sudah masuk ke rekening bank.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">2. Jadwal Pemasangan Teknisi</label>
                        <input type="text" name="jadwal_pemasangan" class="form-control" placeholder="Contoh: Kamis, 20 Nov 2026 - Pukul 09.00 WIB" value="<?= htmlspecialchars($pesanan['jadwal_pemasangan'] ?? '') ?>">
                        <div class="form-text small">Sesuai flowchart: Tentukan jadwal ketersediaan jasa/teknisi.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">3. Tim / Armada Teknisi Bertugas</label>
                        <input type="text" name="kurir" class="form-control" placeholder="Contoh: Tim 1 (Bpk. Joko & Bpk. Rudi)" value="<?= htmlspecialchars($pesanan['kurir'] ?? 'Teknisi PT. Eksavindo') ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">4. Status Keberangkatan / Progres</label>
                        <select name="status_keberangkatan" class="form-select">
                            <option value="0" <?= ($pesanan['status_keberangkatan'] == 0) ? 'selected' : '' ?>>Menunggu Penjadwalan</option>
                            <option value="1" <?= ($pesanan['status_keberangkatan'] == 1) ? 'selected' : '' ?>>Teknisi Ditugaskan</option>
                            <option value="2" <?= ($pesanan['status_keberangkatan'] == 2) ? 'selected' : '' ?>>Sedang Menuju Lokasi</option>
                            <option value="3" <?= ($pesanan['status_keberangkatan'] == 3) ? 'selected' : '' ?>>Tiba di Lokasi / Pengerjaan</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold">5. Status Pemesanan Keseluruhan</label>
                        <select name="status_pemesanan" class="form-select">
                            <option value="0" <?= ($pesanan['status_pemesanan'] == 0) ? 'selected' : '' ?>>Pesanan Baru</option>
                            <option value="1" <?= ($pesanan['status_pemesanan'] == 1) ? 'selected' : '' ?>>Sedang Diproses / Dikerjakan</option>
                            <option value="2" <?= ($pesanan['status_pemesanan'] == 2) ? 'selected' : '' ?>>Pemasangan Selesai</option>
                            <option value="3" <?= ($pesanan['status_pemesanan'] == 3) ? 'selected' : '' ?>>Dibatalkan</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary-custom w-100 py-2 fw-bold">
                        <i class="bi bi-check2-circle me-1"></i> Simpan & Perbarui Status
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

