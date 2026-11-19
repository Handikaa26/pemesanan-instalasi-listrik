<?php
// views/konsumen/kuitansi.php
require_login();
$id_pesanan = $_GET['id'] ?? '';
$db = Database::connect();
$currentUser = get_auth_user();

$sql = "SELECT p.*, pr.nama_produk, pr.daya, pr.ukuran, pr.harga AS harga_asli,
               b.nama_bank, b.norek, b.atas_nama,
               a.alamat, a.penerima, a.telepon_penerima, a.tipe_alamat, a.kota, a.provinsi, a.kode_pos,
               u.username, u.email, u.nomor_telp
        FROM pesanan p
        JOIN produk pr ON p.id_produk = pr.id_produk
        JOIN bank b ON p.id_bank = b.id_bank
        JOIN alamat a ON p.id_alamat = a.id_alamat
        JOIN user u ON p.id_user = u.id_user
        WHERE p.id_pesanan = ?";

// Jika bukan admin, pastikan hanya bisa lihat pesanan miliknya sendiri
if (!is_admin()) {
    $sql .= " AND p.id_user = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$id_pesanan, $currentUser['id_user']]);
} else {
    $stmt = $db->prepare($sql);
    $stmt->execute([$id_pesanan]);
}

$pesanan = $stmt->fetch();
if (!$pesanan) {
    die("Data pesanan / kuitansi tidak ditemukan atau Anda tidak memiliki akses.");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kuitansi & Invoice <?= htmlspecialchars($pesanan['id_pesanan']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f1f5f9; color: #1e293b; }
        .invoice-box {
            max-width: 850px;
            margin: 30px auto;
            padding: 35px;
            background: #fff;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
            border-radius: 8px;
        }
        .invoice-header {
            border-bottom: 2px solid #0b4f9c;
            padding-bottom: 20px;
            margin-bottom: 25px;
        }
        .stamp-lunas {
            border: 3px dashed #16a34a;
            color: #16a34a;
            font-size: 1.4rem;
            font-weight: 800;
            padding: 8px 18px;
            display: inline-block;
            transform: rotate(-5deg);
            border-radius: 6px;
            text-transform: uppercase;
        }
        .stamp-pending {
            border: 3px dashed #f59e0b;
            color: #d97706;
            font-size: 1.2rem;
            font-weight: 800;
            padding: 8px 18px;
            display: inline-block;
            transform: rotate(-5deg);
            border-radius: 6px;
            text-transform: uppercase;
        }
        @media print {
            .no-print { display: none !important; }
            body { background: #fff; }
            .invoice-box { box-shadow: none; margin: 0; padding: 0; }
        }
    </style>
</head>
<body>

<div class="container my-3 no-print text-center">
    <button onclick="window.print()" class="btn btn-primary px-4 fw-bold me-2"><i class="bi bi-printer me-1"></i> Cetak / Simpan PDF</button>
    <a href="index.php?page=pesanan_saya" class="btn btn-outline-secondary px-3"><i class="bi bi-arrow-left me-1"></i> Kembali</a>
</div>

<div class="invoice-box">
    <div class="invoice-header d-flex justify-content-between align-items-center">
        <div>
            <h3 class="fw-bold text-primary mb-1"><i class="bi bi-lightning-charge-fill text-warning"></i> PT. EKSAVINDO MITRA MANDIRI</h3>
            <p class="text-muted small mb-0">Jasa Instalasi & Pemeliharaan Kelistrikan Terstandar PUIL</p>
            <p class="text-muted small mb-0">Jl. Ki Hajar Dewantara No. 15, Metro, Lampung | Telp: (0725) 456-7890</p>
        </div>
        <div class="text-end">
            <h4 class="fw-bold text-secondary mb-0">KUITANSI / INVOICE</h4>
            <div class="text-primary font-monospace fw-bold fs-5"><?= htmlspecialchars($pesanan['id_pesanan']) ?></div>
            <div class="small text-muted">Tanggal: <?= date('d F Y', strtotime($pesanan['created_at'])) ?></div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-6">
            <h6 class="fw-bold text-muted small text-uppercase">Diberikan Kepada (Konsumen):</h6>
            <h5 class="fw-bold mb-1"><?= htmlspecialchars($pesanan['username']) ?></h5>
            <div class="small text-muted">Email: <?= htmlspecialchars($pesanan['email']) ?></div>
            <div class="small text-muted">Telepon: <?= htmlspecialchars($pesanan['nomor_telp'] ?: '-') ?></div>
        </div>
        <div class="col-6 text-end">
            <h6 class="fw-bold text-muted small text-uppercase">Lokasi Pemasangan Instalasi:</h6>
            <div class="fw-bold"><?= htmlspecialchars($pesanan['penerima']) ?> (<?= htmlspecialchars($pesanan['telepon_penerima']) ?>) <span class="badge bg-light text-dark border ms-1"><?= htmlspecialchars($pesanan['tipe_alamat'] ?? 'Rumah') ?></span></div>
            <div class="small text-muted"><?= htmlspecialchars($pesanan['alamat']) ?></div>
            <div class="small text-muted"><?= htmlspecialchars($pesanan['kota'] ?? 'Kota Metro') ?>, <?= htmlspecialchars($pesanan['provinsi'] ?? 'Lampung') ?> <?= !empty($pesanan['kode_pos']) ? '- ' . htmlspecialchars($pesanan['kode_pos']) : '' ?></div>
        </div>
    </div>

    <div class="table-responsive mb-4">
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Deskripsi Paket / Layanan</th>
                    <th class="text-center">Kapasitas / Tipe</th>
                    <th class="text-center">Jumlah</th>
                    <th class="text-end">Total Biaya</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong><?= htmlspecialchars($pesanan['nama_produk']) ?></strong>
                        <div class="small text-muted">Layanan instalasi listrik standar keselamatan PUIL & garansi 30 hari</div>
                    </td>
                    <td class="text-center"><?= htmlspecialchars($pesanan['daya'] ?: '-') ?></td>
                    <td class="text-center"><?= $pesanan['jml_beli'] ?> paket</td>
                    <td class="text-end fw-bold"><?= rupiah($pesanan['total_bayar']) ?></td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3" class="text-end">Total Pembayaran:</th>
                    <th class="text-end fs-5 text-primary"><?= rupiah($pesanan['total_bayar']) ?></th>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="row align-items-center mt-4 pt-2 border-top">
        <div class="col-md-7">
            <div class="p-3 bg-light rounded-3 small">
                <div class="fw-bold mb-1">Informasi Penjadwalan & Armada:</div>
                <div>Jadwal Instalasi: <strong><?= $pesanan['jadwal_pemasangan'] ? htmlspecialchars($pesanan['jadwal_pemasangan']) : 'Akan dijadwalkan oleh admin' ?></strong></div>
                <div>Armada / Teknisi: <strong><?= htmlspecialchars($pesanan['kurir'] ?: 'Teknisi PT. Eksavindo') ?></strong></div>
                <div>Metode Bayar: <strong>Transfer <?= htmlspecialchars($pesanan['nama_bank']) ?> (<?= htmlspecialchars($pesanan['norek']) ?> a/n <?= htmlspecialchars($pesanan['atas_nama']) ?>)</strong></div>
            </div>
        </div>
        <div class="col-md-5 text-center mt-3 mt-md-0">
            <?php if ($pesanan['status_pembayaran'] == 1): ?>
                <div class="stamp-lunas mb-2">LUNAS / VERIFIED</div>
                <div class="small text-muted">PT. Eksavindo Mitra Mandiri</div>
            <?php else: ?>
                <div class="stamp-pending mb-2">MENUNGGU VERIFIKASI</div>
                <div class="small text-muted">Menunggu konfirmasi penerimaan dana</div>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>

