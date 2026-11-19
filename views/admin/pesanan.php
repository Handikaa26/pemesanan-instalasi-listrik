<?php
// views/admin/pesanan.php
require_admin();
$pageTitle = 'Kelola Pesanan & Jadwal Teknisi';
$db = Database::connect();

$filterStatus = isset($_GET['status_bayar']) ? $_GET['status_bayar'] : '';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$sql = "SELECT p.*, pr.nama_produk, pr.daya, u.username, u.email, u.nomor_telp, b.nama_bank, a.alamat, a.penerima, a.telepon_penerima
        FROM pesanan p
        JOIN produk pr ON p.id_produk = pr.id_produk
        JOIN user u ON p.id_user = u.id_user
        LEFT JOIN bank b ON p.id_bank = b.id_bank
        JOIN alamat a ON p.id_alamat = a.id_alamat
        WHERE 1=1";
$params = [];

if ($filterStatus !== '') {
    $sql .= " AND p.status_pembayaran = ?";
    $params[] = intval($filterStatus);
}

if (!empty($search)) {
    $sql .= " AND (p.id_pesanan LIKE ? OR u.username LIKE ? OR a.penerima LIKE ? OR pr.nama_produk LIKE ? OR a.alamat LIKE ?)";
    $like = "%$search%";
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
}

$sql .= " ORDER BY p.created_at DESC";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll();

// AJAX response untuk search live tabel pesanan
if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
    if (empty($orders)) {
        echo '<tr><td colspan="7" class="text-center py-4 text-muted"><i class="bi bi-search me-1"></i> Data pesanan tidak ditemukan.</td></tr>';
    } else {
        foreach ($orders as $ord) {
            ?>
            <tr>
                <td>
                    <span class="font-monospace fw-bold text-dark"><?= htmlspecialchars($ord['id_pesanan']) ?></span>
                    <div class="text-muted small"><?= date('d/m/Y H:i', strtotime($ord['created_at'])) ?></div>
                </td>
                <td>
                    <strong><?= htmlspecialchars($ord['username']) ?></strong>
                    <div class="text-muted small"><?= htmlspecialchars($ord['telepon_penerima'] ?: $ord['nomor_telp']) ?></div>
                    <div class="small text-truncate" style="max-width: 200px;" title="<?= htmlspecialchars($ord['alamat']) ?>"><?= htmlspecialchars($ord['alamat']) ?></div>
                </td>
                <td>
                    <div class="fw-semibold text-primary"><?= htmlspecialchars($ord['nama_produk']) ?></div>
                    <div class="text-muted small"><?= $ord['jml_beli'] ?>x (<?= htmlspecialchars($ord['daya'] ?: '-') ?>)</div>
                </td>
                <td class="fw-bold"><?= rupiah($ord['total_bayar']) ?></td>
                <td>
                    <?php if ($ord['status_pembayaran'] == 1): ?>
                        <span class="badge bg-success">Lunas</span>
                    <?php elseif ($ord['status_pembayaran'] == 2): ?>
                        <span class="badge bg-danger">Ditolak</span>
                    <?php else: ?>
                        <span class="badge bg-warning text-dark">Perlu Dicek</span>
                    <?php endif; ?>

                    <?php if ($ord['bukti_transfer']): ?>
                        <a href="uploads/bukti/<?= htmlspecialchars($ord['bukti_transfer']) ?>" target="_blank" class="d-block small text-decoration-none mt-1">
                            <i class="bi bi-image"></i> Bukti Bayar
                        </a>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($ord['jadwal_pemasangan']): ?>
                        <span class="fw-semibold text-dark small"><i class="bi bi-calendar-event text-primary me-1"></i> <?= htmlspecialchars($ord['jadwal_pemasangan']) ?></span>
                        <div class="text-muted small">Armada: <?= htmlspecialchars($ord['kurir'] ?: 'Teknisi PT. Eksavindo') ?></div>
                    <?php else: ?>
                        <span class="badge bg-light text-secondary border">Belum Dijadwalkan</span>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="index.php?page=admin_pesanan_detail&id=<?= $ord['id_pesanan'] ?>" class="btn btn-sm btn-primary rounded-pill px-3">
                        <i class="bi bi-gear-fill me-1"></i> Proses
                    </a>
                    <a href="index.php?page=kuitansi&id=<?= $ord['id_pesanan'] ?>" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill" title="Cetak Kuitansi">
                        <i class="bi bi-printer"></i>
                    </a>
                </td>
            </tr>
            <?php
        }
    }
    exit;
}

require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container py-5">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h3 class="fw-bold mb-1">Manajemen Pemesanan & Penjadwalan</h3>
            <p class="text-muted small mb-0">Verifikasi transfer konsumen, tetapkan jadwal instalasi & tugaskan teknisi</p>
        </div>
        <div class="d-flex gap-2">
            <a href="index.php?page=admin_pesanan" class="btn btn-sm <?= ($filterStatus === '') ? 'btn-primary' : 'btn-outline-secondary' ?>">Semua</a>
            <a href="index.php?page=admin_pesanan&status_bayar=0" class="btn btn-sm <?= ($filterStatus === '0') ? 'btn-warning text-dark' : 'btn-outline-warning' ?>">Menunggu Verifikasi</a>
            <a href="index.php?page=admin_pesanan&status_bayar=1" class="btn btn-sm <?= ($filterStatus === '1') ? 'btn-success' : 'btn-outline-success' ?>">Sudah Lunas</a>
        </div>
    </div>

    <!-- Search Bar AJAX Realtime -->
    <div class="card card-custom p-3 mb-4">
        <div class="row align-items-center g-2">
            <div class="col-md-7">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted">
                        <i class="bi bi-search" id="pesananSearchIcon"></i>
                        <span class="spinner-border spinner-border-sm text-primary d-none" id="pesananSpinner"></span>
                    </span>
                    <input type="text" id="searchPesananInput" class="form-control border-start-0" placeholder="Cari ID Pesanan, Nama Konsumen, Paket Instalasi, atau Alamat..." autocomplete="off">
                    <button class="btn btn-outline-secondary d-none" type="button" id="btnResetPesanan"><i class="bi bi-x-lg"></i></button>
                </div>
            </div>
            <div class="col-md-5 text-md-end text-muted small">
                <span id="pesananInfoCount"><?= count($orders) ?> pesanan ditemukan</span>
            </div>
        </div>
    </div>

    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'updated'): ?>
        <div class="alert alert-success alert-dismissible fade show py-2 small" role="alert">
            <i class="bi bi-check-circle me-1"></i> Data pesanan, status pembayaran, dan jadwal instalasi berhasil diperbarui!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card card-custom p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light small">
                    <tr>
                        <th>ID Pesanan</th>
                        <th>Konsumen & Alamat</th>
                        <th>Paket Instalasi</th>
                        <th>Total Biaya</th>
                        <th>Pembayaran</th>
                        <th>Jadwal Pemasangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="pesananTableBody">
                    <?php if (empty($orders)): ?>
                        <tr><td colspan="7" class="text-center py-4 text-muted">Tidak ada data pesanan yang sesuai.</td></tr>
                    <?php else: ?>
                        <?php foreach ($orders as $ord): ?>
                        <tr>
                            <td>
                                <span class="font-monospace fw-bold text-dark"><?= htmlspecialchars($ord['id_pesanan']) ?></span>
                                <div class="text-muted small"><?= date('d/m/Y H:i', strtotime($ord['created_at'])) ?></div>
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($ord['username']) ?></strong>
                                <div class="text-muted small"><?= htmlspecialchars($ord['telepon_penerima'] ?: $ord['nomor_telp']) ?></div>
                                <div class="small text-truncate" style="max-width: 200px;" title="<?= htmlspecialchars($ord['alamat']) ?>"><?= htmlspecialchars($ord['alamat']) ?></div>
                            </td>
                            <td>
                                <div class="fw-semibold text-primary"><?= htmlspecialchars($ord['nama_produk']) ?></div>
                                <div class="text-muted small"><?= $ord['jml_beli'] ?>x (<?= htmlspecialchars($ord['daya'] ?: '-') ?>)</div>
                            </td>
                            <td class="fw-bold"><?= rupiah($ord['total_bayar']) ?></td>
                            <td>
                                <?php if ($ord['status_pembayaran'] == 1): ?>
                                    <span class="badge bg-success">Lunas</span>
                                <?php elseif ($ord['status_pembayaran'] == 2): ?>
                                    <span class="badge bg-danger">Ditolak</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark">Perlu Dicek</span>
                                <?php endif; ?>

                                <?php if ($ord['bukti_transfer']): ?>
                                    <a href="uploads/bukti/<?= htmlspecialchars($ord['bukti_transfer']) ?>" target="_blank" class="d-block small text-decoration-none mt-1">
                                        <i class="bi bi-image"></i> Bukti Bayar
                                    </a>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($ord['jadwal_pemasangan']): ?>
                                    <span class="fw-semibold text-dark small"><i class="bi bi-calendar-event text-primary me-1"></i> <?= htmlspecialchars($ord['jadwal_pemasangan']) ?></span>
                                    <div class="text-muted small">Armada: <?= htmlspecialchars($ord['kurir'] ?: 'Teknisi PT. Eksavindo') ?></div>
                                <?php else: ?>
                                    <span class="badge bg-light text-secondary border">Belum Dijadwalkan</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="index.php?page=admin_pesanan_detail&id=<?= $ord['id_pesanan'] ?>" class="btn btn-sm btn-primary rounded-pill px-3">
                                    <i class="bi bi-gear-fill me-1"></i> Proses
                                </a>
                                <a href="index.php?page=kuitansi&id=<?= $ord['id_pesanan'] ?>" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill" title="Cetak Kuitansi">
                                    <i class="bi bi-printer"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchPesananInput');
    const tableBody = document.getElementById('pesananTableBody');
    const spinner = document.getElementById('pesananSpinner');
    const searchIcon = document.getElementById('pesananSearchIcon');
    const btnReset = document.getElementById('btnResetPesanan');
    const infoCount = document.getElementById('pesananInfoCount');
    let timer;

    function fetchPesananAjax() {
        const val = searchInput.value.trim();
        if (val.length > 0) {
            btnReset.classList.remove('d-none');
        } else {
            btnReset.classList.add('d-none');
        }

        searchIcon.classList.add('d-none');
        spinner.classList.remove('d-none');

        const params = new URLSearchParams({
            page: 'admin_pesanan',
            ajax: '1',
            status_bayar: '<?= $filterStatus ?>',
            search: val
        });

        fetch(`index.php?${params.toString()}`)
            .then(res => res.text())
            .then(html => {
                tableBody.innerHTML = html;
                const rows = tableBody.querySelectorAll('tr').length;
                if (infoCount) {
                    infoCount.textContent = `${rows} pesanan ditampilkan`;
                }
            })
            .catch(err => console.error("Error AJAX Pesanan:", err))
            .finally(() => {
                spinner.classList.add('d-none');
                searchIcon.classList.remove('d-none');
            });
    }

    searchInput.addEventListener('input', function() {
        clearTimeout(timer);
        timer = setTimeout(fetchPesananAjax, 250);
    });

    btnReset.addEventListener('click', function() {
        searchInput.value = '';
        btnReset.classList.add('d-none');
        fetchPesananAjax();
        searchInput.focus();
    });
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
